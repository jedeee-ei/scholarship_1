<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - St. Paul University Philippines</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: green;
            --primary-dark: green;
            --primary-light: rgba(128, 0, 0, 0.1);
            --text-color: #333;
            --light-gray: #f5f5f5;
            --border-color: #ddd;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 900px;
            padding: 20px;
        }

        .login-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header-section {
            background-color: var(--primary-color);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 15px;
            background-color: white;
            padding: 10px;
            border-radius: 50%;
        }

        .university-name {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .office-name {
            font-size: 18px;
            font-weight: 400;
            margin: 5px 0 0;
            opacity: 0.9;
        }

        .login-section {
            padding: 40px;
        }

        .login-title {
            font-size: 22px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 30px;
            text-align: center;
        }

        .login-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .login-btn {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: white;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .administrator-btn:hover {
            background-color: rgba(128, 0, 0, 0.05);
        }

        .student-btn:hover {
            background-color: rgba(128, 0, 0, 0.05);
        }

        .btn-icon {
            font-size: 30px;
            margin-right: 20px;
            color: var(--primary-color);
        }

        .btn-text {
            flex: 1;
        }

        .btn-text h4 {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 5px;
        }

        .btn-text p {
            font-size: 14px;
            margin: 0;
            color: #666;
        }

        .back-to-website {
            text-align: center;
            margin-top: 20px;
        }

        .back-link {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 15px;
            }

            .login-section {
                padding: 30px 20px;
            }

            .login-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="header-section">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo" class="login-logo">
                <h1 class="university-name">St. Paul University Philippines</h1>
                <h2 class="office-name">Office of the Registrar</h2>
            </div>

            <!-- Login Type Selection -->
            <div id="login-selection" class="login-section">
                <h3 class="login-title">Select Login Type</h3>
                <div class="login-buttons">
                    <a href="{{ route('login.form', ['type' => 'administrator']) }}" class="login-btn administrator-btn">
                        <div class="btn-icon"><i class="fas fa-user-tie"></i></div>
                        <div class="btn-text">
                            <h4>Administrator</h4>
                            <p>System administration and management</p>
                        </div>
                    </a>

                    <a href="{{ route('login.form', ['type' => 'student']) }}" class="login-btn student-btn">
                        <div class="btn-icon"><i class="fas fa-user-graduate"></i></div>
                        <div class="btn-text">
                            <h4>Student</h4>
                            <p>Current student services</p>
                        </div>
                    </a>
                </div>
                <div class="back-to-website">
                    <a href="{{ route('welcome') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to Website</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add entrance animation to buttons
        document.addEventListener('DOMContentLoaded', function() {
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
    </script>
</body>
</html>


