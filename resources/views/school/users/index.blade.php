<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('User List') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-4">Create User</h3>
                <form method="POST" action="{{ route('school.users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <x-input-label for="name" value="User Name" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name') }}" required />
                    </div>
                    <div>
                        <x-input-label for="phone" value="Mobile No." />
                        <x-text-input id="phone" name="phone" class="mt-1 block w-full" value="{{ old('phone') }}" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" required />
                    </div>
                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label value="Role" />
                        <div class="mt-1 flex gap-6">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="role" value="{{ \App\Models\User::ROLE_SCHOOL_ADMIN }}" checked> Admin
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="role" value="{{ \App\Models\User::ROLE_SCHOOL_OPERATOR }}"> Operator
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="role" value="{{ \App\Models\User::ROLE_SCHOOL_TEACHER_STAFF }}"> Teacher
                            </label>
                        </div>
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">User Name</th>
                            <th class="py-3 px-4">Mobile No.</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $user->name }}</td>
                                <td class="py-3 px-4">{{ $user->phone }}</td>
                                <td class="py-3 px-4">
                                    <span class="{{ $user->is_active ? 'text-green-600' : 'text-gray-400' }}">{{ $user->is_active ? 'Enable' : 'Disable' }}</span>
                                </td>
                                <td class="py-3 px-4">{{ $user->role }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <form method="POST" action="{{ route('school.users.toggle-status', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-indigo-600 hover:underline">{{ $user->is_active ? 'Disable' : 'Enable' }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('school.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 px-4 text-center text-gray-400">No users yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
