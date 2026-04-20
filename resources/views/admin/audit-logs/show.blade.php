@extends('layouts.app')

@section('title', 'Log Details')

@section('content')
<style>
    :root {
        --audit-bg: #f8fafc;
        --audit-primary: #4f46e5;
        --audit-dark: #0f172a;
        --diff-old-bg: #fff1f2;
        --diff-old-text: #991b1b;
        --diff-new-bg: #f0fdf4;
        --diff-new-text: #166534;
    }

    .detail-page {
        background-color: var(--audit-bg);
        min-height: 100vh;
        padding: 2.5rem;
    }

    .back-nav {
        margin-bottom: 2rem;
    }

    .btn-back {
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .btn-back:hover { color: var(--audit-primary); }

    /* Metadata Header */
    .log-header-card {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .log-info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .log-info-label {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .log-info-value {
        font-weight: 700;
        color: var(--audit-dark);
        font-size: 1.1rem;
    }

    .event-tag {
        padding: 0.5rem 1.5rem;
        border-radius: 2rem;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    /* Comparison Table Container */
    .comparison-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .card-header-dark {
        background: var(--audit-dark);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* DataTables Customization for Diff */
    .diff-table thead th {
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1rem 1.5rem !important;
        border: none !important;
    }

    .diff-table tbody td {
        padding: 1.5rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: top !important;
    }

    .field-key {
        font-weight: 800;
        color: #334155;
        font-size: 0.9rem;
    }

    .diff-value-box {
        padding: 1rem;
        border-radius: 0.75rem;
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        font-size: 0.8rem;
        line-height: 1.6;
        white-space: pre-wrap;
        word-break: break-all;
        position: relative;
    }

    .old-value { background: var(--diff-old-bg); color: var(--diff-old-text); border: 1px solid #fecaca; }
    .new-value { background: var(--diff-new-bg); color: var(--diff-new-text); border: 1px solid #bbf7d0; }

    .diff-label {
        position: absolute;
        top: -0.6rem;
        right: 1rem;
        padding: 0.1rem 0.6rem;
        border-radius: 4px;
        font-size: 0.6rem;
        font-weight: 900;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Request Context */
    .context-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 2.5rem;
    }

    .context-item {
        margin-bottom: 1rem;
    }

    .context-item label {
        display: block;
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .context-item code {
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        display: block;
        border: 1px solid #e2e8f0;
        color: var(--audit-primary);
    }
</style>

<div class="detail-page">
    <div class="back-nav">
        <a href="{{ route('admin.audit-logs.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Return to Audit Dashboard
        </a>
    </div>

    <!-- Top Metadata Card -->
    <div class="log-header-card">
        <div class="log-info-item">
            <span class="log-info-label">Action Performed By</span>
            <span class="log-info-value">
                @if($log->user)
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-initial" style="width: 2rem; height: 2rem; font-size: 0.8rem; background: #6366f1; color: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800;">
                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                        </div>
                        {{ $log->user->name }}
                    </div>
                @else
                    <span class="text-secondary">System Automator</span>
                @endif
            </span>
        </div>
        <div class="log-info-item">
            <span class="log-info-label">Model / Entity</span>
            <span class="log-info-value">
                <span class="badge bg-light text-primary border">{{ class_basename($log->auditable_type) }}</span>
                <span class="text-secondary ms-1">#{{ $log->auditable_id }}</span>
            </span>
        </div>
        <div class="log-info-item">
            <span class="log-info-label">IP Address</span>
            <span class="log-info-value font-monospace text-secondary" style="font-size: 0.9rem;">{{ $log->ip_address }}</span>
        </div>
        <div class="log-info-item">
            <span class="log-info-label">Timestamp</span>
            <span class="log-info-value text-secondary" style="font-size: 0.9rem;">{{ $log->created_at->format('d M Y, h:i:s A') }}</span>
        </div>
        <div>
            <span class="event-tag 
                @if($log->event == 'created') bg-success 
                @elseif($log->event == 'updated') bg-primary 
                @else bg-danger @endif text-white shadow-sm">
                <i class="fas fa-circle-notch fa-spin me-2" style="font-size: 0.6rem;"></i>
                {{ $log->event }}
            </span>
        </div>
    </div>

    <!-- Data Comparison Table -->
    <div class="comparison-card">
        <div class="card-header-dark">
            <h5 class="mb-0 fw-bold"><i class="fas fa-exchange-alt me-2"></i> Field-Level Data Comparison</h5>
            <span class="small opacity-75">Log Entry #{{ $log->id }}</span>
        </div>
        <div class="p-4">
            <table id="diffDataTable" class="table diff-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">ATTRIBUTE</th>
                        <th style="width: 40%;">PREVIOUS STATE (OLD)</th>
                        <th style="width: 40%;">CURRENT STATE (NEW)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $keys = array_unique(array_merge(
                            array_keys($log->old_values ?? []),
                            array_keys($log->new_values ?? [])
                        ));
                        sort($keys);
                    @endphp

                    @forelse($keys as $key)
                        @php
                            $oldVal = $log->old_values[$key] ?? null;
                            $newVal = $log->new_values[$key] ?? null;
                            $isChanged = ($oldVal !== $newVal);
                        @endphp
                        <tr>
                            <td>
                                <div class="field-key">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                                <code class="small text-secondary">{{ $key }}</code>
                                @if($isChanged)
                                    <div class="mt-1"><span class="badge bg-warning text-dark" style="font-size: 0.6rem; font-weight: 800;">MODIFIED</span></div>
                                @endif
                            </td>
                            <td>
                                @if($oldVal !== null)
                                    <div class="diff-value-box old-value">
                                        <span class="diff-label bg-danger text-white">OLD</span>
                                        {{ is_array($oldVal) ? json_encode($oldVal, JSON_PRETTY_PRINT) : $oldVal }}
                                    </div>
                                @else
                                    <div class="text-center py-3 opacity-25 italic small">— No Previous Data —</div>
                                @endif
                            </td>
                            <td>
                                @if($newVal !== null)
                                    <div class="diff-value-box new-value">
                                        <span class="diff-label bg-success text-white">NEW</span>
                                        {{ is_array($newVal) ? json_encode($newVal, JSON_PRETTY_PRINT) : $newVal }}
                                    </div>
                                @else
                                    <div class="text-center py-3 opacity-25 italic small">— No New Data —</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-secondary opacity-50">
                                <i class="fas fa-ghost fa-3x mb-3"></i>
                                <p>No data changes were captured for this specific event.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Technical Context Card -->
    <div class="context-card">
        <h6 class="fw-bold text-dark mb-4"><i class="fas fa-code-branch me-2 text-primary"></i> Request Execution Context</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="context-item">
                    <label>ORIGINATING URL</label>
                    <code>{{ $log->url }}</code>
                </div>
            </div>
            <div class="col-md-6">
                <div class="context-item">
                    <label>BROWSER / USER AGENT</label>
                    <div class="small text-secondary bg-white p-2 border rounded" style="word-break: break-all;">
                        {{ $log->user_agent }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#diffDataTable').DataTable({
            "paging": false,
            "searching": true,
            "info": false,
            "order": [], // Disable initial sort
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search specific fields..."
            },
            "dom": '<"d-flex justify-content-between align-items-center mb-3"f>rt'
        });
    });
</script>
@endpush
@endsection
