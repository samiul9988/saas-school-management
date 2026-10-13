@php($forPdf = $forPdf ?? false)
@php($photoSrc = $teacher->photo ? (($forPdf ? public_path('storage/'.$teacher->photo) : asset('storage/'.$teacher->photo))) : null)

@if ($template == 1)
    <div class="id-card tpl1">
        <div class="strip"><span>STAFF ID CARD</span></div>
        <div class="head">
            <div class="sname">{{ $school->name }}</div>
            @if ($school->address)
                <div class="saddr">{{ $school->address }}</div>
            @endif
        </div>
        <div class="body">
            <div class="photo-cell">
                @if ($photoSrc)
                    <img src="{{ $photoSrc }}" class="photo" alt="">
                @else
                    <div class="photo"></div>
                @endif
            </div>
            <div class="info-cell">
                <div class="sname-student">{{ $teacher->name }}</div>
                <div class="info-row"><span class="lbl">ID</span>: {{ $teacher->staff_serial ?: $teacher->id }}</div>
                <div class="info-row"><span class="lbl">Designation</span>: {{ $teacher->designation }}</div>
                <div class="info-row"><span class="lbl">Mobile</span>: {{ $teacher->phone }}</div>
                <div class="info-row"><span class="lbl">Email</span>: {{ $teacher->email }}</div>
                <div class="info-row"><span class="lbl">Blood Group</span>: {{ $teacher->blood_group }}</div>
            </div>
        </div>
    </div>
@else
    <div class="id-card tpl2">
        <div class="seal"></div>
        <div class="photo-wrap">
            @if ($photoSrc)
                <img src="{{ $photoSrc }}" class="photo" alt="">
            @else
                <div class="photo"></div>
            @endif
        </div>
        <div class="sname-student">{{ $teacher->name }}</div>
        <div class="info-list">
            <div class="info-row"><span class="lbl">ID No</span>: {{ $teacher->staff_serial ?: $teacher->id }}</div>
            <div class="info-row"><span class="lbl">Designation</span>: {{ $teacher->designation }}</div>
            <div class="info-row"><span class="lbl">Mobile</span>: {{ $teacher->phone }}</div>
            <div class="info-row"><span class="lbl">Email</span>: {{ $teacher->email }}</div>
            <div class="info-row"><span class="lbl">Blood Group</span>: {{ $teacher->blood_group }}</div>
        </div>
    </div>
@endif
