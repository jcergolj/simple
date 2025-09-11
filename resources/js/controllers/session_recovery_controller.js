import { Controller } from "@hotwired/stimulus";

/**
 * Session Recovery Controller
 *
 * Handles expired sessions (419 errors) by attempting to refresh the CSRF token.
 * If the user is still authenticated (via Remember Me cookie), the request is retried.
 * If not authenticated, redirects to login page with session expired message.
 *
 * Usage: Add data-controller="session-recovery" to <body> or root element
 */
export default class extends Controller {
    isRefreshing = false;
    pendingRequests = [];

    connect() {
        this.handleBeforeFetchResponse = this.handleBeforeFetchResponse.bind(this);
        this.handleFrameMissing = this.handleFrameMissing.bind(this);

        document.addEventListener("turbo:before-fetch-response", this.handleBeforeFetchResponse);
        document.addEventListener("turbo:frame-missing", this.handleFrameMissing);

        // Check if there's a failed request to retry after login redirect
        this.retryFailedRequestIfPresent();
    }

    disconnect() {
        document.removeEventListener("turbo:before-fetch-response", this.handleBeforeFetchResponse);
        document.removeEventListener("turbo:frame-missing", this.handleFrameMissing);
    }

    async handleBeforeFetchResponse(event) {
        const response = event.detail.fetchResponse.response;

        if (response.status === 419) {
            event.preventDefault();
            await this.handle419Error(event);
        }
    }

    handleFrameMissing(event) {
        console.warn("Turbo frame missing:", event.detail);

        // Check if this was caused by a 419 error
        const response = event.detail.response;
        if (response?.status === 419) {
            event.preventDefault();
            this.redirectToLogin();
        }
    }

    async handle419Error(event) {
        // Capture the failed request details
        const failedRequest = this.captureFailedRequest(event);

        // If already refreshing, queue this request
        if (this.isRefreshing) {
            return new Promise((resolve) => {
                this.pendingRequests.push(resolve);
            });
        }

        this.isRefreshing = true;

        try {
            // Try to get a fresh CSRF token
            const newToken = await this.fetchFreshToken();

            if (newToken) {
                // User is still authenticated - update token and retry
                this.updateCsrfToken(newToken);

                // Resolve any pending requests
                this.pendingRequests.forEach((resolve) => resolve());
                this.pendingRequests = [];

                // Retry the failed request if it was an AJAX call
                if (failedRequest && this.isAjaxRequest(failedRequest)) {
                    await this.retryRequest(failedRequest, newToken);
                } else {
                    // Reload the current frame or page to retry with new token
                    const frame = event.target.closest("turbo-frame");
                    if (frame) {
                        frame.reload();
                    } else {
                        // For non-frame requests, reload the page
                        window.location.reload();
                    }
                }
            } else {
                // User is not authenticated - store failed request and redirect to login
                if (failedRequest) {
                    this.storeFailedRequest(failedRequest);
                }
                this.redirectToLogin();
            }
        } finally {
            this.isRefreshing = false;
        }
    }

    async fetchFreshToken() {
        try {
            const response = await fetch("/api/csrf-token", {
                method: "GET",
                credentials: "same-origin",
                headers: {
                    Accept: "application/json",
                },
            });

            if (response.ok) {
                const data = await response.json();
                return data.token;
            }

            // 401 means not authenticated - Remember Me cookie expired/invalid
            if (response.status === 401) {
                return null;
            }

            console.error("Failed to fetch CSRF token:", response.status);
            return null;
        } catch (error) {
            console.error("Error fetching CSRF token:", error);
            return null;
        }
    }

    updateCsrfToken(token) {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute("content", token);
        }
    }

    redirectToLogin() {
        const currentUrl = window.location.href;
        sessionStorage.setItem("intended_url", currentUrl);
        sessionStorage.setItem("session_expired", "1");
        window.location.href = "/login";
    }

    captureFailedRequest(event) {
        try {
            const fetchOptions = event.detail.fetchOptions;
            const url = event.detail.url || fetchOptions?.url;

            if (!url) {
                return null;
            }

            return {
                url: url,
                method: fetchOptions?.method || "GET",
                headers: fetchOptions?.headers || {},
                body: fetchOptions?.body || null,
            };
        } catch (error) {
            console.error("Error capturing failed request:", error);
            return null;
        }
    }

    isAjaxRequest(request) {
        // Check if it's an AJAX request based on headers or method
        const headers = request.headers || {};
        const isXHR = headers["X-Requested-With"] === "XMLHttpRequest";
        const isJSON = headers["Accept"]?.includes("application/json");
        const isNotGetOrHead = !["GET", "HEAD"].includes(request.method);

        return isXHR || isJSON || isNotGetOrHead;
    }

    storeFailedRequest(request) {
        try {
            sessionStorage.setItem("failed_request", JSON.stringify(request));
        } catch (error) {
            console.error("Error storing failed request:", error);
        }
    }

    async retryRequest(request, token) {
        try {
            // Update CSRF token in headers
            const headers = { ...request.headers };
            headers["X-CSRF-TOKEN"] = token;

            const response = await fetch(request.url, {
                method: request.method,
                headers: headers,
                body: request.body,
                credentials: "same-origin",
            });

            if (response.ok) {
                console.log("Failed request successfully retried");
                // Handle response based on content type
                const contentType = response.headers.get("content-type");
                if (contentType?.includes("application/json")) {
                    const data = await response.json();
                    // Dispatch custom event with response data
                    document.dispatchEvent(
                        new CustomEvent("session-recovery:request-retried", {
                            detail: { response: data, originalRequest: request },
                        })
                    );
                } else {
                    // For non-JSON responses, reload the page
                    window.location.reload();
                }
            } else {
                console.error("Failed to retry request:", response.status);
                window.location.reload();
            }
        } catch (error) {
            console.error("Error retrying request:", error);
            window.location.reload();
        }
    }

    async retryFailedRequestIfPresent() {
        try {
            const storedRequest = sessionStorage.getItem("failed_request");
            if (!storedRequest) {
                return;
            }

            // Clear the stored request
            sessionStorage.removeItem("failed_request");

            const request = JSON.parse(storedRequest);

            // Get fresh CSRF token
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

            if (token && request) {
                // Small delay to ensure page is fully loaded
                setTimeout(() => {
                    this.retryRequest(request, token);
                }, 100);
            }
        } catch (error) {
            console.error("Error retrying failed request:", error);
            sessionStorage.removeItem("failed_request");
        }
    }
}
