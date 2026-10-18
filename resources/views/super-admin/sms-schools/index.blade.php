<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('School SMS Wallets') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">School</th>
                            <th class="py-3 px-4">Balance</th>
                            <th class="py-3 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schools as $school)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $school->name }}</td>
                                <td class="py-3 px-4">{{ number_format($school->sms_wallet_balance) }}</td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('super-admin.sms-schools.show', $school) }}" class="text-indigo-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 px-4 text-center text-gray-400">No schools yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
