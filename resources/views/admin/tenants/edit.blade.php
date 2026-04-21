@extends('layouts.app')

@section('page-title', 'Edit Company')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold text-dark mb-0">Edit Company Details</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.tenants.update', $tenant->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-bold small">COMPANY ID (Fixed)</label>
                            <input type="text" class="form-control bg-light px-3 py-2 border-2" value="{{ $tenant->id }}" disabled>
                            <div class="form-text x-small">The environment ID cannot be changed after provisioning.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">DISPLAY NAME</label>
                            <input type="text" name="name" class="form-control px-3 py-2 border-2" value="{{ old('name', $tenant->name) }}" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                                <i class="fas fa-save me-2"></i> Update Details
                            </button>
                            <a href="{{ route('admin.tenants.index') }}" class="btn btn-light fw-bold py-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
