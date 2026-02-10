<!-- Generic Confirmation Modal -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="confirmModalTitle">Confirm Action</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <p id="confirmModalMessage">Are you sure you want to proceed?</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeConfirmationModal()">Cancel</button>
            <button class="btn-confirm" id="confirmModalBtn">Confirm</button>
        </div>
    </div>
</div>

<script>
    // Generic Confirmation Modal Logic
    const confirmationModal = document.getElementById('confirmationModal');
    const confirmModalTitle = document.getElementById('confirmModalTitle');
    const confirmModalMessage = document.getElementById('confirmModalMessage');
    const confirmModalBtn = document.getElementById('confirmModalBtn');
    const closeModalBtn = confirmationModal ? confirmationModal.querySelector('.close-modal') : null;

    let confirmCallback = null;

    /**
     * Show confirmation modal with custom parameters
     * @param {Object} options - Configuration object
     * @param {string} options.title - Modal title
     * @param {string} options.message - Modal message
     * @param {string} options.confirmText - Confirm button text
     * @param {Function} options.onConfirm - Callback function when confirmed
     */
    function showConfirmationModal(options) {
        if (!confirmationModal) return;

        // Set content
        if (confirmModalTitle) confirmModalTitle.textContent = options.title || 'Confirm Action';
        if (confirmModalMessage) confirmModalMessage.textContent = options.message || 'Are you sure?';
        if (confirmModalBtn) confirmModalBtn.textContent = options.confirmText || 'Confirm';

        // Store callback
        confirmCallback = options.onConfirm || null;

        // Show modal
        confirmationModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeConfirmationModal() {
        if (confirmationModal) {
            confirmationModal.classList.remove('show');
            document.body.style.overflow = '';
            confirmCallback = null;
        }
    }

    // Confirm button click
    if (confirmModalBtn) {
        confirmModalBtn.addEventListener('click', function () {
            if (confirmCallback && typeof confirmCallback === 'function') {
                confirmCallback();
            }
            closeConfirmationModal();
        });
    }

    // Close on click outside
    window.addEventListener('click', function (event) {
        if (event.target === confirmationModal) {
            closeConfirmationModal();
        }
    });

    // Close on X click
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeConfirmationModal);
    }

    // Close on Escape key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && confirmationModal && confirmationModal.classList.contains('show')) {
            closeConfirmationModal();
        }
    });

    // Helper function for logout
    function confirmLogout(event) {
        event.preventDefault();
        showConfirmationModal({
            title: 'Confirm Logout',
            message: 'Are you sure you want to log out of your account?',
            confirmText: 'Logout',
            onConfirm: function () {
                window.location.href = '/logout';
            }
        });
    }
</script>