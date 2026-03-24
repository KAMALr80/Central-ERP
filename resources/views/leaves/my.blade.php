@extends('layouts.app')

@section('page-title', 'My Leaves')

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #007bff;
        --primary-dark: #0056b3;
        --success: #28a745;
        --success-dark: #218838;
        --danger: #dc3545;
        --danger-dark: #c82333;
        --warning: #ffc107;
        --warning-dark: #e0a800;
        --info: #17a2b8;
        --text-main: #333;
        --text-muted: #666;
        --border: #ddd;
        --bg-light: #f9f9f9;
        --bg-white: #ffffff;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 8px rgba(0,0,0,0.1);
        --shadow-lg: 0 8px 16px rgba(0,0,0,0.1);
        --radius-sm: 4px;
        --radius-md: 6px;
        --radius-lg: 8px;
        --radius-xl: 12px;
        --font-sans: Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: var(--font-sans);
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .leaves-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: clamp(16px, 3vw, 30px);
        width: 100%;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= HEADER CARD ================= */
    .header-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 4vw, 25px);
        margin-bottom: 30px;
        box-shadow: var(--shadow-lg);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .header-icon {
        width: clamp(50px, 8vw, 60px);
        height: clamp(50px, 8vw, 60px);
        background: rgba(255, 255, 255, 0.2);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(24px, 4vw, 28px);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .header-title h1 {
        margin: 0;
        font-size: clamp(22px, 5vw, 26px);
        font-weight: 700;
        color: white;
        word-break: break-word;
    }

    .header-title p {
        margin: 5px 0 0 0;
        font-size: clamp(13px, 3vw, 14px);
        opacity: 0.9;
        word-break: break-word;
    }

    .leave-stats {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        font-size: clamp(13px, 3vw, 14px);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        white-space: nowrap;
    }

    /* ================= MAIN CARD ================= */
    .main-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 4vw, 25px);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        width: 100%;
    }

    /* ================= FORM SECTION ================= */
    .form-section {
        background: var(--bg-light);
        border-radius: var(--radius-md);
        padding: clamp(20px, 4vw, 25px);
        margin-bottom: 30px;
        border: 1px solid var(--border);
    }

    .section-title {
        color: #444;
        margin-bottom: 20px;
        font-size: clamp(18px, 4vw, 20px);
        font-weight: 700;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .leave-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        margin-bottom: 10px;
    }

    .form-group {
        flex: 1;
        min-width: 180px;
    }

    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
        transition: all 0.3s ease;
        outline: none;
        background: white;
    }

    .form-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .form-select {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
        transition: all 0.3s ease;
        outline: none;
        background: white;
        cursor: pointer;
    }

    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .btn-apply {
        padding: 12px 25px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
        white-space: nowrap;
        min-width: 100px;
    }

    .btn-apply:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .btn-apply:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    /* ================= ALERTS ================= */
    .alert {
        padding: 12px 20px;
        border-radius: var(--radius-md);
        margin-bottom: 20px;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* ================= TABLE SECTION ================= */
    .table-section {
        margin-top: 30px;
    }

    .table-title {
        color: #444;
        margin-bottom: 15px;
        font-size: clamp(18px, 4vw, 20px);
        font-weight: 700;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
    }

    .leaves-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .leaves-table thead tr {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
    }

    .leaves-table th {
        padding: 12px 10px;
        font-size: 14px;
        font-weight: 600;
        text-align: left;
        border: 1px solid var(--primary-dark);
        white-space: nowrap;
    }

    .leaves-table td {
        padding: 12px 10px;
        border: 1px solid var(--border);
        font-size: 14px;
        vertical-align: middle;
        transition: background 0.3s ease;
    }

    .leaves-table tbody tr:hover td {
        background: #f1f9ff;
    }

    /* ================= STATUS BADGES ================= */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        white-space: nowrap;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* ================= EMPTY STATE ================= */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .empty-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .empty-text {
        color: #999;
        font-size: 14px;
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */

    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .container {
            max-width: 1200px;
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .container {
            max-width: 100%;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .leaves-page {
            padding: 15px;
        }

        .form-group {
            min-width: 160px;
        }

        .leave-form {
            gap: 12px;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .leaves-page {
            padding: 12px;
        }

        .header-card {
            flex-direction: column;
            align-items: flex-start;
            text-align: center;
        }

        .header-left {
            width: 100%;
            justify-content: center;
        }

        .leave-stats {
            width: 100%;
            text-align: center;
        }

        .leave-form {
            flex-direction: column;
        }

        .form-group {
            width: 100%;
        }

        .btn-apply {
            width: 100%;
        }

        .leaves-table {
            min-width: 500px;
        }

        .leaves-table th,
        .leaves-table td {
            padding: 10px 8px;
            font-size: 13px;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .leaves-page {
            padding: 10px;
        }

        .header-icon {
            width: 45px;
            height: 45px;
            font-size: 22px;
        }

        .header-title h1 {
            font-size: 20px;
        }

        .header-title p {
            font-size: 12px;
        }

        .section-title {
            font-size: 18px;
        }

        .form-input,
        .form-select,
        .btn-apply {
            padding: 10px 12px;
            font-size: 13px;
        }

        .leaves-table {
            min-width: 400px;
        }

        .leaves-table th,
        .leaves-table td {
            padding: 8px 6px;
            font-size: 12px;
        }

        .status-badge {
            padding: 3px 8px;
            font-size: 11px;
        }

        .empty-icon {
            font-size: 40px;
        }

        .empty-title {
            font-size: 16px;
        }

        .empty-text {
            font-size: 13px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .leaves-page {
            padding: 8px;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }

        .header-title h1 {
            font-size: 18px;
        }

        .section-title {
            font-size: 16px;
        }

        .form-input,
        .form-select,
        .btn-apply {
            padding: 8px 10px;
            font-size: 12px;
        }

        .leaves-table {
            min-width: 350px;
        }

        .leaves-table th,
        .leaves-table td {
            padding: 6px 4px;
            font-size: 11px;
        }

        .status-badge {
            padding: 2px 6px;
            font-size: 10px;
        }

        .alert {
            padding: 8px 12px;
            font-size: 12px;
        }
    }

    /* Print Styles */
    @media print {
        .btn-apply,
        .form-group,
        .header-card {
            display: none !important;
        }

        .leaves-table {
            border: 1px solid #000;
        }

        .leaves-table th {
            background: #f0f0f0 !important;
            color: #000 !important;
        }

        .status-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="leaves-page">
    <div class="container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="header-left">
                <div class="header-icon">📝</div>
                <div class="header-title">
                    <h1>My Leave Requests</h1>
                    <p>Apply and track your leave applications</p>
                </div>
            </div>
            <div class="leave-stats">
                Total Leaves: {{ $leaves->count() }}
            </div>
        </div>

        <!-- Main Card -->
        <div class="main-card">
            <!-- Apply Leave Form -->
            <div class="form-section">
                <h3 class="section-title">
                    <span>📋</span> Apply New Leave
                </h3>

                @if (session('success'))
                    <div class="alert alert-success">
                        <span>✅</span> {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        <span>❌</span> {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <span>⚠️</span> Please fix the errors below
                    </div>
                @endif

                <form method="POST" action="{{ route('leaves.apply') }}" class="leave-form" id="leaveForm">
                    @csrf

                    <div class="form-group">
                        <input type="date" name="from_date" required class="form-input @error('from_date') is-invalid @enderror"
                               value="{{ old('from_date') }}" min="{{ date('Y-m-d') }}">
                        @error('from_date')
                            <small style="color: var(--danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="date" name="to_date" required class="form-input @error('to_date') is-invalid @enderror"
                               value="{{ old('to_date') }}" min="{{ date('Y-m-d') }}">
                        @error('to_date')
                            <small style="color: var(--danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">-- Select Leave Type --</option>
                            <option value="Paid" {{ old('type') == 'Paid' ? 'selected' : '' }}>Paid Leave</option>
                            <option value="Unpaid" {{ old('type') == 'Unpaid' ? 'selected' : '' }}>Unpaid Leave</option>
                            <option value="Sick" {{ old('type') == 'Sick' ? 'selected' : '' }}>Sick Leave</option>
                            <option value="Half Day" {{ old('type') == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                        </select>
                        @error('type')
                            <small style="color: var(--danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="text" name="reason" placeholder="Reason (Optional)"
                               class="form-input @error('reason') is-invalid @enderror"
                               value="{{ old('reason') }}">
                        @error('reason')
                            <small style="color: var(--danger);">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn-apply" onclick="this.disabled=true; this.innerText='Applying...'; this.form.submit();">
                        Apply
                    </button>
                </form>
            </div>

            <!-- My Leaves Table -->
            <div class="table-section">
                <h3 class="table-title">
                    <span>📜</span> My Leave History
                </h3>

                <div class="table-responsive">
                    <table class="leaves-table">
                        <thead>
                            <tr>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $__col = $leaves; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $leave)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($leave->to_date)->format('d M, Y') }}</td>
                                    <td>
                                        <span style="font-weight: 500;">{{ $leave->type }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge
                                            @if($leave->status == 'Pending') status-pending
                                            @elseif($leave->status == 'Approved') status-approved
                                            @elseif($leave->status == 'Rejected') status-rejected
                                            @endif">
                                            @if($leave->status == 'Pending')
                                                ⏳
                                            @elseif($leave->status == 'Approved')
                                                ✅
                                            @elseif($leave->status == 'Rejected')
                                                ❌
                                            @endif
                                            {{ $leave->status }}
                                        </span>
                                    </td>
                                    <td>{{ $leave->reason ?? '-' }}</td>
                                </tr>
                            @endforeach
@else
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <div class="empty-icon">📭</div>
                                            <div class="empty-title">No leave applications found</div>
                                            <div class="empty-text">Apply for a leave using the form above</div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Client-side validation for date range
    document.getElementById('leaveForm')?.addEventListener('submit', function(e) {
        const fromDate = new Date(this.from_date.value);
        const toDate = new Date(this.to_date.value);

        if (toDate < fromDate) {
            e.preventDefault();
            alert('❌ To date cannot be earlier than from date');
        }
    });

    // Auto-format reason field
    const reasonField = document.querySelector('input[name="reason"]');
    if (reasonField) {
        reasonField.addEventListener('input', function() {
            // Capitalize first letter of each sentence
            this.value = this.value.replace(/(^\w|\.\s+\w)/g, letter => letter.toUpperCase());
        });
    }
</script>
@endsection
