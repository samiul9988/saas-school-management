<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @include('school.teachers.id-card.cards._style')
    <style>body { margin: 0; padding: 14px; display: flex; gap: 14px; }</style>
</head>
<body>
    @include('school.teachers.id-card.cards._front', ['forPdf' => true])
    @include('school.teachers.id-card.cards._back')
</body>
</html>
