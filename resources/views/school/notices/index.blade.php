<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Notices') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.notices.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Notice
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Title</th>
                            <th class="py-3 px-4">Audience</th>
                            <th class="py-3 px-4">Published</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notices as $notice)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $notice->title }}</td>
                                <td class="py-3 px-4 capitalize">{{ $notice->audience }}</td>
                                <td class="py-3 px-4">{{ $notice->published_at?->format('Y-m-d H:i') ?? 'Draft' }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('school.notices.edit', $notice) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('school.notices.destroy', $notice) }}" method="POST" class="inline" onsubmit="return confirm('Delete this notice?')">
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

            {{ $notices->links() }}
        </div>
    </div>
</x-app-layout>
