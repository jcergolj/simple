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

        // Get current client_id from the form
        const searchId = this.element.dataset.searchId || 'main';
        const clientIdInput = document.getElementById(searchId + '-client-id');
        const clientId = clientIdInput ? clientIdInput.value : "";

        const searchParams = new URLSearchParams({
            q: q
        });

        if (clientId) {
            searchParams.append('client_id', clientId);
        }

        fetch(`/projects-search?${searchParams}`, {
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

                // Handle existing project links
                this.resultsTarget.querySelectorAll("a").forEach((el) => {
                    el.addEventListener("click", (e) => {
                        e.preventDefault();
                        this.selectProject(el);
                    });
                });

                // Handle form submission for creating new projects
                const form = this.resultsTarget.querySelector("form");
                if (form) {
                    form.addEventListener("submit", (e) => {
                        e.preventDefault();
                        this.createProject(form);
                    });
                }
            })
            .catch((error) => {
                console.error("Search error:", error);
            });
    }

    selectProject(el) {
        this.inputTarget.value = el.textContent.trim();
        const searchId = this.element.dataset.searchId || 'main';
        const projectIdInput = document.getElementById(searchId + '-project-id');
        if (projectIdInput) {
            projectIdInput.value = el.dataset.id;
        }
        this.closeResults();
    }

    createProject(form) {
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
                // Parse the response to extract project information
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Look for the hidden input with project_id
                const projectIdInput = tempDiv.querySelector('input[name="project_id"]');
                const projectName = formData.get('name');

                if (projectIdInput) {
                    // Update the search input and hidden field
                    this.inputTarget.value = projectName;
                    const searchId = this.element.dataset.searchId || 'main';
                    const mainProjectIdInput = document.getElementById(searchId + '-project-id');
                    if (mainProjectIdInput) {
                        mainProjectIdInput.value = projectIdInput.value;
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
                console.error("Create project error:", error);
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
            this.selectProject(items[this.selectedIndex]);
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
