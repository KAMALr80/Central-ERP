@extends('layouts.app')

@section('content')
<div style="background: #f0f2f5; min-height: 100vh; padding: 2rem;">
    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 800; color: #1e293b; margin: 0; letter-spacing: -0.5px;">Super Admin <span style="color: #6366f1;">Dashboard</span></h1>
            <p style="color: #64748b; margin-top: 0.25rem; font-weight: 500;">System Intelligence & Central Infrastructure Overview</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <button style="background: white; border: 1px solid #e2e8f0; padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600; color: #475569; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-cloud-download-alt"></i> System Report
            </button>
            <a href="{{ route('admin.tenants.create') }}" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; padding: 0.6rem 1.5rem; border-radius: 12px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); display: flex; align-items: center; gap: 8px; transition: transform 0.2s;">
                <i class="fas fa-plus-circle"></i> New Company
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        
        <!-- Total Companies -->
        <div style="background: white; border-radius: 20px; padding: 1.5rem; border: 1px solid rgba(99, 102, 241, 0.1); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: #6366f1; opacity: 0.03;"><i class="fas fa-building"></i></div>
            <div style="background: rgba(99, 102, 241, 0.1); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #6366f1; margin-bottom: 1rem;">
                <i class="fas fa-building fa-lg"></i>
            </div>
            <div style="font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Companies</div>
            <div style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 0.25rem;">{{ $stats['total_tenants'] }}</div>
            <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #10b981; font-weight: 600;">
                <i class="fas fa-arrow-up"></i> Platform growth active
            </div>
        </div>

        <!-- Active Domains -->
        <div style="background: white; border-radius: 20px; padding: 1.5rem; border: 1px solid rgba(16, 185, 129, 0.1); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: #10b981; opacity: 0.03;"><i class="fas fa-globe"></i></div>
            <div style="background: rgba(16, 185, 129, 0.1); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #10b981; margin-bottom: 1rem;">
                <i class="fas fa-globe fa-lg"></i>
            </div>
            <div style="font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Active Domains</div>
            <div style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 0.25rem;">{{ $stats['total_domains'] }}</div>
            <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #6366f1; font-weight: 600;">
                <i class="fas fa-check-circle"></i> Multi-tenant isolation live
            </div>
        </div>

        <!-- Platform Health -->
        <div style="background: white; border-radius: 20px; padding: 1.5rem; border: 1px solid rgba(59, 130, 246, 0.1); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: #3b82f6; opacity: 0.03;"><i class="fas fa-microchip"></i></div>
            <div style="background: rgba(59, 130, 246, 0.1); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #3b82f6; margin-bottom: 1rem;">
                <i class="fas fa-server fa-lg"></i>
            </div>
            <div style="font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">System Status</div>
            <div style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 0.25rem;">OPTIMAL</div>
            <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #10b981; font-weight: 600;">
                <span style="display: inline-block; width: 8px; height: 8px; background: #10b981; border-radius: 50%; margin-right: 5px;"></span> All services online
            </div>
        </div>

        <!-- Platform Health -->
        <div style="background: white; border-radius: 20px; padding: 1.5rem; border: 1px solid rgba(245, 158, 11, 0.1); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: #f59e0b; opacity: 0.03;"><i class="fas fa-shield-alt"></i></div>
            <div style="background: rgba(245, 158, 11, 0.1); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #f59e0b; margin-bottom: 1rem;">
                <i class="fas fa-fingerprint fa-lg"></i>
            </div>
            <div style="font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Security Level</div>
            <div style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 0.25rem;">SECURE</div>
            <div style="margin-top: 0.5rem; font-size: 0.75rem; color: #f59e0b; font-weight: 600;">
                <i class="fas fa-lock"></i> Audit trails active
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        
        <!-- Left Side: Recent Activity -->
        <div style="background: white; border-radius: 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9; overflow: hidden;">
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span style="width: 4px; height: 20px; background: #6366f1; border-radius: 2px; display: inline-block;"></span>
                    Recent System Activity
                </h3>
                <a href="{{ route('admin.audit-logs.index') }}" style="font-size: 0.8rem; font-weight: 700; color: #6366f1; text-decoration: none;">View All Logs →</a>
            </div>
            <div style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                            <th style="padding: 1rem 2rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">TIMESTAMP</th>
                            <th style="padding: 1rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">OPERATOR</th>
                            <th style="padding: 1rem 1rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">EVENT</th>
                            <th style="padding: 1rem 2rem; text-align: right; font-size: 0.7rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['recent_logs'] as $log)
                        <tr style="border-bottom: 1px solid #f8fafc; transition: background 0.2s;" onmouseover="this.style.background='#fcfdfe'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1.25rem 2rem;">
                                <div style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">{{ $log->created_at->format('h:i A') }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">{{ $log->created_at->format('d M, Y') }}</div>
                            </td>
                            <td style="padding: 1.25rem 1rem;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 32px; height: 32px; background: #6366f1; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.8rem;">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #1e293b; font-size: 0.85rem;">{{ $log->user->name ?? 'System' }}</div>
                                        <div style="font-size: 0.7rem; color: #94a3b8;">{{ $log->ip_address }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1rem;">
                                @php
                                    $bgColor = $log->event === 'deleted' ? '#fee2e2' : ($log->event === 'updated' ? '#fef3c7' : '#dcfce7');
                                    $textColor = $log->event === 'deleted' ? '#ef4444' : ($log->event === 'updated' ? '#f59e0b' : '#10b981');
                                @endphp
                                <span style="background: {{ $bgColor }}; color: {{ $textColor }}; padding: 4px 12px; border-radius: 20px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $log->event }}
                                </span>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px; font-weight: 600;">{{ class_basename($log->auditable_type) }}</div>
                            </td>
                            <td style="padding: 1.25rem 2rem; text-align: right;">
                                <a href="{{ route('admin.audit-logs.show', $log->id) }}" style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-decoration: none; border: 1px solid #e2e8f0; transition: all 0.2s;">View Data</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 4rem; text-align: center; color: #94a3b8;">
                                <i class="fas fa-history fa-3x" style="opacity: 0.2; margin-bottom: 1rem; display: block;"></i>
                                <div style="font-weight: 600;">No system events recorded yet.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Platform Quick Links & Info -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Quick Management -->
            <div style="background: white; border-radius: 24px; padding: 1.5rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-layer-group text-primary"></i> Control Center
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="{{ route('admin.tenants.index') }}" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 1.25rem; border-radius: 16px; text-decoration: none; display: flex; align-items: center; gap: 15px; transition: transform 0.2s, box-shadow 0.2s;">
                        <div style="width: 44px; height: 44px; background: #6366f1; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-building"></i>
                        </div>
                        <div style="flex-grow: 1;">
                            <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem;">Company Manager</div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Provision and isolate tenants</div>
                        </div>
                        <i class="fas fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
                    </a>

                    <a href="{{ route('admin.audit-logs.index') }}" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 1.25rem; border-radius: 16px; text-decoration: none; display: flex; align-items: center; gap: 15px; transition: transform 0.2s, box-shadow 0.2s;">
                        <div style="width: 44px; height: 44px; background: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div style="flex-grow: 1;">
                            <div style="font-weight: 800; color: #1e293b; font-size: 0.95rem;">System Security</div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Global event audit trail</div>
                        </div>
                        <i class="fas fa-chevron-right" style="color: #cbd5e1; font-size: 0.8rem;"></i>
                    </a>
                </div>
            </div>

            <!-- Environment Details -->
            <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 24px; padding: 1.5rem; color: white; position: relative; overflow: hidden;">
                <div style="position: absolute; bottom: -20px; right: -20px; font-size: 8rem; color: white; opacity: 0.05; transform: rotate(-15deg);"><i class="fas fa-code"></i></div>
                <h3 style="font-size: 1rem; font-weight: 800; margin: 0 0 1.5rem 0; opacity: 0.9;">Infrastructure</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.75rem;">
                        <span style="font-size: 0.8rem; font-weight: 500; opacity: 0.6;">Framework</span>
                        <span style="font-size: 0.8rem; font-weight: 700;">Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.75rem;">
                        <span style="font-size: 0.8rem; font-weight: 500; opacity: 0.6;">Runtime</span>
                        <span style="font-size: 0.8rem; font-weight: 700;">PHP v{{ PHP_VERSION }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.8rem; font-weight: 500; opacity: 0.6;">Architecture</span>
                        <span style="font-size: 0.8rem; font-weight: 700; background: #10b981; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem;">MULTI-TENANT</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
