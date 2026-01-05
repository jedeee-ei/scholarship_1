/**
 * Alert Override Script
 * Replaces default browser alert and confirm dialogs with custom dialogs
 */

document.addEventListener('DOMContentLoaded', function() {
    // Override alert() to use custom dialog
    window.originalAlert = window.alert;
    window.alert = function(message) {
        if (window.customConfirm) {
            return window.customConfirm(message, 'Notice', 'info');
        } else {
            // Fallback to original alert if custom confirm not available
            return window.originalAlert(message);
        }
    };

    // Override confirm() to use custom dialog
    window.originalConfirm = window.confirm;
    window.confirm = function(message) {
        if (window.customConfirm) {
            return window.customConfirm(message, 'Confirm', 'warning');
        } else {
            // Fallback to original confirm if custom confirm not available
            return window.originalConfirm(message);
        }
    };

    // Replace any existing confirm dialogs
    if (window.replaceConfirmDialogs) {
        window.replaceConfirmDialogs();
    }
});
