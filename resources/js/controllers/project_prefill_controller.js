import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static values = { last: String }

  connect() {
    // Auto-select the last used project if available
    if (this.lastValue && this.element.value === '') {
      this.element.value = this.lastValue
      this.element.dispatchEvent(new Event('change', { bubbles: true }))
    }
  }
}