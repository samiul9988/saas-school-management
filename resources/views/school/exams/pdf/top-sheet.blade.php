<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h2 { text-align: center; margin-bottom: 2px; }
        h3 { text-align: center; margin-top: 0; font-weight: normal; font-size: 12px; }
        .subject-block { page-break-after: always; }
        .subject-block:last-child { page-break-after: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 5px 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>{{ $school->name }}</h2>
    <h3>{{ $exam->name }} &mdash; Mark Input Blank Sheet ({{ $section->schoolClass?->name }} - {{ $section->name }})</h3>

    @forelse ($examSubjects as $examSubject)
        <div class="subject-block">
            <h3>{{ $examSubject->subject?->name }} (Full Marks: {{ $examSubject->full_marks }})</h3>
            <table>
                <thead>
                    <tr><th>Roll</th><th>Name</th><th>Marks Obtained</th><th>Signature</th></tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $student->roll }}</td>
                            <td>{{ $student->name }}</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p>No subjects assigned to this exam for this class yet.</p>
    @endforelse
</body>
</html>
