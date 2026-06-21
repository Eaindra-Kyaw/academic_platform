<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Timetable - {{ $lecturer->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #800000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 20px;
            margin: 0;
            color: #800000;
        }

        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 13px;
        }

        .header .mtu {
            font-size: 14px;
            font-weight: bold;
            color: #800000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: #800000;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 12px;
        }

        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .session-type {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .type-lecture {
            background: #fef2f2;
            color: #800000;
        }

        .type-lab {
            background: #fef3c7;
            color: #92400e;
        }

        .type-tutorial {
            background: #dcfce7;
            color: #166534;
        }

        .type-seminar {
            background: #dbeafe;
            color: #1e40af;
        }

        .type-workshop {
            background: #f3e8ff;
            color: #6b21a8;
        }

        .course-code {
            font-weight: 600;
            color: #1f2937;
        }

        .course-name {
            color: #4b5563;
            font-size: 11px;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .day-header {
            background: #f3f4f6;
            font-weight: 600;
        }

        .empty-row td {
            text-align: center;
            color: #999;
            padding: 20px;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        @page {
            margin: 15mm;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="mtu">Mandalay Technological University</div>
        <h1>Weekly Timetable</h1>
        <p>
            <strong>{{ $lecturer->name }}</strong>
            @if ($academicYear)
                | Academic Year: {{ $academicYear }}
            @endif
            @if ($semester)
                | Semester: {{ $semester }}
            @endif
            <br>
            Generated: {{ now()->format('d M Y, h:i A') }}
        </p>
    </div>

    @php
        $hasEntries = false;
        foreach ($grid as $day => $entries) {
            if ($entries->isNotEmpty()) {
                $hasEntries = true;
                break;
            }
        }
    @endphp

    @if (!$hasEntries)
        <div class="empty-row">
            <p style="text-align: center; color: #999; padding: 40px 0;">
                No timetable entries found.
            </p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Day</th>
                    <th style="width: 18%;">Time</th>
                    <th style="width: 30%;">Course</th>
                    <th style="width: 15%;">Room</th>
                    <th style="width: 25%;">Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grid as $day => $entries)
                    @if ($entries->isNotEmpty())
                        @foreach ($entries as $index => $entry)
                            <tr>
                                @if ($index === 0)
                                    <td rowspan="{{ $entries->count() }}" class="day-header">
                                        <strong>{{ $day }}</strong>
                                    </td>
                                @endif
                                <td>
                                    {{ date('h:i A', strtotime($entry->start_time)) }} -<br>
                                    {{ date('h:i A', strtotime($entry->end_time)) }}
                                    <br>
                                    <span style="font-size: 10px; color: #999;">
                                        {{ $entry->duration }} min
                                    </span>
                                </td>
                                <td>
                                    <div class="course-code">{{ $entry->course->course_code ?? 'N/A' }}</div>
                                    <div class="course-name">{{ $entry->course->course_name ?? 'N/A' }}</div>
                                    @if ($entry->year_level)
                                        <span style="font-size: 10px; color: #6b7280;">
                                            <i class="bi bi-mortarboard"></i> {{ $entry->year_level }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    {{ $entry->room ?? 'N/A' }}
                                    @if ($entry->building)
                                        <br>
                                        <span style="font-size: 10px; color: #6b7280;">{{ $entry->building }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="session-type type-{{ $entry->session_type ?? 'lecture' }}">
                                        {{ ucfirst($entry->session_type ?? 'Lecture') }}
                                    </span>
                                    <br>
                                    <span style="font-size: 10px; color: #6b7280;">
                                        {{ $entry->section ?? 'No section' }}
                                    </span>
                                    @if ($entry->notes)
                                        <br>
                                        <span style="font-size: 10px; color: #6b7280; font-style: italic;">
                                            📝 {{ $entry->notes }}
                                        </span>
                                    @endif
                                    @if ($entry->is_alternate_week)
                                        <br>
                                        <span style="font-size: 10px; color: #f59e0b;">
                                            🔄 Alternate Week ({{ $entry->alternate_week_type ?? 'A' }})
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Generated by MTU Academic Intelligence System &bull; {{ now()->format('Y') }}
    </div>
</body>

</html>
