<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1f2937; }
        .outer { border: 3px solid #92400e; padding: 4px; margin-bottom: 18px; page-break-inside: avoid; }
        .inner { border: 1px solid #92400e; padding: 16px; position: relative; }

        .logo { position: absolute; top: 14px; left: 14px; width: 70px; height: 70px; border-radius: 50%; overflow: hidden; border: 1px solid #ccc; }
        .logo img { width: 100%; height: 100%; object-fit: cover; }

        .photo-box { position: absolute; top: 14px; right: 14px; width: 80px; height: 80px; border: 1px solid #333; }
        .qr-box { position: absolute; top: 102px; right: 14px; width: 80px; height: 80px; }
        .qr-box img { width: 100%; height: 100%; }

        .header { text-align: center; padding: 0 90px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header .addr { font-size: 10px; color: #555; margin-top: 2px; }

        .badge-wrap { text-align: center; margin-top: 8px; }
        .badge { display: inline-block; background: #f59e0b; color: #1e293b; font-weight: bold; padding: 4px 16px; border-radius: 3px; font-size: 12px; }
        .exam-title { text-align: center; font-weight: bold; margin-top: 4px; font-size: 11px; }

        table.fields { width: 100%; margin-top: 16px; border-collapse: collapse; }
        table.fields td { padding: 3px 4px; font-size: 10.5px; vertical-align: top; }
        table.fields .label { color: #555; width: 110px; }

        .routine { margin-top: 10px; font-size: 10px; }
        .routine .title { font-weight: bold; }

        .sign { margin-top: 40px; text-align: right; padding-right: 20px; }
        .sign .line { display: inline-block; width: 140px; border-top: 1px solid #333; padding-top: 2px; font-size: 9px; color: #555; }
    </style>
</head>
<body>
    @foreach ($students as $student)
        <div class="outer">
            <div class="inner">
                <div class="logo">
                    @if ($school->logo)
                        <img src="{{ public_path('storage/'.$school->logo) }}">
                    @endif
                </div>

                <div class="photo-box"></div>
                <div class="qr-box"><img src="{{ $qrCodes[$student->id] ?? '' }}"></div>

                <div class="header">
                    <h2>{{ $school->name }}</h2>
                    <div class="addr">{{ $school->address }}</div>
                </div>

                <div class="badge-wrap">
                    <span class="badge">ADMIT CARD</span>
                </div>
                <div class="exam-title">{{ strtoupper($exam->name) }}@if ($exam->academicYear)-{{ $exam->academicYear->name }}@endif</div>

                <table class="fields">
                    <tr>
                        <td class="label">Student Name</td><td>: {{ $student->name }}</td>
                        <td class="label">Class</td><td>: {{ $section->schoolClass?->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Student ID</td><td>: {{ $student->id }}</td>
                        <td class="label">Section</td><td>: {{ $section->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Father's Name</td><td>: {{ $student->father_name ?: 'N/A' }}</td>
                        <td class="label">Roll</td><td>: {{ $student->roll }}</td>
                    </tr>
                    <tr>
                        <td class="label">Mother's Name</td><td>: {{ $student->mother_name ?: 'N/A' }}</td>
                        <td class="label">Group</td><td>: {{ $student->group_name ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date of Birth</td><td>: {{ $student->date_of_birth?->format('Y-m-d') ?? 'N/A' }}</td>
                        <td class="label">Reg. No.</td><td>: {{ $student->admission_number ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Mobile No.</td><td>: {{ $student->guardian_phone ?: 'N/A' }}</td>
                        <td class="label">Year</td><td>: {{ $exam->academicYear?->name ?? $exam->starts_on?->format('Y') ?? now()->year }}</td>
                    </tr>
                </table>

                <div class="routine">
                    <span class="title">Exam Routine:</span>
                    @if ($instruction && $instruction->lines())
                        <ol>
                            @foreach ($instruction->lines() as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ol>
                    @else
                        N/A
                    @endif
                </div>

                <div class="sign">
                    <span class="line">Authorized Signature</span>
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>
