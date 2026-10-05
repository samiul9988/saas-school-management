<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Income') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.incomes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Income
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incomes as $income)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $income->date->format('Y-m-d') }}</td>
                                <td class="py-3 px-4">{{ $income->category }}</td>
                                <td class="py-3 px-4">{{ number_format($income->amount, 2) }}</td>
                                <td class="py-3 px-4">{{ $income->description ?? '—' }}</td>
                                <td class="py-3 px-4">
                                    <form action="{{ route('school.incomes.destroy', $income) }}" method="POST" onsubmit="return confirm('Delete this income record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $incomes->links() }}
        </div>
    </div>
</x-app-layout>
