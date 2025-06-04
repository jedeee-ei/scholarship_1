// Splash Screen Script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Splash screen loaded');
    
    // Add loading animation effects
    const loadingText = document.querySelector('.loading-text');
    const dots = '...';
    let dotCount = 0;
    
    // Animate loading text with dots
    const loadingInterval = setInterval(() => {
        dotCount = (dotCount + 1) % 4;
        loadingText.textContent = 'Loading System' + dots.slice(0, dotCount);
    }, 500);
    
    // Redirect to login page after 2.5 seconds
    setTimeout(() => {
        clearInterval(loadingInterval);
        
        // Add fade out animation
        document.body.style.animation = 'fadeOut 0.5s ease-out';
        
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 500);
    }, 2500);
});

// Add fade out animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }
`;
document.head.appendChild(style);