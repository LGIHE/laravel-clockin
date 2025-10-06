/**
 * Toast notification helper
 * Usage: window.toast('Message here', 'success')
 */
window.toast = function(message, variant = 'info') {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { message, variant }
    }));
};

/**
 * Modal helper
 * Usage: window.openModal('modal-name')
 */
window.openModal = function(name) {
    window.dispatchEvent(new CustomEvent('open-modal', {
        detail: name
    }));
};

window.closeModal = function(name) {
    window.dispatchEvent(new CustomEvent('close-modal', {
        detail: name
    }));
};
