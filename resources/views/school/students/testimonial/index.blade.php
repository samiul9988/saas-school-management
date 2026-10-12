<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Testimonial') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="flex gap-1 px-4 pt-2 border-b">
                <a href="{{ route('school.students.testimonial.create') }}" class="px-4 py-2 text-sm rounded-t-md text-gray-500 hover:text-gray-700">
                    Generate Testimonial
                </a>
                <a href="{{ route('school.students.testimonial.index') }}" class="px-4 py-2 text-sm rounded-t-md border border-b-0 text-indigo-600 font-semibold bg-white">
                    All Testimonial
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b font-semibold">All Testimonial</div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Serial</th>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($testimonials as $testimonial)
                            <tr class="border-t">
                                <td class="py-2 px-4 text-xs text-gray-500">{{ $testimonial->serial }}</td>
                                <td class="py-2 px-4">{{ $testimonial->student?->admission_number }}</td>
                                <td class="py-2 px-4">{{ $testimonial->student_name }}</td>
                                <td class="py-2 px-4">{{ $testimonial->class_name }}</td>
                                <td class="py-2 px-4">{{ $testimonial->roll }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('school.students.testimonial.download', $testimonial) }}" class="inline-flex items-center gap-2 px-4 py-1.5 bg-[#2196f3] text-white rounded-md text-xs font-semibold hover:bg-blue-600">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-gray-400">No testimonials yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
