<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>ERP Nexus · Verify OTP</title>
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
        .otp-main-container {
            max-width: 1280px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* LEFT PANEL - OTP FORM */
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

        /* Email Display */
        .email-display {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 16px;
            padding: 14px 20px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(4px);
        }

        .email-display i {
            font-size: 20px;
            color: #3b82f6;
        }

        .email-display span {
            font-size: 14px;
            color: #ffffff;
            font-weight: 500;
            word-break: break-all;
        }

        /* OTP Input Field */
        .otp-group {
            margin-bottom: 28px;
        }

        .otp-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #9ca3af;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .otp-label i {
            color: #3b82f6;
            margin-right: 6px;
        }

        .otp-input-wrapper {
            position: relative;
        }

        .otp-input {
            width: 100%;
            padding: 16px 20px;
            background: #1a1d26;
            border: 1.5px solid #2a2f3c;
            border-radius: 16px;
            font-size: 32px;
            font-weight: 600;
            letter-spacing: 12px;
            text-align: center;
            color: #ffffff;
            transition: all 0.2s;
            font-family: monospace;
        }

        .otp-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: #1f232f;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .otp-input::placeholder {
            font-size: 18px;
            letter-spacing: 4px;
            color: #4a5568;
            font-weight: 300;
        }

        /* Timer */
        .timer {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px 16px;
            border-radius: 40px;
            width: fit-content;
            margin-bottom: 24px;
        }

        .timer i {
            color: #f59e0b;
            font-size: 14px;
        }

        .timer-text {
            font-size: 14px;
            color: #ffffff;
            font-weight: 500;
        }

        .timer-warning {
            color: #f97316;
        }

        .timer-danger {
            color: #ef4444;
        }

        /* Verify Button */
        .verify-btn {
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
            margin-bottom: 20px;
        }

        .verify-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        /* Resend Section */
        .resend-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .resend-btn {
            background: transparent;
            border: none;
            color: #9ca3af;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            padding: 8px 20px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .resend-btn:hover:not(:disabled) {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }

        .resend-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .resend-btn i {
            font-size: 14px;
        }

        /* Back Link */
        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #9ca3af;
            text-decoration: none;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .back-link a:hover {
            color: #3b82f6;
            gap: 10px;
        }

        /* Alert Messages */
        .alert {
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #4ade80;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .alert i {
            font-size: 16px;
        }

        /* Attempts Counter */
        .attempts {
            font-size: 12px;
            color: #f97316;
            text-align: center;
            margin-top: 12px;
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
            position: relative;
            width: 120px;
            height: 120px;
            margin-bottom: 30px;
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

        .shield-icon {
            font-size: 80px;
            color: #3b82f6;
            filter: drop-shadow(0 0 20px rgba(59, 130, 246, 0.5));
        }

        /* OTP Code Animation */
        .otp-circles {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
        }

        .otp-circle {
            width: 50px;
            height: 50px;
            background: rgba(59, 130, 246, 0.1);
            border: 2px solid rgba(59, 130, 246, 0.3);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            color: #3b82f6;
            animation: fadeInOut 1.5s ease-in-out infinite;
        }

        .otp-circle:nth-child(1) {
            animation-delay: 0s;
        }

        .otp-circle:nth-child(2) {
            animation-delay: 0.2s;
        }

        .otp-circle:nth-child(3) {
            animation-delay: 0.4s;
        }

        .otp-circle:nth-child(4) {
            animation-delay: 0.6s;
        }

        .otp-circle:nth-child(5) {
            animation-delay: 0.8s;
        }

        .otp-circle:nth-child(6) {
            animation-delay: 1s;
        }

        @keyframes fadeInOut {

            0%,
            100% {
                opacity: 0.3;
                transform: scale(0.9);
            }

            50% {
                opacity: 1;
                transform: scale(1.1);
            }
        }

        /* Text Animation */
        .animation-text {
            text-align: center;
            margin-top: 20px;
        }

        .animation-text h3 {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .animation-text p {
            font-size: 13px;
            color: #9ca3af;
        }

        .highlight {
            color: #3b82f6;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .otp-main-container {
                flex-direction: column;
                max-width: 550px;
            }

            .animation-panel {
                width: 100%;
                min-height: 380px;
                border-left: none;
                border-top: 1px solid rgba(59, 130, 246, 0.2);
            }

            .form-panel {
                padding: 40px 32px;
            }

            .otp-circles {
                gap: 8px;
            }

            .otp-circle {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            .form-panel {
                padding: 32px 24px;
            }

            .welcome-text h2 {
                font-size: 24px;
            }

            .otp-input {
                font-size: 24px;
                letter-spacing: 8px;
                padding: 14px 16px;
            }

            .otp-circle {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }

            .shield-icon {
                font-size: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="otp-main-container">
        <!-- LEFT PANEL - OTP FORM -->
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
                    Verify OTP
                </h2>
                <p>Enter the 6-digit verification code sent to your registered email</p>
            </div>

            <!-- Email Display -->
            @if (session('otp_email') || isset($email))
                <div class="email-display">
                    <i class="fas fa-envelope"></i>
                    <span>{{ session('otp_email') ?? ($email ?? 'your@email.com') }}</span>
                </div>
            @endif

            <!-- Error Messages -->
            @error('otp')
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any() && !$errors->has('otp'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify.post') }}">
                @csrf

                <div class="otp-group">
                    <div class="otp-label">
                        <i class="fas fa-key"></i> Enter 6-Digit OTP
                    </div>
                    <div class="otp-input-wrapper">
                        <input type="text" name="otp" id="otpInput" inputmode="numeric" pattern="[0-9]{6}"
                            maxlength="6" required placeholder="••••••" class="otp-input" autofocus>
                    </div>
                </div>

                <!-- Timer -->
                <div class="timer" id="timer">
                    <i class="fas fa-hourglass-half"></i>
                    <span class="timer-text" id="timerText">05:00</span>
                    <span style="font-size: 12px; color: #6b7280;">remaining</span>
                </div>

                <!-- Attempts Counter -->
                @if (isset($attempts_remaining) && $attempts_remaining < 5)
                    <div class="attempts">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $attempts_remaining }} attempts remaining
                    </div>
                @endif

                <button type="submit" class="verify-btn" id="verifyBtn">
                    <i class="fas fa-check-circle"></i> Verify OTP
                </button>

                <div class="resend-section">
                    <form method="POST" action="{{ route('otp.resend') }}" id="resendForm" style="display: inline;">
                        @csrf
                        <button type="submit" class="resend-btn" id="resendBtn">
                            <i class="fas fa-rotate-right"></i> Resend OTP
                        </button>
                    </form>
                </div>

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
                <!-- Particles -->
                <div class="particles" id="particles"></div>

                <!-- Shield Animation -->
                <div class="shield-animation">
                    <i class="fas fa-shield-alt shield-icon"></i>
                </div>

                <!-- OTP Code Animation -->
                <div class="otp-circles">
                    <div class="otp-circle">●</div>
                    <div class="otp-circle">●</div>
                    <div class="otp-circle">●</div>
                    <div class="otp-circle">●</div>
                    <div class="otp-circle">●</div>
                    <div class="otp-circle">●</div>
                </div>

                <!-- Text Animation -->
                <div class="animation-text">
                    <h3>Secure Verification</h3>
                    <p>Your OTP is <span class="highlight">valid for 5 minutes</span></p>
                    <p style="font-size: 12px; margin-top: 8px;">
                        <i class="fas fa-lock"></i> End-to-end encrypted
                    </p>
                </div>

                <!-- Instructions -->
                <div class="animation-text" style="margin-top: 20px;">
                    <p style="font-size: 11px; color: #6b7280;">
                        <i class="fas fa-info-circle"></i> Didn't receive OTP? Check spam folder
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==================== TIMER FUNCTION ====================
        let expiryTime;

        @if (session('otp_expires_at'))
            expiryTime = {{ session('otp_expires_at') }} * 1000;
        @else
            expiryTime = Date.now() + (5 * 60 * 1000);
        @endif

        const timerElement = document.getElementById('timerText');
        const timerContainer = document.getElementById('timer');
        const resendBtn = document.getElementById('resendBtn');
        const verifyBtn = document.getElementById('verifyBtn');

        function updateTimer() {
            const now = Date.now();
            const distance = expiryTime - now;

            if (distance <= 0) {
                timerElement.innerHTML = '00:00';
                timerElement.classList.add('timer-danger');
                timerContainer.classList.add('timer-danger');

                verifyBtn.disabled = true;
                verifyBtn.style.opacity = '0.5';
                verifyBtn.style.cursor = 'not-allowed';

                if (!document.getElementById('expiredMsg')) {
                    const expiredMsg = document.createElement('div');
                    expiredMsg.id = 'expiredMsg';
                    expiredMsg.className = 'alert alert-error';
                    expiredMsg.innerHTML = '<i class="fas fa-clock"></i> OTP expired. Please request a new one.';
                    document.querySelector('.otp-group').after(expiredMsg);
                }
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            timerElement.innerHTML = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (distance < 60000) {
                timerElement.classList.add('timer-warning');
                timerContainer.classList.add('timer-warning');
            } else {
                timerElement.classList.remove('timer-warning');
                timerContainer.classList.remove('timer-warning');
            }
        }

        setInterval(updateTimer, 1000);
        updateTimer();

        // ==================== OTP INPUT VALIDATION ====================
        const otpInput = document.getElementById('otpInput');
        if (otpInput) {
            otpInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 6) {
                    this.value = this.value.slice(0, 6);
                }

                if (this.value.length === 6) {
                    this.style.borderColor = '#10b981';
                    this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1)';
                } else {
                    this.style.borderColor = '';
                    this.style.boxShadow = '';
                }
            });

            otpInput.addEventListener('focus', function() {
                this.setAttribute('inputmode', 'numeric');
            });
        }

        // ==================== RESEND COOLDOWN ====================
        let lastSent = Math.floor(Date.now() / 1000) - 120;

        function updateResendButton() {
            const now = Math.floor(Date.now() / 1000);
            const diff = now - lastSent;

            if (diff < 60) {
                const waitTime = 60 - diff;
                resendBtn.disabled = true;
                resendBtn.innerHTML = `<i class="fas fa-hourglass-half"></i> Wait ${waitTime}s`;
                setTimeout(updateResendButton, 1000);
            } else {
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="fas fa-rotate-right"></i> Resend OTP';
            }
        }

        updateResendButton();

        // ==================== FORM SUBMIT HANDLER ====================
        document.getElementById('resendForm')?.addEventListener('submit', function(e) {
            if (resendBtn.disabled) {
                e.preventDefault();
                return;
            }
            resendBtn.disabled = true;
            resendBtn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Sending...';
        });

        document.querySelector('form[action*="otp.verify"]')?.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Verifying...';
        });

        // ==================== AUTO PASTE ====================
        otpInput?.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numbers = pastedText.replace(/[^0-9]/g, '').slice(0, 6);
            this.value = numbers;
            const inputEvent = new Event('input', {
                bubbles: true
            });
            this.dispatchEvent(inputEvent);
        });

        // ==================== CREATE PARTICLES ====================
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
    </script>
</body>

</html>
