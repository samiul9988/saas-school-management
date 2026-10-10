<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1f2937; margin: 0; }
        .page { page-break-after: always; padding: 20px; }
        .page:last-child { page-break-after: auto; }

        .head { text-align: center; border-bottom: 2px solid #1f2937; padding-bottom: 8px; margin-bottom: 12px; }
        .head h2 { margin: 0; font-size: 16px; }
        .head .addr { font-size: 10px; color: #555; }
        .head .exam-title { font-weight: bold; font-size: 12px; margin-top: 4px; text-transform: uppercase; }

        table.info { width: 100%; margin-bottom: 14px; }
        table.info td { padding: 2px 4px; font-size: 10.5px; }
        table.info .label { color: #555; width: 70px; }

        table.marks { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.marks th, table.marks td { border: 1px solid #999; padding: 5px 8px; font-size: 10.5px; text-align: left; }
        table.marks th { background: #eef2ff; }

        table.summary { width: 100%; border-collapse: collapse; text-align: center; }
        table.summary td { border: 1px solid #999; padding: 8px; }
        table.summary .label { font-size: 9px; color: #555; }
        table.summary .value { font-size: 13px; font-weight: bold; }

        .footer { text-align: center; font-size: 9px; margin-top: 30px; }
    </style>
</head>
<body>
    @foreach ($sheets as $sheet)
        <div class="page">
            <div class="head">
                <h2>{{ $school->name }}</h2>
                <div class="addr">{{ $school->address }}</div>
                <div class="exam-title">Marksheet &mdash; {{ $exam->name }}</div>
            </div>

            <table class="info">
                <tr>
                    <td class="label">Name</td><td>: {{ $sheet['student']->name }}</td>
                    <td class="label">Roll</td><td>: {{ $sheet['student']->roll }}</td>
                </tr>
                <tr>
                    <td class="label">Class</td><td>: {{ $section->schoolClass?->name }}</td>
                    <td class="label">Section</td><td>: {{ $section->name }}</td>
                </tr>
            </table>

            <table class="marks">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Full Marks</th>
                        <th>Pass Marks</th>
                        <th>Obtained</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sheet['subjectRows'] as $row)
                        <tr>
                            <td>{{ $row['subject'] }}</td>
                            <td>{{ $row['full_marks'] }}</td>
                            <td>{{ $row['pass_marks'] }}</td>
                            <td>{{ $row['is_absent'] ? 'Absent' : ($row['obtained'] ?? '-') }}</td>
                            <td>{{ $row['passed'] ? 'Pass' : 'Fail' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="summary">
                <tr>
                    <td>
                        <div class="label">Total</div>
                        <div class="value">{{ $sheet['total_obtained'] }} / {{ $sheet['total_full'] }}</div>
                    </td>
                    <td>
                        <div class="label">Percentage</div>
                        <div class="value">{{ $sheet['percentage'] }}%</div>
                    </td>
                    <td>
                        <div class="label">GPA</div>
                        <div class="value">{{ $sheet['gpa'] }}</div>
                    </td>
                    <td>
                        <div class="label">Grade</div>
                        <div class="value">{{ $sheet['grade']?->name ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="label">Result</div>
                        <div class="value">{{ $sheet['is_passed'] ? 'Pass' : 'Fail' }}</div>
                    </td>
                </tr>
            </table>

            <div class="footer">Controller of Examinations</div>
        </div>
    @endforeach
</body>
</html>
