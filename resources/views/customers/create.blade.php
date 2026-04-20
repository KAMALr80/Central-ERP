@extends('layouts.app')

@section('page-title', 'Add New Customer')

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #0f172a;
        --primary-light: #1e293b;
        --accent: #2563eb;
        --accent-light: #3b82f6;
        --success: #059669;
        --danger: #dc2626;
        --warning: #d97706;
        --text-primary: #0f172a;
        --text-secondary: #334155;
        --text-muted: #64748b;
        --bg-primary: #ffffff;
        --bg-secondary: #f8fafc;
        --bg-tertiary: #f1f5f9;
        --border: #e2e8f0;
        --border-dark: #cbd5e1;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --radius-sm: 4px;
        --radius-md: 6px;
        --radius-lg: 8px;
        --radius-xl: 12px;
        --radius-2xl: 16px;
        --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: var(--bg-secondary);
        font-family: var(--font-sans);
        color: var(--text-primary);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .form-page {
        min-height: 100vh;
        background: var(--bg-secondary);
        padding: 2rem 1rem;
        width: 100%;
    }

    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: var(--bg-primary);
        border-radius: var(--radius-2xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        overflow: hidden;
        width: 100%;
    }

    /* ================= FORM HEADER ================= */
    .form-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        padding: 2rem;
        text-align: center;
        border-bottom: 1px solid var(--border);
    }

    .form-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
        color: white;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .form-title {
        font-size: 1.875rem;
        font-weight: 600;
        color: white;
        margin: 0 0 0.5rem;
        letter-spacing: -0.01em;
        word-break: break-word;
    }

    .form-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.95rem;
        margin: 0;
        word-break: break-word;
    }

    /* ================= FORM BODY ================= */
    .form-body {
        padding: 2rem;
    }

    /* ================= ALERTS ================= */
    .alert {
        padding: 1rem 1.25rem;
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        border-left: 4px solid transparent;
        font-size: 0.95rem;
        word-break: break-word;
    }

    .alert-success {
        background: #ecfdf5;
        border-left-color: var(--success);
        color: #065f46;
    }

    .alert-danger {
        background: #fef2f2;
        border-left-color: var(--danger);
        color: #991b1b;
    }

    .alert-warning {
        background: #fffbeb;
        border-left-color: var(--warning);
        color: #92400e;
    }

    .alert ul {
        margin: 0.5rem 0 0 1.5rem;
    }

    /* ================= FORM GROUPS ================= */
    .form-group {
        margin-bottom: 1.5rem;
        width: 100%;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
        width: 100%;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--text-secondary);
        word-break: break-word;
    }

    .form-label .required {
        color: var(--danger);
        margin-left: 0.25rem;
    }

    .input-group {
        position: relative;
        width: 100%;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 1.1rem;
        pointer-events: none;
        z-index: 1;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        background: var(--bg-primary);
        font-size: 0.95rem;
        color: var(--text-primary);
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-control.error {
        border-color: var(--danger);
    }

    .form-control.success {
        border-color: var(--success);
    }

    .form-control:read-only {
        background: var(--bg-secondary);
        cursor: not-allowed;
    }

    textarea.form-control {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        min-height: 100px;
        resize: vertical;
    }

    textarea.form-control + .input-icon {
        top: 1rem;
        transform: none;
    }

    /* ================= VALIDATION ICONS ================= */
    .validation-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.1rem;
        display: none;
        z-index: 1;
    }

    textarea ~ .validation-icon {
        top: 1rem;
        transform: none;
    }

    .validation-icon.success {
        color: var(--success);
        display: block;
    }

    .validation-icon.error {
        color: var(--danger);
        display: block;
    }

    /* ================= CHARACTER COUNT ================= */
    .char-count {
        position: absolute;
        right: 1rem;
        bottom: 0.75rem;
        font-size: 0.75rem;
        color: var(--text-muted);
        background: var(--bg-primary);
        padding: 0.25rem 0.5rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        z-index: 1;
    }

    /* ================= ERROR MESSAGES ================= */
    .error-message {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: var(--danger);
        word-break: break-word;
    }

    .error-message:not(.show) {
        display: none;
    }

    /* ================= HINT TEXT ================= */
    .hint-text {
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        word-break: break-word;
    }

    /* ================= LOADING SPINNER ================= */
    .spinner {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        border: 2px solid var(--border);
        border-top-color: var(--accent);
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        display: none;
        z-index: 1;
    }

    .spinner.show {
        display: block;
    }

    @keyframes spin {
        to { transform: translateY(-50%) rotate(360deg); }
    }

    /* ================= FORM FOOTER ================= */
    .form-footer {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
        width: 100%;
    }

    .btn {
        flex: 1;
        padding: 0.875rem 1.5rem;
        border-radius: var(--radius-lg);
        font-weight: 500;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        min-width: 120px;
        word-break: break-word;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
        background: var(--accent-light);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: var(--bg-tertiary);
        color: var(--text-secondary);
        border: 1px solid var(--border);
    }

    .btn-secondary:hover {
        background: var(--border);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* ================= LOADING BUTTON SPINNER ================= */
    .btn-spinner {
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: btn-spin 0.6s linear infinite;
        flex-shrink: 0;
    }

    @keyframes btn-spin {
        to { transform: rotate(360deg); }
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */

    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .form-container {
            max-width: 800px;
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .form-container {
            max-width: 700px;
        }

        .form-title {
            font-size: 1.75rem;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .form-page {
            padding: 1.5rem 1rem;
        }

        .form-container {
            max-width: 100%;
        }

        .form-header {
            padding: 1.75rem;
        }

        .form-body {
            padding: 1.75rem;
        }

        .form-title {
            font-size: 1.6rem;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .form-page {
            padding: 1rem 0.75rem;
        }

        .form-header {
            padding: 1.5rem;
        }

        .form-body {
            padding: 1.5rem;
        }

        .form-icon {
            width: 56px;
            height: 56px;
            font-size: 1.75rem;
        }

        .form-title {
            font-size: 1.4rem;
        }

        .form-subtitle {
            font-size: 0.85rem;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .form-footer {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .alert {
            padding: 0.875rem 1rem;
            font-size: 0.85rem;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .form-page {
            padding: 0.75rem 0.5rem;
        }

        .form-header {
            padding: 1.25rem;
        }

        .form-body {
            padding: 1.25rem;
        }

        .form-icon {
            width: 48px;
            height: 48px;
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .form-title {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }

        .form-subtitle {
            font-size: 0.8rem;
        }

        .form-label {
            font-size: 0.85rem;
        }

        .form-control {
            padding: 0.7rem 1rem 0.7rem 2.5rem;
            font-size: 0.9rem;
        }

        .input-icon {
            left: 0.75rem;
            font-size: 1rem;
        }

        .validation-icon {
            right: 0.75rem;
            font-size: 1rem;
        }

        .spinner {
            right: 0.75rem;
            width: 18px;
            height: 18px;
        }

        .char-count {
            right: 0.75rem;
            bottom: 0.5rem;
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }

        .error-message {
            font-size: 0.8rem;
        }

        .hint-text {
            font-size: 0.75rem;
        }

        .btn {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .form-page {
            padding: 0.5rem 0.25rem;
        }

        .form-header {
            padding: 1rem;
        }

        .form-body {
            padding: 1rem;
        }

        .form-icon {
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
        }

        .form-title {
            font-size: 1.1rem;
        }

        .form-subtitle {
            font-size: 0.75rem;
        }

        .form-label {
            font-size: 0.8rem;
        }

        .form-control {
            padding: 0.6rem 0.75rem 0.6rem 2.25rem;
            font-size: 0.85rem;
        }

        .input-icon {
            left: 0.5rem;
            font-size: 0.9rem;
        }

        .validation-icon {
            right: 0.5rem;
            font-size: 0.9rem;
        }

        .spinner {
            right: 0.5rem;
            width: 16px;
            height: 16px;
        }

        .alert {
            padding: 0.75rem;
            font-size: 0.8rem;
        }

        .alert ul {
            margin: 0.25rem 0 0 1.25rem;
        }

        .btn {
            padding: 0.6rem 0.75rem;
            font-size: 0.85rem;
        }

        .btn-spinner {
            width: 16px;
            height: 16px;
        }
    }
</style>

<div class="form-page">
    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <div class="form-icon">👤</div>
            <h1 class="form-title">Add New Customer</h1>
            <p class="form-subtitle">Fill in the customer details to add to your database</p>
        </div>

        <!-- Body -->
        <div class="form-body">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success">
                    <span>✅</span>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Summary -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <span>⚠️</span>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('customers.store') }}" id="customerForm">
                @csrf

                <!-- Full Name -->
                <div class="form-group">
                    <label class="form-label">
                        Full Name
                        <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-icon">👤</span>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') error @enderror"
                               value="{{ old('name') }}"
                               placeholder="Enter customer's full name"
                               required
                               autofocus>
                        <span class="validation-icon success">✓</span>
                        <span class="validation-icon error">✗</span>
                    </div>
                    @error('name')
                        <div class="error-message show">
                            <span>⚠️</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Mobile Number -->
                <div class="form-group">
                    <label class="form-label">
                        Mobile Number
                        <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-icon">📱</span>
                        <input type="tel"
                               name="mobile"
                               class="form-control @error('mobile') error @enderror"
                               value="{{ old('mobile') }}"
                               placeholder="Enter 10-digit mobile number"
                               required
                               maxlength="10"
                               pattern="[0-9]{10}">
                        <span class="validation-icon success">✓</span>
                        <span class="validation-icon error">✗</span>
                    </div>
                    @error('mobile')
                        <div class="error-message show">
                            <span>⚠️</span>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="hint-text">
                        <span>ℹ️</span>
                        Enter 10-digit mobile number without country code
                    </div>
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-icon">📧</span>
                        <input type="email"
                               name="email"
                               class="form-control @error('email') error @enderror"
                               value="{{ old('email') }}"
                               placeholder="customer@example.com">
                        <span class="validation-icon success">✓</span>
                        <span class="validation-icon error">✗</span>
                    </div>
                    @error('email')
                        <div class="error-message show">
                            <span>⚠️</span>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="hint-text">
                        <span>ℹ️</span>
                        Optional - for sending invoices and updates
                    </div>
                </div>

                <!-- GST Number -->
                <div class="form-group">
                    <label class="form-label">GST Number</label>
                    <div class="input-group">
                        <span class="input-icon">🏢</span>
                        <input type="text"
                               name="gst_no"
                               class="form-control @error('gst_no') error @enderror"
                               value="{{ old('gst_no') }}"
                               placeholder="27ABCDE1234F1Z5"
                               pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}">
                        <span class="validation-icon success">✓</span>
                        <span class="validation-icon error">✗</span>
                    </div>
                    @error('gst_no')
                        <div class="error-message show">
                            <span>⚠️</span>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="hint-text">
                        <span>ℹ️</span>
                        Optional - 15-character GSTIN format
                    </div>
                </div>

                <!-- PIN Code with Auto-fetch -->
                <div class="form-group">
                    <label class="form-label">
                        PIN Code
                        <span class="required">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-icon">📮</span>
                        <input type="text"
                               name="pincode"
                               id="pincode"
                               class="form-control @error('pincode') error @enderror"
                               value="{{ old('pincode') }}"
                               placeholder="Enter 6-digit PIN code"
                               required
                               maxlength="6"
                               pattern="[0-9]{6}">
                        <span class="validation-icon success">✓</span>
                        <span class="validation-icon error">✗</span>
                        <span class="spinner" id="pincodeSpinner"></span>
                    </div>
                    @error('pincode')
                        <div class="error-message show">
                            <span>⚠️</span>
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="hint-text">
                        <span>ℹ️</span>
                        Enter 6-digit PIN code to auto-fetch city and state
                    </div>
                </div>

                <!-- City and State Row -->
                <div class="form-row">
                    <!-- City -->
                    <div class="form-group">
                        <label class="form-label">
                            City
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-icon">🏙️</span>
                            <input type="text"
                                   name="city"
                                   id="city"
                                   class="form-control @error('city') error @enderror"
                                   value="{{ old('city') }}"
                                   placeholder="City will auto-fill"
                                   required
                                   readonly>
                            <span class="validation-icon success">✓</span>
                            <span class="validation-icon error">✗</span>
                        </div>
                        @error('city')
                            <div class="error-message show">
                                <span>⚠️</span>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- State -->
                    <div class="form-group">
                        <label class="form-label">
                            State
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-icon">🗺️</span>
                            <input type="text"
                                   name="state"
                                   id="state"
                                   class="form-control @error('state') error @enderror"
                                   value="{{ old('state') }}"
                                   placeholder="State will auto-fill"
                                   required
                                   readonly>
                            <span class="validation-icon success">✓</span>
                            <span class="validation-icon error">✗</span>
                        </div>
                        @error('state')
                            <div class="error-message show">
                                <span>⚠️</span>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <div class="input-group">
                        <span class="input-icon">📍</span>
                        <textarea name="address"
                                  class="form-control @error('address') error @enderror"
                                  placeholder="Enter complete address including street, landmark, etc."
                                  rows="4">{{ old('address') }}</textarea>
                        <span class="validation-icon success">✓</span>
                        <span class="validation-icon error">✗</span>
                        <span class="char-count">
                            <span id="addressCount">0</span>/200
                        </span>
                    </div>
                    @error('address')
                        <div class="error-message show">
                            <span>⚠️</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="form-footer">
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <span>←</span>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span>💾</span>
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ================= DOM ELEMENTS =================
        const form = document.getElementById('customerForm');
        const submitBtn = document.getElementById('submitBtn');
        const pincodeInput = document.getElementById('pincode');
        const cityInput = document.getElementById('city');
        const stateInput = document.getElementById('state');
        const pincodeSpinner = document.getElementById('pincodeSpinner');
        const addressTextarea = document.querySelector('textarea[name="address"]');
        const addressCount = document.getElementById('addressCount');
        const mobileInput = document.querySelector('input[name="mobile"]');
        const gstInput = document.querySelector('input[name="gst_no"]');

        // ================= API CONFIGURATION =================
        const PINCODE_API_URL = 'https://api.postalpincode.in/pincode/';

        // ================= UTILITY FUNCTIONS =================
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        function showNotification(message, type = 'success') {
            // Remove any existing notifications
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <span>${type === 'success' ? '✅' : '⚠️'}</span>
                ${message}
            `;

            form.insertBefore(alert, form.firstChild);

            setTimeout(() => alert.remove(), 3000);
        }

        // ================= PIN CODE AUTO-FETCH =================
        async function fetchLocationFromPincode(pincode) {
            if (!pincode || pincode.length !== 6 || !/^\d+$/.test(pincode)) {
                return;
            }

            // Show spinner
            pincodeSpinner.classList.add('show');
            pincodeInput.classList.remove('success', 'error');

            try {
                const response = await fetch(PINCODE_API_URL + pincode);
                const data = await response.json();

                if (data[0].Status === 'Success' && data[0].PostOffice?.length > 0) {
                    const postOffice = data[0].PostOffice[0];

                    // Auto-fill city and state
                    cityInput.value = postOffice.District || postOffice.Block || postOffice.Region;
                    stateInput.value = postOffice.State;

                    // Add success styling
                    pincodeInput.classList.add('success');
                    cityInput.classList.add('success');
                    stateInput.classList.add('success');

                    showNotification('Location fetched successfully!', 'success');
                } else {
                    // Clear fields on invalid PIN
                    cityInput.value = '';
                    stateInput.value = '';
                    pincodeInput.classList.add('error');

                    showNotification('Invalid PIN code or location not found', 'warning');
                }
            } catch (error) {
                console.error('Error fetching location:', error);
                cityInput.value = '';
                stateInput.value = '';
                pincodeInput.classList.add('error');

                showNotification('Failed to fetch location. Please try again.', 'warning');
            } finally {
                // Hide spinner
                pincodeSpinner.classList.remove('show');
            }
        }

        // Debounced PIN code handler
        const debouncedFetchLocation = debounce((e) => {
            fetchLocationFromPincode(e.target.value);
        }, 500);

        // PIN code event listeners
        if (pincodeInput) {
            pincodeInput.addEventListener('input', function(e) {
                // Allow only numbers
                this.value = this.value.replace(/[^0-9]/g, '');

                // Limit to 6 digits
                if (this.value.length > 6) {
                    this.value = this.value.slice(0, 6);
                }

                // Clear city/state when PIN is cleared or invalid
                if (this.value.length < 6) {
                    cityInput.value = '';
                    stateInput.value = '';
                    cityInput.classList.remove('success');
                    stateInput.classList.remove('success');
                }

                debouncedFetchLocation(e);
            });

            pincodeInput.addEventListener('blur', function() {
                if (this.value.length === 6 && /^\d+$/.test(this.value)) {
                    fetchLocationFromPincode(this.value);
                }
            });
        }

        // ================= ADDRESS CHARACTER COUNTER =================
        if (addressTextarea && addressCount) {
            // Initialize counter
            addressCount.textContent = addressTextarea.value.length;

            addressTextarea.addEventListener('input', function() {
                const length = this.value.length;
                addressCount.textContent = length;

                if (length > 200) {
                    this.value = this.value.substring(0, 200);
                    addressCount.textContent = 200;
                }
            });
        }

        // ================= AUTO-FORMAT MOBILE NUMBER =================
        if (mobileInput) {
            mobileInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
                if (this.value.length > 10) {
                    this.value = this.value.substring(0, 10);
                }
            });
        }

        // ================= AUTO-FORMAT GST (UPPERCASE) =================
        if (gstInput) {
            gstInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }

        // ================= FORM VALIDATION =================
        function validateField(field) {
            const value = field.value.trim();
            const isRequired = field.hasAttribute('required');
            const successIcon = field.parentElement.querySelector('.validation-icon.success');
            const errorIcon = field.parentElement.querySelector('.validation-icon.error');
            const errorMessage = field.parentElement.parentElement.querySelector('.error-message');

            // Reset classes
            field.classList.remove('success', 'error');
            if (successIcon) successIcon.style.display = 'none';
            if (errorIcon) errorIcon.style.display = 'none';
            if (errorMessage) errorMessage.classList.remove('show');

            // Check required
            if (isRequired && !value) {
                field.classList.add('error');
                if (errorIcon) errorIcon.style.display = 'block';
                if (errorMessage) {
                    errorMessage.innerHTML = '<span>⚠️</span> This field is required';
                    errorMessage.classList.add('show');
                }
                return false;
            }

            // Email validation
            if (field.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    field.classList.add('error');
                    if (errorIcon) errorIcon.style.display = 'block';
                    if (errorMessage) {
                        errorMessage.innerHTML = '<span>⚠️</span> Please enter a valid email address';
                        errorMessage.classList.add('show');
                    }
                    return false;
                }
            }

            // Mobile validation
            if (field.name === 'mobile' && value) {
                if (value.length !== 10) {
                    field.classList.add('error');
                    if (errorIcon) errorIcon.style.display = 'block';
                    if (errorMessage) {
                        errorMessage.innerHTML = '<span>⚠️</span> Mobile number must be 10 digits';
                        errorMessage.classList.add('show');
                    }
                    return false;
                }
            }

            // GST validation
            if (field.name === 'gst_no' && value) {
                const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
                if (!gstRegex.test(value)) {
                    field.classList.add('error');
                    if (errorIcon) errorIcon.style.display = 'block';
                    if (errorMessage) {
                        errorMessage.innerHTML = '<span>⚠️</span> Please enter a valid 15-character GSTIN';
                        errorMessage.classList.add('show');
                    }
                    return false;
                }
            }

            // If we get here, field is valid
            if (value) {
                field.classList.add('success');
                if (successIcon) successIcon.style.display = 'block';
            }

            return true;
        }

        // Add validation listeners to all inputs
        form.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', () => validateField(input));
            input.addEventListener('blur', () => validateField(input));
        });

        // ================= FORM SUBMISSION =================
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Validate all required fields
            form.querySelectorAll('[required]').forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });

            // Validate PIN code specifically
            if (pincodeInput && pincodeInput.value.length !== 6) {
                isValid = false;
                pincodeInput.classList.add('error');
                showNotification('Please enter a valid 6-digit PIN code', 'warning');
            }

            if (!isValid) {
                e.preventDefault();
                showNotification('Please fix the errors before submitting', 'warning');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="btn-spinner"></span>
                Saving...
            `;
        });
    });
</script>
@endsection
