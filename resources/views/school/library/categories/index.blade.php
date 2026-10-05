<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Book Categories') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.book-categories.store') }}" class="flex gap-2">
                    @csrf
                    <x-text-input name="name" class="flex-1" placeholder="New category name" required />
                    <x-primary-button>Add</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50"><tr class="text-gray-500"><th class="py-3 px-4">Name</th><th class="py-3 px-4">Books</th><th class="py-3 px-4">Actions</th></tr></thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $category->name }}</td>
                                <td class="py-3 px-4">{{ $category->books_count }}</td>
                                <td class="py-3 px-4">
                                    <form action="{{ route('school.book-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
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

            <a href="{{ route('school.books.index') }}" class="text-indigo-600 hover:underline">&larr; Back to books</a>
        </div>
    </div>
</x-app-layout>
