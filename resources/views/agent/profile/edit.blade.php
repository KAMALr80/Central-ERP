{{-- resources/views/agent/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .profile-page {
            min-height: 100vh;
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .profile-card {
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

        .profile-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
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

        .form-section {
            padding: clamp(1.5rem, 4vw, 2rem);
            border-bottom: 1px solid #e5e7eb;
            background: white;
            transition: all 0.3s ease;
        }

        .form-section:hover {
            background: #f8fafc;
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-label i {
            color: #667eea;
        }

        .required-star {
            color: #ef4444;
            margin-left: 0.25rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 0.95rem;
            color: #1e293b;
            background: white;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control[readonly] {
            background: #f1f5f9;
        }

        .map-preview {
            height: 300px;
            border-radius: 16px;
            margin-top: 1rem;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .map-preview:hover {
            border-color: #667eea;
        }

        .location-detect-btn {
            margin-top: 0.5rem;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .location-detect-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .photo-preview {
            width: 120px;
            height: 120px;
            border-radius: 60px;
            object-fit: cover;
            border: 4px solid #e5e7eb;
            margin-bottom: 1rem;
        }

        .photo-upload-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #f1f5f9;
            border: 1px solid #e5e7eb;
            border-radius: 30px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .photo-upload-btn:hover {
            background: #e2e8f0;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            flex-wrap: wrap;
            padding: 1.5rem clamp(1.5rem, 4vw, 2rem);
            background: #f8fafc;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

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

        .alert-error {
            background: #fee2e2;
            border-left-color: #ef4444;
            color: #991b1b;
        }

        .toast {
            position: fixed;
            top: 30px;
            right: 30px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            display: none;
            z-index: 10000;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.success {
            border-left-color: #10b981;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            z-index: 11000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1.5rem;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e5e7eb;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .header-left {
                flex-direction: column;
                text-align: center;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .map-preview {
                height: 250px;
            }

            .photo-preview {
                width: 100px;
                height: 100px;
            }
        }
    </style>

    <div class="profile-page">
        <div class="container">
            <div class="profile-card">
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="spinner"></div>
                    <div class="loading-text">Updating profile...</div>
                </div>

                <div class="profile-header">
                    <div class="header-content">
                        <div class="header-left">
                            <div class="header-icon"><i class="fas fa-user-edit"></i></div>
                            <div>
                                <h1 class="header-title">Edit Profile</h1>
                                <p class="header-subtitle"><i class="fas fa-motorcycle"></i> Update your personal
                                    information
                                </p>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="{{ route('agent.dashboard') }}" class="header-btn"><i class="fas fa-arrow-left"></i>
                                Back to Dashboard</a>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="form-section" style="padding-bottom: 0;">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="form-section" style="padding-bottom: 0;">
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <ul style="margin:0; padding-left:1rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('agent.profile.update') }}" enctype="multipart/form-data"
                    id="profileForm">
                    @csrf
                    @method('PUT')

                    {{-- Profile Photo Section --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-camera"></i></div>
                            <div>
                                <h3 class="section-title">Profile Photo</h3>
                                <p class="section-subtitle">Update your profile picture</p>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            @if ($agent && $agent->photo)
                                <img id="photoPreview" src="{{ Storage::url($agent->photo) }}" class="photo-preview"
                                    alt="Profile Photo">
                            @else
                                <img id="photoPreview"
                                    src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=667eea&color=fff&size=120"
                                    class="photo-preview" alt="Profile Photo">
                            @endif
                            <div>
                                <label class="photo-upload-btn">
                                    <i class="fas fa-upload"></i> Choose New Photo
                                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                                        style="display: none;" onchange="previewPhoto(this)">
                                </label>
                                <p class="input-hint" style="margin-top: 0.5rem; font-size: 0.8rem; color: #64748b;">JPG,
                                    PNG, GIF. Max 2MB</p>
                            </div>
                        </div>
                    </div>

                    {{-- Personal Information --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-user"></i></div>
                            <div>
                                <h3 class="section-title">Personal Information</h3>
                                <p class="section-subtitle">Update your personal details</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user"></i> Full Name <span
                                        class="required-star">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $user->name) }}" required placeholder="Enter full name">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" readonly
                                    style="background:#f1f5f9;">
                                <small class="text-muted" style="font-size: 0.75rem;">Email cannot be changed</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $agent->phone ?? '') }}" placeholder="10 digit mobile number">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone-alt"></i> Alternate Phone</label>
                                <input type="text" name="alternate_phone" class="form-control"
                                    value="{{ old('alternate_phone', $agent->alternate_phone ?? '') }}"
                                    placeholder="Alternate contact">
                            </div>
                        </div>
                    </div>

                    {{-- Location Information with Google Maps --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h3 class="section-title">Current Location</h3>
                                <p class="section-subtitle">Set your current location for live tracking</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-search"></i> Search Address / Current
                                Location</label>
                            <input type="text" id="location-search" class="form-control"
                                placeholder="Search for address or drop pin on map" autocomplete="off"
                                value="{{ old('current_location', $agent->current_location ?? '') }}">
                            <button type="button" id="detect-location" class="location-detect-btn">
                                <i class="fas fa-location-dot"></i> Use My Current Location
                            </button>
                        </div>

                        <div id="map-preview" class="map-preview"></div>

                        <input type="hidden" name="current_latitude" id="latitude"
                            value="{{ old('current_latitude', $agent->current_latitude ?? '') }}">
                        <input type="hidden" name="current_longitude" id="longitude"
                            value="{{ old('current_longitude', $agent->current_longitude ?? '') }}">
                        <input type="hidden" name="current_location" id="current_location"
                            value="{{ old('current_location', $agent->current_location ?? '') }}">

                        <div class="form-grid" style="margin-top: 1rem;">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-city"></i> City</label>
                                <input type="text" name="city" id="city" class="form-control"
                                    value="{{ old('city', $agent->city ?? '') }}" readonly style="background:#f8fafc;">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-map"></i> State</label>
                                <input type="text" name="state" id="state" class="form-control"
                                    value="{{ old('state', $agent->state ?? '') }}" readonly style="background:#f8fafc;">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-mail-bulk"></i> Pincode</label>
                                <input type="text" name="pincode" id="pincode" class="form-control"
                                    value="{{ old('pincode', $agent->pincode ?? '') }}" readonly
                                    style="background:#f8fafc;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Full Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Enter complete address">{{ old('address', $agent->address ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Vehicle Information --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-truck"></i></div>
                            <div>
                                <h3 class="section-title">Vehicle Information</h3>
                                <p class="section-subtitle">Update your vehicle details</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-motorcycle"></i> Vehicle Type</label>
                                <select name="vehicle_type" class="form-control">
                                    <option value="">Select Vehicle</option>
                                    <option value="bike"
                                        {{ old('vehicle_type', $agent->vehicle_type ?? '') == 'bike' ? 'selected' : '' }}>
                                        🏍️ Bike</option>
                                    <option value="scooter"
                                        {{ old('vehicle_type', $agent->vehicle_type ?? '') == 'scooter' ? 'selected' : '' }}>
                                        🛵 Scooter</option>
                                    <option value="van"
                                        {{ old('vehicle_type', $agent->vehicle_type ?? '') == 'van' ? 'selected' : '' }}>🚐
                                        Van</option>
                                    <option value="cycle"
                                        {{ old('vehicle_type', $agent->vehicle_type ?? '') == 'cycle' ? 'selected' : '' }}>
                                        🚲 Cycle</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-hashtag"></i> Vehicle Number</label>
                                <input type="text" name="vehicle_number" class="form-control"
                                    value="{{ old('vehicle_number', $agent->vehicle_number ?? '') }}"
                                    placeholder="e.g., GJ01AB1234">
                            </div>
                        </div>
                    </div>

                    {{-- Change Password Section --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-lock"></i></div>
                            <div>
                                <h3 class="section-title">Change Password</h3>
                                <p class="section-subtitle">Update your password (leave blank to keep current)</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-key"></i> Current Password</label>
                                <input type="password" name="current_password" class="form-control"
                                    placeholder="Enter current password">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-lock"></i> New Password</label>
                                <input type="password" name="new_password" class="form-control"
                                    placeholder="Enter new password">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-check-circle"></i> Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control"
                                    placeholder="Confirm new password">
                            </div>
                        </div>
                        <div class="input-hint" style="margin-top: 0.5rem; font-size: 0.8rem; color: #64748b;">
                            <i class="fas fa-info-circle"></i> Password must be at least 6 characters
                        </div>
                    </div>

                    {{-- Emergency Contact --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-ambulance"></i></div>
                            <div>
                                <h3 class="section-title">Emergency Contact</h3>
                                <p class="section-subtitle">For safety purposes</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user"></i> Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control"
                                    value="{{ old('emergency_contact_name', $agent->emergency_contact_name ?? '') }}"
                                    placeholder="Name">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone"></i> Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control"
                                    value="{{ old('emergency_contact_phone', $agent->emergency_contact_phone ?? '') }}"
                                    placeholder="Phone number">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-tint"></i> Blood Group</label>
                                <select name="blood_group" class="form-control">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+"
                                        {{ old('blood_group', $agent->blood_group ?? '') == 'A+' ? 'selected' : '' }}>A+
                                    </option>
                                    <option value="A-"
                                        {{ old('blood_group', $agent->blood_group ?? '') == 'A-' ? 'selected' : '' }}>A-
                                    </option>
                                    <option value="B+"
                                        {{ old('blood_group', $agent->blood_group ?? '') == 'B+' ? 'selected' : '' }}>B+
                                    </option>
                                    <option value="B-"
                                        {{ old('blood_group', $agent->blood_group ?? '') == 'B-' ? 'selected' : '' }}>B-
                                    </option>
                                    <option value="AB+"
                                        {{ old('blood_group', $agent->blood_group ?? '') == 'AB+' ? 'selected' : '' }}>AB+
                                    </option>
                                    <option value="AB-"
                                        {{ old('blood_group', $agent->blood_group ?? '') == 'AB-' ? 'selected' : '' }}>AB-
                                    </option>
                                    <option value="O+"
                                        {{ old('blood_group', $agent->blood_group ?? '') == 'O+' ? 'selected' : '' }}>O+
                                    </option>
                                    <option value="O-"
                                        {{ old('blood_group', $agent->blood_group ?? '') == 'O-' ? 'selected' : '' }}>O-
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('agent.dashboard') }}" class="btn btn-secondary"><i class="fas fa-times"></i>
                            Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> Update
                            Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap"
        async defer></script>

    <script>
        let map;
        let marker;
        let geocoder;
        let autocompleteService;

        function initMap() {
            const defaultLat = 22.524768;
            const defaultLng = 72.955568;
            const existingLat = parseFloat(document.getElementById('latitude').value);
            const existingLng = parseFloat(document.getElementById('longitude').value);
            const centerLat = existingLat && existingLat !== 0 ? existingLat : defaultLat;
            const centerLng = existingLng && existingLng !== 0 ? existingLng : defaultLng;

            map = new google.maps.Map(document.getElementById('map-preview'), {
                center: {
                    lat: centerLat,
                    lng: centerLng
                },
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [{
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{
                        visibility: 'off'
                    }]
                }]
            });

            geocoder = new google.maps.Geocoder();
            autocompleteService = new google.maps.places.AutocompleteService();

            if (existingLat && existingLat !== 0) {
                marker = new google.maps.Marker({
                    position: {
                        lat: existingLat,
                        lng: existingLng
                    },
                    map: map,
                    title: 'Your Location',
                    draggable: true,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        scaledSize: new google.maps.Size(40, 40)
                    }
                });
                google.maps.event.addListener(marker, 'dragend', function() {
                    updateLocationFromLatLng(marker.getPosition().lat(), marker.getPosition().lng());
                });
            } else {
                map.addListener('click', function(e) {
                    if (marker) marker.setMap(null);
                    marker = new google.maps.Marker({
                        position: e.latLng,
                        map: map,
                        title: 'Your Location',
                        draggable: true,
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                            scaledSize: new google.maps.Size(40, 40)
                        }
                    });
                    updateLocationFromLatLng(e.latLng.lat(), e.latLng.lng());
                    google.maps.event.addListener(marker, 'dragend', function() {
                        updateLocationFromLatLng(marker.getPosition().lat(), marker.getPosition().lng());
                    });
                });
            }

            setupPlaceAutocomplete();
        }

        function setupPlaceAutocomplete() {
            const input = document.getElementById('location-search');
            let debounceTimer;

            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();
                if (query.length < 3) return;

                debounceTimer = setTimeout(() => {
                    autocompleteService.getPlacePredictions({
                        input: query,
                        types: ['address'],
                        componentRestrictions: {
                            country: 'IN'
                        }
                    }, (predictions, status) => {
                        if (status === google.maps.places.PlacesServiceStatus.OK && predictions &&
                            predictions.length) {
                            const place = predictions[0];
                            geocoder.geocode({
                                placeId: place.place_id
                            }, (results, status) => {
                                if (status === 'OK' && results[0]) {
                                    const location = results[0].geometry.location;
                                    if (marker) marker.setMap(null);
                                    marker = new google.maps.Marker({
                                        position: location,
                                        map: map,
                                        title: 'Your Location',
                                        draggable: true,
                                        icon: {
                                            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                                            scaledSize: new google.maps.Size(40, 40)
                                        }
                                    });
                                    map.setCenter(location);
                                    map.setZoom(15);
                                    updateLocationFromLatLng(location.lat(), location
                                .lng());
                                    document.getElementById('location-search').value =
                                        results[0].formatted_address;
                                }
                            });
                        }
                    });
                }, 300);
            });
        }

        document.getElementById('detect-location').addEventListener('click', function() {
            if (!navigator.geolocation) {
                showToast('Geolocation not supported', 'error');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<div class="spinner" style="width:20px;height:20px;"></div> Detecting...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    if (marker) marker.setMap(null);
                    marker = new google.maps.Marker({
                        position: {
                            lat: lat,
                            lng: lng
                        },
                        map: map,
                        title: 'Your Location',
                        draggable: true,
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                            scaledSize: new google.maps.Size(40, 40)
                        }
                    });
                    map.setCenter({
                        lat: lat,
                        lng: lng
                    });
                    map.setZoom(15);
                    updateLocationFromLatLng(lat, lng);

                    document.getElementById('detect-location').disabled = false;
                    document.getElementById('detect-location').innerHTML =
                        '<i class="fas fa-location-dot"></i> Use My Current Location';
                },
                function(error) {
                    showToast('Could not detect location: ' + error.message, 'error');
                    document.getElementById('detect-location').disabled = false;
                    document.getElementById('detect-location').innerHTML =
                        '<i class="fas fa-location-dot"></i> Use My Current Location';
                }
            );
        });

        function updateLocationFromLatLng(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            geocoder.geocode({
                location: {
                    lat: lat,
                    lng: lng
                }
            }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    document.getElementById('current_location').value = results[0].formatted_address;
                    document.getElementById('location-search').value = results[0].formatted_address;

                    const components = {};
                    results[0].address_components.forEach(comp => {
                        const type = comp.types[0];
                        components[type] = comp;
                    });

                    if (components.locality) document.getElementById('city').value = components.locality.long_name;
                    if (components.administrative_area_level_1) document.getElementById('state').value = components
                        .administrative_area_level_1.long_name;
                    if (components.postal_code) document.getElementById('pincode').value = components.postal_code
                        .long_name;
                }
            });
        }

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `<div><span>${type === 'success' ? '✅' : '❌'}</span> ${message}</div>`;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const newPassword = document.querySelector('[name="new_password"]').value;
            const confirmPassword = document.querySelector('[name="new_password_confirmation"]').value;

            if (newPassword && newPassword !== confirmPassword) {
                e.preventDefault();
                showToast('New password and confirmation do not match', 'error');
                return;
            }

            if (newPassword && newPassword.length < 6) {
                e.preventDefault();
                showToast('Password must be at least 6 characters', 'error');
                return;
            }

            document.getElementById('loadingOverlay').style.display = 'flex';
        });

        window.initMap = initMap;
    </script>
@endsection
