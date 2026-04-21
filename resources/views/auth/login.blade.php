<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>BrainBean ERP · Interactive Login</title>
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
            cursor: default;
        }

        /* Main Container */
        .login-container {
            max-width: 1280px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* LEFT PANEL - LOGIN FORM */
        .login-panel {
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
        }

        .welcome-text p {
            color: #9ca3af;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Form Styles */
        .login-form {
            margin-top: 32px;
        }

        .input-group {
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

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .checkbox span {
            font-size: 13px;
            color: #9ca3af;
        }

        .forgot-link {
            font-size: 13px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #60a5fa;
            text-decoration: underline;
        }

        /* Login Button */
        .login-btn {
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

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        /* Signup Link */
        .signup-link {
            text-align: center;
            font-size: 14px;
            color: #9ca3af;
        }

        .signup-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
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
            color: #4ade80;
            font-size: 12px;
            margin-top: 6px;
            text-align: center;
        }

        /* ==================== RIGHT PANEL - MOUSE ARROW GAME ==================== */
        .game-panel {
            width: 45%;
            background: linear-gradient(135deg, #08090e 0%, #0a0c12 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-left: 1px solid rgba(59, 130, 246, 0.2);
            cursor: default;
        }

        .game-container {
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

        /* Main Circle - TARGET */
        .target-circle {
            position: relative;
            width: 280px;
            height: 280px;
            margin-bottom: 50px;
            cursor: pointer;
            transition: transform 0.1s ease;
        }

        .target-circle:active {
            transform: scale(0.98);
        }

        .outer-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            animation: pulseRing 2s infinite;
        }

        @keyframes pulseRing {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        .inner-circle {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(30, 64, 175, 0.08));
            border-radius: 50%;
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(59, 130, 246, 0.4);
            transition: all 0.3s;
            cursor: pointer;
        }

        .inner-circle:hover {
            transform: scale(1.02);
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.3);
            border-color: rgba(59, 130, 246, 0.8);
        }

        .erp-text {
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 4px;
            animation: glowText 2s ease-in-out infinite;
            cursor: pointer;
        }

        @keyframes glowText {

            0%,
            100% {
                text-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
            }

            50% {
                text-shadow: 0 0 20px rgba(59, 130, 246, 0.8);
            }
        }

        /* Floating Arrow Indicator (visual guide) */
        .floating-arrow {
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 36px;
            color: #3b82f6;
            opacity: 0.7;
            animation: floatArrow 2s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes floatArrow {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
                opacity: 0.5;
            }

            50% {
                transform: translateX(-50%) translateY(-10px);
                opacity: 1;
            }
        }

        /* Mouse Cursor Trail Effect */
        .cursor-trail {
            position: fixed;
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.2s;
        }

        /* Score Area */
        .score-area {
            text-align: center;
            margin-top: 20px;
            z-index: 10;
        }

        .score-text {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .score-text span {
            color: #3b82f6;
            font-size: 48px;
            font-weight: 800;
        }

        .click-hint {
            font-size: 14px;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }

        .click-hint i {
            animation: bounce 1s infinite;
            color: #3b82f6;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* Instruction */
        .instruction {
            margin-top: 30px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .instruction span {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: rgba(59, 130, 246, 0.1);
            border-radius: 30px;
        }

        .instruction i {
            color: #3b82f6;
            font-size: 14px;
        }

        /* Hover Effect on Circle */
        .target-circle.hover-effect {
            transform: scale(1.03);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
                max-width: 550px;
            }

            .game-panel {
                width: 100%;
                min-height: 500px;
                border-left: none;
                border-top: 1px solid rgba(59, 130, 246, 0.2);
            }

            .target-circle {
                width: 220px;
                height: 220px;
            }

            .erp-text {
                font-size: 36px;
            }

            .score-text span {
                font-size: 36px;
            }
        }

        @media (max-width: 480px) {
            .login-panel {
                padding: 32px 24px;
            }

            .target-circle {
                width: 180px;
                height: 180px;
            }

            .erp-text {
                font-size: 28px;
            }

            .score-text {
                font-size: 24px;
            }

            .score-text span {
                font-size: 32px;
            }

            .instruction {
                gap: 10px;
            }

            .instruction span {
                font-size: 11px;
                padding: 4px 10px;
            }
        }

        /* Role-specific styling */
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
        }

        .role-badge.admin {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .role-badge.agent {
            background: rgba(16, 185, 129, 0.2);
            color: #4ade80;
        }

        .role-badge.staff {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- LEFT PANEL - LOGIN FORM -->
        <div class="login-panel">
            <div class="brand">
                <div class="brand-logo">
                    <div class="logo-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h1>BrainBean ERP</h1>
                </div>
                <p>Enterprise Resource Planning</p>
            </div>

            <div class="welcome-text">
                <h2>Welcome back</h2>
                <p>Sign in to access your dashboard and manage your business operations</p>
            </div>

            @if (session('status'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @php $prefix = tenant() ? '' : 'central.'; @endphp

            <form method="POST" action="{{ route($prefix . 'login') }}" class="login-form">
                @csrf

                <div class="input-group">
                    <label class="input-label">Email Address</label>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="admin@erp.com">
                    </div>
                    @error('email')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group">
                    <label class="input-label">Password</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                @error('role')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror

                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    @if (Route::has($prefix . 'password.request'))
                        <a href="{{ route($prefix . 'password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-arrow-right-to-bracket"></i> Sign In
                </button>

                <div class="signup-link">
                    Don't have an account? <a href="{{ route($prefix . 'register') }}">Create account</a>
                </div>
            </form>
        </div>

        <!-- RIGHT PANEL - MOUSE ARROW GAME -->
        <div class="game-panel">
            <div class="game-container">
                <!-- Animated Particles -->
                <div class="particles" id="particles"></div>

                <!-- Floating Arrow Indicator (Visual Guide) -->
                <div class="floating-arrow">
                    <i class="fas fa-mouse-pointer"></i>
                </div>

                <!-- Target Circle - Click to Score with Mouse -->
                <div class="target-circle" id="targetCircle">
                    <div class="outer-ring"></div>
                    <div class="inner-circle" id="clickTarget">
                        <div class="erp-text">ERP</div>
                    </div>
                </div>

                <!-- Score Area -->
                <div class="score-area">
                    <div class="score-text">
                        <span id="clickCount">0</span>
                    </div>
                    <div class="click-hint">
                        <i class="fas fa-hand-pointer"></i>
                        <span>Click the ERP Circle with your Mouse!</span>
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>

                <!-- Role Information -->
                <div class="instruction" style="margin-top: 20px;">
                    <span><i class="fas fa-crown"></i> Admin</span>
                    <span><i class="fas fa-motorcycle"></i> Delivery Agent</span>
                    <span><i class="fas fa-users"></i> Staff</span>
                </div>
                <div class="instruction">
                    <span><i class="fas fa-shield-alt"></i> Auto-redirect based on your role</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cursor Trail Effect -->
    <div class="cursor-trail" id="cursorTrail"></div>

    <script>
        // ==================== MOUSE ARROW GAME ====================
        let clickCount = 0;
        const clickCountSpan = document.getElementById('clickCount');
        const clickTarget = document.getElementById('clickTarget');
        const targetCircle = document.getElementById('targetCircle');

        // Load saved score from localStorage
        const savedScore = localStorage.getItem('erp_game_score');
        if (savedScore) {
            clickCount = parseInt(savedScore);
            clickCountSpan.textContent = clickCount;
        }

        // Play click sound function
        function playClickSound() {
            try {
                const audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                oscillator.frequency.value = 880;
                gainNode.gain.value = 0.15;

                oscillator.start();
                gainNode.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + 0.15);
                oscillator.stop(audioCtx.currentTime + 0.15);

                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
            } catch (e) {
                console.log('Audio not supported');
            }
        }

        // Create visual burst effect at click position
        function createBurstEffect(x, y) {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 16; i++) {
                const burst = document.createElement('div');
                burst.style.position = 'absolute';
                burst.style.width = '6px';
                burst.style.height = '6px';
                burst.style.backgroundColor = '#3b82f6';
                burst.style.borderRadius = '50%';
                burst.style.left = x + 'px';
                burst.style.top = y + 'px';
                burst.style.pointerEvents = 'none';
                burst.style.zIndex = '100';

                const angle = (Math.PI * 2 * i) / 16;
                const velocity = Math.random() * 4 + 2;
                const vx = Math.cos(angle) * velocity;
                const vy = Math.sin(angle) * velocity;

                particlesContainer.appendChild(burst);

                let posX = x;
                let posY = y;
                let opacity = 1;

                function animateBurst() {
                    posX += vx;
                    posY += vy;
                    opacity -= 0.025;
                    burst.style.left = posX + 'px';
                    burst.style.top = posY + 'px';
                    burst.style.opacity = opacity;
                    burst.style.transform = `scale(${opacity * 2})`;

                    if (opacity > 0) {
                        requestAnimationFrame(animateBurst);
                    } else {
                        burst.remove();
                    }
                }

                requestAnimationFrame(animateBurst);
            }
        }

        // Handle click on target
        function handleTargetClick(event) {
            // Increment score
            clickCount++;
            clickCountSpan.textContent = clickCount;

            // Save score to localStorage
            localStorage.setItem('erp_game_score', clickCount);

            // Visual feedback on circle
            clickTarget.style.transform = 'scale(0.98)';
            setTimeout(() => {
                clickTarget.style.transform = 'scale(1)';
            }, 150);

            // Flash effect
            clickTarget.style.boxShadow = '0 0 50px rgba(59, 130, 246, 0.9)';
            clickTarget.style.borderColor = 'rgba(59, 130, 246, 1)';
            setTimeout(() => {
                clickTarget.style.boxShadow = '';
                clickTarget.style.borderColor = '';
            }, 250);

            // Burst effect at click position
            const rect = clickTarget.getBoundingClientRect();
            const panelRect = document.querySelector('.game-panel').getBoundingClientRect();
            const clickX = event.clientX - panelRect.left;
            const clickY = event.clientY - panelRect.top;
            createBurstEffect(clickX, clickY);

            // Play sound
            playClickSound();

            // Prevent event from bubbling
            event.stopPropagation();
        }

        // Add click event listener to the target circle
        clickTarget.addEventListener('click', handleTargetClick);

        // Also add to inner-circle for better hit area
        const innerCircle = document.querySelector('.inner-circle');
        if (innerCircle) {
            innerCircle.addEventListener('click', handleTargetClick);
        }

        // Hover effect when mouse enters circle
        clickTarget.addEventListener('mouseenter', function() {
            targetCircle.classList.add('hover-effect');
        });

        clickTarget.addEventListener('mouseleave', function() {
            targetCircle.classList.remove('hover-effect');
        });

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

        // Cursor Trail Effect (optional visual flair)
        const trail = document.getElementById('cursorTrail');
        let trailTimeout;

        document.addEventListener('mousemove', function(e) {
            trail.style.left = e.clientX - 4 + 'px';
            trail.style.top = e.clientY - 4 + 'px';
            trail.style.opacity = '0.6';

            clearTimeout(trailTimeout);
            trailTimeout = setTimeout(() => {
                trail.style.opacity = '0';
            }, 100);
        });

        // Optional: Add keyboard shortcut (Enter key) for fun
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && document.activeElement !== document.querySelector('input')) {
                const fakeEvent = {
                    clientX: clickTarget.getBoundingClientRect().left + clickTarget.offsetWidth / 2,
                    clientY: clickTarget.getBoundingClientRect().top + clickTarget.offsetHeight / 2,
                    stopPropagation: function() {}
                };
                handleTargetClick(fakeEvent);
            }
        });

        // Show welcome message with score tip
        console.log('🎯 Game Ready! Click the ERP Circle to increase your score!');
        console.log('📊 Your score is saved! Total clicks: ' + clickCount);

        // Display role info on console
        console.log('👥 Role-based login:');
        console.log('   - Admin: Full system access');
        console.log('   - Delivery Agent: Delivery dashboard');
        console.log('   - Staff: Limited access');
    </script>
</body>

</html>
