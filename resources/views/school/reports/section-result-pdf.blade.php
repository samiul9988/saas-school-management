@include('school.students.report.pdf._layout-start')

<div class="report-title">Section Wise Result</div>
<div class="report-meta">Class: {{ $section?->schoolClass?->name }}, Section: {{ $section?->name }}</div>

<table class="data">
    <thead>
        <tr>
            <th>ID No</th>
            <th>Roll</th>
            <th>Name</th>
            <th>Percentage</th>
            <th>GPA</th>
            <th>Grade</th>
            <th>Position</th>
            <th>Status</th>
            <th>Total Failed Subject</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row->student->admission_number }}</td>
                <td>{{ $row->student->roll }}</td>
                <td>{{ $row->student->name }}</td>
                <td>{{ $row->percentage }}%</td>
                <td>{{ $row->gpa }}</td>
                <td>{{ $row->grade?->name ?? '—' }}</td>
                <td>{{ $row->is_passed ? $row->position : '—' }}</td>
                <td>{{ $row->is_passed ? 'Pass' : 'Fail' }}</td>
                <td>{{ $row->failed_subjects_count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@include('school.students.report.pdf._layout-end')
