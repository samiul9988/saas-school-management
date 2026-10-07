<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .card { border: 2px solid #2563eb; border-radius: 6px; padding: 14px; margin-bottom: 14px; page-break-inside: avoid; position: relative; }
        .card h2 { margin: 0; font-size: 15px; text-align: center; color: #1e3a8a; }
        .card .sub { text-align: center; font-size: 10px; color: #555; margin-bottom: 6px; }
        .badge { display: inline-block; margin: 6px auto; padding: 3px 14px; background: #2563eb; color: #fff; border-radius: 4px; font-size: 11px; text-align: center; }
        .badge-wrap { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td { padding: 3px 4px; font-size: 10px; }
        .label { color: #555; width: 100px; }
        .instructions { margin-top: 8px; font-size: 9px; }
    </style>
</head>
<body>
    @foreach ($students as $student)
        <div class="card">
            <h2>{{ $school->name }}</h2>
            <div class="sub">{{ $school->address }}</div>
            <div class="badge-wrap"><span class="badge">ADMIT CARD &mdash; {{ $exam->name }}</span></div>
            <table>
                <tr><td class="label">Student Name</td><td>: {{ $student->name }}</td><td class="label">Class</td><td>: {{ $section->schoolClass?->name }}</td></tr>
                <tr><td class="label">Student ID</td><td>: {{ $student->id }}</td><td class="label">Section</td><td>: {{ $section->name }}</td></tr>
                <tr><td class="label">Father's Name</td><td>: {{ $student->father_name }}</td><td class="label">Roll</td><td>: {{ $student->roll }}</td></tr>
                <tr><td class="label">Mother's Name</td><td>: {{ $student->mother_name }}</td><td class="label">Mobile No.</td><td>: {{ $student->guardian_phone }}</td></tr>
            </table>
            @if ($instruction && $instruction->lines())
                <div class="instructions">
                    <strong>Subjects &amp; codes:</strong>
                    <ol>
                        @foreach ($instruction->lines() as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>
    @endforeach
</body>
</html>
