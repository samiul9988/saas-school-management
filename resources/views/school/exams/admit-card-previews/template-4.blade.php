@php($schoolLogo = $school->logo ?? null)
<div class="mb-3" style="font-family: sans-serif; border: 1px solid #92400e; border-radius: 2px; padding: 8px;">
    <div class="flex items-start gap-2">
        <div class="flex-shrink-0" style="width:28px; height:28px; border-radius:50%; overflow:hidden; border:1px solid #d6a85c; background:#fef3c7; display:flex; align-items:center; justify-content:center;">
            @if ($schoolLogo)
                <img src="{{ Storage::url($schoolLogo) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
            @else
                <i class="bi bi-mortarboard-fill" style="font-size:13px;color:#92400e;"></i>
            @endif
        </div>

        <div class="flex-grow text-center" style="min-width:0;">
            <div class="font-semibold text-[9px] truncate">{{ $school->name ?? 'Bangladesh Bank Adarsha High School' }}</div>
            <div class="text-gray-500 text-[7px] truncate">{{ $school->address ?? 'Dhaka, Bangladesh' }}</div>
        </div>

        <div class="flex-shrink-0 text-center">
            <div style="width:20px; height:20px; border:1px solid #cbd5e1; background:#f8fafc; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-person-fill" style="font-size:11px;color:#64748b;"></i>
            </div>
            <div style="width:20px; height:20px; margin-top:2px;">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(20)->margin(0)->generate('PREVIEW') !!}
            </div>
        </div>
    </div>

    <div class="text-center mt-1">
        <span class="inline-block px-3 py-0.5 rounded text-[8px] font-bold" style="background:#f59e0b;color:#1e293b;">ADMIT CARD &mdash; 1ST SEMESTER-2026</span>
    </div>

    <table class="w-full mt-2" style="font-size:7.5px; border-collapse: collapse;">
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Student Name</td><td style="padding-right:6px;">: Khan</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Class</td><td>: Nine</td>
        </tr>
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Student ID</td><td style="padding-right:6px;">: 202318</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Section</td><td>: Padma</td>
        </tr>
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Father's Name</td><td style="padding-right:6px;">: Abu Bakar Siddique</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Roll</td><td>: 5</td>
        </tr>
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Mother's Name</td><td style="padding-right:6px;">: Nasreen Sultana</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Group</td><td>: Science</td>
        </tr>
        <tr>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Mobile No.</td><td style="padding-right:6px;">: 01725431947</td>
            <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Reg. No.</td><td>: N/A</td>
        </tr>
    </table>

    <div class="mt-1" style="font-size:7.5px;">
        <span class="font-semibold">Exam Routine:</span> 11-11 Bangla, 12-11 English&hellip;
    </div>

    <div class="flex justify-between mt-2" style="font-size:7.5px;">
        <span style="display:inline-block;width:65px;border-top:1px solid #333;padding-top:2px;color:#555;text-align:center;">Class Teacher</span>
        <span style="display:inline-block;width:65px;border-top:1px solid #333;padding-top:2px;color:#555;text-align:center;">Head Teacher</span>
    </div>
</div>
