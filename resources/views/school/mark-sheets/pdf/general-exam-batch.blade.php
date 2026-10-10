<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @include('school.mark-sheets.pdf._academic-transcript-style')
    <style>.page-wrap { page-break-after: always; } .page-wrap:last-child { page-break-after: auto; }</style>
</head>
<body>
    @foreach ($pages as $page)
        <div class="page-wrap">
            @include('school.mark-sheets.pdf._academic-transcript-sheet', $page)
        </div>
    @endforeach
</body>
</html>
