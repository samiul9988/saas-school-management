<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1f2937; margin: 0; }
        .frame { border: 3px double #333; margin: 16px; padding: 30px 40px; position: relative; min-height: 520px; }
        .corner { position: absolute; width: 26px; height: 26px; border: 2px solid #333; }
        .corner.tl { top: 8px; left: 8px; border-right: none; border-bottom: none; }
        .corner.tr { top: 8px; right: 8px; border-left: none; border-bottom: none; }
        .corner.bl { bottom: 8px; left: 8px; border-right: none; border-top: none; }
        .corner.br { bottom: 8px; right: 8px; border-left: none; border-top: none; }

        .icon { text-align: center; }
        .icon-box { display: inline-block; width: 40px; height: 40px; border-radius: 6px; background: #fecaca; border: 2px solid #991b1b; }

        .school-name { text-align: center; font-size: 22px; font-weight: bold; margin-top: 10px; }
        .school-addr { text-align: center; font-size: 13px; color: #444; margin-top: 2px; }

        table.head-row { width: 100%; margin-top: 26px; }
        table.head-row td { vertical-align: middle; }
        .sn-box, .issue-box { background: #f3f4f6; padding: 6px 10px; display: inline-block; font-size: 10px; }
        .title-box { border: 1px solid #333; padding: 8px 20px; text-align: center; font-size: 18px; font-weight: bold; color: #7c2d12; }

        .body-text { font-size: 13px; line-height: 2.1; margin-top: 30px; }
        .blank { display: inline-block; border-bottom: 1px dotted #333; min-width: 120px; text-align: center; font-weight: bold; font-style: italic; padding: 0 4px; }

        .sign-area { margin-top: 90px; text-align: right; }
        .sign-area .line { display: inline-block; width: 180px; border-top: 1px solid #333; margin-bottom: 4px; }
        .sign-area .head-label { font-weight: bold; }
        .sign-area .school-sub { color: #7c2d12; }

        .footer-credit { position: absolute; bottom: 14px; left: 40px; font-size: 8px; color: #16a34a; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="corner tl"></div>
        <div class="corner tr"></div>
        <div class="corner bl"></div>
        <div class="corner br"></div>

        <div class="icon"><div class="icon-box"></div></div>
        <div class="school-name">{{ $school?->name }}</div>
        @if ($school?->address)
            <div class="school-addr">{{ $school->address }}</div>
        @endif

        <table class="head-row">
            <tr>
                <td style="width: 33%;">
                    <div class="sn-box">SN:<br>{{ $testimonial->serial }}</div>
                </td>
                <td style="width: 34%; text-align: center;">
                    <div class="title-box">Testimonial</div>
                </td>
                <td style="width: 33%; text-align: right;">
                    <div class="issue-box">Issue Date:<br>{{ $testimonial->issue_date?->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="body-text">
            This is to certify that <span class="blank">{{ $testimonial->student_name }}</span>
            {{ $student?->gender === 'female' ? 'daughter' : 'son' }} of
            <span class="blank">{{ $testimonial->father_name ?: 'N/A' }}</span>
            and <span class="blank">{{ $testimonial->mother_name ?: 'N/A' }}</span>
            of <span class="blank">{{ $testimonial->address ?: 'N/A' }}</span>
            continued <span class="blank">{{ $testimonial->class_name ?: 'N/A' }}</span>
            bearing Roll No. <span class="blank">{{ $testimonial->roll ?: 'N/A' }}</span>.
            <br><br>
            He/She is Bangladeshi by birth. To the best of my knowledge he/she did not take part in any subversive activity against the discipline of state.
            <br><br>
            He/She has a good moral character. I wish him/her every success in life.
        </div>

        <div class="sign-area">
            <div><div class="line"></div></div>
            <div class="head-label">Head</div>
            <div class="school-sub">{{ $school?->name }}</div>
        </div>

        <div class="footer-credit">SaaS School Management :: Powered by SaaS School Management</div>
    </div>
</body>
</html>
