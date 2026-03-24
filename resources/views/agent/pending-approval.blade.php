@extends('layouts.app')

@section('title', 'Pending Approval')

@section('content')
    <div style="max-width: 600px; margin: 50px auto; text-align: center;">
        <div style="background: white; border-radius: 24px; padding: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div
                style="width: 80px; height: 80px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-clock" style="font-size: 40px; color: #f59e0b;"></i>
            </div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Account Pending Approval</h2>
            <p style="color: #6b7280; margin-bottom: 24px;">Your delivery agent account is waiting for admin approval. You
                will be notified once approved.</p>
            <div style="background: #f8fafc; border-radius: 12px; padding: 16px; text-align: left; margin-bottom: 24px;">
                <p><strong>Name:</strong> {{ $agent->name }}</p>
                <p><strong>Email:</strong> {{ $agent->email }}</p>
                <p><strong>Agent Code:</strong> {{ $agent->agent_code }}</p>
                <p><strong>Status:</strong> <span style="color: #f59e0b;">Pending Approval</span></p>
            </div>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn-logout"
                style="display: inline-block; padding: 12px 24px; background: #ef4444; color: white; border-radius: 30px; text-decoration: none;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
@endsection
