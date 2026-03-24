<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>ERP Nexus · Confirm Password</title>
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
        .confirm-container {
            max-width: 1280px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* LEFT PANEL - CONFIRM PASSWORD FORM */
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

        /* Security Notice */
        .security-notice {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 32px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .security-icon {
            width: 40px;
            height: 40px;
            background: rgba(59, 130, 246, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .security-icon i {
            font-size: 20px;
            color: #3b82f6;
        }

        .security-text h4 {
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .security-text p {
            font-size: 12px;
            color: #9ca3af;
            line-height: 1.4;
        }

        /* Form Styles */
        .confirm-form {
            margin-top: 24px;
        }

        .form-group {
            margin-bottom: 28px;
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

        /* Confirm Button */
        .confirm-btn {
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
            margin-top: 8px;
        }

        .confirm-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #60a5fa, #2563eb);
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

        /* Shield Animation */
        .shield-animation {
            text-align: center;
            margin-bottom: 30px;
        }

        .shield-icon {
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

        .shield-icon i {
            font-size: 48px;
            color: #3b82f6;
        }

        .shield-animation h3 {
            font-size: 20px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .shield-animation p {
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

        /* Session Timer */
        .session-timer {
            margin-top: 30px;
            text-align: center;
            padding: 12px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }

        .session-timer p {
            font-size: 12px;
            color: #9ca3af;
        }

        .session-timer i {
            color: #f59e0b;
            margin-right: 6px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .confirm-container {
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

            .security-notice {
                padding: 16px;
            }

            .shield-icon {
                width: 70px;
                height: 70px;
            }

            .shield-icon i {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>
    <div class="confirm-container">
        <!-- LEFT PANEL - CONFIRM PASSWORD FORM -->
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
                    <i class="fas fa-shield-alt"></i>
                    Secure Area
                </h2>
                <p>Please confirm your password to continue</p>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <div class="security-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="security-text">
                    <h4>Why do we need your password?</h4>
                    <p>This is a secure area of the application. We need to verify your identity before accessing
                        sensitive information.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="confirm-form">
                @csrf

                <!-- Password -->
                <div class="form-group">
                    <label class="input-label"><i class="fas fa-lock"></i> Your Password</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" required autocomplete="current-password"
                            placeholder="Enter your password">
                    </div>
                    @error('password')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="confirm-btn">
                    <i class="fas fa-check-circle"></i> Confirm Password
                </button>
            </form>

            <!-- Session Timer (Optional) -->
            <div class="session-timer">
                <p><i class="fas fa-hourglass-half"></i> Session expires in <span id="timer">5:00</span></p>
            </div>
        </div>

        <!-- RIGHT PANEL - ANIMATION -->
        <div class="animation-panel">
            <div class="animation-container">
                <div class="particles" id="particles"></div>

                <div class="shield-animation">
                    <div class="shield-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Extra Security Layer</h3>
                    <p>Confirm your identity to continue</p>
                </div>

                <div class="security-features">
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <span>Protects sensitive data</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-user-check"></i>
                        <span>Verifies your identity</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-clock"></i>
                        <span>Session timeout: 5 minutes</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-virus"></i>
                        <span>Prevents unauthorized access</span>
                    </div>
                </div>

                <!-- Security Tip -->
                <div class="session-timer" style="margin-top: 20px; background: transparent;">
                    <p><i class="fas fa-lightbulb"></i> Tip: Never share your password with anyone</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==================== SESSION TIMER ====================
        let timer = 300; // 5 minutes in seconds
        const timerElement = document.getElementById('timer');

        function updateTimer() {
            const minutes = Math.floor(timer / 60);
            const seconds = timer % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (timer > 0) {
                timer--;
                setTimeout(updateTimer, 1000);
            } else {
                timerElement.textContent = "0:00";
                // Optional: Show message that session expired
                const sessionDiv = document.querySelector('.session-timer');
                if (sessionDiv) {
                    sessionDiv.innerHTML =
                        '<p><i class="fas fa-exclamation-triangle"></i> Session expired. Please login again.</p>';
                    sessionDiv.style.background = 'rgba(239, 68, 68, 0.1)';
                    sessionDiv.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                }
            }
        }

        // Start timer (only if not expired)
        setTimeout(updateTimer, 1000);

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
        const confirmForm = document.querySelector('.confirm-form');
        if (confirmForm) {
            confirmForm.addEventListener('submit', function(e) {
                const btn = this.querySelector('.confirm-btn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Verifying...';
            });
        }

        // ==================== SHOW/HIDE PASSWORD (Optional) ====================
        // Add password toggle if needed
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            // Optional: Add show/hide password functionality
            const parentDiv = passwordInput.parentElement;
            const toggleEye = document.createElement('i');
            toggleEye.className = 'fas fa-eye-slash';
            toggleEye.style.position = 'absolute';
            toggleEye.style.right = '16px';
            toggleEye.style.top = '50%';
            toggleEye.style.transform = 'translateY(-50%)';
            toggleEye.style.cursor = 'pointer';
            toggleEye.style.color = '#6b7280';
            toggleEye.style.zIndex = '10';

            toggleEye.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            parentDiv.appendChild(toggleEye);
        }
    </script>
</body>

</html>
