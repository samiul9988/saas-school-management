<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Inventory') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="flex justify-between">
                <a href="{{ route('school.inventory-categories.index') }}" class="text-indigo-600 hover:underline text-sm">Categories</a>
                <a href="{{ route('school.inventory-items.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Item
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Quantity</th>
                            <th class="py-3 px-4">Reorder Level</th>
                            <th class="py-3 px-4">Stock In/Out</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $item->name }}</td>
                                <td class="py-3 px-4">{{ $item->category?->name ?? '—' }}</td>
                                <td class="py-3 px-4 {{ $item->isLowStock() ? 'text-red-600 font-semibold' : '' }}">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="py-3 px-4">{{ $item->reorder_level }}</td>
                                <td class="py-3 px-4">
                                    <form action="{{ route('school.inventory-items.transactions.store', $item) }}" method="POST" class="flex gap-1">
                                        @csrf
                                        <select name="type" class="border-gray-300 rounded-md text-xs">
                                            <option value="in">In</option>
                                            <option value="out">Out</option>
                                        </select>
                                        <input type="number" name="quantity" min="1" class="w-16 border-gray-300 rounded-md text-xs" required>
                                        <input type="hidden" name="date" value="{{ now()->toDateString() }}">
                                        <button type="submit" class="text-indigo-600 hover:underline text-xs">Apply</button>
                                    </form>
                                </td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.inventory-items.edit', $item) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('school.inventory-items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item?')">
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
