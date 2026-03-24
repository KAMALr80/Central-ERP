<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>ERP Nexus · Forgot Password</title>
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
        .forgot-container {
            max-width: 1280px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* LEFT PANEL - FORM */
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
        .forgot-form {
            margin-top: 32px;
        }

        .input-group {
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

        /* Submit Button */
        .submit-btn {
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
            margin-bottom: 24px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        /* Back to Login Link */
        .back-link {
            text-align: center;
            margin-top: 20px;
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

        /* Success/Error Messages */
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

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #f87171;
            font-size: 13px;
        }

        /* RIGHT PANEL - INTERACTIVE ANIMATION */
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

        /* Animated Background Particles */
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
        .envelope-container {
            position: relative;
            width: 200px;
            height: 150px;
            margin-bottom: 40px;
            cursor: pointer;
            animation: floatEnvelope 3s ease-in-out infinite;
        }

        @keyframes floatEnvelope {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .envelope {
            position: relative;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .envelope-flap {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            clip-path: polygon(0% 0%, 100% 0%, 50% 100%);
            animation: flap 2s ease-in-out infinite;
            transform-origin: top;
        }

        @keyframes flap {

            0%,
            100% {
                transform: rotateX(0deg);
            }

            50% {
                transform: rotateX(15deg);
            }
        }

        .envelope-body {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 90px;
            background: linear-gradient(135deg, #2d3a4e, #1a2538);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .envelope-icon {
            font-size: 40px;
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

        /* Key Icon Animation */
        .key-icon {
            font-size: 60px;
            color: #3b82f6;
            margin-bottom: 20px;
            animation: rotateKey 4s linear infinite;
        }

        @keyframes rotateKey {
            0% {
                transform: rotate(0deg);
                opacity: 0.5;
            }

            50% {
                transform: rotate(15deg);
                opacity: 1;
            }

            100% {
                transform: rotate(0deg);
                opacity: 0.5;
            }
        }

        /* Text Animation */
        .animation-text {
            text-align: center;
            margin-top: 30px;
        }

        .animation-text h3 {
            font-size: 20px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .animation-text p {
            font-size: 14px;
            color: #9ca3af;
        }

        .highlight {
            color: #3b82f6;
            font-weight: 600;
        }

        /* Instruction */
        .instruction {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .instruction span {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(59, 130, 246, 0.1);
            border-radius: 30px;
        }

        .instruction i {
            color: #3b82f6;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .forgot-container {
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

            .envelope-container {
                width: 160px;
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            .form-panel {
                padding: 32px 24px;
            }

            .welcome-text h2 {
                font-size: 24px;
            }

            .envelope-container {
                width: 140px;
                height: 105px;
            }

            .key-icon {
                font-size: 48px;
            }
        }
    </style>
</head>

<body>
    <div class="forgot-container">
        <!-- LEFT PANEL - FORGOT PASSWORD FORM -->
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
                    Forgot Password?
                </h2>
                <p>Don't worry! Enter your registered email and we'll send you a reset link to create a new password.
                </p>
            </div>

            @if (session('status'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="forgot-form">
                @csrf

                <div class="input-group">
                    <label class="input-label">Email Address</label>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="admin@erp.com">
                    </div>
                    @error('email')
                        <div style="color: #f87171; font-size: 12px; margin-top: 6px;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
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
                <!-- Animated Particles -->
                <div class="particles" id="particles"></div>

                <!-- Envelope Animation -->
                <div class="envelope-container">
                    <div class="envelope">
                        <div class="envelope-flap"></div>
                        <div class="envelope-body">
                            <i class="fas fa-envelope envelope-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Key Icon Animation -->
                <div class="key-icon">
                    <i class="fas fa-key"></i>
                </div>

                <!-- Animation Text -->
                <div class="animation-text">
                    <h3>Reset Password Link</h3>
                    <p>We'll send a secure link to your <span class="highlight">registered email</span> to reset your
                        password</p>
                </div>

                <!-- Instruction -->
                <div class="instruction">
                    <span><i class="fas fa-envelope"></i> Check your inbox</span>
                    <span><i class="fas fa-clock"></i> Link valid for 60 min</span>
                    <span><i class="fas fa-shield-alt"></i> Secure delivery</span>
                </div>

                <div class="instruction" style="margin-top: 10px;">
                    <span><i class="fas fa-info-circle"></i> Didn't receive? Check spam folder</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Create background particles
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

        // Add ripple effect to button
        const btn = document.querySelector('.submit-btn');
        if (btn) {
            btn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.3)';
                ripple.style.width = '0';
                ripple.style.height = '0';
                ripple.style.transform = 'translate(-50%, -50%)';
                ripple.style.left = e.clientX - btn.offsetLeft + 'px';
                ripple.style.top = e.clientY - btn.offsetTop + 'px';
                ripple.style.transition = 'width 0.6s, height 0.6s, opacity 0.6s';
                ripple.style.pointerEvents = 'none';
                btn.style.position = 'relative';
                btn.style.overflow = 'hidden';
                btn.appendChild(ripple);

                setTimeout(() => {
                    ripple.style.width = '300px';
                    ripple.style.height = '300px';
                    ripple.style.opacity = '0';
                }, 10);

                setTimeout(() => ripple.remove(), 600);
            });
        }
    </script>
</body>

</html>
