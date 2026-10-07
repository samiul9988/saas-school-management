<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h2 { text-align: center; margin-bottom: 2px; }
        h3 { text-align: center; margin-top: 0; font-weight: normal; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 4px 6px; text-align: left; }
        th { background: #eee; }
        .room-block { page-break-after: always; }
        .room-block:last-child { page-break-after: auto; }
    </style>
</head>
<body>
    <h2>{{ $school->name }}</h2>
    <h3>{{ $exam->name }} &mdash; Seat Plan ({{ $section->schoolClass?->name }} - {{ $section->name }})</h3>

    @foreach ($seats->groupBy('room') as $room => $group)
        <div class="room-block">
            <h3>Room {{ $room }}</h3>
            <table>
                <thead>
                    <tr><th>Roll</th><th>Name</th><th>Seat No.</th></tr>
                </thead>
                <tbody>
                    @foreach ($group as $row)
                        <tr>
                            <td>{{ $row['student']->roll }}</td>
                            <td>{{ $row['student']->name }}</td>
                            <td>{{ $row['seat_no'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
