<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 9px; color: #1f2937; margin: 0; }
        .card { border: 2px solid #2563eb; border-radius: 6px; padding: 10px; height: 85mm; box-sizing: border-box; margin-bottom: 5mm; position: relative; }

        .logo { position: absolute; top: 10px; left: 10px; width: 46px; height: 46px; border-radius: 50%; overflow: hidden; border: 1px solid #93c5fd; background: #dbeafe; }
        .logo img { width: 100%; height: 100%; object-fit: cover; }

        .photo-box { position: absolute; top: 10px; right: 46px; width: 48px; height: 56px; border: 1px solid #93c5fd; }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }

        .qr-box { position: absolute; top: 10px; right: 10px; width: 32px; height: 32px; }
        .qr-box img { width: 100%; height: 100%; }

        .header { text-align: center; padding: 0 60px; }
        .header h2 { margin: 0; font-size: 12px; color: #1e3a8a; }
        .header .addr { font-size: 8px; color: #555; margin-top: 2px; }

        .badge-wrap { text-align: center; margin-top: 6px; }
        .badge { display: inline-block; background: #2563eb; color: #fff; padding: 3px 14px; border-radius: 12px; font-size: 10px; }

        table.fields { width: 100%; margin-top: 10px; border-collapse: collapse; }
        table.fields td { padding: 2px 4px; font-size: 8.5px; vertical-align: top; }
        table.fields .label { color: #555; width: 80px; white-space: nowrap; }

        .subjects { margin-top: 6px; font-size: 8px; color: #444; }
        .subjects .title { font-weight: bold; color: #555; }

        .sign { margin-top: 12px; text-align: right; padding-right: 10px; }
        .sign .line { display: inline-block; width: 110px; border-top: 1px solid #333; padding-top: 2px; font-size: 8px; color: #555; }
    </style>
</head>
<body>
    @foreach ($students as $student)
        <div class="card">
            <div class="logo">
                @if ($school->logo)
                    <img src="{{ public_path('storage/'.$school->logo) }}">
                @endif
            </div>

            <div class="photo-box">
                @if ($student->photo)
                    <img src="{{ public_path('storage/'.$student->photo) }}">
                @endif
            </div>

            <div class="qr-box"><img src="{{ $qrCodes[$student->id] ?? '' }}"></div>

            <div class="header">
                <h2>{{ $school->name }}</h2>
                <div class="addr">{{ $school->address }}</div>
            </div>

            <div class="badge-wrap"><span class="badge">ADMIT CARD &mdash; {{ $exam->name }}</span></div>

            <table class="fields">
                <tr>
                    <td class="label">Student Name</td><td>: {{ $student->name }}</td>
                    <td class="label">Class</td><td>: {{ $section->schoolClass?->name }}</td>
                </tr>
                <tr>
                    <td class="label">Student ID</td><td>: {{ $student->admission_number ?: $student->id }}</td>
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
                    <td class="label">Mobile No.</td><td>: {{ $student->guardian_phone ?: 'N/A' }}</td>
                    <td class="label">Year</td><td>: {{ $exam->academicYear?->name ?? $exam->starts_on?->format('Y') ?? now()->year }}</td>
                </tr>
            </table>

            <div class="subjects">
                <span class="title">Subjects &amp; codes:</span>
                @if ($instruction && $instruction->lines())
                    {{ implode(', ', $instruction->lines()) }}
                @else
                    N/A
                @endif
            </div>

            <div class="sign">
                <span class="line">Headmaster</span>
            </div>
        </div>
        @if (($loop->index + 1) % 3 === 0 && ! $loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>
