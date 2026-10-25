<div class="id-card id-card-back">
    <div class="tag">IF FOUND, PLEASE RETURN TO</div>
    <div class="seal-big"></div>
    <div class="sname">{{ $school->name }}</div>
    @if ($school->address)
        <div class="saddr">{{ $school->address }}</div>
    @endif
    @if ($school->phone)
        <div class="sphone">Official Mobile No: {{ $school->phone }}</div>
    @endif
    <div class="qr">
        <img src="data:image/png;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(90)->margin(0)->generate('Student:'.($student->id ?? 0).'|ID:'.($student->admission_number ?? '').'|'.($student->name ?? ''))) }}" alt="QR">
    </div>
    <div class="sign">
        <div class="line"></div>
        Authorised Signature
    </div>
</div>
