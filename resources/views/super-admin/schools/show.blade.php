<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $school->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 grid grid-cols-2 gap-4">
                <div><span class="text-gray-500 text-sm">Email</span><div>{{ $school->email ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Phone</span><div>{{ $school->phone ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Plan</span><div>{{ $school->plan?->name ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Status</span><div class="capitalize">{{ $school->status }}</div></div>
                <div><span class="text-gray-500 text-sm">Address</span><div>{{ $school->address ?? '—' }}</div></div>
                <div><span class="text-gray-500 text-sm">Registered</span><div>{{ $school->created_at->format('Y-m-d') }}</div></div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Users</h3>
                <table class="w-full text-sm text-left">
                    <thead><tr class="text-gray-500 border-b"><th class="py-2">Name</th><th class="py-2">Email</th><th class="py-2">Role</th></tr></thead>
                    <tbody>
                        @forelse ($school->users as $user)
                            <tr class="border-b"><td class="py-2">{{ $user->name }}</td><td class="py-2">{{ $user->email }}</td><td class="py-2">{{ $user->role }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-400">No users yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <a href="{{ route('super-admin.schools.index') }}" class="text-indigo-600 hover:underline">&larr; Back to schools</a>
        </div>
    </div>
</x-app-layout>
