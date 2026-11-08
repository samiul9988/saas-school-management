<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Users & Positions') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @include('school.users._tabs', ['active' => 'positions'])

            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Create Position</h3>
                <form method="POST" action="{{ route('school.positions.store') }}" class="flex gap-4 items-end">
                    @csrf
                    <div class="flex-1">
                        <x-input-label for="name" value="Position Name" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" placeholder="e.g. Accountant, Front Desk" required />
                    </div>
                    <x-primary-button>Save</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Position</th>
                            <th class="py-3 px-4">Modules Configured</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($positions as $position)
                            <tr class="border-t {{ $selectedPosition?->id === $position->id ? 'bg-blue-50' : '' }}">
                                <td class="py-3 px-4">{{ $position->name }}</td>
                                <td class="py-3 px-4">{{ $position->permissions_count }} / {{ count($modules) }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.positions.index', ['position_id' => $position->id]) }}" class="text-indigo-600 hover:underline">Set Permissions</a>
                                    <form method="POST" action="{{ route('school.positions.destroy', $position) }}" class="inline" onsubmit="return confirm('Delete this position? Sub users assigned to it will lose module access until reassigned.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 px-4 text-center text-gray-400">No positions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($selectedPosition)
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 pb-0">
                        <h3 class="font-semibold">Module Permissions for "{{ $selectedPosition->name }}"</h3>
                        <p class="text-sm text-gray-500">Whichever module you grant here, a user with this position will only see and work with that module's features.</p>
                    </div>
                    <form method="POST" action="{{ route('school.positions.permissions.update', $selectedPosition) }}">
                        @csrf
                        @method('PUT')
                        <table class="w-full text-sm text-left mt-4">
                            <thead class="bg-blue-100">
                                <tr class="text-gray-700">
                                    <th class="py-2 px-4">Module</th>
                                    <th class="py-2 px-4 text-center">View</th>
                                    <th class="py-2 px-4 text-center">Create</th>
                                    <th class="py-2 px-4 text-center">Edit</th>
                                    <th class="py-2 px-4 text-center">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modules as $moduleKey => $label)
                                    @php($existing = $permissionsByModule[$moduleKey] ?? null)
                                    <tr class="border-t">
                                        <td class="py-2 px-4">{{ $label }}</td>
                                        @foreach (['can_view' => 'View', 'can_create' => 'Create', 'can_edit' => 'Edit', 'can_delete' => 'Delete'] as $field => $fieldLabel)
                                            <td class="py-2 px-4 text-center">
                                                <input type="checkbox"
                                                       name="modules[{{ $moduleKey }}][{{ $field }}]"
                                                       value="1"
                                                       @checked($existing?->{$field})>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="p-4 flex justify-end">
                            <x-primary-button>Save Permissions</x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
