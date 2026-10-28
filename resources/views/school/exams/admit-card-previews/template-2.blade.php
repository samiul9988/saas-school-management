@php($schoolLogo = $school->logo ?? null)
<div class="mb-3" style="font-family: sans-serif; border: 2px solid #2563eb; border-radius: 6px; padding: 8px;">
    <div class="flex items-start gap-2">
        <div class="flex-shrink-0" style="width:30px; height:30px; border-radius:50%; overflow:hidden; border:1px solid #93c5fd; background:#dbeafe; display:flex; align-items:center; justify-content:center;">
            @if ($schoolLogo)
                <img src="{{ Storage::url($schoolLogo) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
            @else
                <i class="bi bi-mortarboard-fill" style="font-size:14px;color:#1d4ed8;"></i>
            @endif
        </div>

        <div class="flex-grow text-center" style="min-width:0;">
            <div class="font-semibold text-[9px] text-blue-900 truncate">{{ $school->name ?? 'Ibrahim Memorial Shiksha Niketan' }}</div>
            <div class="text-gray-500 text-[7px] truncate">Upazila : Birganj, District : Dinajpur</div>
        </div>

        <div class="flex-shrink-0 text-center">
            <div style="width:22px; height:22px; border:1px solid #93c5fd; background:#eff6ff; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-person-fill" style="font-size:12px;color:#60a5fa;"></i>
            </div>
            <div style="width:22px; height:22px; margin-top:2px;">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(22)->margin(0)->generate('PREVIEW') !!}
            </div>
        </div>
    </div>

    <div class="text-center mt-1">
        <span class="inline-block bg-blue-600 text-white px-3 py-0.5 rounded-full text-[8px]">ADMIT CARD &mdash; Annual-2026</span>
    </div>

    <table class="w-full mt-2 text-[8px]">
        <tr><td class="text-gray-500 pr-1" style="width:62px;">Student Name</td><td>: Afiya Ayesha Jaman</td></tr>
        <tr><td class="text-gray-500 pr-1">Student ID</td><td>: 1001</td></tr>
        <tr><td class="text-gray-500 pr-1">Father's Name</td><td>: Md. Ashrafujjaman Arif</td></tr>
        <tr><td class="text-gray-500 pr-1">Mother's Name</td><td>: Monalisa Sharmin</td></tr>
        <tr><td class="text-gray-500 pr-1">Date of Birth</td><td>: 2017-12-24</td></tr>
        <tr><td class="text-gray-500 pr-1">Mobile No.</td><td>: 01714624263</td></tr>
        <tr><td class="text-gray-500 pr-1">Class / Section</td><td>: Play &mdash; Lal</td></tr>
        <tr><td class="text-gray-500 pr-1">Roll / Year</td><td>: 1 / 2026</td></tr>
    </table>

    <div class="mt-2 text-[8px]">
        <span class="font-semibold text-gray-600">Subjects &amp; codes:</span> N/A
    </div>

    <div class="flex justify-between mt-3 text-[8px]">
        <span style="display:inline-block;width:70px;border-top:1px solid #333;padding-top:2px;color:#555;text-align:center;">Head Accountant</span>
        <span style="display:inline-block;width:70px;border-top:1px solid #333;padding-top:2px;color:#555;text-align:center;">Headmaster</span>
    </div>
</div>
