import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["input", "results"];
    selectedIndex = -1;

    connect() {
        this.handleClickOutside = this.handleClickOutside.bind(this);
        document.addEventListener('click', this.handleClickOutside);
    }

    disconnect() {
        document.removeEventListener('click', this.handleClickOutside);
    }

    handleClickOutside(event) {
        if (!this.element.contains(event.target)) {
            this.closeResults();
        }
    }

    closeResults() {
        this.resultsTarget.innerHTML = "";
        this.selectedIndex = -1;
    }

    query() {
        const q = this.inputTarget.value.trim();
        this.selectedIndex = -1;

        if (q === "") {
            this.resultsTarget.innerHTML = "";
            return;
        }

        fetch(`/clients-search?q=${encodeURIComponent(q)}`, {
            headers: {
                Accept: "text/vnd.turbo-stream.html",
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => {
                if (response.ok) {
                    return response.text();
                }
                throw new Error("Network response was not ok");
            })
            .then((html) => {
                this.resultsTarget.innerHTML = html;

                // Handle existing client links
                this.resultsTarget.querySelectorAll("a").forEach((el) => {
                    el.addEventListener("click", (e) => {
                        e.preventDefault();
                        this.selectClient(el);
                    });
                });

                // Handle form submission for creating new clients
                const form = this.resultsTarget.querySelector("form");
                if (form) {
                    form.addEventListener("submit", (e) => {
                        e.preventDefault();
                        this.createClient(form);
                    });
                }
            })
            .catch((error) => {
                console.error("Search error:", error);
            });
    }

    selectClient(el) {
        this.inputTarget.value = el.textContent.trim();
        const searchId = this.element.dataset.searchId || 'main';
        const clientIdInput = document.getElementById(searchId + '-client-id');
        if (clientIdInput) {
            clientIdInput.value = el.dataset.id;
        }
        this.closeResults();
    }

    createClient(form) {
        const formData = new FormData(form);

        fetch(form.action, {
            method: "POST",
            body: formData,
            headers: {
                Accept: "text/vnd.turbo-stream.html",
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => {
                if (response.ok) {
                    return response.text();
                }
                throw new Error("Network response was not ok");
            })
            .then((html) => {
                // Parse the response to extract client information
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Look for the hidden input with client_id
                const clientIdInput = tempDiv.querySelector('input[name="client_id"]');
                const clientName = formData.get('name');

                if (clientIdInput) {
                    // Update the search input and hidden field
                    this.inputTarget.value = clientName;
                    const searchId = this.element.dataset.searchId || 'main';
                    const mainClientIdInput = document.getElementById(searchId + '-client-id');
                    if (mainClientIdInput) {
                        mainClientIdInput.value = clientIdInput.value;
                    }
                }

                // Show the success message
                this.resultsTarget.innerHTML = html;
                this.selectedIndex = -1;

                // Clear the results after a brief delay
                setTimeout(() => {
                    this.resultsTarget.innerHTML = "";
                }, 3000);
            })
            .catch((error) => {
                console.error("Create client error:", error);
            });
    }

    navigate(event) {
        const items = Array.from(this.resultsTarget.querySelectorAll("a"));
        if (items.length === 0) return;

        if (event.key === "ArrowDown") {
            event.preventDefault();
            this.selectedIndex = (this.selectedIndex + 1) % items.length;
            this.highlight(items);
        } else if (event.key === "ArrowUp") {
            event.preventDefault();
            this.selectedIndex = (this.selectedIndex - 1 + items.length) % items.length;
            this.highlight(items);
        } else if (event.key === "Enter" && this.selectedIndex >= 0) {
            event.preventDefault();
            this.selectClient(items[this.selectedIndex]);
        } else if (event.key === "Escape") {
            this.closeResults();
        }
    }

    highlight(items) {
        items.forEach((el, i) => {
            if (i === this.selectedIndex) {
                el.classList.add("bg-primary", "text-primary-content");
            } else {
                el.classList.remove("bg-primary", "text-primary-content");
            }
        });
    }
}
