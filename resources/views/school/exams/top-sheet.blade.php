<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Top Sheet') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6 print:hidden">
            <form method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="exam_id" value="Exam" />
                    <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($exams as $e)
                            <option value="{{ $e->id }}" @selected(request('exam_id') == $e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="school_class_id" value="Class" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" @selected(request('school_class_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="subject_id" value="Subject" />
                    <select id="subject_id" name="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($subjects as $s)
                            <option value="{{ $s->id }}" @selected(request('subject_id') == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-primary-button>Generate</x-primary-button>
                </div>
            </form>
        </div>

        @if ($students->isNotEmpty())
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-center mb-4 print:hidden">
                    <h3 class="font-semibold">
                        {{ $examSubject?->exam?->name }} &mdash; {{ $examSubject?->schoolClass?->name }} &mdash; {{ $examSubject?->subject?->name }}
                        (Full Marks: {{ $examSubject?->full_marks }})
                    </h3>
                    <button onclick="window.print()" class="text-indigo-600 hover:underline text-sm">Print</button>
                </div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-3">Roll</th>
                            <th class="py-2 px-3">Name</th>
                            <th class="py-2 px-3">Marks Obtained</th>
                            <th class="py-2 px-3">Signature</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr class="border-t">
                                <td class="py-3 px-3">{{ $student->roll }}</td>
                                <td class="py-3 px-3">{{ $student->name }}</td>
                                <td class="py-3 px-3" style="min-width: 120px;">&nbsp;</td>
                                <td class="py-3 px-3" style="min-width: 120px;">&nbsp;</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
