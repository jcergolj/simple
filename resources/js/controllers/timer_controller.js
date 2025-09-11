import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["display"]
  static values = {
    running: Boolean,
    startTime: Number
  }

  connect() {
    if (this.runningValue) {
      this.startTimer()
    }
  }

  disconnect() {
    this.stopTimer()
  }

  startTimer() {
    this.updateDisplay()
    this.timer = setInterval(() => {
      this.updateDisplay()
    }, 1000)
  }

  stopTimer() {
    if (this.timer) {
      clearInterval(this.timer)
      this.timer = null
    }
  }

  updateDisplay() {
    if (!this.hasDisplayTarget || !this.runningValue) return

    const now = Math.floor(Date.now() / 1000)
    const elapsed = now - this.startTimeValue

    const hours = Math.floor(elapsed / 3600)
    const minutes = Math.floor((elapsed % 3600) / 60)
    const seconds = elapsed % 60

    const timeString = [
      hours.toString().padStart(2, '0'),
      minutes.toString().padStart(2, '0'),
      seconds.toString().padStart(2, '0')
    ].join(':')

    this.displayTarget.textContent = timeString
  }

  runningValueChanged() {
    if (this.runningValue) {
      this.startTimer()
    } else {
      this.stopTimer()
    }
  }
}