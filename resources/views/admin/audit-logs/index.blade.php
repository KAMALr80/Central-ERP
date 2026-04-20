@extends('layouts.app')

@section('title', 'System Audit Logs')

@section('content')
<style>
    :root {
        --audit-bg: #f8fafc;
        --audit-card-border: #e2e8f0;
        --audit-primary: #4f46e5;
        --audit-secondary: #64748b;
    }

    .audit-page {
        background-color: var(--audit-bg);
        min-height: 100vh;
        padding: 2rem;
    }

    .header-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 1.25rem;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .header-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .stat-mini-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1rem;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s;
    }

    .stat-mini-card:hover { transform: translateY(-5px); }

    .stat-mini-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Filters */
    .filter-section {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--audit-card-border);
    }

    /* DataTables Overrides */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 0.75rem;
        margin-bottom: 1rem;
    }

    table.dataTable {
        border-radius: 1rem;
        overflow: hidden;
        border: none !important;
        margin-top: 1rem !important;
    }

    table.dataTable thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        padding: 1rem 1.5rem !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }

    table.dataTable tbody td {
        padding: 1.25rem 1.5rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .event-pill {
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .pill-created { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .pill-updated { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .pill-deleted { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

    .user-badge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-initial {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        background: var(--audit-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }

    .btn-view-log {
        background: #f1f5f9;
        color: #475569;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.6rem;
        font-weight: 600;
        font-size: 0.75rem;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-view-log:hover {
        background: var(--audit-primary);
        color: white;
    }
</style>

<div class="audit-page">
    <!-- Header -->
    <div class="header-card">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold mb-1">🛡️ System Audit Logs</h1>
                <p class="opacity-75 mb-0">Monitor and track every change across your ERP infrastructure.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.audit-logs.export') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                    <i class="fas fa-file-export me-2"></i> Export CSV
                </a>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-mini-card">
                <div class="stat-mini-icon" style="background: rgba(99, 102, 241, 0.2); color: #818cf8;">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <div class="small opacity-75">Total Events</div>
                    <div class="h4 fw-bold mb-0">{{ $totalLogs }}</div>
                </div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-icon" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div>
                    <div class="small opacity-75">Created</div>
                    <div class="h4 fw-bold mb-0">{{ $stats['created'] ?? 0 }}</div>
                </div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-icon" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <div class="small opacity-75">Updated</div>
                    <div class="h4 fw-bold mb-0">{{ $stats['updated'] ?? 0 }}</div>
                </div>
            </div>
            <div class="stat-mini-card">
                <div class="stat-mini-icon" style="background: rgba(239, 68, 68, 0.2); color: #f87171;">
                    <i class="fas fa-trash"></i>
                </div>
                <div>
                    <div class="small opacity-75">Deleted</div>
                    <div class="h4 fw-bold mb-0">{{ $stats['deleted'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary">USER ACCOUNT</label>
                <select name="user_id" class="form-select border-0 bg-light">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-secondary">EVENT TYPE</label>
                <select name="event" class="form-select border-0 bg-light">
                    <option value="">All Events</option>
                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-secondary">MODEL</label>
                <input type="text" name="model" class="form-control border-0 bg-light" placeholder="e.g. Sale" value="{{ request('model') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-secondary">FROM DATE</label>
                <input type="date" name="date_from" class="form-control border-0 bg-light" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 fw-bold">
                    <i class="fas fa-search me-2"></i> Filter
                </button>
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-light border fw-bold text-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive p-4">
                <table id="auditDataTable" class="table audit-table">
                    <thead>
                        <tr>
                            <th>TIMESTAMP</th>
                            <th>USER</th>
                            <th>ACTION</th>
                            <th>ENTITY / MODEL</th>
                            <th>RECORD ID</th>
                            <th>IP ADDRESS</th>
                            <th class="text-end">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="small text-secondary">{{ $log->created_at->format('h:i:s A') }}</div>
                                </td>
                                <td>
                                    <div class="user-badge">
                                        <div class="user-initial">
                                            {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $log->user->name ?? 'System' }}</div>
                                            <div class="small text-secondary">{{ $log->user->role ?? 'Process' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="event-pill pill-{{ $log->event }}">
                                        @if($log->event == 'created') <i class="fas fa-plus-circle"></i>
                                        @elseif($log->event == 'updated') <i class="fas fa-sync-alt"></i>
                                        @else <i class="fas fa-trash-alt"></i>
                                        @endif
                                        {{ $log->event }}
                                    </span>
                                </td>
                                <td>
                                    <code class="text-primary fw-bold" style="background: #eff6ff; padding: 4px 8px; border-radius: 6px;">
                                        {{ class_basename($log->auditable_type) }}
                                    </code>
                                </td>
                                <td>
                                    <span class="fw-bold text-secondary">#{{ $log->auditable_id }}</span>
                                </td>
                                <td>
                                    <small class="text-secondary font-monospace">{{ $log->ip_address }}</small>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="btn-view-log">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- DataTables & Styles --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#auditDataTable').DataTable({
            "order": [[ 0, "desc" ]],
            "pageLength": 25,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Instant Search logs...",
                "paginate": {
                    "previous": "<i class='fas fa-chevron-left'></i>",
                    "next": "<i class='fas fa-chevron-right'></i>"
                }
            },
            "dom": '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
        });
    });
</script>
@endpush
@endsection
