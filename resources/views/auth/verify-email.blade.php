<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>ERP Nexus · Verify Email</title>
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
        .verify-container {
            max-width: 1280px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* LEFT PANEL - VERIFICATION FORM */
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

        /* Info Box */
        .info-box {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 20px;
            padding: 24px;
            margin: 24px 0;
            text-align: center;
        }

        .info-icon {
            width: 60px;
            height: 60px;
            background: rgba(59, 130, 246, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .info-icon i {
            font-size: 28px;
            color: #3b82f6;
        }

        .info-box h3 {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .info-box p {
            font-size: 14px;
            color: #9ca3af;
            line-height: 1.5;
        }

        /* Email Display */
        .email-display {
            background: #1a1d26;
            border-radius: 14px;
            padding: 12px 16px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 14px;
            color: #e2e8f0;
            border: 1px solid #2a2f3c;
        }

        .email-display i {
            color: #3b82f6;
        }

        /* Success Message */
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4ade80;
            font-size: 13px;
        }

        /* Buttons */
        .resend-btn {
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
            margin-bottom: 16px;
        }

        .resend-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        .logout-btn {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1.5px solid #2a2f3c;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 500;
            color: #9ca3af;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            color: #f87171;
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

        /* Envelope Animation */
        .envelope-animation {
            text-align: center;
            margin-bottom: 30px;
        }

        .envelope-icon {
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

        .envelope-icon i {
            font-size: 48px;
            color: #3b82f6;
            animation: bounce 1s ease-in-out infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .envelope-animation h3 {
            font-size: 20px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .envelope-animation p {
            font-size: 14px;
            color: #9ca3af;
        }

        /* Email Features */
        .email-features {
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

        /* Checkmark Animation */
        .checkmark {
            width: 60px;
            height: 60px;
            margin: 20px auto;
        }

        .checkmark-circle {
            width: 60px;
            height: 60px;
            background: rgba(16, 185, 129, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease;
        }

        .checkmark-circle i {
            font-size: 32px;
            color: #10b981;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 900px) {
            .verify-container {
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

            .envelope-icon {
                width: 70px;
                height: 70px;
            }

            .envelope-icon i {
                font-size: 32px;
            }

            .info-box {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="verify-container">
        <!-- LEFT PANEL - VERIFICATION FORM -->
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
                    <i class="fas fa-envelope"></i>
                    Verify Your Email
                </h2>
                <p>Please verify your email address to continue</p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span>A new verification link has been sent to your email address.</span>
                </div>
            @endif

            <!-- Info Box -->
            <div class="info-box">
                <div class="info-icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <h3>Check Your Inbox</h3>
                <p>We've sent a verification link to your registered email address. Click the link to verify your
                    account.</p>
            </div>

            <!-- Email Display -->
            @if (auth()->user())
                <div class="email-display">
                    <i class="fas fa-envelope"></i>
                    <span>{{ auth()->user()->email }}</span>
                </div>
            @endif

            <!-- Tips -->
            <div class="email-features" style="margin-bottom: 24px;">
                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <span>Link expires in 60 minutes</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-spam"></i>
                    <span>Check your spam folder if not received</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-sync-alt"></i>
                    <span>Request a new link if expired</span>
                </div>
            </div>

            <!-- Resend Button -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="resend-btn">
                    <i class="fas fa-paper-plane"></i> Resend Verification Email
                </button>
            </form>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </button>
            </form>
        </div>

        <!-- RIGHT PANEL - ANIMATION -->
        <div class="animation-panel">
            <div class="animation-container">
                <div class="particles" id="particles"></div>

                <div class="envelope-animation">
                    <div class="envelope-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Verification Required</h3>
                    <p>Verify your email to access all features</p>
                </div>

                <div class="email-features">
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure account protection</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-bell"></i>
                        <span>Get important notifications</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <span>Password recovery enabled</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Full dashboard access after verification</span>
                    </div>
                </div>

                <!-- Success Animation (if verification link sent) -->
                @if (session('status') == 'verification-link-sent')
                    <div class="checkmark">
                        <div class="checkmark-circle">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <div style="text-align: center; margin-top: 16px;">
                        <p style="font-size: 12px; color: #4ade80;">
                            <i class="fas fa-envelope"></i> Verification email sent!
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
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
        const resendForm = document.querySelector('form[action*="verification.send"]');
        if (resendForm) {
            resendForm.addEventListener('submit', function(e) {
                const btn = this.querySelector('.resend-btn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Sending...';
            });
        }
    </script>
</body>

</html>
