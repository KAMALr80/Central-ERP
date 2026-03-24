<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>ERP Nexus · Register</title>
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
        .register-container {
            max-width: 1280px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* LEFT PANEL - REGISTRATION FORM */
        .form-panel {
            flex: 1;
            padding: 48px 56px;
            background: linear-gradient(135deg, #0f1117 0%, #0b0d12 100%);
        }

        /* Logo & Brand */
        .brand {
            margin-bottom: 32px;
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
            margin-bottom: 28px;
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

        /* Role Selection Tabs */
        .role-tabs {
            display: flex;
            gap: 16px;
            margin-bottom: 32px;
            background: #1a1d26;
            padding: 6px;
            border-radius: 60px;
            border: 1px solid #2a2f3c;
        }

        .role-tab {
            flex: 1;
            padding: 12px 24px;
            border-radius: 50px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #9ca3af;
            background: transparent;
        }

        .role-tab i {
            font-size: 18px;
        }

        .role-tab.active {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .role-tab:hover:not(.active) {
            background: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
        }

        /* Form Styles */
        .register-form {
            transition: all 0.3s ease;
        }

        .form-group {
            margin-bottom: 20px;
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

        .input-field input,
        .input-field select {
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

        .input-field select {
            appearance: none;
            cursor: pointer;
            padding-right: 40px;
        }

        .input-field select option {
            background: #1a1d26;
            color: #ffffff;
        }

        .input-field input:focus,
        .input-field select:focus {
            outline: none;
            border-color: #3b82f6;
            background: #1f232f;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .input-field input:focus+i,
        .input-field select:focus+i {
            color: #3b82f6;
        }

        .select-arrow {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            pointer-events: none;
        }

        /* Form Row (2 columns) */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Agent Fields (Conditional) */
        .agent-fields {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .agent-fields.show {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Document Upload */
        .document-upload {
            border: 2px dashed #2a2f3c;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #1a1d26;
        }

        .document-upload:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.05);
        }

        .document-upload i {
            font-size: 28px;
            color: #3b82f6;
            margin-bottom: 8px;
        }

        .document-upload p {
            font-size: 12px;
            color: #9ca3af;
        }

        .document-upload input {
            display: none;
        }

        .file-name {
            font-size: 11px;
            color: #4ade80;
            margin-top: 8px;
            display: none;
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
            margin-top: 24px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #60a5fa, #2563eb);
        }

        /* Login Link */
        .login-link {
            text-align: center;
            margin-top: 24px;
        }

        .login-link a {
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .login-link a:hover {
            color: #3b82f6;
            gap: 12px;
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
            min-height: 650px;
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

        /* Role Animation Icons */
        .role-animation {
            text-align: center;
            margin-bottom: 30px;
        }

        .role-icon {
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

        .role-icon i {
            font-size: 48px;
            color: #3b82f6;
        }

        .role-animation h3 {
            font-size: 20px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .role-animation p {
            font-size: 14px;
            color: #9ca3af;
        }

        .role-features {
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

        /* Responsive */
        @media (max-width: 900px) {
            .register-container {
                flex-direction: column;
                max-width: 600px;
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

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .form-panel {
                padding: 32px 24px;
            }

            .welcome-text h2 {
                font-size: 24px;
            }

            .role-tab {
                padding: 10px 16px;
                font-size: 13px;
            }

            .role-tab i {
                font-size: 14px;
            }

            .role-icon {
                width: 70px;
                height: 70px;
            }

            .role-icon i {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <!-- LEFT PANEL - REGISTRATION FORM -->
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
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </h2>
                <p>Join our platform and start managing your operations</p>
            </div>

            @if (session('status'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Role Selection Tabs -->
            <div class="role-tabs">
                <div class="role-tab active" data-role="staff">
                    <i class="fas fa-users"></i> Staff
                </div>
                <div class="role-tab" data-role="agent">
                    <i class="fas fa-motorcycle"></i> Delivery Agent
                </div>
            </div>

            <!-- STAFF REGISTRATION FORM (with OTP) -->
            <form method="POST" action="{{ route('register') }}" class="register-form" id="staffForm">
                @csrf
                <input type="hidden" name="role" value="staff">

                <div class="form-group">
                    <label class="input-label"><i class="fas fa-user"></i> Full Name</label>
                    <div class="input-field">
                        <i class="fas fa-user-circle"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="Enter your full name">
                    </div>
                    @error('name')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="input-label"><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="you@example.com">
                    </div>
                    @error('email')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="input-label"><i class="fas fa-phone"></i> Mobile Number</label>
                    <div class="input-field">
                        <i class="fas fa-mobile-alt"></i>
                        <input type="tel" name="mobile" value="{{ old('mobile') }}" required
                            placeholder="10-digit mobile number" maxlength="10">
                    </div>
                    @error('mobile')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-lock"></i> Password</label>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" required placeholder="••••••••">
                        </div>
                        @error('password')
                            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-check-circle"></i> Confirm Password</label>
                        <div class="input-field">
                            <i class="fas fa-check-circle"></i>
                            <input type="password" name="password_confirmation" required placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Register & Get OTP
                </button>
            </form>

            <!-- AGENT REGISTRATION FORM (NO OTP - Direct Approval) -->
            <form method="POST" action="{{ route('agent.register') }}" class="register-form" id="agentForm"
                style="display: none;">
                @csrf
                <input type="hidden" name="role" value="delivery_agent">

                <div class="form-group">
                    <label class="input-label"><i class="fas fa-user"></i> Full Name</label>
                    <div class="input-field">
                        <i class="fas fa-user-circle"></i>
                        <input type="text" name="name" required placeholder="Enter your full name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-envelope"></i> Email Address</label>
                        <div class="input-field">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" required placeholder="agent@example.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-phone"></i> Mobile Number</label>
                        <div class="input-field">
                            <i class="fas fa-mobile-alt"></i>
                            <input type="tel" name="phone" required placeholder="10-digit mobile number"
                                maxlength="10">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-motorcycle"></i> Vehicle Type</label>
                        <div class="input-field">
                            <i class="fas fa-motorcycle"></i>
                            <select name="vehicle_type" required>
                                <option value="">Select Vehicle</option>
                                <option value="bike">🏍️ Bike</option>
                                <option value="scooter">🛵 Scooter</option>
                                <option value="van">🚐 Van</option>
                            </select>
                            <i class="fas fa-chevron-down select-arrow"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-hashtag"></i> Vehicle Number</label>
                        <div class="input-field">
                            <i class="fas fa-hashtag"></i>
                            <input type="text" name="vehicle_number" placeholder="GJ01AB1234">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-city"></i> City</label>
                        <div class="input-field">
                            <i class="fas fa-city"></i>
                            <input type="text" name="city" placeholder="Ahmedabad">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-map-pin"></i> Pincode</label>
                        <div class="input-field">
                            <i class="fas fa-map-pin"></i>
                            <input type="text" name="pincode" placeholder="380001">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-lock"></i> Password</label>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label"><i class="fas fa-check-circle"></i> Confirm Password</label>
                        <div class="input-field">
                            <i class="fas fa-check-circle"></i>
                            <input type="password" name="password_confirmation" required placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- Document Upload -->
                <div class="form-group">
                    <label class="input-label"><i class="fas fa-id-card"></i> Aadhar Card (Optional)</label>
                    <div class="document-upload" onclick="document.getElementById('aadharInput').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload Aadhar Card</p>
                        <input type="file" id="aadharInput" name="aadhar_card" accept="image/*,.pdf">
                        <div class="file-name" id="aadharName"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="input-label"><i class="fas fa-id-card"></i> Driving License (Optional)</label>
                    <div class="document-upload" onclick="document.getElementById('licenseInput').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload Driving License</p>
                        <input type="file" id="licenseInput" name="driving_license" accept="image/*,.pdf">
                        <div class="file-name" id="licenseName"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="input-label"><i class="fas fa-camera"></i> Profile Photo (Optional)</label>
                    <div class="document-upload" onclick="document.getElementById('photoInput').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload Photo</p>
                        <input type="file" id="photoInput" name="photo" accept="image/*">
                        <div class="file-name" id="photoName"></div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i> Register as Delivery Agent
                </button>
            </form>

            <div class="login-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Already have an account? Sign In
                </a>
            </div>
        </div>

        <!-- RIGHT PANEL - ANIMATION -->
        <div class="animation-panel">
            <div class="animation-container">
                <div class="particles" id="particles"></div>

                <div class="role-animation" id="roleAnimation">
                    <div class="role-icon">
                        <i class="fas fa-users" id="roleIcon"></i>
                    </div>
                    <h3 id="roleTitle">Staff Account</h3>
                    <p id="roleDesc">Work as office staff, manage operations, and track shipments</p>
                </div>

                <div class="role-features" id="roleFeatures">
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Access to dashboard and reports</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-truck"></i>
                        <span>Manage shipments and tracking</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-users"></i>
                        <span>Collaborate with team members</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure OTP verification</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ==================== ROLE SWITCHING ====================
        const staffTab = document.querySelector('.role-tab[data-role="staff"]');
        const agentTab = document.querySelector('.role-tab[data-role="agent"]');
        const staffForm = document.getElementById('staffForm');
        const agentForm = document.getElementById('agentForm');
        const roleIcon = document.getElementById('roleIcon');
        const roleTitle = document.getElementById('roleTitle');
        const roleDesc = document.getElementById('roleDesc');
        const roleFeatures = document.getElementById('roleFeatures');

        const staffFeatures = [{
                icon: 'chart-line',
                text: 'Access to dashboard and reports'
            },
            {
                icon: 'truck',
                text: 'Manage shipments and tracking'
            },
            {
                icon: 'users',
                text: 'Collaborate with team members'
            },
            {
                icon: 'shield-alt',
                text: 'Secure OTP verification'
            }
        ];

        const agentFeatures = [{
                icon: 'motorcycle',
                text: 'Real-time delivery tracking'
            },
            {
                icon: 'map-marker-alt',
                text: 'Live location updates'
            },
            {
                icon: 'rupee-sign',
                text: 'Track your earnings'
            },
            {
                icon: 'star',
                text: 'Build your rating & reputation'
            }
        ];

        function updateRoleUI(role) {
            if (role === 'staff') {
                staffForm.style.display = 'block';
                agentForm.style.display = 'none';
                staffTab.classList.add('active');
                agentTab.classList.remove('active');
                roleIcon.className = 'fas fa-users';
                roleTitle.textContent = 'Staff Account';
                roleDesc.textContent = 'Work as office staff, manage operations, and track shipments';

                roleFeatures.innerHTML = '';
                staffFeatures.forEach(f => {
                    roleFeatures.innerHTML += `
                        <div class="feature-item">
                            <i class="fas fa-${f.icon}"></i>
                            <span>${f.text}</span>
                        </div>
                    `;
                });
            } else {
                staffForm.style.display = 'none';
                agentForm.style.display = 'block';
                agentTab.classList.add('active');
                staffTab.classList.remove('active');
                roleIcon.className = 'fas fa-motorcycle';
                roleTitle.textContent = 'Delivery Agent Account';
                roleDesc.textContent = 'Deliver packages, track earnings, and get real-time updates';

                roleFeatures.innerHTML = '';
                agentFeatures.forEach(f => {
                    roleFeatures.innerHTML += `
                        <div class="feature-item">
                            <i class="fas fa-${f.icon}"></i>
                            <span>${f.text}</span>
                        </div>
                    `;
                });
            }
        }

        staffTab.addEventListener('click', () => updateRoleUI('staff'));
        agentTab.addEventListener('click', () => updateRoleUI('agent'));

        // ==================== FILE UPLOAD HANDLERS ====================
        function setupFileUpload(inputId, nameId) {
            const input = document.getElementById(inputId);
            const nameSpan = document.getElementById(nameId);
            if (input) {
                input.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        nameSpan.textContent = this.files[0].name;
                        nameSpan.style.display = 'block';
                    } else {
                        nameSpan.style.display = 'none';
                    }
                });
            }
        }

        setupFileUpload('aadharInput', 'aadharName');
        setupFileUpload('licenseInput', 'licenseName');
        setupFileUpload('photoInput', 'photoName');

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

        // ==================== MOBILE NUMBER VALIDATION ====================
        const mobileInputs = document.querySelectorAll('input[type="tel"]');
        mobileInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
            });
        });

        // ==================== FORM SUBMISSION HANDLER ====================
        document.getElementById('staffForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('.submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Registering...';
        });

        document.getElementById('agentForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('.submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Registering...';
        });
    </script>
</body>

</html>
