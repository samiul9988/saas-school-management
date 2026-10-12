@include('school.students.report.pdf._layout-start')

<div class="report-title">Religion Wise Student Summary</div>

<table class="data">
    <thead>
        <tr>
            <th>Class</th>
            @foreach ($religions as $religion)
                <th>{{ $religion }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row['class'] }}</td>
                @foreach ($religions as $religion)
                    <td>{{ $row[$religion] ?? 0 }}</td>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <td><strong>Total</strong></td>
            @foreach ($religions as $religion)
                <td><strong>{{ $rows->sum($religion) }}</strong></td>
            @endforeach
        </tr>
    </tbody>
</table>

@include('school.students.report.pdf._layout-end')
