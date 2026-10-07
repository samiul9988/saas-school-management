<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1f2937; }

        .header-table { width: 100%; }
        .header-table .logo-cell { width: 56px; vertical-align: top; }
        .logo { width: 48px; height: 48px; border-radius: 50%; overflow: hidden; border: 1px solid #ccc; }
        .logo img { width: 100%; height: 100%; object-fit: cover; }
        .header-table h2 { margin: 0; font-size: 17px; }
        .header-table .addr { font-size: 10px; color: #555; }

        hr { border: none; border-top: 1px solid #333; margin: 8px 0 10px; }

        .title { text-align: center; font-weight: bold; font-size: 13px; margin-bottom: 10px; }

        table.info { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.info th, table.info td { border: 1px solid #333; padding: 5px 6px; text-align: center; font-size: 10px; }
        table.info th { background: #f3f4f6; }

        table.students { width: 100%; border-collapse: collapse; }
        table.students th, table.students td { border: 1px solid #333; padding: 5px 6px; font-size: 10px; text-align: left; }
        table.students th { background: #f3f4f6; text-align: center; }
        table.students td.center { text-align: center; }
        table.students .blank { min-width: 40px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <div class="logo">
                    @if ($school->logo)
                        <img src="{{ public_path('storage/'.$school->logo) }}">
                    @endif
                </div>
            </td>
            <td>
                <h2>{{ $school->name }}</h2>
                <div class="addr">{{ $school->address }}</div>
            </td>
        </tr>
    </table>
    <hr>

    <div class="title">Student Wise Mark Blank Sheet</div>

    <table class="info">
        <tr>
            <th>Academic Year</th>
            <th>Class</th>
            <th>Shift</th>
            <th>Section</th>
            <th>Total Student</th>
        </tr>
        <tr>
            <td>{{ $exam->academicYear?->name ?? $exam->starts_on?->format('Y') ?? now()->year }}</td>
            <td>{{ $section->schoolClass?->name }}</td>
            <td></td>
            <td>{{ $section->name }}</td>
            <td>{{ $students->count() }}</td>
        </tr>
    </table>

    <table class="info">
        <tr>
            <th style="width: 20%;">Exam</th>
            <td style="width: 30%;">{{ $exam->name }}</td>
            <th style="width: 20%;">Subject</th>
            <td style="width: 30%;"></td>
        </tr>
    </table>

    <table class="students">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Roll No.</th>
                <th class="blank">WR</th>
                <th class="blank">WC</th>
                <th class="blank"></th>
                <th class="blank"></th>
                <th class="blank"></th>
                <th class="blank"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td class="center">{{ $student->id }}</td>
                    <td>{{ $student->name }}</td>
                    <td class="center">{{ $student->roll }}</td>
                    <td class="blank">&nbsp;</td>
                    <td class="blank">&nbsp;</td>
                    <td class="blank">&nbsp;</td>
                    <td class="blank">&nbsp;</td>
                    <td class="blank">&nbsp;</td>
                    <td class="blank">&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
