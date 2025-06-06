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
            --primary-color: #052F11;
            --primary-dark: #052F11;
            --primary-light: rgba(5, 47, 17, 0.1);
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
            max-width: 500px;
            padding: 20px;
        }

        .login-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .login-form-section {
            padding: 40px;
        }

        .login-title {
            font-size: 22px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 30px;
            text-align: center;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
        }

        .login-submit-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-submit-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-footer {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }

        .back-options {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
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

        @media (max-width: 576px) {
            .login-container {
                padding: 15px;
            }

            .login-form-section {
                padding: 30px 20px;
            }

            .back-options {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="header-section">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo"
                    class="login-logo">
                <h1 class="university-name">St. Paul University Philippines</h1>
                <h2 class="office-name">Office of the Registrar</h2>
            </div>

            <!-- Login Form -->
            <div class="login-form-section">
                <h3 class="login-title">{{ ucfirst($type) }} Login</h3>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_type" value="{{ $type }}">

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                            placeholder="{{ $type === 'administrator' ? 'Admin Email (admin@spup.edu.ph)' : 'Student Email (student@spup.edu.ph)' }}"
                            value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password"
                            required>
                    </div>

                    <button type="submit" class="login-submit-btn">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>

                    <div class="form-footer">
                        <div class="back-options">
                            <a href="{{ route('login') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to
                                Login Options</a>
                            <a href="{{ route('welcome') }}" class="back-link"><i class="fas fa-home"></i> Back to
                                Website</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
