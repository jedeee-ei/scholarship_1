<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading - St. Paul University Philippines</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #052F11 0%, #052F11 50%, #052F11 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .splash-container {
            text-align: center;
            color: white;
            animation: fadeIn 0.8s ease-in;
        }

        .logo-container {
            margin-bottom: 3rem;
        }

        .splash-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 1.5rem;
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: logoGlow 2s ease-in-out infinite alternate;
        }

        .university-name {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }

        .office-name {
            font-size: 1.5rem;
            font-weight: 400;
            opacity: 0.9;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            display: inline-block;
            padding-bottom: 0.5rem;
        }

        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            font-size: 1.2rem;
            font-weight: 500;
            opacity: 0.8;
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes logoGlow {
            from {
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            }

            to {
                box-shadow: 0 8px 32px rgba(255, 255, 255, 0.2);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 0.8;
            }

            50% {
                opacity: 1;
                transform: scale(1.05);
            }
        }

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

        /* Responsive Design */
        @media (max-width: 768px) {
            .university-name {
                font-size: 2rem;
            }

            .office-name {
                font-size: 1.2rem;
            }

            .splash-logo {
                width: 100px;
                height: 100px;
            }
        }

        @media (max-width: 480px) {
            .university-name {
                font-size: 1.5rem;
            }

            .office-name {
                font-size: 1rem;
            }

            .splash-logo {
                width: 80px;
                height: 80px;
            }
        }
    </style>
</head>

<body>
    <div class="splash-container">
        <div class="logo-container">
            <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo"
                class="splash-logo">
            <h1 class="university-name">St. Paul University Philippines</h1>
            <h2 class="office-name">Office of the Registrar</h2>
        </div>

        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p class="loading-text">Loading System...</p>
        </div>
    </div>

    <script>
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

            // Redirect to welcome page after 2.5 seconds
            setTimeout(() => {
                clearInterval(loadingInterval);

                // Add fade out animation
                document.body.style.animation = 'fadeOut 0.5s ease-out';

                setTimeout(() => {
                    window.location.href = '{{ url('/welcome')}}';
                }, 500);
            }, 2500);
        });
    </script>
</body>

</html>
