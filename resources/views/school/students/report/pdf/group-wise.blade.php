@include('school.students.report.pdf._layout-start')

<div class="report-title">Student Report (Group Wise)</div>
<div class="report-meta">Class: {{ $class->name }}, Group: {{ $groupName }}</div>

<table class="data">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Roll</th>
            <th>Group</th>
            <th>Father</th>
            <th>Mother</th>
            <th>Gender</th>
            <th>Religion</th>
            <th>Mobile</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $student)
            <tr>
                <td>{{ $student->admission_number }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->roll }}</td>
                <td>{{ $student->group_name }}</td>
                <td>{{ $student->father_name }}</td>
                <td>{{ $student->mother_name }}</td>
                <td>{{ ucfirst((string) $student->gender) }}</td>
                <td>{{ $student->religion }}</td>
                <td>{{ $student->guardian_phone }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@include('school.students.report.pdf._layout-end')
