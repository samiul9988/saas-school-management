<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .card { border: 1px solid #333; padding: 12px; margin-bottom: 14px; page-break-inside: avoid; }
        .card h2 { margin: 0 0 4px; font-size: 14px; text-align: center; }
        .card h3 { margin: 0 0 10px; font-size: 11px; text-align: center; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 4px; }
        .label { color: #555; width: 110px; }
        .instructions { margin-top: 8px; font-size: 10px; }
        .instructions li { margin-bottom: 2px; }
    </style>
</head>
<body>
    @foreach ($students as $student)
        <div class="card">
            <h2>{{ $school->name }}</h2>
            <h3>Admit Card &mdash; {{ $exam->name }}</h3>
            <table>
                <tr><td class="label">Student Name</td><td>: {{ $student->name }}</td><td class="label">Roll</td><td>: {{ $student->roll }}</td></tr>
                <tr><td class="label">Class</td><td>: {{ $section->schoolClass?->name }}</td><td class="label">Section</td><td>: {{ $section->name }}</td></tr>
                <tr><td class="label">Father's Name</td><td>: {{ $student->father_name }}</td><td class="label">Mother's Name</td><td>: {{ $student->mother_name }}</td></tr>
            </table>
            @if ($instruction && $instruction->lines())
                <div class="instructions">
                    <strong>Instructions:</strong>
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
