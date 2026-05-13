<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Admin Login</title>
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

        html, body {
            height: 100%;
            background: linear-gradient(135deg, var(--primary-black) 0%, #2a2a2a 100%);
        }

        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }

        .login-card {
            background: var(--primary-white);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-top: 4px solid var(--primary-orange);
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--primary-white);
            font-size: 28px;
        }

        .login-header h1 {
            color: var(--primary-black);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: var(--primary-black);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .form-check {
            margin-bottom: 20px;
        }

        .form-check-input {
            border: 2px solid #e0e0e0;
            width: 18px;
            height: 18px;
        }

        .form-check-input:checked {
            background-color: var(--primary-orange);
            border-color: var(--primary-orange);
        }

        .form-check-label {
            color: #666;
            font-size: 14px;
            margin-left: 8px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            color: var(--primary-white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 107, 53, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            font-size: 14px;
        }

        .error-message i {
            margin-right: 8px;
        }

        .error-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .error-list li {
            padding: 4px 0;
        }

        .footer-text {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #666;
            font-size: 12px;
            margin-top: 15px;
        }

        .security-badge i {
            color: var(--primary-orange);
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h1>System Admin</h1>
                <p>Super Administrator Access</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <i class="bi bi-exclamation-circle"></i>
                    <strong>Login Failed!</strong>
                    @if ($errors->count() === 1)
                        <div>{{ $errors->first() }}</div>
                    @else
                        <ul class="error-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('super-admin.login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">
                        <i class="bi bi-envelope"></i> Email Address
                    </label>
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="admin@system.local"
                    >
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        required
                        placeholder="••••••••"
                    >
                </div>

                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="remember"
                        id="remember"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </button>

                <div class="security-badge">
                    <i class="bi bi-shield-check"></i>
                    <span>Secure Admin Login</span>
                </div>
            </form>

            <div class="footer-text">
                <p>🔒 This login is exclusively for system administrators.</p>
                <p>Unauthorized access attempts are logged.</p>
                <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; font-size: 13px; border-top: 1px solid #e0e0e0; padding-top: 15px;">
                    <a href="{{ route('landing') }}" style="color: var(--primary-orange); text-decoration: none; display: flex; align-items: center; gap: 5px;">
                        <i class="bi bi-house"></i> Back to Home
                    </a>
                    <a href="{{ route('login') }}" style="color: var(--primary-orange); text-decoration: none; display: flex; align-items: center; gap: 5px;">
                        Merchant Login <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
