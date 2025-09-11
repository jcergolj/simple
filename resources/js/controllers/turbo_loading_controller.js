import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["select"]
  static values = {
    frameIds: Array,
    routeUrl: String,
    debounceDelay: { type: Number, default: 150 }
  }

  connect() {
    this.setupTurboEventListeners()
    this.debouncedHandleChange = this.debounce(
      this.handleClientChange.bind(this),
      this.debounceDelayValue
    )
  }

  setupTurboEventListeners() {
    document.addEventListener('turbo:frame-missing', this.handleFrameMissing.bind(this))
    document.addEventListener('turbo:before-fetch-request', this.handleBeforeFetch.bind(this))
    document.addEventListener('turbo:frame-load', this.handleFrameLoad.bind(this))
    document.addEventListener('turbo:frame-render', this.handleFrameRender.bind(this))
  }

  handleFrameMissing(event) {
    console.warn('Turbo frame missing:', event.detail)
  }

  handleBeforeFetch(event) {
    const frame = event.target.closest('turbo-frame')
    if (frame && frame.id.includes('project-filter')) {
      this.setLoadingState(frame, true)
    }
  }

  handleFrameLoad(event) {
    const frame = event.target
    if (frame && frame.id.includes('project-filter')) {
      this.setLoadingState(frame, false)
    }
  }

  handleFrameRender(event) {
    const frame = event.target
    if (frame && frame.id.includes('project-filter')) {
      frame.removeAttribute('aria-busy')
    }
  }

  setLoadingState(frame, isLoading) {
    if (isLoading) {
      frame.setAttribute('aria-busy', 'true')
      const select = frame.querySelector('select')
      if (select) {
        select.style.opacity = '0.7'
        select.style.backgroundColor = '#f9fafb'
      }
    } else {
      frame.removeAttribute('aria-busy')
      const select = frame.querySelector('select')
      if (select) {
        select.style.opacity = ''
        select.style.backgroundColor = ''
      }
    }
  }

  change(event) {
    this.debouncedHandleChange()
  }

  handleClientChange() {
    const selectedClientId = this.selectTarget.value
    const currentProjectId = document.querySelector('select[name="project_id"]')?.value || ''

    const routeUrl = this.hasRouteUrlValue ?
      this.routeUrlValue :
      document.querySelector('meta[name="project-filter-route"]')?.content

    if (!routeUrl) {
      console.warn('Project filter route not found')
      return
    }

    const filterUrl = `${routeUrl}?client_id=${selectedClientId}&selected_project_id=${currentProjectId}`

    const frameIds = this.hasFrameIdsValue ?
      this.frameIdsValue :
      ['project-filter-mobile', 'project-filter-desktop']

    frameIds.forEach(frameId => {
      const frame = document.getElementById(frameId)
      if (frame) {
        frame.setAttribute('aria-busy', 'true')
        frame.setAttribute('src', filterUrl)

        const select = frame.querySelector('select')
        if (select) {
          select.style.opacity = '0.7'
          select.style.backgroundColor = '#f9fafb'

          const loadingOption = select.querySelector('option')
          if (loadingOption && selectedClientId) {
            loadingOption.textContent = 'Loading projects...'
          }
        }

        frame.reload()
      }
    })
  }

  debounce(func, wait) {
    let timeout
    return (...args) => {
      const later = () => {
        clearTimeout(timeout)
        func(...args)
      }
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
    }
  }

  disconnect() {
    document.removeEventListener('turbo:frame-missing', this.handleFrameMissing)
    document.removeEventListener('turbo:before-fetch-request', this.handleBeforeFetch)
    document.removeEventListener('turbo:frame-load', this.handleFrameLoad)
    document.removeEventListener('turbo:frame-render', this.handleFrameRender)
  }
}
