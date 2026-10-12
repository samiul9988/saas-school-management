<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @include('school.students.id-card.cards._style')
    <style>body { margin: 0; padding: 14px; }</style>
</head>
<body>
    <table class="cards-grid">
        @foreach ($students->chunk(2) as $row)
            <tr>
                @foreach ($row as $student)
                    <td>@include('school.students.id-card.cards._back')</td>
                @endforeach
            </tr>
        @endforeach
    </table>
</body>
</html>
