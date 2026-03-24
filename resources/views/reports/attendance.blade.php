@extends('layouts.app')

@section('content')
    <style>
        /* ===== REPORT BOX ===== */
        .report-box {
            background: #ffffff;
            padding: 25px;
            border-radius: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        /* ===== HEADER ===== */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .report-header h2 {
            margin: 0;
            color: #111827;
        }

        /* ===== FILTER ===== */
        .report-filter {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .report-filter input {
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
        }

        .report-filter button {
            background: #111827;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
        }

        .report-filter button:hover {
            background: #1f2937;
        }

        /* ===== TABLE ===== */
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #111827;
        }

        .report-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }

        .report-table tr:hover {
            background: #f9fafb;
        }

        /* ===== BUTTONS ===== */
        .btn-excel {
            background: #16a34a;
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            margin-right: 8px;
        }

        .btn-excel:hover {
            background: #15803d;
        }

        .btn-pdf {
            background: #dc2626;
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-pdf:hover {
            background: #b91c1c;
        }

        /* ===== STATUS COLORS ===== */
        .status-present {
            color: #16a34a;
            font-weight: 600;
        }

        .status-absent {
            color: #dc2626;
            font-weight: 600;
        }

        .status-leave {
            color: #ca8a04;
            font-weight: 600;
        }
    </style>

    <div class="report-box">

        {{-- HEADER + EXPORT --}}
        <div class="report-header">
            <h2>🕒 Attendance Report</h2>

            <div>
                <a href="{{ route('reports.attendance.excel', ['from' => $from, 'to' => $to]) }}" class="btn-excel">⬇
                    Excel</a>

                <a href="{{ route('reports.attendance.pdf', ['from' => $from, 'to' => $to]) }}" class="btn-pdf">⬇ PDF</a>
            </div>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="report-filter">
            <input type="date" name="from" value="{{ $from }}">
            <input type="date" name="to" value="{{ $to }}">
            <button type="submit">Filter</button>
        </form>

        {{-- TABLE --}}
        <table class="report-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th style="padding:10px;">Working Hours</th>
                </tr>
            </thead>
            <tbody>
                @php $__col = $attendance; @endphp
@if(is_array($__col) || $__col instanceof \Countable ? count($__col) > 0 : !empty($__col))
@foreach($__col as $a)
                    <tr>
                        <td>{{ $a->employee->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($a->attendance_date)->format('d M Y') }}</td>
                        <td>{{ $a->check_in ?? '-' }}</td>
                        <td>{{ $a->check_out ?? '-' }}</td>
                        <td>
                            @if ($a->status === 'Present')
                                <span class="status-present">Present</span>
                            @elseif ($a->status === 'Absent')
                                <span class="status-absent">Absent</span>
                            @else
                                <span class="status-leave">Leave</span>
                            @endif
                        </td>
                                                <td style="padding:10px;">{{ $row->working_hours ?? '-' }}</td>
                    </tr>
                @endforeach
@else
                    <tr>
                        <td colspan="5">No attendance data found</td>
                    </tr>
                @endif
            </tbody>
        </table>

    </div>
@endsection
