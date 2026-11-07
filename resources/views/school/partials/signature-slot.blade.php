@php
    $__signature = \App\Models\Signature::activeForPosition($position);
@endphp
@if ($__signature && $__signature->imageUrl())
    <img src="{{ $__signature->imageUrl() }}" alt="{{ $__signature->title }}" style="height: 30px; display: block; margin: 0 auto;">
@endif
<span class="line">{{ $label ?? $__signature?->title ?? 'Authorized Signature' }}</span>
