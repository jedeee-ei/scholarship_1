@if (session('success') || session('error') || session('warning') || session('info'))
    <div class="notification-container">
        @if (session('success'))
            <div class="notification success">
                <span>{{ session('success') }}</span>
                <button type="button" class="close-btn" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        @if (session('error'))
            <div class="notification error">
                <span>{{ session('error') }}</span>
                <button type="button" class="close-btn" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        @if (session('warning'))
            <div class="notification warning">
                <span>{{ session('warning') }}</span>
                <button type="button" class="close-btn" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        @if (session('info'))
            <div class="notification info">
                <span>{{ session('info') }}</span>
                <button type="button" class="close-btn" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif
    </div>

    <script>
        // Auto-dismiss notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(function(notification) {
                setTimeout(function() {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            });
        });
    </script>
@endif
