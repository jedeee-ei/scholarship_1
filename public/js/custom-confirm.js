/**
 * Custom Confirmation Dialog
 * Replaces browser's default confirm() to avoid "127.0.0.1:8000 says" messages
 */

// Create and inject the modal HTML
function createConfirmModal() {
    if (document.getElementById('customConfirmModal')) {
        return; // Modal already exists
    }

    const modalHTML = `
        <div id="customConfirmModal" class="custom-confirm-modal" style="display: none;">
            <div class="custom-confirm-overlay"></div>
            <div class="custom-confirm-dialog">
                <div class="custom-confirm-header">
                    <h4 id="customConfirmTitle">Confirm Action</h4>
                </div>
                <div class="custom-confirm-body">
                    <p id="customConfirmMessage">Are you sure you want to proceed?</p>
                </div>
                <div class="custom-confirm-footer">
                    <button id="customConfirmCancel" class="btn-cancel">Cancel</button>
                    <button id="customConfirmOk" class="btn-confirm">OK</button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    addConfirmModalStyles();
}

// Add CSS styles for the modal
function addConfirmModalStyles() {
    if (document.getElementById('customConfirmStyles')) {
        return; // Styles already added
    }

    const styles = `
        <style id="customConfirmStyles">
            .custom-confirm-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .custom-confirm-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(2px);
            }

            .custom-confirm-dialog {
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                max-width: 450px;
                width: 90%;
                position: relative;
                z-index: 1;
                animation: confirmSlideIn 0.3s ease-out;
            }

            @keyframes confirmSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(-30px) scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .custom-confirm-header {
                padding: 20px 25px 15px;
                border-bottom: 1px solid #e9ecef;
            }

            .custom-confirm-header h4 {
                margin: 0;
                color: #2c3e50;
                font-size: 18px;
                font-weight: 600;
            }

            .custom-confirm-body {
                padding: 20px 25px;
            }

            .custom-confirm-body p {
                margin: 0;
                color: #495057;
                line-height: 1.5;
                font-size: 15px;
            }

            .custom-confirm-footer {
                padding: 15px 25px 20px;
                display: flex;
                gap: 10px;
                justify-content: flex-end;
            }

            .custom-confirm-footer button {
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
                min-width: 80px;
            }

            .btn-cancel {
                background: #f8f9fa;
                color: #6c757d;
                border: 1px solid #dee2e6;
            }

            .btn-cancel:hover {
                background: #e9ecef;
                color: #495057;
            }

            .btn-confirm {
                background: #007bff;
                color: white;
            }

            .btn-confirm:hover {
                background: #0056b3;
            }

            .btn-confirm.danger {
                background: #dc3545;
            }

            .btn-confirm.danger:hover {
                background: #c82333;
            }

            .btn-confirm.warning {
                background: #ffc107;
                color: #212529;
            }

            .btn-confirm.warning:hover {
                background: #e0a800;
            }
        </style>
    `;

    document.head.insertAdjacentHTML('beforeend', styles);
}

// Custom confirm function
function customConfirm(message, title = 'Confirm Action', type = 'default') {
    return new Promise((resolve) => {
        createConfirmModal();

        const modal = document.getElementById('customConfirmModal');
        const titleElement = document.getElementById('customConfirmTitle');
        const messageElement = document.getElementById('customConfirmMessage');
        const cancelBtn = document.getElementById('customConfirmCancel');
        const okBtn = document.getElementById('customConfirmOk');

        // Set content
        titleElement.textContent = title;
        // Handle multiline messages by converting \n to <br>
        if (message.includes('\n')) {
            messageElement.innerHTML = message.replace(/\n/g, '<br>');
        } else {
            messageElement.textContent = message;
        }

        // Set button style based on type
        okBtn.className = 'btn-confirm';
        if (type === 'danger') {
            okBtn.classList.add('danger');
        } else if (type === 'warning') {
            okBtn.classList.add('warning');
        }

        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Focus on OK button
        setTimeout(() => okBtn.focus(), 100);

        // Handle button clicks
        function handleCancel() {
            hideModal();
            resolve(false);
        }

        function handleOk() {
            hideModal();
            resolve(true);
        }

        function hideModal() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            cancelBtn.removeEventListener('click', handleCancel);
            okBtn.removeEventListener('click', handleOk);
            document.removeEventListener('keydown', handleKeydown);
        }

        function handleKeydown(e) {
            if (e.key === 'Escape') {
                handleCancel();
            } else if (e.key === 'Enter') {
                handleOk();
            }
        }

        // Add event listeners
        cancelBtn.addEventListener('click', handleCancel);
        okBtn.addEventListener('click', handleOk);
        document.addEventListener('keydown', handleKeydown);

        // Close on overlay click
        modal.querySelector('.custom-confirm-overlay').addEventListener('click', handleCancel);
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Replace all onclick confirm dialogs
    replaceConfirmDialogs();
});

// Function to replace existing confirm dialogs
function replaceConfirmDialogs() {
    // Find all elements with onclick containing confirm()
    const elementsWithConfirm = document.querySelectorAll('[onclick*="confirm("]');

    elementsWithConfirm.forEach(element => {
        const onclickAttr = element.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('confirm(')) {
            // Extract the confirm message and action
            const confirmMatch = onclickAttr.match(/confirm\(['"`]([^'"`]+)['"`]\)/);
            if (confirmMatch) {
                const message = confirmMatch[1];
                const actionAfterConfirm = onclickAttr.replace(/if\s*\(\s*confirm\([^)]+\)\s*\)\s*/, '');

                // Remove the original onclick
                element.removeAttribute('onclick');

                // Add new click handler
                element.addEventListener('click', async function(e) {
                    e.preventDefault();

                    const confirmed = await customConfirm(message, 'Confirm Action', 'warning');
                    if (confirmed) {
                        // Execute the original action
                        try {
                            eval(actionAfterConfirm);
                        } catch (error) {
                            console.error('Error executing action:', error);
                        }
                    }
                });
            }
        }
    });
}

// Export for global use
window.customConfirm = customConfirm;
window.replaceConfirmDialogs = replaceConfirmDialogs;
