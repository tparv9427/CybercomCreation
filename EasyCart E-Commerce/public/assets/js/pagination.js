// ============================================
// AJAX PAGINATION
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const paginationLinks = document.querySelectorAll('.pagination-link');

    paginationLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            // Skip if disabled
            if (this.classList.contains('disabled') || this.classList.contains('active')) {
                e.preventDefault();
                return;
            }

            e.preventDefault();

            const page = this.getAttribute('data-page');
            const url = this.getAttribute('href');

            // Get the product grid container
            const gridView = document.getElementById('gridView');
            const rowView = document.getElementById('rowView');
            const container = gridView && gridView.style.display !== 'none' ? gridView : rowView;

            if (!container) return;

            // Save scroll position relative to container
            const containerTop = container.getBoundingClientRect().top + window.scrollY;

            // Show loading state
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';

            // Fetch new page content
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.text())
                .then(html => {
                    // Parse the HTML
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Update product grid
                    const newGridView = doc.getElementById('gridView');
                    const newRowView = doc.getElementById('rowView');

                    if (gridView && newGridView) {
                        gridView.innerHTML = newGridView.innerHTML;
                    }
                    if (rowView && newRowView) {
                        rowView.innerHTML = newRowView.innerHTML;
                    }

                    // Update pagination (both top and bottom)
                    const paginationElements = document.querySelectorAll('.pagination');
                    const newPaginationElements = doc.querySelectorAll('.pagination');

                    paginationElements.forEach((pagination, index) => {
                        if (newPaginationElements[index]) {
                            pagination.innerHTML = newPaginationElements[index].innerHTML;
                        }
                    });

                    // Update results count
                    const resultsCount = document.querySelector('.results-count');
                    const newResultsCount = doc.querySelector('.results-count');
                    if (resultsCount && newResultsCount) {
                        resultsCount.textContent = newResultsCount.textContent;
                    }

                    // Restore container state
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';

                    // Scroll to the top of the container (not page top)
                    window.scrollTo({
                        top: containerTop - 100, // 100px offset for header
                        behavior: 'smooth'
                    });

                    // Update URL without page reload
                    window.history.pushState({ page: page }, '', url);

                    // Reinitialize pagination links
                    initPaginationLinks();
                })
                .catch(error => {
                    console.error('Error loading page:', error);
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                    // Fallback to normal navigation
                    window.location.href = url;
                });
        });
    });
});

function initPaginationLinks() {
    const paginationLinks = document.querySelectorAll('.pagination-link');

    paginationLinks.forEach(link => {
        // Remove existing listeners by cloning
        const newLink = link.cloneNode(true);
        link.parentNode.replaceChild(newLink, link);
    });

    // Re-run the event listener setup
    document.dispatchEvent(new Event('DOMContentLoaded'));
}

