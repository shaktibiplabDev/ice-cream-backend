<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Celesty Admin | Secure Access</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0a0c10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated gradient background */
        .bg-orb {
            position: fixed;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .orb-1 {
            position: absolute;
            top: -20%;
            left: -10%;
            width: 70%;
            height: 70%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.4) 0%, rgba(79, 70, 229, 0) 70%);
            border-radius: 50%;
            filter: blur(80px);
            animation: float 20s ease-in-out infinite;
        }

        .orb-2 {
            position: absolute;
            bottom: -15%;
            right: -10%;
            width: 65%;
            height: 65%;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.35) 0%, rgba(168, 85, 247, 0) 70%);
            border-radius: 50%;
            filter: blur(80px);
            animation: float 18s ease-in-out infinite reverse;
        }

        .orb-3 {
            position: absolute;
            top: 40%;
            left: 30%;
            width: 40%;
            height: 40%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0) 70%);
            border-radius: 50%;
            filter: blur(70px);
            animation: float 25s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Grid pattern overlay */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 1;
        }

        /* Main container */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1152px;
            margin: 2rem;
            display: flex;
            min-height: 600px;
            background: rgba(18, 22, 28, 0.7);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        /* Left panel - Brand section */
        .brand-panel {
            flex: 1.2;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.15) 0%, rgba(168, 85, 247, 0.08) 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4f46e5, #a855f7);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #a78bfa);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.02em;
        }

        .brand-quote {
            margin-top: auto;
            margin-bottom: 2rem;
        }

        .quote-text {
            font-size: 1.5rem;
            font-weight: 500;
            line-height: 1.4;
            color: rgba(255, 255, 255, 0.9);
            letter-spacing: -0.02em;
        }

        .quote-author {
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .divider-line {
            width: 40px;
            height: 2px;
            background: linear-gradient(90deg, #4f46e5, transparent);
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            margin-top: 1rem;
        }

        /* Right panel - Login form */
        .form-panel {
            flex: 0.9;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.875rem;
        }

        /* Form styles */
        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-label {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            color: rgba(255, 255, 255, 0.4);
            font-size: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            color: #fff;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        /* Options row */
        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .checkbox-wrapper input {
            width: 16px;
            height: 16px;
            accent-color: #4f46e5;
            cursor: pointer;
        }

        .checkbox-wrapper span {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8125rem;
        }

        .forgot-link {
            color: #a78bfa;
            font-size: 0.8125rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #c4b5fd;
        }

        /* Login button */
        .login-btn {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 0.75rem;
            color: #fff;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        /* Divider */
        .divider {
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.75rem;
        }

        .divider-line-form {
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        /* SSO Options */
        .sso-options {
            display: flex;
            gap: 1rem;
        }

        .sso-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.625rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8125rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .sso-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.15);
        }

        /* Alert */
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #fca5a5;
            font-size: 0.8125rem;
        }

        .alert-icon {
            font-size: 1rem;
        }

        /* Footer */
        .form-footer {
            margin-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
                margin: 1rem;
            }
            
            .brand-panel {
                padding: 2rem;
                text-align: center;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            }
            
            .brand-quote {
                margin-top: 1.5rem;
                margin-bottom: 0;
            }
            
            .quote-text {
                font-size: 1.25rem;
            }
            
            .form-panel {
                padding: 2rem;
            }
            
            .logo-area {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .sso-options {
                flex-direction: column;
            }
            
            .options-row {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }

        /* Loading state */
        .login-btn.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="bg-orb">
        <div class="orb-1"></div>
        <div class="orb-2"></div>
        <div class="orb-3"></div>
    </div>
    <div class="grid-overlay"></div>

    <div class="login-container">
        <!-- Left Brand Panel -->
        <div class="brand-panel">
            <div class="logo-area">
                <div class="logo-icon">
                    <span>✨</span>
                </div>
                <span class="logo-text">Celesty</span>
            </div>
            
            <div class="brand-quote">
                <div class="quote-text">
                    Enterprise administration<br>
                    reimagined for scale.
                </div>
                <div class="quote-author">
                    <div class="divider-line"></div>
                    <span>Secure Platform v4.0</span>
                </div>
                
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="form-panel">

            @if($errors->any())
                <div class="alert-error">
                    <span class="alert-icon">⚠️</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" id="loginForm">
                @csrf
                <div class="input-group">
                    <label class="input-label">Email address</label>
                    <div class="input-wrapper">
                        <span class="input-icon">📧</span>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="admin@celesty.com" required autofocus>
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="options-row">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember this device</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="login-btn" id="loginBtn">
                    Sign in
                </button>
            </form>

        </div>
    </div>

    <script>
        // Simple loading state for the login button
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            if (btn) {
                btn.classList.add('loading');
                btn.textContent = 'Authenticating...';
            }
        });

        // Remove loading state if there are errors (restore button)
        @if($errors->any())
            document.getElementById('loginBtn')?.classList.remove('loading');
            document.getElementById('loginBtn')?.textContent = 'Sign in';
        @endif

    </script>
</body>
</html>