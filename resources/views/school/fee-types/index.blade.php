<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Fee Types') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.fee-types.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Fee Type
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Default Amount</th>
                            <th class="py-3 px-4">Frequency</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($feeTypes as $feeType)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $feeType->name }}</td>
                                <td class="py-3 px-4">{{ number_format($feeType->default_amount, 2) }}</td>
                                <td class="py-3 px-4">{{ ucfirst(str_replace('_', ' ', $feeType->frequency)) }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.fee-types.edit', $feeType) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('school.fee-types.destroy', $feeType) }}" method="POST" class="inline" onsubmit="return confirm('Delete this fee type?')">
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
        </div>
    </div>
</x-app-layout>
