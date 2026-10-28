@php($schoolLogo = $school->logo ?? null)
<div class="mb-3" style="font-family: serif; background:#fffbeb; border: 3px double #92400e; border-radius: 4px; padding: 8px;">
    <div class="flex items-start gap-2">
        <div class="flex-shrink-0" style="width:28px; height:28px; border-radius:50%; overflow:hidden; border:1px solid #d6a85c; background:#fef3c7; display:flex; align-items:center; justify-content:center;">
            @if ($schoolLogo)
                <img src="{{ Storage::url($schoolLogo) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
            @else
                <i class="bi bi-mortarboard-fill" style="font-size:13px;color:#92400e;"></i>
            @endif
        </div>

        <div class="flex-grow text-center" style="min-width:0;">
            <div class="font-semibold text-[9px] truncate" style="color:#78350f;">{{ $school->name ?? 'Ibrahim Memorial Shiksha Niketan' }}</div>
            <div class="text-[7px] truncate" style="color:#92400e;">Upazila : Birganj, District : Dinajpur</div>
        </div>

        <div class="flex-shrink-0 text-center">
            <div style="width:20px; height:20px; border:1px solid #d6a85c; background:#fef3c7; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-person-fill" style="font-size:11px;color:#b45309;"></i>
            </div>
            <div style="width:20px; height:20px; margin-top:2px;">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(20)->margin(0)->generate('PREVIEW') !!}
            </div>
        </div>
    </div>

    <div class="text-center mt-1">
        <span class="inline-block px-3 py-0.5 rounded text-[8px]" style="background:#92400e;color:#fff8e1;">ADMIT CARD</span>
    </div>

    <table class="w-full mt-2" style="font-size:7.5px; border-collapse: collapse;">
        <tr>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Student Name</td><td style="padding-right:6px;">: Afiya Ayesha Jaman</td>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Class</td><td>: Play</td>
        </tr>
        <tr>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Student ID</td><td style="padding-right:6px;">: 1001</td>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Section</td><td>: Lal</td>
        </tr>
        <tr>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Father's Name</td><td style="padding-right:6px;">: Md. Ashrafujjaman</td>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Roll</td><td>: 1</td>
        </tr>
        <tr>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Mother's Name</td><td style="padding-right:6px;">: Monalisa Sharmin</td>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Group</td><td>: N/A</td>
        </tr>
        <tr>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Date of Birth</td><td style="padding-right:6px;">: 2017-12-24</td>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Reg. No.</td><td>: N/A</td>
        </tr>
        <tr>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Mobile No.</td><td style="padding-right:6px;">: 01714624263</td>
            <td style="color:#78350f;white-space:nowrap;padding-right:2px;">Year</td><td>: 2026</td>
        </tr>
    </table>

    <div class="flex justify-between mt-2" style="font-size:7.5px;">
        <span style="display:inline-block;width:65px;border-top:1px solid #78350f;padding-top:2px;color:#78350f;text-align:center;">Head Accountant</span>
        <span style="display:inline-block;width:65px;border-top:1px solid #78350f;padding-top:2px;color:#78350f;text-align:center;">Headmaster</span>
    </div>
</div>
