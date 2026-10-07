<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: serif; font-size: 11px; }
        .card { border: 3px double #92400e; padding: 16px; margin-bottom: 14px; page-break-inside: avoid; background: #fffbeb; }
        .card h2 { margin: 0; font-size: 15px; text-align: center; color: #78350f; letter-spacing: .5px; }
        .card .sub { text-align: center; font-size: 10px; color: #92400e; margin-bottom: 6px; }
        .badge-wrap { text-align: center; }
        .badge { display: inline-block; margin: 6px 0; padding: 3px 16px; background: #92400e; color: #fff8e1; border-radius: 2px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        td { padding: 3px 4px; font-size: 10px; }
        .label { color: #78350f; width: 100px; }
    </style>
</head>
<body>
    @foreach ($students as $student)
        <div class="card">
            <h2>{{ $school->name }}</h2>
            <div class="sub">{{ $school->address }}</div>
            <div class="badge-wrap"><span class="badge">ADMIT CARD</span></div>
            <table>
                <tr><td class="label">Student Name</td><td>: {{ $student->name }}</td><td class="label">Class</td><td>: {{ $section->schoolClass?->name }}</td></tr>
                <tr><td class="label">Student ID</td><td>: {{ $student->id }}</td><td class="label">Section</td><td>: {{ $section->name }}</td></tr>
                <tr><td class="label">Father's Name</td><td>: {{ $student->father_name }}</td><td class="label">Roll</td><td>: {{ $student->roll }}</td></tr>
                <tr><td class="label">Mother's Name</td><td>: {{ $student->mother_name }}</td><td class="label">Exam</td><td>: {{ $exam->name }}</td></tr>
            </table>
        </div>
    @endforeach
</body>
</html>
