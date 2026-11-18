<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 8mm 10mm; }
        body { font-family: sans-serif; font-size: 9px; color: #1f2937; margin: 0; }
        .card { border: 2px solid #1f2937; padding: 1mm; height: 80mm; box-sizing: border-box; margin-bottom: 3mm; background: #1f2937; }
        .card .inner { border: 1px solid #b4802f; background: #fbf6e8; padding: 4mm; height: 100%; box-sizing: border-box; position: relative; }

        .logo { position: absolute; top: 4mm; left: 4mm; width: 17mm; height: 17mm; border-radius: 50%; overflow: hidden; border: 1px solid #b4802f; background: #fff; }
        .logo img { width: 100%; height: 100%; object-fit: cover; }

        .photo-box { position: absolute; top: 4mm; right: 4mm; width: 18mm; height: 21mm; border: 1px solid #b4802f; background: #fff; }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }

        .qr-box { position: absolute; top: 30mm; right: 4mm; width: 17mm; height: 17mm; }
        .qr-box img { width: 100%; height: 100%; }

        .header { text-align: center; padding: 0 22mm; }
        .header h2 { margin: 0; font-size: 13px; font-weight: bold; }
        .header .addr { font-size: 8.5px; color: #444; margin-top: 2px; }

        .badge-wrap { text-align: center; margin-top: 6px; }
        .badge { display: inline-block; background: #d9952f; color: #1f2937; font-weight: bold; padding: 3px 16px; border: 1px solid #1f2937; font-size: 10px; }
        .exam-title { text-align: center; font-weight: bold; margin-top: 5px; font-size: 9.5px; }

        table.fields { width: 100%; margin-top: 10px; border-collapse: collapse; }
        table.fields td { padding: 2px 4px; font-size: 8.5px; vertical-align: top; }
        table.fields .label { color: #333; width: 78px; white-space: nowrap; }

        table.sign-table { width: 100%; margin-top: 10mm; border-collapse: collapse; }
        table.sign-table td { text-align: center; vertical-align: bottom; }
        table.sign-table .spacer { width: 42%; }
        table.sign-table .slot { width: 29%; }
        .sign-table .line { display: inline-block; width: 100px; border-top: 1px solid #333; padding-top: 2px; font-size: 8px; color: #333; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    @foreach ($students->chunk(3) as $group)
        <div @if (! $loop->last) style="page-break-after: always;" @endif>
            @foreach ($group as $student)
                <div class="card">
                    <div class="inner">
                        <div class="logo">
                            @if ($school->logo)
                                <img src="{{ public_path('storage/'.$school->logo) }}">
                            @endif
                        </div>

                        <div class="photo-box">
                            @if ($student->photo)
                                <img src="{{ public_path('storage/'.$student->photo) }}">
                            @endif
                        </div>

                        <div class="qr-box"><img src="{{ $qrCodes[$student->id] ?? '' }}"></div>

                        <div class="header">
                            <h2>{{ strtoupper($school->name) }}</h2>
                            <div class="addr">{{ $school->address }}</div>
                        </div>

                        <div class="badge-wrap"><span class="badge">ADMIT CARD</span></div>
                        <div class="exam-title">{{ $exam->name }}@if ($exam->academicYear)-{{ $exam->academicYear->name }}@endif</div>

                        <table class="fields">
                            <tr>
                                <td class="label">Student Name</td><td>: {{ $student->name }}</td>
                                <td class="label">Class</td><td>: {{ $section->schoolClass?->name }}</td>
                            </tr>
                            <tr>
                                <td class="label">Student ID</td><td>: {{ $student->admission_number ?: $student->id }}</td>
                                <td class="label">Section</td><td>: {{ $section->name }}</td>
                            </tr>
                            <tr>
                                <td class="label">Father's Name</td><td>: {{ $student->father_name ?: 'N/A' }}</td>
                                <td class="label">Roll</td><td>: {{ $student->roll }}</td>
                            </tr>
                            <tr>
                                <td class="label">Mother's Name</td><td>: {{ $student->mother_name ?: 'N/A' }}</td>
                                <td class="label">Group</td><td>: {{ $student->group_name ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Date of Birth</td><td>: {{ $student->date_of_birth?->format('d M, Y') ?: 'N/A' }}</td>
                                <td class="label">Reg. No.</td><td>: {{ $student->admission_number ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Mobile No.</td><td>: {{ $student->guardian_phone ?: 'N/A' }}</td>
                                <td class="label">Year</td><td>: {{ $exam->academicYear?->name ?? $exam->starts_on?->format('Y') ?? now()->year }}</td>
                            </tr>
                        </table>

                        <table class="sign-table">
                            <tr>
                                <td class="spacer"></td>
                                <td class="slot">
                                    @include('school.partials.signature-slot', ['position' => 'Admit Card Left', 'label' => 'Class Teacher'])
                                </td>
                                <td class="slot">
                                    @include('school.partials.signature-slot', ['position' => 'Admit Card Right', 'label' => 'Head Teacher'])
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>
