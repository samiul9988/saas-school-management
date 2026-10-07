<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1f2937; margin: 0; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }

        table.grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.grid td.cell { width: 50%; vertical-align: top; padding: 0 1% 14px 1%; }

        .card { border: 1px dashed #666; padding: 10px 14px; }

        table.head-table { width: 100%; }
        table.head-table .logo-cell { width: 56px; vertical-align: top; }
        .logo { width: 48px; height: 48px; border-radius: 50%; overflow: hidden; border: 1px solid #ccc; }
        .logo img { width: 100%; height: 100%; object-fit: cover; }

        .head h3 { margin: 0; font-size: 12.5px; }
        .head .addr { font-size: 9px; color: #555; }
        .seat-badge { display: inline-block; border: 1px solid #333; padding: 2px 10px; font-weight: bold; font-size: 10px; margin-top: 4px; }
        .exam-title { font-weight: bold; font-size: 9.5px; margin-top: 2px; }

        table.body-table { width: 100%; margin-top: 10px; }
        table.body-table .info-cell { width: 62%; vertical-align: top; }
        table.body-table .roll-cell { width: 36%; vertical-align: top; }

        table.info td { padding: 1px 2px; font-size: 9.5px; }
        table.info .label { color: #555; width: 60px; }

        .roll-box { border: 1px solid #333; text-align: center; padding: 6px 0; }
        .roll-box .roll-label { font-size: 9px; font-weight: bold; border-bottom: 1px solid #333; padding-bottom: 3px; margin-bottom: 4px; }
        .roll-box .roll-no { font-size: 20px; font-weight: bold; }

        .footer { text-align: center; font-size: 9px; margin-top: 14px; border-top: 1px dashed #999; padding-top: 4px; }
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
                                                    <tr><td class="label">Student ID</td><td>: {{ $student->id }}</td></tr>
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
