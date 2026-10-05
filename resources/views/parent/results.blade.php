<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Results') }} &mdash; {{ $student->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50"><tr class="text-gray-500"><th class="py-3 px-4">Exam</th><th class="py-3 px-4">Status</th><th class="py-3 px-4">Marksheet</th></tr></thead>
                    <tbody>
                        @forelse ($exams as $exam)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $exam->name }}</td>
                                <td class="py-3 px-4 capitalize">{{ $exam->status }}</td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('parent.results.marksheet', $exam) }}" class="text-indigo-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-400">No exams found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
