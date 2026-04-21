@extends('layouts.app')

@section('page-title', 'Register New Company')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="mb-4">
                <a href="{{ route('admin.tenants.index') }}" class="text-decoration-none text-secondary small d-flex align-items-center">
                    <i class="fas fa-arrow-left me-2"></i> Back to Company Management
                </a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="row g-0">
                    {{-- Left Side: Info/Decoration --}}
                    <div class="col-lg-4 bg-primary p-5 d-none d-lg-flex flex-column justify-content-between text-white">
                        <div>
                            <h3 class="fw-bold mb-3">Enterprise Provisioning</h3>
                            <p class="opacity-75">Registering a new company will automatically:</p>
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex align-items-top">
                                    <i class="fas fa-database mt-1 me-3 opacity-50"></i>
                                    <span>Create an isolated SQL Database</span>
                                </li>
                                <li class="mb-3 d-flex align-items-top">
                                    <i class="fas fa-network-wired mt-1 me-3 opacity-50"></i>
                                    <span>Configure sub-domain mapping</span>
                                </li>
                                <li class="mb-3 d-flex align-items-top">
                                    <i class="fas fa-shield-alt mt-1 me-3 opacity-50"></i>
                                    <span>Initialize secure workspace</span>
                                </li>
                            </ul>
                        </div>
                        <div class="bg-white bg-opacity-10 p-3 rounded-3 small">
                            <i class="fas fa-info-circle me-2"></i> System version 1.2.0 • Central Auth Engine
                        </div>
                    </div>

                    {{-- Right Side: Form --}}
                    <div class="col-lg-8 p-5 bg-white">
                        <div class="mb-5">
                            <h2 class="fw-bold text-dark mb-1">Onboard New Business</h2>
                            <p class="text-secondary">Complete the details below to initialize the company's ERP environment.</p>
                        </div>

                        <form action="{{ route('admin.tenants.store') }}" method="POST">
                            @csrf
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">BUSINESS NAME</label>
                                    <input type="text" name="name" class="form-control form-control-lg border-2 rounded-3 @error('name') is-invalid @enderror" 
                                        placeholder="e.g. Acme Corporation" value="{{ old('name') }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold small">ENVIRONMENT ID</label>
                                    <input type="text" name="id" id="tenant_id" class="form-control form-control-lg border-2 rounded-3 @error('id') is-invalid @enderror" 
                                        placeholder="e.g. acme-corp" value="{{ old('id') }}" required>
                                    <div class="form-text x-small">Used for database and folder names.</div>
                                    @error('id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <label class="form-label text-dark fw-bold small">ACCESS DOMAIN</label>
                                    <div class="input-group input-group-lg">
                                        <input type="text" name="domain" id="domain_input" class="form-control border-2 rounded-start-3 @error('domain') is-invalid @enderror" 
                                            placeholder="acme-corp" value="{{ old('domain') }}" required>
                                            @php
                                                $host = request()->getHost();
                                                $suffix = in_array($host, ['127.0.0.1', 'localhost']) ? '.localhost' : '.' . $host;
                                            @endphp
                                            <span class="input-group-text bg-light border-2 text-secondary small fw-bold">{{ $suffix }}</span>
                                    </div>
                                    <div class="form-text x-small">The URL where the company will access their ERP.</div>
                                    @error('domain') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 mt-5">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3 shadow-sm shadow-primary">
                                        <i class="fas fa-rocket me-2"></i> Launch Environment
                                    </button>
                                    <div class="text-center mt-3">
                                        <p class="x-small text-secondary mb-0">Initial provisioning typically takes 2-5 seconds.</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.75rem; }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
    }
    .shadow-primary {
        box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.39) !important;
    }
    .bg-primary {
        background-color: var(--primary) !important;
    }
</style>

@push('scripts')
<script>
    // Simple auto-slug behavior for convenience
    $(document).ready(function() {
        $('input[name="name"]').on('keyup', function() {
            let val = $(this).val().toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            
            if (!$('#tenant_id').val()) {
                $('#tenant_id').val(val);
                $('#domain_input').val(val);
            }
        });
        
        $('#tenant_id').on('keyup', function() {
            $('#domain_input').val($(this).val());
        });
    });
</script>
@endpush
@endsection
