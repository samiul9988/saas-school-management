@php($schoolLogo = $school->logo ?? null)
<div class="mb-3" style="font-family: sans-serif; border: 2px solid #334155; border-radius: 4px; padding: 6px; background:#f1f5f9;">
    <div style="border: 1px solid #334155; background:#fff; padding: 8px;">
        <div class="flex items-start gap-2">
            <div class="flex-shrink-0" style="width:28px; height:28px; border-radius:50%; overflow:hidden; border:1px solid #94a3b8; background:#e2e8f0; display:flex; align-items:center; justify-content:center;">
                @if ($schoolLogo)
                    <img src="{{ Storage::url($schoolLogo) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                @else
                    <i class="bi bi-mortarboard-fill" style="font-size:13px;color:#475569;"></i>
                @endif
            </div>

            <div class="flex-grow text-center" style="min-width:0;">
                <div class="font-semibold text-[9px] truncate">{{ $school->name ?? 'Demo International School' }}</div>
                <div class="text-gray-500 text-[7px] truncate">{{ $school->address ?? 'Address line' }}</div>
            </div>

            <div class="flex-shrink-0" style="width:24px; height:24px;">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(24)->margin(0)->generate('PREVIEW') !!}
            </div>
        </div>

        <div class="text-center mt-1">
            <span class="inline-block px-3 py-0.5 rounded text-[8px] font-bold" style="background:#f59e0b;color:#1e293b;">ADMIT CARD</span>
        </div>
        <div class="text-center font-bold mt-1 text-[8px]">1ST SEMESTER-2026</div>

        <table class="w-full mt-2" style="font-size:7.5px; border-collapse: collapse;">
            <tr>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Student Name</td><td style="padding-right:6px;">: Shahidul Islam</td>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Exam</td><td>: 1st Sem.</td>
            </tr>
            <tr>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Student ID</td><td style="padding-right:6px;">: 1034</td>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Group</td><td>: Common</td>
            </tr>
            <tr>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Father's Name</td><td style="padding-right:6px;">: Abdul Karim</td>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Class</td><td>: Play</td>
            </tr>
            <tr>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Mother's Name</td><td style="padding-right:6px;">: Rahima Begum</td>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Section</td><td>: Night Mango</td>
            </tr>
            <tr>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Contact No.</td><td style="padding-right:6px;">: 01861006000</td>
                <td class="text-gray-500" style="white-space:nowrap;padding-right:2px;">Roll</td><td>: 1</td>
            </tr>
        </table>

        <div class="mt-1" style="font-size:7.5px;">
            <span class="font-semibold text-gray-600">Exam Routine:</span> N/A
        </div>

        <div class="text-right mt-2" style="font-size:7.5px;">
            <span style="display:inline-block;width:70px;border-top:1px solid #333;padding-top:2px;color:#555;">Authorized Signature</span>
        </div>
    </div>
</div>
