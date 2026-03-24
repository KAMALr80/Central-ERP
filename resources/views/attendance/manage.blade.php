@extends('layouts.app')

@section('content')
    <div style="background:#fff; padding:25px; border-radius:8px; width:100%; box-sizing:border-box;">

        <h2 style="margin-bottom:20px;">🗂 Attendance Management</h2>

        {{-- TABLE --}}
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:10px;">Employee</th>
                    <th style="padding:10px;">Date</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px;">Check In</th>
                    <th style="padding:10px;">Check Out</th>
                    <th style="padding:10px;">Working Hours</th>
                </tr>
            </thead>
            <tbody>
                @php $__col = $records; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $row)
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <td style="padding:10px;">
                            {{ $row->employee->name }}
                            <br>
                            <small style="color:#6b7280;">
                                {{ $row->employee->employee_code }}
                            </small>
                        </td>

                        <td style="padding:10px;">
                            {{ \Carbon\Carbon::parse($row->attendance_date)->format('d M Y') }}
                        </td>

                        <td style="padding:10px;">
                            @if ($row->status === 'Present')
                                <span style="color:#16a34a; font-weight:600;">Present</span>
                            @elseif($row->status === 'Absent')
                                <span style="color:#dc2626; font-weight:600;">Absent</span>
                            @else
                                <span style="color:#ca8a04; font-weight:600;">Leave</span>
                            @endif
                        </td>

                        <td style="padding:10px;">
                            {{ $row->check_in ?? '-' }}
                        </td>

                        <td style="padding:10px;">
                            {{ $row->check_out ?? '-' }}
                        </td>
                        <td style="padding:10px;">
                            {{ $row->working_hours ?? '-' }}
                        </td>
                    </tr>
                @endforeach
@else
                    <tr>
                        <td colspan="5" style="padding:10px; text-align:center;">
                            No attendance records found
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- PAGINATION --}}
        <div style="margin-top:20px;">
            {{ $records->links() }}
        </div>

    </div>
@endsection
