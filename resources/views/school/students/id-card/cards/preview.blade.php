<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @include('school.students.id-card.cards._style')
    <style>body { margin: 0; padding: 10px; background: #f3f4f6; display: flex; gap: 10px; }</style>
</head>
<body>
    @if ($side !== 'back')
        @include('school.students.id-card.cards._front', ['forPdf' => false])
    @endif
    @if ($side !== 'front')
        @include('school.students.id-card.cards._back')
    @endif
</body>
</html>
