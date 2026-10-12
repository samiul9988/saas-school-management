@include('school.students.report.pdf._layout-start')

<div class="report-title">Student Report (Section Wise)</div>
<div class="report-meta">Class: {{ $section->schoolClass?->name }}, Section: {{ $section->name }}</div>

<table class="data">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Roll</th>
            <th>Father</th>
            <th>Mother</th>
            <th>Gender</th>
            <th>Religion</th>
            <th>Mobile</th>
            @if ($withImage)
                <th>Photo</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $student)
            <tr>
                <td>{{ $student->admission_number }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->roll }}</td>
                <td>{{ $student->father_name }}</td>
                <td>{{ $student->mother_name }}</td>
                <td>{{ ucfirst((string) $student->gender) }}</td>
                <td>{{ $student->religion }}</td>
                <td>{{ $student->guardian_phone }}</td>
                @if ($withImage)
                    <td class="photo">
                        @if ($student->photo)
                            <img src="{{ public_path('storage/'.$student->photo) }}" alt="">
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

@include('school.students.report.pdf._layout-end')
