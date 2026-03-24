@extends('layouts.app')

@section('content')
    <div style="background:#fff; padding:25px; border-radius:8px; width:100%;">

        <h2 style="margin-bottom:15px;">🕒 My Attendance</h2>

        @if (session('success'))
            <p style="color:green;">{{ session('success') }}</p>
        @endif

        @if (session('error'))
            <p style="color:red;">{{ session('error') }}</p>
        @endif

        <div style="margin-bottom:25px; padding:15px; background:#f9fafb; border-radius:6px;">
            <strong>Employee:</strong> {{ $employee->name ?? '-' }} <br>
            <strong>Date:</strong> {{ now()->format('d M Y') }}
        </div>

        <div style="display:flex; gap:15px; margin-bottom:30px;">
            <form method="POST" action="{{ route('attendance.checkin') }}">
                @csrf
                <button type="submit" style="background:#16a34a;color:#fff;padding:10px 18px;border:none;border-radius:6px;"
                    {{ $todayAttendance && $todayAttendance->check_in ? 'disabled' : '' }}>
                    ✅ Check In
                </button>
            </form>

            <form method="POST" action="{{ route('attendance.checkout') }}">
                @csrf
                <button type="submit"
                    style="background:#dc2626;color:#fff;padding:10px 18px;border:none;border-radius:6px;"
                    {{ !$todayAttendance || $todayAttendance->check_out ? 'disabled' : '' }}>
                    ⏹ Check Out
                </button>
            </form>
        </div>

        @if ($todayAttendance)
            <div style="margin-bottom:25px;">
                <strong>Check In:</strong> {{ $todayAttendance->check_in ?? '-' }} <br>
                <strong>Check Out:</strong> {{ $todayAttendance->check_out ?? '-' }} <br>

                <strong>Status:</strong>
                <span
                    style="
                font-weight:600;
                color:
                    {{ $todayAttendance->status === 'Late'
                        ? '#dc2626'
                        : ($todayAttendance->status === 'Half Day'
                            ? '#f59e0b'
                            : '#16a34a') }};
            ">
                    {{ $todayAttendance->status }}
                </span>

                {{-- ⚠ Status Messages --}}
                @if ($todayAttendance->status === 'Late')
                    <p style="color:#dc2626; font-weight:600; margin-top:8px;">
                        ⚠ You are marked Late today
                    </p>
                @elseif ($todayAttendance->status === 'Half Day')
                    <p style="color:#f59e0b; font-weight:600; margin-top:8px;">
                        ⚠ Half Day marked (less working hours)
                    </p>
                @elseif ($todayAttendance->status === 'Present')
                    <p style="color:#16a34a; font-weight:600; margin-top:8px;">
                        ✅ You are On Time today
                    </p>
                @endif

                {{-- ⏱ LIVE WORKING TIMER --}}
                @if ($todayAttendance->check_in && !$todayAttendance->check_out)
                    <div style="margin-top:15px;">
                        <strong>⏱ Working Time:</strong>
                        <span id="working-timer" style="font-weight:700; color:#2563eb;">
                            00:00:00
                        </span>
                    </div>
                @endif
            </div>
        @endif




        <h3>📜 Attendance History</h3>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:10px;">Date</th>
                    <th style="padding:10px;">Check In</th>
                    <th style="padding:10px;">Check Out</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px;">Working Hours</th>

                </tr>
            </thead>
            <tbody>
                @php $__col = $history; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $row)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px;">
                            {{ \Carbon\Carbon::parse($row->attendance_date)->format('d M Y') }}
                        </td>
                        <td style="padding:10px;">{{ $row->check_in ?? '-' }}</td>
                        <td style="padding:10px;">{{ $row->check_out ?? '-' }}</td>
                        <td style="padding:10px;">
                            <span style="color:#16a34a;font-weight:600;">
                                {{ $row->status }}
                            </span>
                        </td>
                        <td style="padding:10px; font-weight:600; color:#2563eb;">
                            {{ $row->working_hours ?? '-' }}
                        </td>

                    </tr>
                @endforeach
@else
                    <tr>
                        <td colspan="4" style="padding:10px;">No records found</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div style="margin-top:20px;">
            {{ $history->links() }}
        </div>

    </div>
    @if ($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out)
        <script>
            // Check-in time from server
            const checkInTime = new Date(
                "{{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('Y-m-d H:i:s') }}"
            );

            function startWorkingTimer() {
                const now = new Date();
                let diff = Math.floor((now - checkInTime) / 1000); // total seconds

                if (diff < 0) diff = 0;

                const hours = String(Math.floor(diff / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
                const seconds = String(diff % 60).padStart(2, '0');

                document.getElementById('working-timer').innerText =
                    `${hours}:${minutes}:${seconds}`;
            }

            // Start immediately
            startWorkingTimer();

            // Update every second
            setInterval(startWorkingTimer, 1000);
        </script>
    @endif

@endsection
