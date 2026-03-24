<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>ERP Nexus · Reset Password</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a0c10 0%, #12151c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Main Container */
        .reset-container {
            max-width: 1280px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* LEFT PANEL - RESET FORM */
        .form-panel {
            flex: 1;
            padding: 48px 56px;
            background: linear-gradient(135deg, #0f1117 0%, #0b0d12 100%);
        }

        /* Logo & Brand */
        .brand {
            margin-bottom: 48px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .brand h1 {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff, #a8b3cf);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.3px;
        }

        .brand p {
            color: #6b7280;
            font-size: 14px;
            margin-top: 6px;
        }

        /* Welcome Text */
        .welcome-text {
            margin-bottom: 32px;
        }

        .welcome-text h2 {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .welcome-text h2 i {
            color: #3b82f6;
            font-size: 28px;
        }

        .welcome-text p {
            color: #9ca3af;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Form Styles */
        .reset-form {
            margin-top: 32px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #9ca3af;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-label i {
            color: #3b82f6;
            margin-right: 6px;
        }

        .input-field {
            position: relative;
        }

        .input-field i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 18px;
            transition: all 0.2s;
        }

        .input-field input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: #1a1d26;
            border: 1.5px solid #2a2f3c;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 500;
            color: #ffffff;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .input-field input:focus {
            outline: none;
            border-color: #3b82f6;
            background: #1f232f;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .input-field input:focus+i {
            color: #3b82f6;
        }

        /* Reset Button */
        .reset-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 16px;
        }

        .reset-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        /* Back to Login Link */
        .back-link {
            text-align: center;
            margin-top: 24px;
        }

        .back-link a {
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            padding: 8px 16px;
            border-radius: 30px;
            background: rgba(59, 130, 246, 0.05);
        }

        .back-link a:hover {
            color: #3b82f6;
            gap: 12px;
            background: rgba(59, 130, 246, 0.1);
        }

        /* Error Messages */
        .error-message {
            color: #f87171;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4ade80;
            font-size: 13px;
        }

        /* RIGHT PANEL - ANIMATION */
        .animation-panel {
            width: 45%;
            background: linear-gradient(135deg, #08090e 0%, #0a0c12 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-left: 1px solid rgba(59, 130, 246, 0.2);
        }

        .animation-container {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 550px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        /* Particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 0.5;
            }

            90% {
                opacity: 0.5;
            }

            100% {
                transform: translateY(-100px) translateX(50px);
                opacity: 0;
            }
        }

        /* Key Animation */
        .key-animation {
            text-align: center;
            margin-bottom: 30px;
        }

        .key-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(30, 64, 175, 0.1));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.8;
            }

            50% {
                transform: scale(1.05);
                opacity: 1;
            }
        }

        .key-icon i {
            font-size: 48px;
            color: #3b82f6;
        }

        .key-animation h3 {
            font-size: 20px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .key-animation p {
            font-size: 14px;
            color: #9ca3af;
        }

        /* Security Features */
        .security-features {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: rgba(59, 130, 246, 0.05);
            border-radius: 12px;
            transition: all 0.3s;
        }

        .feature-item i {
            width: 30px;
            height: 30px;
            background: rgba(59, 130, 246, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 14px;
        }

        .feature-item span {
            font-size: 13px;
            color: #e2e8f0;
        }

        /* Password Strength Indicator */
        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #2a2f3c;
            border-radius: 4px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 4px;
        }

        .strength-text {
            font-size: 11px;
            margin-top: 6px;
            color: #9ca3af;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .reset-container {
                flex-direction: column;
                max-width: 550px;
            }

            .animation-panel {
                width: 100%;
                min-height: 400px;
                border-left: none;
                border-top: 1px solid rgba(59, 130, 246, 0.2);
            }

            .form-panel {
                padding: 40px 32px;
            }
        }

        @media (max-width: 480px) {
            .form-panel {
                padding: 32px 24px;
            }

            .welcome-text h2 {
                font-size: 24px;
            }

            .key-icon {
                width: 70px;
                height: 70px;
            }

            .key-icon i {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>
    <div class="reset-container">
        <!-- LEFT PANEL - RESET PASSWORD FORM -->
        <div class="form-panel">
            <div class="brand">
                <div class="brand-logo">
                    <div class="logo-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h1>ERP Nexus</h1>
                </div>
                <p>Enterprise Resource Planning</p>
            </div>

            <div class="welcome-text">
                <h2>
                    <i class="fas fa-key"></i>
                    Reset Password
                </h2>
                <p>Create a new strong password for your account</p>
            </div>

            @if (session('status'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="reset-form">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div class="form-group">
                    <label class="input-label"><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}" required
                            autofocus placeholder="your@email.com">
                    </div>
                    @error('email')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="form-group">
                    <label class="input-label"><i class="fas fa-lock"></i> New Password</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" required placeholder="••••••••">
                    </div>
                    <div class="password-strength" id="strengthBar">
                        <div class="strength-bar" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                    @error('password')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label class="input-label"><i class="fas fa-check-circle"></i> Confirm Password</label>
                    <div class="input-field">
                        <i class="fas fa-check-circle"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            placeholder="••••••••">
                    </div>
                    @error('password_confirmation')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="reset-btn">
                    <i class="fas fa-sync-alt"></i> Reset Password
                </button>

                <div class="back-link">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-arrow-left"></i>
                        Back to Login
                    </a>
                </div>
            </form>
        </div>

        <!-- RIGHT PANEL - ANIMATION -->
        <div class="animation-panel">
            <div class="animation-container">
                <div class="particles" id="particles"></div>

                <div class="key-animation">
                    <div class="key-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h3>Secure Password Reset</h3>
                    <p>Create a strong password to protect your account</p>
                </div>

                <div class="security-features">
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>End-to-end encrypted</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <span>Link expires in 60 minutes</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <span>Strong password recommended</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-eye"></i>
                        <span>Password not shared with anyone</span>
                    </div>
                </div>

                <div class="security-features" style="margin-top: 20px;">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Min 8 characters</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>At least one uppercase letter</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>At least one number</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==================== PASSWORD STRENGTH METER ====================
        const passwordInput = document.getElementById('password');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        function checkPasswordStrength(password) {
            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            return strength;
        }

        function updateStrengthMeter() {
            const password = passwordInput.value;
            const strength = checkPasswordStrength(password);
            const percent = (strength / 5) * 100;

            strengthFill.style.width = percent + '%';

            let color = '#ef4444';
            let text = 'Very Weak';

            if (strength >= 4) {
                color = '#10b981';
                text = 'Strong';
            } else if (strength >= 3) {
                color = '#f59e0b';
                text = 'Medium';
            } else if (strength >= 2) {
                color = '#f97316';
                text = 'Weak';
            }

            strengthFill.style.backgroundColor = color;
            strengthText.textContent = text + ' password';
            strengthText.style.color = color;

            if (password.length === 0) {
                strengthFill.style.width = '0%';
                strengthText.textContent = '';
            }
        }

        passwordInput?.addEventListener('input', updateStrengthMeter);

        // ==================== PARTICLES ====================
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 40; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                const size = Math.random() * 6 + 2;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 8 + 's';
                particle.style.animationDuration = Math.random() * 6 + 4 + 's';
                particlesContainer.appendChild(particle);
            }
        }
        createParticles();

        // ==================== FORM SUBMISSION HANDLER ====================
        const resetForm = document.querySelector('.reset-form');
        if (resetForm) {
            resetForm.addEventListener('submit', function(e) {
                const btn = this.querySelector('.reset-btn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Resetting...';
            });
        }
    </script>
</body>

</html>
