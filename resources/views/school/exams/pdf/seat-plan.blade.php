<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 6mm; }
        body { font-family: sans-serif; font-size: 9.5px; color: #1f2937; margin: 0; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }

        table.grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.grid td.cell { width: 50%; vertical-align: top; padding: 0 1% 6px 1%; }

        .card { border: 1px dashed #666; padding: 6px 8px; }

        table.head-table { width: 100%; }
        table.head-table .logo-cell { width: 40px; vertical-align: top; }
        .logo { width: 34px; height: 34px; border-radius: 50%; overflow: hidden; border: 1px solid #ccc; }
        .logo img { width: 100%; height: 100%; object-fit: cover; }

        .head h3 { margin: 0; font-size: 11px; }
        .head .addr { font-size: 8px; color: #555; }
        .seat-badge { display: inline-block; border: 1px solid #333; padding: 1px 8px; font-weight: bold; font-size: 9px; margin-top: 2px; }
        .exam-title { font-weight: bold; font-size: 8.5px; margin-top: 1px; }

        table.body-table { width: 100%; margin-top: 5px; }
        table.body-table .info-cell { width: 62%; vertical-align: top; }
        table.body-table .roll-cell { width: 36%; vertical-align: top; }

        table.info td { padding: 0.5px 2px; font-size: 8.5px; }
        table.info .label { color: #555; width: 55px; }

        .roll-box { border: 1px solid #333; text-align: center; padding: 4px 0; }
        .roll-box .roll-label { font-size: 8px; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 2px; margin-bottom: 2px; }
        .roll-box .roll-no { font-size: 16px; font-weight: bold; }

        .footer-spacer { height: 6px; font-size: 1px; line-height: 1px;}
        .footer { text-align: center; font-size: 8px; border-top: 1px dashed #999; padding-top: 3px; }
    </style>
</head>
<body>
    @foreach ($seats->chunk($perRoom) as $pageSeats)
        <div class="page">
            <table class="grid">
                @foreach ($pageSeats->chunk(2) as $pair)
                    <tr>
                        @foreach ($pair as $row)
                            @php($student = $row['student'])
                            <td class="cell">
                                <div class="card">
                                    <table class="head-table">
                                        <tr>
                                            <td class="logo-cell">
                                                <div class="logo">
                                                    @if ($school->logo)
                                                        <img src="{{ public_path('storage/'.$school->logo) }}">
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="head">
                                                <h3>{{ $school->name }}</h3>
                                                <div class="addr">{{ $school->address }}</div>
                                                <div class="seat-badge">Seat of Exam</div>
                                                <div class="exam-title">{{ strtoupper($exam->name) }}@if ($exam->academicYear) - {{ $exam->academicYear->name }}@endif</div>
                                            </td>
                                        </tr>
                                    </table>

                                    <table class="body-table">
                                        <tr>
                                            <td class="info-cell">
                                                <table class="info">
                                                    <tr><td class="label">Name</td><td>: {{ $student->name }}</td></tr>
                                                    <tr><td class="label">Student ID</td><td>: {{ $student->admission_number ?: $student->id }}</td></tr>
                                                    <tr><td class="label">Class</td><td>: {{ $section->schoolClass?->name }}</td></tr>
                                                    <tr><td class="label">Section</td><td>: {{ $section->name }}</td></tr>
                                                    <tr><td class="label">Group</td><td>: {{ $student->group_name ?: 'N/A' }}</td></tr>
                                                    <tr><td class="label">Gender</td><td>: {{ ucfirst($student->gender ?? 'N/A') }}</td></tr>
                                                </table>
                                            </td>
                                            <td class="roll-cell">
                                                <div class="roll-box">
                                                    <div class="roll-label">Roll No.</div>
                                                    <div class="roll-no">{{ $student->roll }}</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <div class="footer-spacer">&nbsp;</div>
                                    <div class="footer">Convener, Exam Committee</div>
                                </div>
                            </td>
                        @endforeach
                        @if ($pair->count() === 1)
                            <td class="cell"></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach
</body>
</html>
