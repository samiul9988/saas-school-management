<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #1f2937; margin: 0; padding: 20px; }
        .school-name { font-size: 15px; font-weight: bold; }
        .school-addr { font-size: 9.5px; color: #555; margin-top: 2px; }
        .divider { border-top: 1px solid #333; margin: 8px 0 14px 0; }
        .report-title { text-align: center; font-size: 13px; font-weight: bold; margin-bottom: 2px; }
        .report-meta { text-align: center; font-size: 10px; color: #333; margin-bottom: 12px; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.data th { background: #27a080; color: #fff; text-align: left; padding: 6px 8px; font-size: 9.5px; }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 9.5px; }
        table.data td.photo img { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; }

        .footer-row { position: fixed; bottom: 0; left: 0; right: 0; font-size: 8px; color: #555; }
        .footer-row .left, .footer-row .right { display: inline-block; }
        .footer-row .right { float: right; }
    </style>
</head>
<body>
    <div class="school-name">{{ $school?->name }}</div>
    @if ($school?->address)
        <div class="school-addr">{{ $school->address }}</div>
    @endif
    <div class="divider"></div>
