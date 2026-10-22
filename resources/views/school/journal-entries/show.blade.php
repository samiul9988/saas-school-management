<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Journal Entry') }} #{{ $journalEntry->id }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-2">
                <p><span class="text-gray-500">Date:</span> {{ $journalEntry->date->format('Y-m-d') }}</p>
                <p><span class="text-gray-500">Reference:</span> {{ $journalEntry->reference }}</p>
                <p><span class="text-gray-500">Description:</span> {{ $journalEntry->description }}</p>
                <p><span class="text-gray-500">Status:</span> <span class="capitalize">{{ $journalEntry->status }}</span></p>
                @if ($journalEntry->createdBy)
                    <p><span class="text-gray-500">Created By:</span> {{ $journalEntry->createdBy->name }}</p>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-4">Account</th>
                            <th class="py-2 px-4">Debit</th>
                            <th class="py-2 px-4">Credit</th>
                            <th class="py-2 px-4">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($journalEntry->lines as $line)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $line->account->code }} - {{ $line->account->name }}</td>
                                <td class="py-2 px-4">{{ $line->debit > 0 ? number_format($line->debit, 2) : '' }}</td>
                                <td class="py-2 px-4">{{ $line->credit > 0 ? number_format($line->credit, 2) : '' }}</td>
                                <td class="py-2 px-4 text-gray-500">{{ $line->note }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t font-semibold">
                            <td class="py-2 px-4">Total</td>
                            <td class="py-2 px-4">{{ number_format($journalEntry->lines->sum('debit'), 2) }}</td>
                            <td class="py-2 px-4">{{ number_format($journalEntry->lines->sum('credit'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($journalEntry->status === 'posted')
                <form method="POST" action="{{ route('school.journal-entries.reverse', $journalEntry) }}" onsubmit="return confirm('Reverse this journal entry? This creates an offsetting entry and marks this one as reversed.')">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Reverse Entry</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
