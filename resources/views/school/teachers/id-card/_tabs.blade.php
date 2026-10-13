@php($active = $active ?? 'all')

<div class="flex gap-2 bg-[#d6f0fb] rounded-lg p-2">
    <a href="{{ route('school.teachers.id-card.index') }}"
       class="px-4 py-2 rounded-md text-sm font-semibold {{ $active === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
        All HR
    </a>
    <a href="{{ route('school.teachers.id-card.single') }}"
       class="px-4 py-2 rounded-md text-sm font-semibold {{ $active === 'single' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
        Single HR
    </a>
    <span class="px-4 py-2 rounded-md text-sm font-semibold bg-gray-200 text-gray-400 cursor-not-allowed" title="Coming soon">
        New Templates
    </span>
</div>
