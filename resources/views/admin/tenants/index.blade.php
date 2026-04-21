@extends('layouts.app')

@section('page-title', 'Company Management')

@section('content')
<div style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); min-height: 100vh; padding: 2rem; position: relative; overflow: hidden;">
    
    <!-- Background Water Waves Decoration -->
    <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 200px; opacity: 0.5; pointer-events: none; z-index: 1;">
        <svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg" style="position: absolute; bottom: 0; width: 100%; height: 100%;">
            <path fill="#6366f1" fill-opacity="0.1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            <path fill="#3b82f6" fill-opacity="0.1" d="M0,64L48,85.3C96,107,192,149,288,149.3C384,149,480,107,576,112C672,117,768,171,864,186.7C960,203,1056,181,1152,149.3C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>

    <!-- Header Section -->
    <div style="position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; color: #0369a1; margin: 0; letter-spacing: -1px; display: flex; align-items: center; gap: 15px;">
                <div style="background: white; width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                    <i class="fas fa-ship" style="color: #0284c7;"></i>
                </div>
                Company Fleet <span style="color: #6366f1;">Manager</span>
            </h1>
            <p style="color: #64748b; margin-top: 0.5rem; font-weight: 600; font-size: 0.95rem;">Manage and provision isolated business environments across the infrastructure.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('central.admin.tenants.create') }}" style="background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%); color: white; padding: 0.75rem 1.75rem; border-radius: 16px; font-weight: 800; text-decoration: none; box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4); display: flex; align-items: center; gap: 10px; transition: all 0.3s transform;">
                <i class="fas fa-anchor"></i> Launch New Company
            </a>
        </div>
    </div>

    <!-- Glassmorphism Table Container -->
    <div style="position: relative; z-index: 2; background: rgba(255, 255, 255, 0.4); backdrop-filter: blur(20px); border-radius: 30px; border: 1px solid rgba(255, 255, 255, 0.6); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); overflow: hidden;">
        <div style="padding: 2rem;">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="tenantsTable" style="margin-bottom: 0;">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(3, 105, 161, 0.1);">
                            <th style="padding: 1.25rem; font-size: 0.75rem; font-weight: 800; color: #0369a1; text-transform: uppercase; letter-spacing: 1px;">Company ID</th>
                            <th style="padding: 1.25rem; font-size: 0.75rem; font-weight: 800; color: #0369a1; text-transform: uppercase; letter-spacing: 1px;">Business Entity</th>
                            <th style="padding: 1.25rem; font-size: 0.75rem; font-weight: 800; color: #0369a1; text-transform: uppercase; letter-spacing: 1px;">Digital Anchor (URL)</th>
                            <th style="padding: 1.25rem; font-size: 0.75rem; font-weight: 800; color: #0369a1; text-transform: uppercase; letter-spacing: 1px;">Status</th>
                            <th style="padding: 1.25rem; font-size: 0.75rem; font-weight: 800; color: #0369a1; text-transform: uppercase; letter-spacing: 1px;">Launch Date</th>
                            <th style="padding: 1.25rem; text-align: right; font-size: 0.75rem; font-weight: 800; color: #0369a1; text-transform: uppercase; letter-spacing: 1px;">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.03); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1.5rem 1.25rem;">
                                    <span style="background: rgba(3, 105, 161, 0.1); color: #0369a1; padding: 6px 12px; border-radius: 10px; font-weight: 800; font-size: 0.8rem; border: 1px solid rgba(3, 105, 161, 0.2);">
                                        {{ strtoupper($tenant->id) }}
                                    </span>
                                </td>
                                <td style="padding: 1.5rem 1.25rem;">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);">
                                            {{ strtoupper(substr($tenant->name ?? $tenant->id, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 800; color: #1e293b; font-size: 1rem;">{{ $tenant->name ?? 'Untitled Entity' }}</div>
                                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">System ID: {{ $tenant->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1.5rem 1.25rem;">
                                    @php
                                        $url = url('/' . $tenant->id);
                                    @endphp
                                    <a href="{{ $url }}" target="_blank" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px; background: white; padding: 6px 14px; border-radius: 10px; border: 1px solid #e2e8f0; color: #0ea5e9; font-weight: 700; font-size: 0.85rem; box-shadow: 0 2px 5px rgba(0,0,0,0.02); transition: all 0.2s;">
                                        <i class="fas fa-external-link-alt" style="font-size: 0.7rem;"></i>
                                        /{{ $tenant->id }}
                                    </a>
                                </td>
                                <td style="padding: 1.5rem 1.25rem;">
                                    <span style="background: #dcfce7; color: #166534; padding: 6px 16px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
                                        <span style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%;"></span>
                                        ACTIVE
                                    </span>
                                </td>
                                <td style="padding: 1.5rem 1.25rem;">
                                    <div style="font-weight: 700; color: #475569; font-size: 0.9rem;">{{ $tenant->created_at->format('d M, Y') }}</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">{{ $tenant->created_at->diffForHumans() }}</div>
                                </td>
                                <td style="padding: 1.5rem 1.25rem; text-align: right;">
                                    <div class="dropdown">
                                        <button class="btn" style="background: white; border: 1px solid #e2e8f0; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #64748b; transition: all 0.2s;" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-xl border-0 p-2" style="border-radius: 15px; min-width: 200px;">
                                            <li><a class="dropdown-item py-2 px-3" style="border-radius: 10px; font-weight: 600; color: #475569;" href="{{ route('central.admin.tenants.edit', $tenant->id) }}"><i class="fas fa-edit me-2 text-info"></i> Edit Details</a></li>
                                            <li>
                                                <form action="{{ route('central.admin.tenants.sync', $tenant->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-2 px-3" style="border-radius: 10px; font-weight: 600; color: #475569;"><i class="fas fa-sync me-2 text-primary"></i> Sync Data</button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <form action="{{ route('central.admin.tenants.destroy', $tenant->id) }}" method="POST" onsubmit="return confirm('WARNING: Permanent data deletion. Continue?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 px-3 text-danger" style="border-radius: 10px; font-weight: 700;">
                                                        <i class="fas fa-trash-alt me-2"></i> Terminate
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .dropdown-item:hover { background: #f8fafc !important; color: #0ea5e9 !important; }
    .dataTables_wrapper .dataTables_length select {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 5px 10px;
    }
    .dataTables_wrapper .dataTables_filter input {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 8px 15px;
        font-weight: 600;
        min-width: 300px;
    }
    .page-link { border-radius: 10px !important; margin: 0 3px; font-weight: 700; border: none; background: rgba(255,255,255,0.5); color: #0369a1; }
    .page-item.active .page-link { background: #0ea5e9 !important; }
    .table-responsive { overflow: visible !important; }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#tenantsTable').DataTable({
            responsive: true,
            pageLength: 10,
            dom: '<"d-flex justify-content-between align-items-center mb-4"lf>rt<"d-flex justify-content-between align-items-center mt-4"ip>',
            language: {
                search: "",
                searchPlaceholder: "Find a company...",
                lengthMenu: "Show _MENU_"
            }
        });
    });
</script>
@endpush
@endsection
