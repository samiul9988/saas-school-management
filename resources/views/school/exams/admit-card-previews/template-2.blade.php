@php($schoolLogo = $school->logo ?? null)
<div class="mb-3" style="font-family: sans-serif; border: 2px solid #2563eb; border-radius: 6px; padding: 8px;">
    <div class="flex items-start gap-2">
        <div class="flex-shrink-0" style="width:28px; height:28px; border-radius:50%; overflow:hidden; border:1px solid #93c5fd; background:#dbeafe; display:flex; align-items:center; justify-content:center;">
            @if ($schoolLogo)
                <img src="{{ Storage::url($schoolLogo) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
            @else
                <i class="bi bi-mortarboard-fill" style="font-size:13px;color:#1d4ed8;"></i>
            @endif
        </div>

        <div class="flex-grow text-center" style="min-width:0;">
            <div class="font-semibold text-[9px] text-blue-900 truncate">{{ $school->name ?? 'Ibrahim Memorial Shiksha Niketan' }}</div>
            <div class="text-gray-500 text-[7px] truncate">Upazila : Birganj, District : Dinajpur</div>
        </div>

        <div class="flex-shrink-0 text-center">
            <div style="width:20px; height:20px; border:1px solid #93c5fd; background:#eff6ff; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-person-fill" style="font-size:11px;color:#60a5fa;"></i>
            </div>
            <div style="width:20px; height:20px; margin-top:2px;">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(20)->margin(0)->generate('PREVIEW') !!}
            </div>
        </div>
    </div>

    <div class="text-center mt-1">
        <span class="inline-block bg-blue-600 text-white px-3 py-0.5 rounded-full text-[8px]">ADMIT CARD &mdash; Annual-2026</span>
    </div>

    <table class="w-full mt-2" style="font-size:7.5px; border-collapse: collapse;">
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Student Name</td><td style="padding-right:6px;">: Afiya Ayesha Jaman</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Class</td><td>: Play</td>
        </tr>
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Student ID</td><td style="padding-right:6px;">: 1001</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Section</td><td>: Lal</td>
        </tr>
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Father's Name</td><td style="padding-right:6px;">: Md. Ashrafujjaman</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Roll</td><td>: 1</td>
        </tr>
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Mother's Name</td><td style="padding-right:6px;">: Monalisa Sharmin</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Group</td><td>: N/A</td>
        </tr>
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Date of Birth</td><td style="padding-right:6px;">: 2017-12-24</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Reg. No.</td><td>: N/A</td>
        </tr>
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Mobile No.</td><td style="padding-right:6px;">: 01714624263</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Year</td><td>: 2026</td>
        </tr>
    </table>

    <div class="mt-1" style="font-size:7.5px;">
        <span class="font-semibold text-gray-600">Subjects &amp; codes:</span> 1. N/A 2. N/A 3. N/A 4. N/A
    </div>

    <div class="flex justify-between mt-2" style="font-size:7.5px;">
        <span style="display:inline-block;width:65px;border-top:1px solid #333;padding-top:2px;color:#555;text-align:center;">Head Accountant</span>
        <span style="display:inline-block;width:65px;border-top:1px solid #333;padding-top:2px;color:#555;text-align:center;">Headmaster</span>
    </div>
</div>
