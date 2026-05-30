<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounting System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-orange: #ff6b35;
            --dark-orange: #e55a2b;
            --primary-black: #1a1a1a;
            --primary-white: #ffffff;
            --light-gray: #f5f5f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body,
        button,
        input,
        select,
        textarea,
        table,
        th,
        td,
        .btn,
        .nav,
        .navbar,
        .dropdown-menu,
        .card,
        .modal,
        .form-control,
        .form-select,
        .alert,
        .badge {
            font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        html, body {
            height: 100%;
            background: linear-gradient(135deg, var(--primary-black) 0%, #2a2a2a 100%);
            font-weight: 500;
        }

        .landing-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }

        .landing-content {
            text-align: center;
            max-width: 600px;
        }

        .logo-section {
            margin-bottom: 50px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--primary-white);
            font-size: 44px;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.2);
        }

        .logo-text h1 {
            color: var(--primary-white);
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .logo-text p {
            color: #aaa;
            font-size: 16px;
            margin: 0;
        }

        .login-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }

        .login-card {
            background: var(--primary-white);
            border-radius: 12px;
            padding: 40px 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            border-top: 4px solid var(--primary-orange);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        }

        .login-card.super-admin {
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--primary-white) 100%);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-white);
            font-size: 28px;
            margin-bottom: 15px;
        }

        .login-card.super-admin .card-icon {
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a1a 100%);
        }

        .card-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-black);
            margin-bottom: 8px;
        }

        .card-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .card-button {
            padding: 10px 24px;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            color: var(--primary-white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            justify-content: center;
            text-decoration: none;
        }

        .login-card.super-admin .card-button {
            background: linear-gradient(135deg, #2c3e50 0%, #1a1a1a 100%);
        }

        .card-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 107, 53, 0.3);
        }

        .login-card.super-admin .card-button:hover {
            box-shadow: 0 5px 20px rgba(44, 62, 80, 0.3);
        }

        .footer-info {
            color: #888;
            font-size: 12px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 768px) {
            .landing-container {
                padding: 20px;
            }

            .logo-text h1 {
                font-size: 32px;
            }

            .login-options {
                grid-template-columns: 1fr;
            }

            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="landing-content">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="logo-text">
                    <h1>Accounting System</h1>
                    <p>Professional Financial Management Platform</p>
                </div>
            </div>

            <p style="color: #ccc; margin-bottom: 30px; font-size: 15px;">
                Select your login type to get started
            </p>

            <div class="login-options">
                <!-- Merchant/User Login -->
                <a href="{{ route('login') }}" class="login-card">
                    <div class="card-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h3 class="card-title">Merchant</h3>
                    <p class="card-description">
                        Access your business dashboard and manage your operations
                    </p>
                    <button type="button" class="card-button">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Login as Merchant
                    </button>
                </a>

                <!-- Super Admin Login -->
                <a href="{{ route('super-admin.login') }}" class="login-card super-admin">
                    <div class="card-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h3 class="card-title">System Admin</h3>
                    <p class="card-description">
                        Exclusive access for system administrators and super users
                    </p>
                    <button type="button" class="card-button">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Admin Login
                    </button>
                </a>
            </div>

            <div class="footer-info">
                <p>🔒 Secure Platform • 🛡️ Enterprise Grade • 📊 Real-time Analytics</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
