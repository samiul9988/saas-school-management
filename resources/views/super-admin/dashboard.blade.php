<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Super Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Schools</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_schools'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Active Schools</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['active_schools'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Trial Schools</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $stats['trial_schools'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Expired Schools</div>
                    <div class="text-3xl font-bold text-red-600">{{ $stats['expired_schools'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Monthly Revenue</div>
                    <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['monthly_revenue'], 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Pending Payments</div>
                    <div class="text-3xl font-bold text-orange-600">{{ $stats['pending_payments'] }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">Recent Schools</h3>
                    <a href="{{ route('super-admin.schools.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                </div>
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b text-gray-500">
                            <th class="py-2">Name</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Plan</th>
                            <th class="py-2">Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentSchools as $school)
                            <tr class="border-b">
                                <td class="py-2">
                                    <a href="{{ route('super-admin.schools.show', $school) }}" class="text-indigo-600 hover:underline">{{ $school->name }}</a>
                                </td>
                                <td class="py-2 capitalize">{{ $school->status }}</td>
                                <td class="py-2">{{ $school->plan?->name ?? '—' }}</td>
                                <td class="py-2">{{ $school->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-400">No schools yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
