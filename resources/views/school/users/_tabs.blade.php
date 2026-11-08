@php($active = $active ?? 'users')
<div class="flex gap-2 border-b">
    <a href="{{ route('school.users.index') }}" class="px-4 py-2 text-sm font-semibold rounded-t-md {{ $active === 'users' ? 'bg-white border border-b-0 text-gray-800' : 'text-gray-500 hover:text-gray-700' }}">
        Sub Users
    </a>
    <a href="{{ route('school.positions.index') }}" class="px-4 py-2 text-sm font-semibold rounded-t-md {{ $active === 'positions' ? 'bg-white border border-b-0 text-gray-800' : 'text-gray-500 hover:text-gray-700' }}">
        Positions &amp; Permissions
    </a>
</div>
