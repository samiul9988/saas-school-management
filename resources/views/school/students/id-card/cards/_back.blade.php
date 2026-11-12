@php($forPdf = $forPdf ?? false)
@if ($template == 1)
    <div class="id-card id-card-back">
        <div class="tag">IF FOUND, PLEASE RETURN TO</div>
        <div class="seal-big">
            @if ($school->logo)
                <img src="{{ $forPdf ? public_path('storage/'.$school->logo) : asset('storage/'.$school->logo) }}" alt="">
            @endif
        </div>
        <div class="sname">{{ $school->name }}</div>
        @if ($school->address)
            <div class="saddr">{{ $school->address }}</div>
        @endif
        @if ($school->email)
            <div class="sphone">Email: {{ $school->email }}</div>
        @endif
        @if ($school->phone)
            <div class="sphone">Official Mobile No: {{ $school->phone }}</div>
        @endif
        <div class="qr">
            <img src="data:image/png;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(90)->margin(0)->generate('Student:'.($student->id ?? 0).'|ID:'.($student->admission_number ?? '').'|'.($student->name ?? ''))) }}" alt="QR">
        </div>
        <div class="sign">
            @php($__idSignature = \App\Models\Signature::activeForPosition('Student ID Card'))
            @if ($__idSignature && $__idSignature->imageUrl())
                <img src="{{ $forPdf ? public_path('storage/'.$__idSignature->image_path) : $__idSignature->imageUrl() }}" alt="" style="height: 22px; display: block; margin: 0 auto;">
            @else
                <div class="line"></div>
            @endif
            Authorised Signature
        </div>
    </div>
@else
    <div class="id-card tpl2-back">
        <div class="inner">
            <div class="tag">If Found, Please Return to</div>
            <div class="seal-big">
                @if ($school->logo)
                    <img src="{{ $forPdf ? public_path('storage/'.$school->logo) : asset('storage/'.$school->logo) }}" alt="">
                @endif
            </div>
            <div class="sname">{{ $school->name }}</div>
            @if ($school->address)
                <div class="saddr">{{ $school->address }}</div>
            @endif
            @if ($school->email)
                <div class="sphone">Email: {{ $school->email }}</div>
            @endif
            @if ($school->phone)
                <div class="sphone">Official Mobile No: {{ $school->phone }}</div>
            @endif
            <div class="qr-cell">
                <img src="data:image/png;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(90)->margin(0)->generate('Student:'.($student->id ?? 0).'|ID:'.($student->admission_number ?? '').'|'.($student->name ?? ''))) }}" alt="QR">
            </div>
            <div class="validity-cell">Validity<br>{{ now()->year }} - {{ now()->year + 4 }}</div>
        </div>
    </div>
@endif
