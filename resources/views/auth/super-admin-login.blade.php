<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Admin Login</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
            font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 500;
        }

        .admin-login-shell {
            height: 100vh;
            width: 100vw;
            position: fixed;
            top: 0;
            left: 0;
            background:
                radial-gradient(circle at top left, rgba(255, 140, 0, 0.25), transparent 40%),
                radial-gradient(circle at bottom right, rgba(255, 179, 71, 0.2), transparent 35%),
                linear-gradient(135deg, #0e0f12 0%, #17191f 48%, #101216 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            z-index: 0;
        }

        .admin-login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .admin-login-container {
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
        }

        .admin-login-hero {
            background: linear-gradient(160deg, #17191f 0%, #1a1a2e 50%, #ff8c00 150%);
            color: #fff;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .admin-login-form-section {
            background: #ffffff;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .admin-login-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.15);
            font-size: 13px;
            width: fit-content;
            margin-bottom: 24px;
        }

        .admin-login-hero h1 {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 16px;
            letter-spacing: -0.03em;
        }

        .admin-login-hero p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .admin-login-features {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .admin-login-feature {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .admin-login-feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .admin-login-feature-text {
            flex: 1;
        }

        .admin-login-feature-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .admin-login-feature-desc {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .admin-login-footer {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 13px;
        }

        .admin-login-footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .admin-login-footer a:hover {
            color: #ffffff;
        }

        .admin-form-header {
            margin-bottom: 28px;
        }

        .admin-form-header-badge {
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 8px;
            display: block;
        }

        .admin-form-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 8px;
            letter-spacing: -0.03em;
        }

        .admin-form-header p {
            font-size: 13px;
            color: #6b7280;
        }

        .admin-form-group {
            margin-bottom: 20px;
        }

        .admin-form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .admin-form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #d9dce3;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .admin-form-control:hover {
            border-color: #bbc0c7;
        }

        .admin-form-control:focus {
            outline: none;
            border-color: #ff8c00;
            box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.12);
        }

        .admin-form-control.is-invalid {
            border-color: #ef4444;
        }

        .admin-form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
        }

        .admin-form-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-form-checkbox input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #ff8c00;
        }

        .admin-form-checkbox label {
            font-size: 13px;
            color: #1f2937;
            cursor: pointer;
            margin: 0;
        }

        .admin-form-button {
            width: 100%;
            padding: 12px 16px;
            background: linear-gradient(135deg, #ff8c00, #ffb347);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(255, 140, 0, 0.25);
        }

        .admin-form-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(255, 140, 0, 0.35);
        }

        .admin-form-button:active {
            transform: translateY(0);
        }

        .admin-invalid-feedback {
            font-size: 12px;
            color: #ef4444;
            margin-top: 6px;
            display: block;
        }

        .admin-error-alert {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .admin-login-container {
                grid-template-columns: 1fr;
            }

            .admin-login-hero {
                padding: 30px;
                min-height: 300px;
            }

            .admin-login-form-section {
                padding: 30px;
            }

            .admin-login-hero h1 {
                font-size: 22px;
            }

            .admin-form-header h2 {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .admin-login-wrapper {
                padding: 12px;
            }

            .admin-login-hero {
                padding: 24px;
                min-height: 280px;
            }

            .admin-login-form-section {
                padding: 24px;
            }

            .admin-login-hero h1 {
                font-size: 20px;
            }

            .admin-form-header h2 {
                font-size: 18px;
            }

            .admin-login-features {
                gap: 16px;
            }

            .admin-login-feature-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }

            .admin-login-feature-title {
                font-size: 13px;
            }

            .admin-login-feature-desc {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-login-shell">
        <div class="admin-login-wrapper">
            <div class="admin-login-container">
                <!-- Hero Section -->
                <div class="admin-login-hero">
                    <div>
                        <div class="admin-login-pill">
                            <i class="bi bi-shield-lock"></i>
                            System Administration
                        </div>
                        <h1>Admin Portal</h1>
                        <p>Secure access for system administrators and super users.</p>

                        <div class="admin-login-features">
                            <div class="admin-login-feature">
                                <div class="admin-login-feature-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div class="admin-login-feature-text">
                                    <div class="admin-login-feature-title">Full system control</div>
                                    <div class="admin-login-feature-desc">Complete administrative access</div>
                                </div>
                            </div>
                            <div class="admin-login-feature">
                                <div class="admin-login-feature-icon">
                                    <i class="bi bi-lock"></i>
                                </div>
                                <div class="admin-login-feature-text">
                                    <div class="admin-login-feature-title">Enhanced security</div>
                                    <div class="admin-login-feature-desc">All access attempts are logged</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="admin-login-footer">
                        <a href="{{ route('landing') }}">
                            <i class="bi bi-house me-1"></i> Back to Home
                        </a>
                        <a href="{{ route('login') }}">
                            Merchant Login <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="admin-login-form-section">
                    <div class="admin-form-header">
                        <span class="admin-form-header-badge">Administrator</span>
                        <h2>System Admin Login</h2>
                        <p>Enter your admin credentials to continue.</p>
                    </div>

                    @if($errors->any())
                        <div class="admin-error-alert">
                            @foreach($errors->all() as $error)
                                <div><i class="bi bi-exclamation-triangle me-1"></i> {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('super-admin.login') }}">
                        @csrf

                        <div class="admin-form-group">
                            <label class="admin-form-label">Email Address</label>
                            <input type="email" name="email" class="admin-form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="admin@system.local">
                            @error('email')
                                <span class="admin-invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">Password</label>
                            <input type="password" name="password" class="admin-form-control @error('password') is-invalid @enderror" required placeholder="••••••••">
                            @error('password')
                                <span class="admin-invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <div class="admin-form-checkbox">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">Remember me</label>
                            </div>
                        </div>

                        <button type="submit" class="admin-form-button">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </button>
                    </form>

                    <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280;">
                        <i class="bi bi-shield-check" style="color: #ff8c00;"></i> Secure admin login · All access logged
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
