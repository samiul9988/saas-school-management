@php($activeTab = request('tab', 'exam'))
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Initial Exam Setup') }}</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="border-b flex gap-1 px-4 pt-2">
            @foreach (['exam' => 'Exam', 'subject' => 'Subject', 'short-code' => 'Exam Short Code', 'grade' => 'Exam Grade'] as $key => $label)
                <a href="{{ route('school.exams.settings', ['tab' => $key]) }}"
                   class="px-4 py-2 text-sm rounded-t-md {{ $activeTab === $key ? 'bg-white border border-b-0 text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="p-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
            @endif

            @if ($activeTab === 'exam')
                <p class="text-sm text-gray-500 mb-4">Manage exams from the full Exams page.</p>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50"><tr class="text-gray-500"><th class="py-2 px-3">Name</th><th class="py-2 px-3">Status</th><th class="py-2 px-3">Actions</th></tr></thead>
                    <tbody>
                        @foreach ($exams as $exam)
                            <tr class="border-t">
                                <td class="py-2 px-3">{{ $exam->name }}</td>
                                <td class="py-2 px-3 capitalize">{{ $exam->status }}</td>
                                <td class="py-2 px-3"><a href="{{ route('school.exams.edit', $exam) }}" class="text-indigo-600 hover:underline">Edit</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('school.exams.create') }}" class="inline-block mt-4 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">+ Add Exam</a>

            @elseif ($activeTab === 'subject')
                <p class="text-sm text-gray-500 mb-4">Manage subjects from the full Subjects page.</p>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50"><tr class="text-gray-500"><th class="py-2 px-3">Name</th><th class="py-2 px-3">Code</th><th class="py-2 px-3">Actions</th></tr></thead>
                    <tbody>
                        @foreach ($subjects as $subject)
                            <tr class="border-t">
                                <td class="py-2 px-3">{{ $subject->name }}</td>
                                <td class="py-2 px-3">{{ $subject->code ?? '—' }}</td>
                                <td class="py-2 px-3"><a href="{{ route('school.subjects.edit', $subject) }}" class="text-indigo-600 hover:underline">Edit</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('school.subjects.create') }}" class="inline-block mt-4 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">+ Add Subject</a>

            @elseif ($activeTab === 'short-code')
                <form method="POST" action="{{ route('school.exam-short-codes.store') }}" class="grid grid-cols-2 sm:grid-cols-5 gap-3 items-end mb-6">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Title" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" placeholder="e.g. SC-1" required />
                    </div>
                    <div>
                        <x-input-label for="note" value="Note" />
                        <x-text-input id="note" name="note" class="mt-1 block w-full" placeholder="e.g. Short Code -1" />
                    </div>
                    <div>
                        <x-input-label for="default_total" value="Total Marks" />
                        <x-text-input id="default_total" type="number" step="0.01" min="0" name="default_total" class="mt-1 block w-full" value="100" />
                    </div>
                    <div>
                        <x-input-label for="default_pass_mark" value="Pass Mark" />
                        <x-text-input id="default_pass_mark" type="number" step="0.01" min="0" name="default_pass_mark" class="mt-1 block w-full" value="0" />
                    </div>
                    <div>
                        <x-input-label for="default_acceptance" value="Acceptance" />
                        <x-text-input id="default_acceptance" type="number" step="0.01" min="0" name="default_acceptance" class="mt-1 block w-full" value="1" />
                    </div>
                    <div class="sm:col-span-5 flex justify-end">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-2 px-3">Title</th>
                            <th class="py-2 px-3">Note</th>
                            <th class="py-2 px-3">Total Marks</th>
                            <th class="py-2 px-3">Pass Mark</th>
                            <th class="py-2 px-3">Acceptance</th>
                            <th class="py-2 px-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shortCodes as $shortCode)
                            <tr class="border-t">
                                <td class="py-2 px-3">{{ $shortCode->name }}</td>
                                <td class="py-2 px-3">{{ $shortCode->note ?? '—' }}</td>
                                <td class="py-2 px-3">{{ rtrim(rtrim(number_format($shortCode->default_total, 2), '0'), '.') }}</td>
                                <td class="py-2 px-3">{{ rtrim(rtrim(number_format($shortCode->default_pass_mark, 2), '0'), '.') }}</td>
                                <td class="py-2 px-3">{{ rtrim(rtrim(number_format($shortCode->default_acceptance, 2), '0'), '.') }}</td>
                                <td class="py-2 px-3">
                                    <form action="{{ route('school.exam-short-codes.destroy', $shortCode) }}" method="POST" onsubmit="return confirm('Delete this short code?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 text-center text-gray-400">No short codes yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>

            @elseif ($activeTab === 'grade')
                <p class="text-sm text-gray-500 mb-4">Manage grading scale from the full Grading Scale page.</p>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50"><tr class="text-gray-500"><th class="py-2 px-3">Grade</th><th class="py-2 px-3">Min %</th><th class="py-2 px-3">Max %</th><th class="py-2 px-3">Actions</th></tr></thead>
                    <tbody>
                        @foreach ($grades as $grade)
                            <tr class="border-t">
                                <td class="py-2 px-3">{{ $grade->name }}</td>
                                <td class="py-2 px-3">{{ $grade->min_percent }}</td>
                                <td class="py-2 px-3">{{ $grade->max_percent }}</td>
                                <td class="py-2 px-3"><a href="{{ route('school.grades.edit', $grade) }}" class="text-indigo-600 hover:underline">Edit</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('school.grades.create') }}" class="inline-block mt-4 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">+ Add Grade</a>
            @endif
        </div>
    </div>
</x-app-layout>
