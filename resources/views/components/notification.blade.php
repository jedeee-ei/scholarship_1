@if (session('success') || session('error') || session('warning') || session('info'))
    <div id="notification-container">
        @if (session('success'))
            <div class="notification success" id="notification">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="close-btn" onclick="closeNotification()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="notification error" id="notification">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="close-btn" onclick="closeNotification()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (session('warning'))
            <div class="notification warning" id="notification">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ session('warning') }}</span>
                <button type="button" class="close-btn" onclick="closeNotification()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="notification info" id="notification">
                <i class="fas fa-info-circle"></i>
                <span>{{ session('info') }}</span>
                <button type="button" class="close-btn" onclick="closeNotification()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notification = document.getElementById('notification');
            if (notification) {
                // Auto-dismiss notification after 7 seconds
                setTimeout(function() {
                    closeNotification();
                }, 7000);

                // Add click to dismiss functionality
                notification.addEventListener('click', function(e) {
                    if (e.target.closest('.close-btn')) {
                        closeNotification();
                    }
                });
            }
        });

        // Close notification manually
        function closeNotification() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }
        }

        // Show notification programmatically (for future use)
        function showNotification(type, message) {
            const container = document.getElementById('notification-container') || document.body;
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.id = 'notification';

            let icon = 'fas fa-info-circle';
            if (type === 'success') icon = 'fas fa-check-circle';
            else if (type === 'error') icon = 'fas fa-exclamation-circle';
            else if (type === 'warning') icon = 'fas fa-exclamation-triangle';

            notification.innerHTML = `
                <i class="${icon}"></i>
                <span>${message}</span>
                <button type="button" class="close-btn" onclick="closeNotification()">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(notification);

            // Auto-dismiss after 7 seconds
            setTimeout(function() {
                if (notification.parentNode) {
                    closeNotification();
                }
            }, 7000);
        }
    </script>
@endif
