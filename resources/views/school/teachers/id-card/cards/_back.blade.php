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
        @php($__idSignature = \App\Models\Signature::activeForPosition('HR ID Card'))
        @if ($__idSignature && $__idSignature->imageUrl())
            <img src="{{ $forPdf ?? false ? public_path('storage/'.$__idSignature->image_path) : $__idSignature->imageUrl() }}" alt="" style="height: 22px; display: block; margin: 0 auto;">
        @else
            <div class="line"></div>
        @endif
        Authorised Signature
    </div>
</div>
