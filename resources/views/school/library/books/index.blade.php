<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Library Books') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-between">
                <div class="space-x-4 text-sm">
                    <a href="{{ route('school.book-categories.index') }}" class="text-indigo-600 hover:underline">Categories</a>
                    <a href="{{ route('school.book-issues.index') }}" class="text-indigo-600 hover:underline">Issued Books</a>
                </div>
                <a href="{{ route('school.books.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Book
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Title</th>
                            <th class="py-3 px-4">Author</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Available / Total</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $book->title }}</td>
                                <td class="py-3 px-4">{{ $book->author ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $book->category?->name ?? '—' }}</td>
                                <td class="py-3 px-4">{{ $book->available_copies }} / {{ $book->total_copies }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.books.edit', $book) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('school.books.destroy', $book) }}" method="POST" class="inline" onsubmit="return confirm('Delete this book?')">
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

            {{ $books->links() }}
        </div>
    </div>
</x-app-layout>
