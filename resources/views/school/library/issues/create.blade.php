<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Issue Book') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.book-issues.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="book_id" value="Book" />
                        <select id="book_id" name="book_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">— Select book —</option>
                            @foreach ($books as $book)
                                <option value="{{ $book->id }}">{{ $book->title }} ({{ $book->available_copies }} available)</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('book_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="student_id" value="Student (if borrower is a student)" />
                        <select id="student_id" name="student_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">—</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="teacher_id" value="Teacher (if borrower is a teacher)" />
                        <select id="teacher_id" name="teacher_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">—</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="issued_on" value="Issued On" />
                            <x-text-input id="issued_on" type="date" name="issued_on" class="mt-1 block w-full" value="{{ old('issued_on', now()->toDateString()) }}" required />
                        </div>
                        <div>
                            <x-input-label for="due_on" value="Due On" />
                            <x-text-input id="due_on" type="date" name="due_on" class="mt-1 block w-full" value="{{ old('due_on', now()->addDays(14)->toDateString()) }}" required />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Issue Book</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
