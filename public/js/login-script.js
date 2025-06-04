// Login Page Script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Login page loaded');
    
    // Add entrance animation to buttons
    const buttons = document.querySelectorAll('.login-btn');
    buttons.forEach((button, index) => {
        button.style.opacity = '0';
        button.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            button.style.transition = 'all 0.5s ease';
            button.style.opacity = '1';
            button.style.transform = 'translateY(0)';
        }, 200 + (index * 100));
    });
});

// Handle login selection
function selectLogin(loginType) {
    console.log(`Selected login type: ${loginType}`);
    
    // Add click animation
    const clickedButton = event.target.closest('.login-btn');
    clickedButton.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        clickedButton.style.transform = '';
    }, 150);
    
    // Show loading state
    showLoginLoading(loginType);
    
    // Simulate navigation to respective login forms
    setTimeout(() => {
        switch(loginType) {
            case 'administrator':
                showLoginForm('Administrator Login', 'administrator');
                break;
            case 'alumni':
                showLoginForm('Alumni Login', 'alumni');
                break;
            case 'student':
                showLoginForm('Student Login', 'student');
                break;
        }
    }, 1500);
}

// Show loading state
function showLoginLoading(loginType) {
    const container = document.querySelector('.login-container');
    
    // Create loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p>Preparing ${loginType} login...</p>
        </div>
    `;
    
    // Add loading styles
    const loadingStyles = `
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 20px;
            z-index: 100;
            animation: fadeIn 0.3s ease;
        }
        
        .loading-content {
            text-align: center;
            color: #1e5631;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f0f0f0;
            border-top: 3px solid #1e5631;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    `;
    
    // Add styles to head
    const styleSheet = document.createElement('style');
    styleSheet.textContent = loadingStyles;
    document.head.appendChild(styleSheet);
    
    // Make container relative for absolute positioning
    container.style.position = 'relative';
    container.appendChild(loadingOverlay);
}

// Show login form (placeholder for future implementation)
function showLoginForm(title, type) {
    const container = document.querySelector('.login-container');
    
    // Remove loading overlay
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
    
    // Create login form
    container.innerHTML = `
        <div class="header-section">
            <img src="logo-placeholder.png" alt="St. Paul University Philippines Logo" class="login-logo">
            <h1 class="university-name">St. Paul University Philippines</h1>
            <h2 class="office-name">Office of the Registrar</h2>
        </div>

        <div class="login-form-section">
            <h3 class="login-title">${title}</h3>
            
            <form class="login-form" onsubmit="handleLogin(event, '${type}')">
                <div class="form-group">
                    <label for="username">Username/Email:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="login-submit-btn">${title}</button>
            </form>
            
            <div class="form-footer">
                <a href="#" class="forgot-password">Forgot Password?</a>
                <div class="back-options">
                    <a href="#" onclick="goBackToLoginOptions()" class="back-link">← Back to Login Options</a>
                    <a href="website.html" class="back-link">← Back to Website</a>
                </div>
            </div>
        </div>
    `;
    
    // Add form styles
    addFormStyles();
}

// Add form styles
function addFormStyles() {
    const formStyles = `
        .login-form-section {
            animation: slideIn 0.5s ease-out;
        }
        
        .login-form {
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #1e5631;
        }
        
        .login-submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #1e5631, #2d7a3d);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .login-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 86, 49, 0.3);
        }
        
        .form-footer {
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
        }
        
        .forgot-password {
            color: #1e5631;
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: inline-block;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
        }
        
        .back-options {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .back-options .back-link {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-options .back-link:hover {
            color: #1e5631;
        }
        
        @media (max-width: 480px) {
            .back-options {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    `;
    
    const styleSheet = document.createElement('style');
    styleSheet.textContent = formStyles;
    document.head.appendChild(styleSheet);
}

// Handle login form submission
function handleLogin(event, type) {
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    console.log(`Login attempt for ${type}:`, { username, password });
    
    // Show loading state
    const submitBtn = document.querySelector('.login-submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Logging in...';
    submitBtn.disabled = true;
    
    // Simulate login process
    setTimeout(() => {
        // For demo purposes, show success message
        alert(`Login successful for ${type}!\nUsername: ${username}\n\nNote: This is a demo. In a real system, this would redirect to the appropriate dashboard.`);
        
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

// Go back to login options
function goBackToLoginOptions() {
    location.reload();
}

// Add some entrance effects
window.addEventListener('load', function() {
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '1';
    }, 100);
});