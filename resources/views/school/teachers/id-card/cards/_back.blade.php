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
    <div class="sign">
        <div class="line"></div>
        Authorised Signature
    </div>
</div>
