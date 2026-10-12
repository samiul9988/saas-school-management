@include('school.students.report.pdf._layout-start')

<div class="report-title">Gender Wise Student Summary (By Class)</div>
<div class="report-meta">Year: {{ $year }}</div>

<table class="data">
    <thead>
        <tr>
            <th>Class</th>
            <th>Total Student</th>
            <th>Male</th>
            <th>Female</th>
            <th>Other</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row['class'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td>{{ $row['male'] }}</td>
                <td>{{ $row['female'] }}</td>
                <td>{{ $row['other'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>{{ $rows->sum('total') }}</strong></td>
            <td><strong>{{ $rows->sum('male') }}</strong></td>
            <td><strong>{{ $rows->sum('female') }}</strong></td>
            <td><strong>{{ $rows->sum('other') }}</strong></td>
        </tr>
    </tbody>
</table>

@include('school.students.report.pdf._layout-end')
