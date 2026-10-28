@php($schoolLogo = $school->logo ?? null)
<div class="mb-3 relative" style="font-family: sans-serif; border: 1px solid #92400e; border-radius: 2px; padding: 10px 70px 10px 46px; position: relative; min-height: 150px;">
    <div class="absolute" style="position:absolute; top:8px; left:8px; width:34px; height:34px; border-radius:50%; overflow:hidden; border:1px solid #d6a85c; background:#fef3c7; display:flex; align-items:center; justify-content:center;">
        @if ($schoolLogo)
            <img src="{{ Storage::url($schoolLogo) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
        @else
            <i class="bi bi-mortarboard-fill" style="font-size:16px;color:#92400e;"></i>
        @endif
    </div>

    <div class="absolute" style="position:absolute; top:8px; right:8px; width:30px; height:30px; border:1px solid #cbd5e1; background:#f8fafc; display:flex; align-items:center; justify-content:center;">
        <i class="bi bi-person-fill" style="font-size:14px;color:#64748b;"></i>
    </div>
    <div class="absolute" style="position:absolute; top:42px; right:8px; width:30px; height:30px;">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(30)->margin(0)->generate('PREVIEW') !!}
    </div>

    <div class="text-center font-semibold text-[10px]">{{ $school->name ?? 'Bangladesh Bank Adarsha High School' }}</div>
    <div class="text-center text-gray-500 text-[8px]">{{ $school->address ?? 'Dhaka, Bangladesh' }}</div>
    <div class="text-center mt-1">
        <span class="inline-block px-3 py-0.5 rounded text-[8px] font-bold" style="background:#f59e0b;color:#1e293b;">ADMIT CARD &mdash; 1ST SEMESTER-2026</span>
    </div>

    <table class="w-full mt-2 text-[8px]">
        <tr><td class="text-gray-500 pr-1" style="width:62px;">Student Name</td><td>: Khan</td></tr>
        <tr><td class="text-gray-500 pr-1">Student ID</td><td>: 202318</td></tr>
        <tr><td class="text-gray-500 pr-1">Father's Name</td><td>: Abu Bakar Siddique</td></tr>
        <tr><td class="text-gray-500 pr-1">Mother's Name</td><td>: Nasreen Sultana</td></tr>
        <tr><td class="text-gray-500 pr-1">Mobile No.</td><td>: 01725431947</td></tr>
        <tr><td class="text-gray-500 pr-1">Class / Section</td><td>: Nine &mdash; Padma</td></tr>
        <tr><td class="text-gray-500 pr-1">Roll / Group</td><td>: 5 / Science</td></tr>
    </table>

    <div class="mt-2 text-[8px]">
        <div class="font-semibold">Exam Routine</div>
        <table class="w-full" style="margin-top:2px;">
            <tr><td>11-11-2026&mdash;Sunday&mdash;Bangla 1st Paper</td><td>11-11-2026&mdash;Sunday&mdash;English 1st Paper</td></tr>
            <tr><td>12-11-2026&mdash;Monday&mdash;Bangla 2nd Paper</td><td>12-11-2026&mdash;Monday&mdash;English 2nd Paper</td></tr>
            <tr><td>13-11-2026&mdash;Tuesday&mdash;Math</td><td>13-11-2026&mdash;Tuesday&mdash;Science</td></tr>
            <tr><td>15-11-2026&mdash;Thursday&mdash;Religion</td><td>15-11-2026&mdash;Thursday&mdash;ICT</td></tr>
        </table>
    </div>

    <div class="flex justify-between mt-3 text-[8px]" style="padding:0 4px;">
        <span style="display:inline-block;width:70px;border-top:1px solid #333;padding-top:2px;color:#555;text-align:center;">Class Teacher</span>
        <span style="display:inline-block;width:70px;border-top:1px solid #333;padding-top:2px;color:#555;text-align:center;">Head Teacher</span>
    </div>
</div>
