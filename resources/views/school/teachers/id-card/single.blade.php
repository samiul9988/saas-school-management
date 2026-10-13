<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('ID Card') }}</h2>
    </x-slot>

    @include('school.teachers.id-card._tabs', ['active' => 'single'])

    <div class="space-y-4 mt-4">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b font-semibold">Single ID Card Download</div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Designation</th>
                        <th class="py-3 px-4">Mobile</th>
                        <th class="py-3 px-4">Image</th>
                        <th class="py-3 px-4">Download</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $teacher->staff_serial ?: $teacher->id }}</td>
                            <td class="py-2 px-4">{{ $teacher->name }}</td>
                            <td class="py-2 px-4">{{ $teacher->designation }}</td>
                            <td class="py-2 px-4">{{ $teacher->phone }}</td>
                            <td class="py-2 px-4">
                                <div class="w-9 h-9 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center">
                                    @if ($teacher->photo)
                                        <img src="{{ asset('storage/'.$teacher->photo) }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <i class="bi bi-person text-gray-400"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-4">
                                <a href="{{ route('school.teachers.id-card.download', $teacher) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-400">No staff found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
