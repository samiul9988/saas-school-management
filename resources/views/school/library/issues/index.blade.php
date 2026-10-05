<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Issued Books') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.book-issues.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Issue Book
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Book</th>
                            <th class="py-3 px-4">Borrower</th>
                            <th class="py-3 px-4">Issued</th>
                            <th class="py-3 px-4">Due</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($issues as $issue)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $issue->book->title }}</td>
                                <td class="py-3 px-4">{{ $issue->student?->name ?? $issue->teacher?->name ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $issue->issued_on->format('Y-m-d') }}</td>
                                <td class="py-3 px-4">{{ $issue->due_on->format('Y-m-d') }}</td>
                                <td class="py-3 px-4">
                                    @if ($issue->returned_on)
                                        <span class="text-green-600">Returned</span>
                                    @elseif ($issue->isOverdue())
                                        <span class="text-red-600">Overdue</span>
                                    @else
                                        <span class="text-yellow-600">Issued</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @unless ($issue->returned_on)
                                        <form action="{{ route('school.book-issues.return', $issue) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:underline">Mark Returned</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-500">Fine: {{ number_format($issue->fine_amount, 2) }}</span>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $issues->links() }}
        </div>
    </div>
</x-app-layout>
