@php($schoolLogo = $school->logo ?? null)
<div class="mb-3 relative" style="font-family: serif; background:#fffbeb; border: 3px double #92400e; border-radius: 4px; padding: 10px 70px 10px 46px; position: relative; min-height: 130px;">
    <div class="absolute" style="position:absolute; top:8px; left:8px; width:34px; height:34px; border-radius:50%; overflow:hidden; border:1px solid #d6a85c; background:#fef3c7; display:flex; align-items:center; justify-content:center;">
        @if ($schoolLogo)
            <img src="{{ Storage::url($schoolLogo) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
        @else
            <i class="bi bi-mortarboard-fill" style="font-size:16px;color:#92400e;"></i>
        @endif
    </div>

    <div class="absolute" style="position:absolute; top:8px; right:8px; width:30px; height:30px; border:1px solid #d6a85c; background:#fef3c7; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-person-fill" style="font-size:14px;color:#b45309;"></i>
    </div>
    <div class="absolute" style="position:absolute; bottom:28px; right:8px; width:30px; height:30px;">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(30)->margin(0)->generate('PREVIEW') !!}
    </div>

    <div class="text-center font-semibold text-[10px]" style="color:#78350f;">{{ $school->name ?? 'Ibrahim Memorial Shiksha Niketan' }}</div>
    <div class="text-center text-[8px]" style="color:#92400e;">Upazila : Birganj, District : Dinajpur</div>
    <div class="text-center mt-1">
        <span class="inline-block px-3 py-0.5 rounded text-[8px]" style="background:#92400e;color:#fff8e1;">ADMIT CARD</span>
    </div>

    <table class="w-full mt-2 text-[8px]">
        <tr><td style="color:#78350f;width:62px;" class="pr-1">Student Name</td><td>: Afiya Ayesha Jaman</td></tr>
        <tr><td style="color:#78350f;" class="pr-1">Student ID</td><td>: 1001</td></tr>
        <tr><td style="color:#78350f;" class="pr-1">Father's Name</td><td>: Md. Ashrafujjaman Arif</td></tr>
        <tr><td style="color:#78350f;" class="pr-1">Mother's Name</td><td>: Monalisa Sharmin</td></tr>
        <tr><td style="color:#78350f;" class="pr-1">Date of Birth</td><td>: 2017-12-24</td></tr>
        <tr><td style="color:#78350f;" class="pr-1">Mobile No.</td><td>: 01714624263</td></tr>
        <tr><td style="color:#78350f;" class="pr-1">Class / Section</td><td>: Play &mdash; Lal</td></tr>
        <tr><td style="color:#78350f;" class="pr-1">Roll / Year</td><td>: 1 / 2026</td></tr>
    </table>

    <div class="flex justify-between mt-4 text-[8px]" style="padding:0 4px;">
        <span style="display:inline-block;width:70px;border-top:1px solid #78350f;padding-top:2px;color:#78350f;text-align:center;">Head Accountant</span>
        <span style="display:inline-block;width:70px;border-top:1px solid #78350f;padding-top:2px;color:#78350f;text-align:center;">Headmaster</span>
    </div>
</div>
