<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Enter Marks') }} &mdash; {{ $examSubject->subject->name }} ({{ $examSubject->schoolClass->name }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">Full marks: {{ $examSubject->full_marks }} &middot; Pass marks: {{ $examSubject->pass_marks }}</p>

                <form method="POST" action="{{ route('school.marks.update', $examSubject) }}">
                    @csrf
                    @method('PUT')

                    <table class="w-full text-sm text-left mb-4">
                        <thead>
                            <tr class="text-gray-500 border-b">
                                <th class="py-2">Roll</th>
                                <th class="py-2">Name</th>
                                <th class="py-2">Marks</th>
                                <th class="py-2">Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                @php($mark = $existing->get($student->id))
                                <tr class="border-b">
                                    <td class="py-2">{{ $student->roll ?? '—' }}</td>
                                    <td class="py-2">{{ $student->name }}</td>
                                    <td class="py-2">
                                        <input type="number" step="0.01" min="0" max="{{ $examSubject->full_marks }}"
                                               name="marks[{{ $student->id }}]"
                                               value="{{ old('marks.'.$student->id, $mark?->marks_obtained) }}"
                                               class="w-24 border-gray-300 rounded-md shadow-sm text-sm" />
                                    </td>
                                    <td class="py-2">
                                        <input type="checkbox" name="absent[{{ $student->id }}]" value="1" @checked($mark?->is_absent) />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <x-primary-button>Save Marks</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
