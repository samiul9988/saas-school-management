<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Journal Entries') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.journal-entries.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ New Journal Entry</a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-4">Date</th>
                            <th class="py-2 px-4">Reference</th>
                            <th class="py-2 px-4">Description</th>
                            <th class="py-2 px-4">Amount</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $entry)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $entry->date->format('Y-m-d') }}</td>
                                <td class="py-2 px-4">{{ $entry->reference }}</td>
                                <td class="py-2 px-4">{{ $entry->description }}</td>
                                <td class="py-2 px-4">{{ number_format($entry->lines->sum('debit'), 2) }}</td>
                                <td class="py-2 px-4 capitalize">{{ $entry->status }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('school.journal-entries.show', $entry) }}" class="text-indigo-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 px-4 text-center text-gray-400">No journal entries yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $entries->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
