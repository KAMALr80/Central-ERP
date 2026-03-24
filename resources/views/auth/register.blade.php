<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>ERP · Secure Registration</title>

    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* ====================================================================
           REGISTRATION PAGE WITH ROLE SELECTION (STAFF + AGENT)
           Dark theme, neon accents, Poppins font, responsive
        ==================================================================== */

        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            color: #fff;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #1a1a2e;
            padding: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #fff;
        }

        .footer a {
            color: #00d4ff;
            text-decoration: none;
            font-weight: 600;
            transition: .3s;
        }

        .footer a:hover {
            text-decoration: underline;
            color: #00b8d4;
        }

        /* ---------- REGISTRATION CARD – AUTH WRAPPER ADAPTED ---------- */
        .auth-wrapper {
            position: relative;
            width: 100%;
            max-width: 550px;
            min-height: auto;
            border: 2px solid #00d4ff;
            box-shadow: 0 0 25px #00d4ff;
            overflow: hidden;
            background: #1a1a2e;
            border-radius: 20px;
            padding: 40px 35px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Background shapes */
        .auth-wrapper .background-shape {
            position: absolute;
            right: -50px;
            top: -50px;
            height: 500px;
            width: 600px;
            background: linear-gradient(45deg, #1a1a2e, #00d4ff);
            transform: rotate(15deg) skewY(25deg);
            transform-origin: bottom right;
            opacity: 0.25;
            z-index: 0;
        }

        .auth-wrapper .secondary-shape {
            position: absolute;
            left: -80px;
            bottom: -80px;
            height: 500px;
            width: 600px;
            background: #1a1a2e;
            border-top: 3px solid #00d4ff;
            transform: rotate(-10deg) skewY(-20deg);
            transform-origin: bottom left;
            opacity: 0.2;
            z-index: 0;
        }

        /* ---------- CONTENT – HIGHEST Z-INDEX ---------- */
        .register-content {
            position: relative;
            z-index: 10;
            width: 100%;
        }

        /* ---------- HEADER ---------- */
        .register-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .register-header h2 {
            font-size: 32px;
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .register-header h2 i {
            color: #00d4ff;
            font-size: 32px;
        }

        .register-header p {
            font-size: 13px;
            color: #b0c4de;
            background: rgba(0, 212, 255, 0.08);
            padding: 10px 18px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(0, 212, 255, 0.2);
            margin-top: 5px;
        }

        .register-header p i {
            color: #00d4ff;
        }

        /* ---------- ROLE SELECTION TOGGLE ---------- */
        .role-toggle {
            display: flex;
            gap: 15px;
            margin: 25px 0 20px;
            background: rgba(0, 212, 255, 0.05);
            padding: 5px;
            border-radius: 60px;
            border: 1px solid rgba(0, 212, 255, 0.2);
        }

        .role-option {
            flex: 1;
            text-align: center;
            padding: 12px 20px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: transparent;
            color: #94a3b8;
        }

        .role-option i {
            font-size: 16px;
            color: #94a3b8;
        }

        .role-option.active {
            background: linear-gradient(135deg, #00d4ff, #00b8d4);
            color: #fff;
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        }

        .role-option.active i {
            color: #fff;
        }

        .role-option:hover:not(.active) {
            background: rgba(0, 212, 255, 0.1);
            color: #00d4ff;
        }

        .role-option:hover:not(.active) i {
            color: #00d4ff;
        }

        /* ---------- FORM STYLES ---------- */
        .register-form {
            width: 100%;
            transition: all 0.3s ease;
        }

        .field-wrapper {
            position: relative;
            width: 100%;
            height: 55px;
            margin-top: 20px;
        }

        .field-wrapper input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            outline: none;
            font-size: 15px;
            color: #fff;
            font-weight: 500;
            border-bottom: 2px solid #fff;
            padding-right: 40px;
            padding-left: 5px;
            transition: .5s;
        }

        .field-wrapper input:focus,
        .field-wrapper input:valid {
            border-bottom: 2px solid #00d4ff;
        }

        .field-wrapper label {
            position: absolute;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            font-size: 15px;
            color: #fff;
            transition: .5s;
            pointer-events: none;
            font-weight: 400;
        }

        .field-wrapper input:focus~label,
        .field-wrapper input:valid~label {
            top: -10px;
            font-size: 12px;
            color: #00d4ff;
        }

        .field-wrapper i {
            position: absolute;
            top: 50%;
            right: 5px;
            font-size: 18px;
            transform: translateY(-50%);
            color: #fff;
            transition: .5s;
        }

        .field-wrapper input:focus~i,
        .field-wrapper input:valid~i {
            color: #00d4ff;
        }

        /* Form Row for 2 columns */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Error messages */
        .error-message {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 6px;
            margin-left: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .error-message i {
            color: #ff6b6b;
            font-size: 12px;
        }

        /* Success Message */
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4ade80;
            font-size: 13px;
        }

        /* NEON SUBMIT BUTTON */
        .submit-button {
            position: relative;
            width: 100%;
            height: 50px;
            background: transparent;
            border-radius: 40px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            border: 2px solid #00d4ff;
            overflow: hidden;
            z-index: 1;
            color: white;
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.3s;
        }

        .submit-button::before {
            content: "";
            position: absolute;
            height: 300%;
            width: 100%;
            background: linear-gradient(#1a1a2e, #00d4ff, #1a1a2e, #00d4ff);
            top: -100%;
            left: 0;
            z-index: -1;
            transition: .5s;
        }

        .submit-button:hover:before {
            top: 0;
        }

        .submit-button:hover {
            border-color: #fff;
            box-shadow: 0 0 15px #00d4ff;
        }

        /* Agent Note */
        .agent-note {
            background: rgba(0, 212, 255, 0.05);
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border: 1px solid rgba(0, 212, 255, 0.2);
        }

        .agent-note i {
            color: #00d4ff;
            margin-right: 6px;
        }

        /* DIVIDER */
        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0 20px;
            color: #94a3b8;
            font-size: 13px;
            gap: 15px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: rgba(0, 212, 255, 0.3);
        }

        /* BACK TO LOGIN */
        .back-login {
            display: block;
            text-align: center;
            padding: 14px;
            border-radius: 40px;
            background: transparent;
            border: 2px solid #00d4ff;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .back-login::before {
            content: "";
            position: absolute;
            height: 300%;
            width: 100%;
            background: linear-gradient(#1a1a2e, #00d4ff, #1a1a2e, #00d4ff);
            top: -100%;
            left: 0;
            z-index: -1;
            transition: .5s;
        }

        .back-login:hover:before {
            top: 0;
        }

        .back-login:hover {
            border-color: #fff;
            color: #fff;
            text-decoration: none;
        }

        .back-login i {
            margin-right: 8px;
            color: #00d4ff;
            transition: 0.3s;
        }

        .back-login:hover i {
            color: #fff;
        }

        /* OTP hint */
        .otp-hint {
            font-size: 12px;
            color: #aad4ff;
            margin-top: 8px;
            text-align: right;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 5px;
        }

        .otp-hint i {
            color: #00d4ff;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .footer {
                margin-top: 20px;
                font-size: 13px;
            }

            .auth-wrapper {
                padding: 35px 25px;
            }

            .auth-wrapper .background-shape,
            .auth-wrapper .secondary-shape {
                display: none;
            }

            .register-header h2 {
                font-size: 28px;
            }

            .field-wrapper {
                height: 50px;
                margin-top: 18px;
            }

            .submit-button {
                height: 48px;
                font-size: 15px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .role-option {
                padding: 10px 15px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .auth-wrapper {
                padding: 30px 20px;
            }

            .register-header h2 {
                font-size: 24px;
            }

            .register-header h2 i {
                font-size: 24px;
            }

            .field-wrapper input,
            .field-wrapper label {
                font-size: 14px;
            }

            .submit-button {
                font-size: 14px;
                height: 46px;
            }

            .back-login {
                padding: 12px;
                font-size: 13px;
            }

            .role-option {
                padding: 8px 12px;
                font-size: 11px;
            }

            .role-option i {
                font-size: 12px;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-wrapper {
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-element {
            animation: slideInUp 0.5s ease forwards;
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">
        <!-- Background shapes -->
        <div class="background-shape"></div>
        <div class="secondary-shape"></div>

        <div class="register-content">
            <!-- HEADER SECTION -->
            <div class="register-header">
                <h2>
                    <i class="fas fa-pen-to-square"></i> ERP Register
                </h2>
                <p>
                    <i class="fas fa-shield-alt"></i>
                    <i class="fas fa-bolt" style="margin-left: 5px;"></i>
                    <span>Create your account</span>
                </p>
            </div>

            <!-- ROLE SELECTION TOGGLE -->
            <div class="role-toggle">
                <div class="role-option active" data-role="staff">
                    <i class="fas fa-users"></i> Staff
                </div>
                <div class="role-option" data-role="agent">
                    <i class="fas fa-motorcycle"></i> Delivery Agent
                </div>
            </div>

            <!-- STAFF REGISTRATION FORM (with OTP) -->
            <form method="POST" action="{{ route('register') }}" class="register-form" id="staffForm">
                @csrf
                <input type="hidden" name="role" value="staff">

                <!-- FULL NAME -->
                <div class="field-wrapper">
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                    <label for="name">Full Name</label>
                    <i class="fas fa-user"></i>
                </div>
                @error('name')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror

                <!-- EMAIL ADDRESS -->
                <div class="field-wrapper">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                    <label for="email">Email Address</label>
                    <i class="fas fa-envelope"></i>
                </div>
                @error('email')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror

                <!-- MOBILE NUMBER -->
                <div class="field-wrapper">
                    <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}" required
                        maxlength="10" pattern="\d{10}">
                    <label for="mobile">Mobile Number</label>
                    <i class="fas fa-mobile-alt"></i>
                </div>
                @error('mobile')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror

                <div class="form-row">
                    <!-- PASSWORD -->
                    <div class="field-wrapper">
                        <input type="password" name="password" id="password" required>
                        <label for="password">Password</label>
                        <i class="fas fa-lock"></i>
                    </div>

                    <!-- CONFIRM PASSWORD -->
                    <div class="field-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation" required>
                        <label for="password_confirmation">Confirm Password</label>
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                @error('password')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror

                <!-- OTP Hint -->
                <div class="otp-hint">
                    <i class="fas fa-bolt"></i>
                    <span>OTP will be sent to your mobile & email</span>
                </div>

                <!-- REGISTER BUTTON -->
                <button type="submit" class="submit-button">
                    <i class="fas fa-message"></i> Register & Get OTP
                </button>
            </form>

            <!-- AGENT REGISTRATION FORM (SIMPLE - NO OTP) -->
            <form method="POST" action="{{ route('agent.register') }}" class="register-form" id="agentForm"
                style="display: none;">
                @csrf
                <input type="hidden" name="role" value="delivery_agent">

                <!-- FULL NAME -->
                <div class="field-wrapper">
                    <input type="text" name="name" id="agent_name" required>
                    <label for="agent_name">Full Name</label>
                    <i class="fas fa-user"></i>
                </div>

                <!-- EMAIL ADDRESS -->
                <div class="field-wrapper">
                    <input type="email" name="email" id="agent_email" required>
                    <label for="agent_email">Email Address</label>
                    <i class="fas fa-envelope"></i>
                </div>

                <div class="form-row">
                    <!-- PASSWORD -->
                    <div class="field-wrapper">
                        <input type="password" name="password" id="agent_password" required>
                        <label for="agent_password">Password</label>
                        <i class="fas fa-lock"></i>
                    </div>

                    <!-- CONFIRM PASSWORD -->
                    <div class="field-wrapper">
                        <input type="password" name="password_confirmation" id="agent_password_confirmation"
                            required>
                        <label for="agent_password_confirmation">Confirm Password</label>
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <!-- AGENT NOTE - Profile completion after login -->
                <div class="agent-note" id="agentNote">
                    <i class="fas fa-info-circle"></i>
                    After registration, your account will be reviewed by admin. You will be notified once approved.
                </div>

                <button type="submit" class="submit-button" id="agentSubmitBtn">
                    <i class="fas fa-user-plus"></i> Register as Delivery Agent
                </button>
            </form>

            <!-- DIVIDER -->
            <div class="divider">
                <span class="divider-line"></span>
                <span>OR</span>
                <span class="divider-line"></span>
            </div>

            <!-- BACK TO LOGIN -->
            <a href="{{ route('login') }}" class="back-login">
                <i class="fas fa-arrow-left"></i> 🔐 Back to Login
            </a>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <span>© 2025 ERP System — </span>
        <a href="#">Secure Registration</a>
        <span> · Role based access</span>
    </div>

    <script>
        // ==================== ROLE SWITCHING ====================
        const staffTab = document.querySelector('.role-option[data-role="staff"]');
        const agentTab = document.querySelector('.role-option[data-role="agent"]');
        const staffForm = document.getElementById('staffForm');
        const agentForm = document.getElementById('agentForm');

        function switchRole(role) {
            if (role === 'staff') {
                staffForm.style.display = 'block';
                agentForm.style.display = 'none';
                staffTab.classList.add('active');
                agentTab.classList.remove('active');
            } else {
                staffForm.style.display = 'none';
                agentForm.style.display = 'block';
                agentTab.classList.add('active');
                staffTab.classList.remove('active');
            }
        }

        staffTab.addEventListener('click', () => switchRole('staff'));
        agentTab.addEventListener('click', () => switchRole('agent'));

        // ==================== MOBILE NUMBER VALIDATION ====================
        const mobileInput = document.getElementById('mobile');
        if (mobileInput) {
            mobileInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
            });
        }

        // ==================== FORM SUBMISSION HANDLER ====================
        document.getElementById('staffForm')?.addEventListener('submit', function(e) {
            const btn = this.querySelector('.submit-button');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Registering...';
        });

        // ✅ AGENT FORM SUBMISSION - Shows success message before redirect
        document.getElementById('agentForm')?.addEventListener('submit', function(e) {
            const btn = this.querySelector('.submit-button');
            const note = document.getElementById('agentNote');

            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Registering...';

            // Show processing message
            note.innerHTML = '<i class="fas fa-hourglass-half"></i> Processing your registration...';
            note.style.background = 'rgba(0, 212, 255, 0.15)';
            note.style.color = '#00d4ff';

            // Allow form to submit normally
            // The controller will handle the redirect to login page
        });
    </script>

    <!-- Add this script to show success message on page load if redirected from registration -->
    @if (session('success'))
        <script>
            setTimeout(() => {
                alert('✅ {{ session('success') }}');
            }, 500);
        </script>
    @endif

    @if (session('error'))
        <script>
            setTimeout(() => {
                alert('❌ {{ session('error') }}');
            }, 500);
        </script>
    @endif

</body>

</html>
