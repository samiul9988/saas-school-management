@php($book = $book ?? null)

<div>
    <x-input-label for="title" value="Title" />
    <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title', $book?->title) }}" required />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>
<div>
    <x-input-label for="author" value="Author" />
    <x-text-input id="author" name="author" class="mt-1 block w-full" value="{{ old('author', $book?->author) }}" />
</div>
<div>
    <x-input-label for="publisher" value="Publisher" />
    <x-text-input id="publisher" name="publisher" class="mt-1 block w-full" value="{{ old('publisher', $book?->publisher) }}" />
</div>
<div>
    <x-input-label for="isbn" value="ISBN" />
    <x-text-input id="isbn" name="isbn" class="mt-1 block w-full" value="{{ old('isbn', $book?->isbn) }}" />
</div>
<div>
    <x-input-label for="book_category_id" value="Category" />
    <select id="book_category_id" name="book_category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">—</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('book_category_id', $book?->book_category_id) == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
</div>
<div>
    <x-input-label for="total_copies" value="Total Copies" />
    <x-text-input id="total_copies" type="number" name="total_copies" class="mt-1 block w-full" value="{{ old('total_copies', $book?->total_copies ?? 1) }}" required />
    @if ($book)
        <p class="text-xs text-gray-500 mt-1">Currently available: {{ $book->available_copies }}</p>
    @endif
</div>
