'// Enhanced Turbo Frame Loading States
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to turbo frames
    document.addEventListener('turbo:frame-missing', function(event) {
        console.warn('Turbo frame missing:', event.detail);
    });

    // Handle turbo frame loading states
    document.addEventListener('turbo:before-fetch-request', function(event) {
        const frame = event.target.closest('turbo-frame');
        if (frame && frame.id.includes('project-filter')) {
            frame.setAttribute('aria-busy', 'true');
            
            // Add subtle loading indicator
            const select = frame.querySelector('select');
            if (select) {
                select.style.opacity = '0.7';
                select.style.backgroundColor = '#f9fafb';
            }
        }
    });

    document.addEventListener('turbo:frame-load', function(event) {
        const frame = event.target;
        if (frame && frame.id.includes('project-filter')) {
            frame.removeAttribute('aria-busy');
            
            // Remove loading state
            const select = frame.querySelector('select');
            if (select) {
                select.style.opacity = '';
                select.style.backgroundColor = '';
            }
        }
    });

    // Handle frame render
    document.addEventListener('turbo:frame-render', function(event) {
        const frame = event.target;
        if (frame && frame.id.includes('project-filter')) {
            frame.removeAttribute('aria-busy');
        }
    });

    // Enhance client selection change with debouncing
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Enhanced client selection handling
    document.querySelectorAll('select[name="client_id"]').forEach(function(clientSelect) {
        clientSelect.addEventListener('change', debounce(function() {
            const selectedClientId = this.value;
            const currentProjectId = document.querySelector('select[name="project_id"]')?.value || '';
            
            // Only proceed if we have a valid route
            const routeUrl = document.querySelector('meta[name="project-filter-route"]')?.content;
            const filterUrl = routeUrl ? 
                `${routeUrl}?client_id=${selectedClientId}&selected_project_id=${currentProjectId}` :
                `{{ route('project-filter') }}?client_id=${selectedClientId}&selected_project_id=${currentProjectId}`;

            // Update both mobile and desktop turbo frames with loading states
            ['project-filter-mobile', 'project-filter-desktop'].forEach(frameId => {
                const frame = document.getElementById(frameId);
                if (frame) {
                    frame.setAttribute('aria-busy', 'true');
                    frame.setAttribute('src', filterUrl);
                    
                    // Add immediate visual feedback
                    const select = frame.querySelector('select');
                    if (select) {
                        select.style.opacity = '0.7';
                        select.style.backgroundColor = '#f9fafb';
                        
                        // Update loading text
                        const loadingOption = select.querySelector('option');
                        if (loadingOption && selectedClientId) {
                            loadingOption.textContent = 'Loading projects...';
                        }
                    }
                    
                    frame.reload();
                }
            });
        }, 150)); // 150ms debounce
    });
});
