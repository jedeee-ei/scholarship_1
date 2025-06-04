// Website functionality for St. Paul University Philippines - Office of the Registrar

// DOM Content Loaded Event
document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
    initializeServiceButtons();
    initializeBottomButtons();
    initializeAnimations();
    initializeSmoothScrolling();
});

// Navigation functionality
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    const contentSections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active class from all nav links and sections
            navLinks.forEach(nav => nav.classList.remove('active'));
            contentSections.forEach(section => section.classList.remove('active'));

            // Add active class to clicked nav link
            this.classList.add('active');

            // Show corresponding content section
            const targetSection = this.getAttribute('data-section');
            const targetElement = document.getElementById(targetSection);

            if (targetElement) {
                targetElement.classList.add('active');

                // Scroll to main content area
                document.querySelector('.main-content').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Service buttons functionality
function initializeServiceButtons() {
    const credentialBtn = document.getElementById('credentialBtn');
    const scholarshipBtn = document.getElementById('scholarshipBtn');

    if (credentialBtn) {
        credentialBtn.addEventListener('click', function() {
            // Redirect to splash screen for credential request
            window.location.href = 'splash-screen.html';
        });
    }

    if (scholarshipBtn) {
        scholarshipBtn.addEventListener('click', function() {
            showServiceModal('Scholarship Application', 'scholarship');
        });
    }
}

// Service modal functionality
function showServiceModal(serviceName, serviceType) {
    // Create modal overlay
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'modal-overlay';
    modalOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
        animation: fadeIn 0.3s ease;
    `;

    // Create modal content
    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    modalContent.style.cssText = `
        background: white;
        padding: 2rem;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease;
    `;

    // Modal content HTML
    modalContent.innerHTML = `
        <div style="color: #1e5631; font-size: 2rem; margin-bottom: 1rem;">
            ${serviceType === 'credential' ? '📋' : '🎓'}
        </div>
        <h2 style="color: #1e5631; margin-bottom: 1rem;">${serviceName}</h2>
        <p style="color: #666; margin-bottom: 1.5rem; line-height: 1.6;">
            ${serviceType === 'credential'
                ? 'Our credential request system allows you to request official transcripts, diplomas, and other academic documents. Please visit our office or contact us for more information about the application process.'
                : 'Explore scholarship opportunities available at St. Paul University Philippines. Our scholarship program helps deserving students achieve their academic goals. Please visit our office for application requirements and deadlines.'}
        </p>
        <div>
            <button id="contactOfficeBtn" style="
                background: linear-gradient(135deg, #1e5631, #2d7a3d);
                color: white;
                padding: 12px 30px;
                border: none;
                border-radius: 25px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                margin: 0 10px;
                transition: all 0.3s ease;
            ">Contact Office</button>
            <button id="closeModalBtn" style="
                background: #6c757d;
                color: white;
                padding: 12px 30px;
                border: none;
                border-radius: 25px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                margin: 0 10px;
                transition: all 0.3s ease;
            ">Close</button>
        </div>
    `;

    // Add modal to page
    modalOverlay.appendChild(modalContent);
    document.body.appendChild(modalOverlay);

    // Add event listeners
    document.getElementById('closeModalBtn').addEventListener('click', closeModal);
    document.getElementById('contactOfficeBtn').addEventListener('click', function() {
        closeModal();
        navigateToContact();
    });

    // Close modal when clicking overlay
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });

    // Close modal with escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    function closeModal() {
        modalOverlay.style.animation = 'fadeOut 0.3s ease';
        setTimeout(() => {
            if (document.body.contains(modalOverlay)) {
                document.body.removeChild(modalOverlay);
            }
        }, 300);
    }
}

// Navigate to contact section
function navigateToContact() {
    const navLinks = document.querySelectorAll('.nav-link');
    const contentSections = document.querySelectorAll('.content-section');

    // Remove active class from all nav links and sections
    navLinks.forEach(nav => nav.classList.remove('active'));
    contentSections.forEach(section => section.classList.remove('active'));

    // Activate contact nav and section
    document.querySelector('[data-section="contact"]').classList.add('active');
    document.getElementById('contact').classList.add('active');

    // Scroll to main content
    document.querySelector('.main-content').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

// Initialize animations
function initializeAnimations() {
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    `;
    document.head.appendChild(style);

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeIn 0.6s ease forwards';
            }
        });
    }, observerOptions);

    // Observe cards for animation
    document.querySelectorAll('.card, .info-item').forEach(card => {
        observer.observe(card);
    });
}

// Smooth scrolling functionality
function initializeSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Utility functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 5px;
        z-index: 3000;
        animation: slideInRight 0.3s ease;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add slide animations for notifications
const notificationStyle = document.createElement('style');
notificationStyle.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(notificationStyle);

// Add button hover effects
document.addEventListener('mouseover', function(e) {
    if (e.target.classList.contains('btn')) {
        e.target.style.transform = 'translateY(-2px)';
    }
});

document.addEventListener('mouseout', function(e) {
    if (e.target.classList.contains('btn')) {
        e.target.style.transform = 'translateY(0)';
    }
});

// Console log for debugging
console.log('St. Paul University Philippines - Registrar Website Loaded Successfully');
console.log('All navigation and interactive features are now active.');

// Bottom buttons functionality
function initializeBottomButtons() {
    const bottomCredentialBtn = document.getElementById('bottomCredentialBtn');
    const scholarshipLoginBtn = document.getElementById('scholarshipLoginBtn');

    if (bottomCredentialBtn) {
        bottomCredentialBtn.addEventListener('click', function() {
            // Redirect to splash screen for credential request
            window.location.href = 'splash-screen.html';
        });
    }

    if (scholarshipLoginBtn) {
        scholarshipLoginBtn.addEventListener('click', function() {
            // Redirect to scholarship login page
            window.location.href = '/login';
        });
    }
}

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
    initializeServiceButtons();
    initializeBottomButtons();
});

