import { Controller } from "@hotwired/stimulus"

// Connects to data-controller="delete-toggle"
export default class extends Controller {
    static targets = ["deleteButton"];

    connect() {
        // Initialize all delete buttons as hidden
        this.hideAll();
    }

    toggle() {
        this.deleteButtonTargets.forEach(button => {
            button.classList.toggle("hidden");
        });
    }

    show() {
        this.deleteButtonTargets.forEach(button => {
            button.classList.remove("hidden");
        });
    }

    hide() {
        this.deleteButtonTargets.forEach(button => {
            button.classList.add("hidden");
        });
    }

    showAll() {
        this.deleteButtonTargets.forEach(button => {
            button.classList.remove("hidden");
        });
    }

    hideAll() {
        this.deleteButtonTargets.forEach(button => {
            button.classList.add("hidden");
        });
    }
}