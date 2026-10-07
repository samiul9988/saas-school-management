<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        h2 { text-align: center; margin-bottom: 2px; }
        h3 { text-align: center; margin-top: 0; font-weight: normal; }
        .class-block { page-break-after: always; }
        .class-block:last-child { page-break-after: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 4px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>{{ $school->name }}</h2>
    <h3>{{ $exam->name }} &mdash; Tabulation Sheet</h3>

    @foreach ($classBlocks as $block)
        <div class="class-block">
            <h3>{{ $block['class']->name }}</h3>
            <table>
                <thead>
                    <tr>
                        <th>Roll</th>
                        <th>Name</th>
                        @foreach ($block['examSubjects'] as $examSubject)
                            <th>{{ $examSubject->subject?->name }} ({{ $examSubject->full_marks }})</th>
                        @endforeach
                        <th>Total</th>
                        <th>%</th>
                        <th>Grade</th>
                        <th>Position</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($block['rows'] as $index => $row)
                        <tr>
                            <td>{{ $row['student']->roll }}</td>
                            <td>{{ $row['student']->name }}</td>
                            @foreach ($block['examSubjects'] as $examSubject)
                                <td>{{ $row['marks'][$examSubject->id] ?? '—' }}</td>
                            @endforeach
                            <td>{{ $row['total_obtained'] }}/{{ $row['total_full'] }}</td>
                            <td>{{ $row['percentage'] }}%</td>
                            <td>{{ $row['grade'] ?? '—' }}</td>
                            <td>{{ $index + 1 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
