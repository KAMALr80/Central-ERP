{{-- resources/views/agent/support/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Support Center')

@section('content')
    <style>
        .support-page {
            min-height: 100vh;
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .support-card {
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .support-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .support-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
            min-width: 280px;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            flex-shrink: 0;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .header-title {
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .header-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-btn:hover {
            background: white;
            color: #1e293b;
            transform: translateY(-2px);
        }

        /* Contact Cards */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: clamp(1.5rem, 4vw, 2rem);
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            cursor: pointer;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .contact-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 2rem;
        }

        .contact-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }

        .contact-detail {
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .contact-timing {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Form Section */
        .form-section {
            padding: clamp(1.5rem, 4vw, 2rem);
            background: white;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .section-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .section-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0.25rem 0 0;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        textarea.form-control {
            resize: vertical;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 40px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        /* FAQ Section */
        .faq-section {
            padding: 0 clamp(1.5rem, 4vw, 2rem) clamp(1.5rem, 4vw, 2rem);
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }

        .faq-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-top: 1rem;
        }

        .faq-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .faq-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
        }

        .accordion-item {
            border: none;
            margin-bottom: 0.75rem;
            background: white;
            border-radius: 16px !important;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .accordion-button {
            background: white;
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }

        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            color: #667eea;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: transparent;
        }

        .accordion-body {
            padding: 1rem 1.25rem;
            color: #64748b;
            border-top: 1px solid #e5e7eb;
        }

        /* Alert Styles */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: #d1fae5;
            border-left-color: #10b981;
            color: #065f46;
        }

        @media (max-width: 768px) {
            .header-left {
                flex-direction: column;
                text-align: center;
            }

            .contact-grid {
                grid-template-columns: 1fr;
            }

            .btn-primary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="support-page">
        <div class="container">
            <div class="support-card">
                <div class="support-header">
                    <div class="header-content">
                        <div class="header-left">
                            <div class="header-icon"><i class="fas fa-headset"></i></div>
                            <div>
                                <h1 class="header-title">Support Center</h1>
                                <p class="header-subtitle">
                                    <i class="fas fa-life-ring"></i> We're here to help you 24/7
                                </p>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="{{ route('agent.dashboard') }}" class="header-btn">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="form-section" style="padding-bottom: 0;">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle fa-lg"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Contact Cards -->
                <div class="contact-grid">
                    <div class="contact-card" onclick="window.location.href='tel:+919876543210'">
                        <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                        <h5 class="contact-title">Call Us</h5>
                        <p class="contact-detail"><strong>+91 98765 43210</strong></p>
                        <p class="contact-timing">Mon-Sat, 9AM - 6PM</p>
                        <small class="text-muted">Toll-free support</small>
                    </div>

                    <div class="contact-card" onclick="window.location.href='mailto:support@invozia.com'">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <h5 class="contact-title">Email Us</h5>
                        <p class="contact-detail"><strong>support@invozia.com</strong></p>
                        <p class="contact-timing">24x7 Support</p>
                        <small class="text-muted">Response within 24 hours</small>
                    </div>

                    <div class="contact-card" onclick="openChat()">
                        <div class="contact-icon"><i class="fas fa-comments"></i></div>
                        <h5 class="contact-title">Live Chat</h5>
                        <p class="contact-detail"><strong>Chat with an expert</strong></p>
                        <p class="contact-timing">Available Now</p>
                        <small class="text-muted">Instant response</small>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="fas fa-paper-plane"></i></div>
                        <div>
                            <h3 class="section-title">Send us a Message</h3>
                            <p class="section-subtitle">We'll get back to you within 24 hours</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('agent.support.send') }}" id="supportForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user text-primary me-1"></i> Your Name
                                </label>
                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly
                                    disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-envelope text-primary me-1"></i> Email Address
                                </label>
                                <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly
                                    disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tag text-primary me-1"></i> Subject <span class="text-danger">*</span>
                                </label>
                                <select name="subject" class="form-control @error('subject') is-invalid @enderror" required>
                                    <option value="">Select a subject</option>
                                    <option value="delivery_issue">🚚 Delivery Issue</option>
                                    <option value="payment_issue">💰 Payment/Earnings Issue</option>
                                    <option value="technical_issue">🔧 Technical Issue</option>
                                    <option value="account_issue">👤 Account Related</option>
                                    <option value="vehicle_issue">🏍️ Vehicle/Equipment Issue</option>
                                    <option value="other">❓ Other</option>
                                </select>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-comment-dots text-primary me-1"></i> Message <span
                                        class="text-danger">*</span>
                                </label>
                                <textarea name="message" rows="6" class="form-control @error('message') is-invalid @enderror"
                                    placeholder="Describe your issue in detail..."></textarea>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle"></i> Please provide as much detail as possible for faster
                                    resolution.
                                </small>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-3">
                                    <button type="reset" class="btn btn-secondary"
                                        style="background: #f1f5f9; color: #475569; padding: 0.875rem 2rem; border-radius: 40px;">
                                        <i class="fas fa-undo me-2"></i> Clear
                                    </button>
                                    <button type="submit" class="btn-primary" id="submitBtn">
                                        <i class="fas fa-paper-plane me-2"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- FAQ Section -->
                <div class="faq-section">
                    <div class="faq-header">
                        <div class="faq-icon"><i class="fas fa-question"></i></div>
                        <h3 class="faq-title">Frequently Asked Questions</h3>
                    </div>

                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq1">
                                    <i class="fas fa-check-circle text-success me-2"></i> How do I mark a delivery as
                                    complete?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>To mark a delivery as complete:</p>
                                    <ol class="mb-0">
                                        <li>Navigate to the delivery in your <strong>Active Deliveries</strong> list</li>
                                        <li>Tap on <strong>"Track & Complete"</strong> button</li>
                                        <li>When you reach the destination, click <strong>"Mark as Delivered"</strong></li>
                                        <li>Capture the customer's signature (required)</li>
                                        <li>Take a photo of the delivered package (optional)</li>
                                        <li>Add any delivery notes</li>
                                        <li>Confirm to complete the delivery</li>
                                    </ol>
                                    <div class="alert alert-info mt-2 mb-0 py-2">
                                        <i class="fas fa-info-circle"></i> The customer will receive an SMS notification
                                        once delivered.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq2">
                                    <i class="fas fa-user-slash text-warning me-2"></i> What if the customer is not
                                    available?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>If the customer is not available at the delivery location:</p>
                                    <ol class="mb-0">
                                        <li>Try calling the customer twice</li>
                                        <li>Wait for 10 minutes at the location</li>
                                        <li>If still unavailable, mark the delivery as <strong>"Failed - Customer Not
                                                Available"</strong></li>
                                        <li>Provide a reason in the delivery notes</li>
                                        <li>The shipment will be rescheduled for the next working day</li>
                                        <li>If second attempt fails, it will be returned to warehouse</li>
                                    </ol>
                                    <div class="alert alert-warning mt-2 mb-0 py-2">
                                        <i class="fas fa-clock"></i> Maximum 2 delivery attempts allowed per shipment.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq3">
                                    <i class="fas fa-rupee-sign text-success me-2"></i> How are my earnings calculated?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Your earnings are calculated based on your commission structure:</p>
                                    <ul class="mb-2">
                                        <li><strong>Fixed Commission:</strong> ₹{{ $agent->commission_value ?? 50 }} per
                                            successful delivery</li>
                                        <li><strong>Percentage Commission:</strong> {{ $agent->commission_value ?? 10 }}% of
                                            order value</li>
                                    </ul>
                                    <p class="mb-2">You can view your earnings breakdown in the <strong>Earnings</strong>
                                        section:</p>
                                    <ul class="mb-0">
                                        <li>Daily earnings summary</li>
                                        <li>Monthly statements</li>
                                        <li>Per-shipment commission details</li>
                                        <li>Payout history</li>
                                    </ul>
                                    <div class="alert alert-success mt-2 mb-0 py-2">
                                        <i class="fas fa-calendar-alt"></i> Earnings are settled weekly on every Friday.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq4">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i> How does live tracking work?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Live tracking uses your device's GPS to show:</p>
                                    <ul class="mb-0">
                                        <li><strong>Real-time location</strong> - Your current position on the map</li>
                                        <li><strong>Speed tracking</strong> - How fast you're moving (km/h)</li>
                                        <li><strong>Distance to destination</strong> - Remaining distance in kilometers</li>
                                        <li><strong>ETA</strong> - Estimated time of arrival</li>
                                        <li><strong>Route optimization</strong> - Best path to destination</li>
                                        <li><strong>Progress percentage</strong> - How much of the journey is completed</li>
                                    </ul>
                                    <div class="alert alert-info mt-2 mb-0 py-2">
                                        <i class="fas fa-battery-full"></i> Make sure GPS is enabled and battery
                                        optimization is disabled for accurate tracking.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq5">
                                    <i class="fas fa-motorcycle text-info me-2"></i> How do I update my vehicle details?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>To update your vehicle information:</p>
                                    <ol class="mb-0">
                                        <li>Go to <strong>Profile Settings</strong> from the sidebar</li>
                                        <li>Navigate to the <strong>Vehicle Information</strong> section</li>
                                        <li>Update your vehicle type (Bike/Scooter/Van)</li>
                                        <li>Enter your vehicle number</li>
                                        <li>Click <strong>Update Profile</strong> to save changes</li>
                                    </ol>
                                    <div class="alert alert-warning mt-2 mb-0 py-2">
                                        <i class="fas fa-id-card"></i> Make sure vehicle number matches RC document for
                                        verification.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq6">
                                    <i class="fas fa-trophy text-warning me-2"></i> How can I improve my rating?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Tips to maintain a high rating:</p>
                                    <ul class="mb-0">
                                        <li><strong>Be punctual</strong> - Deliver on time or early</li>
                                        <li><strong>Communicate</strong> - Call customers before arrival</li>
                                        <li><strong>Be professional</strong> - Wear uniform, be polite</li>
                                        <li><strong>Handle packages carefully</strong> - Ensure no damage</li>
                                        <li><strong>Follow instructions</strong> - Check delivery notes</li>
                                        <li><strong>Collect proof</strong> - Always get signature/photo</li>
                                    </ul>
                                    <div class="alert alert-success mt-2 mb-0 py-2">
                                        <i class="fas fa-star"></i> 5-star ratings increase your priority for high-value
                                        deliveries!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openChat() {
            // You can integrate a live chat service like Tawk.to, Crisp, etc.
            alert('Live chat support coming soon! Please call or email us for immediate assistance.');
            // Or open a chat widget:
            // window.open('https://tawk.to/chat/...', '_blank');
        }

        document.getElementById('supportForm')?.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
            submitBtn.disabled = true;
            // Form will submit normally
        });
    </script>
@endsection
