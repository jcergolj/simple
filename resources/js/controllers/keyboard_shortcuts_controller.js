import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["startButton", "stopButton"]

  connect() {
    this.boundHandleKeydown = this.handleKeydown.bind(this)
    document.addEventListener('keydown', this.boundHandleKeydown)
    this.showShortcutHints()
  }

  disconnect() {
    document.removeEventListener('keydown', this.boundHandleKeydown)
  }

  handleKeydown(event) {
    // Check for Ctrl+Shift+S (Start Timer)
    if (event.ctrlKey && event.shiftKey && event.key === 'S') {
      event.preventDefault()
      this.startTimer()
      return
    }

    // Check for Ctrl+Shift+T (Stop Timer)
    if (event.ctrlKey && event.shiftKey && event.key === 'T') {
      event.preventDefault()
      this.stopTimer()
      return
    }

    // Check for Ctrl+Shift+Space (Toggle Timer)
    if (event.ctrlKey && event.shiftKey && event.code === 'Space') {
      event.preventDefault()
      this.toggleTimer()
      return
    }
  }

  startTimer() {
    if (this.hasStartButtonTarget) {
      this.startButtonTarget.click()
      this.showNotification('Timer started via keyboard shortcut!', 'success')
    }
  }

  stopTimer() {
    if (this.hasStopButtonTarget) {
      this.stopButtonTarget.click()
      this.showNotification('Timer stopped via keyboard shortcut!', 'success')
    }
  }

  toggleTimer() {
    if (this.hasStartButtonTarget) {
      this.startTimer()
    } else if (this.hasStopButtonTarget) {
      this.stopTimer()
    }
  }

  showNotification(message, type = 'info') {
    // Create a temporary toast notification
    const toast = document.createElement('div')
    toast.className = `toast toast-top toast-end z-50`
    toast.innerHTML = `
      <div class="alert alert-${type} alert-sm">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>${message}</span>
      </div>
    `

    document.body.appendChild(toast)

    // Auto-remove after 3 seconds
    setTimeout(() => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast)
      }
    }, 3000)
  }

  showShortcutHints() {
    // Add data attributes to show keyboard shortcuts in tooltips
    if (this.hasStartButtonTarget) {
      this.startButtonTarget.setAttribute('title', 'Keyboard shortcut: Ctrl+Shift+S')
    }
    if (this.hasStopButtonTarget) {
      this.stopButtonTarget.setAttribute('title', 'Keyboard shortcut: Ctrl+Shift+T')
    }
  }
}