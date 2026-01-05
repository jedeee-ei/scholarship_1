/**
 * Global Error Handler Utility
 * Provides user-friendly error messages instead of technical ones
 */

window.ErrorHandler = {
    /**
     * Handle AJAX/Fetch errors with user-friendly messages
     */
    handleAjaxError: function(error, operation = 'operation') {
        console.error('Error:', error);
        
        // Map of common operations to user-friendly messages
        const messages = {
            'save': 'Save failed. Please try again.',
            'delete': 'Delete failed. Please try again.',
            'update': 'Update failed. Please try again.',
            'load': 'Loading failed. Please try again.',
            'submit': 'Submission failed. Please try again.',
            'register': 'Registration failed. Please try again.',
            'login': 'Login failed. Please try again.',
            'search': 'Search failed. Please try again.',
            'import': 'Import failed. Please try again.',
            'export': 'Export failed. Please try again.',
            'upload': 'Upload failed. Please try again.',
            'download': 'Download failed. Please try again.',
            'operation': 'Operation failed. Please try again.'
        };

        return messages[operation] || messages['operation'];
    },

    /**
     * Handle HTTP response errors
     */
    handleHttpError: function(response, operation = 'operation') {
        if (response.status === 419) {
            return 'Session expired. Please refresh the page and try again.';
        } else if (response.status === 403) {
            return 'Access denied. You do not have permission to perform this action.';
        } else if (response.status === 404) {
            return 'Resource not found. Please try again.';
        } else if (response.status >= 500) {
            return 'Server error. Please try again later.';
        } else {
            return this.handleAjaxError(null, operation);
        }
    },

    /**
     * Show user-friendly alert
     */
    showAlert: function(message, type = 'error') {
        if (window.customConfirm) {
            // Use custom confirm if available
            window.customConfirm(message, type === 'error' ? 'Error' : 'Success', type);
        } else {
            // Fallback to regular alert
            alert(message);
        }
    },

    /**
     * Show success message
     */
    showSuccess: function(message) {
        this.showAlert(message, 'success');
    },

    /**
     * Show error message
     */
    showError: function(message) {
        this.showAlert(message, 'error');
    },

    /**
     * Handle form submission errors
     */
    handleFormError: function(error, formType = 'form') {
        const message = this.handleAjaxError(error, 'submit');
        this.showError(message);
    },

    /**
     * Handle network errors
     */
    handleNetworkError: function(operation = 'operation') {
        const message = 'Network error. Please check your connection and try again.';
        this.showError(message);
    }
};

// Make it globally available
window.handleError = window.ErrorHandler.handleAjaxError;
window.showErrorMessage = window.ErrorHandler.showError;
window.showSuccessMessage = window.ErrorHandler.showSuccess;
