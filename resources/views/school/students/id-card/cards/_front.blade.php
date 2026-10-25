@php($forPdf = $forPdf ?? false)
@php($photoSrc = $student->photo ? (($forPdf ? public_path('storage/'.$student->photo) : asset('storage/'.$student->photo))) : null)

@if ($template == 1)
    <div class="id-card tpl1">
        <div class="strip"><span>STUDENT ID CARD</span></div>
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
                <div class="sname-student">{{ $student->name }}</div>
                <div class="info-row"><span class="lbl">ID</span>: {{ $student->admission_number }}</div>
                <div class="info-row"><span class="lbl">Class</span>: {{ $student->schoolClass?->name }}</div>
                <div class="info-row"><span class="lbl">Section</span>: {{ $student->section?->name }}</div>
                <div class="info-row"><span class="lbl">Group</span>: {{ $student->group_name }}</div>
                <div class="info-row"><span class="lbl">Roll</span>: {{ $student->roll }}</div>
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
        <div class="sname-student">{{ $student->name }}</div>
        <div class="info-list">
            <div class="info-row"><span class="lbl">Student ID</span>: {{ $student->admission_number }}</div>
            <div class="info-row"><span class="lbl">Father's Name</span>: {{ $student->father_name ?? '-' }}</div>
            <div class="info-row"><span class="lbl">Mother's Name</span>: {{ $student->mother_name ?? '-' }}</div>
            <div class="info-row"><span class="lbl">Blood Group</span>: {{ $student->blood_group ?? '-' }}</div>
            <div class="info-row"><span class="lbl">Mobile</span>: {{ $student->guardian_phone }}</div>
        </div>
    </div>
@endif
