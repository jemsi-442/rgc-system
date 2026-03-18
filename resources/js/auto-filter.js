/**
 * Auto Filter - Automatic form submission for filters without button click
 * Supports: select, date, text inputs with debounce
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get all filter forms
    const filterForms = document.querySelectorAll('[data-auto-filter="true"]');

    filterForms.forEach(form => {
        const ajaxTarget = form.getAttribute('data-ajax-target');
        if (ajaxTarget) {
            initAjaxAutoFilter(form, ajaxTarget);
            return;
        }

        const inputs = form.querySelectorAll('input, select');
        let debounceTimer;

        inputs.forEach(input => {
            // For select and date inputs - submit immediately on change
            if (input.tagName === 'SELECT' || input.type === 'date') {
                input.addEventListener('change', function() {
                    form.submit();
                });
            }

            // For text inputs - debounce to avoid too many requests
            if (input.type === 'text' || input.type === 'search') {
                input.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        form.submit();
                    }, 500); // Wait 500ms after user stops typing
                });
            }
        });
    });
});

/**
 * AJAX-based auto filter with real-time data loading
 * This function handles filter changes without page reload
 */
function initAjaxAutoFilter(formSelector, tableSelector, options = {}) {
    const form = typeof formSelector === 'string' ? document.querySelector(formSelector) : formSelector;
    const tableContainer = typeof tableSelector === 'string' ? document.querySelector(tableSelector) : tableSelector;

    if (!form || !tableContainer) return;

    const inputs = form.querySelectorAll('input, select');
    let debounceTimer;
    const debounceDelay = options.debounceDelay || 500;

    // Create loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10';
    loadingOverlay.innerHTML = '<div class="flex flex-col items-center"><i class="fas fa-spinner fa-spin text-4xl text-primary-500 mb-2"></i><p class="text-gray-600">Inapakia...</p></div>';
    loadingOverlay.style.display = 'none';

    // Make table container relative for overlay positioning
    if (tableContainer.style.position !== 'relative') {
        tableContainer.style.position = 'relative';
    }
    tableContainer.appendChild(loadingOverlay);

    // Function to fetch and update data
    function fetchData() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        const url = form.action + '?' + params.toString();

        // Show loading overlay
        loadingOverlay.style.display = 'flex';

        // Fetch data using AJAX
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Update table content
            const newTableContainer = doc.querySelector(tableSelector);
            if (newTableContainer) {
                tableContainer.innerHTML = newTableContainer.innerHTML;

                // Re-attach the loading overlay
                tableContainer.appendChild(loadingOverlay);
            }

            // Hide loading overlay
            loadingOverlay.style.display = 'none';

            // Update URL without page reload
            window.history.pushState({}, '', url);

            // Trigger custom event for other scripts
            const event = new CustomEvent('dataUpdated', { detail: { url: url } });
            document.dispatchEvent(event);
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            loadingOverlay.style.display = 'none';

            // Fallback to normal form submission
            form.submit();
        });
    }

    // Attach event listeners
    inputs.forEach(input => {
        // For select and date inputs - submit immediately on change
        if (input.tagName === 'SELECT' || input.type === 'date') {
            input.addEventListener('change', fetchData);
        }

        // For text inputs - debounce to avoid too many requests
        if (input.type === 'text' || input.type === 'search') {
            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchData, debounceDelay);
            });
        }
    });

    // Handle pagination links
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('a[href*="page="]');
        if (paginationLink && tableContainer.contains(paginationLink)) {
            e.preventDefault();

            const url = paginationLink.href;
            loadingOverlay.style.display = 'flex';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableContainer = doc.querySelector(tableSelector);

                if (newTableContainer) {
                    tableContainer.innerHTML = newTableContainer.innerHTML;
                    tableContainer.appendChild(loadingOverlay);
                }

                loadingOverlay.style.display = 'none';
                window.history.pushState({}, '', url);

                // Scroll to top of table
                tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(error => {
                console.error('Error fetching pagination:', error);
                loadingOverlay.style.display = 'none';
                window.location.href = url;
            });
        }
    });
}

if (typeof window !== 'undefined') {
    window.initAjaxAutoFilter = initAjaxAutoFilter;
    window.ajaxReloadContainer = function (selector) {
        const container = document.querySelector(selector);
        if (!container) return;

        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.querySelector(selector);
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            }
        });
    };
}
