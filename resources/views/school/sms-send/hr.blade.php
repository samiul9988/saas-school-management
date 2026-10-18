<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Send SMS to HR') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('school.sms-send.hr.store') }}">
                @csrf
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <x-input-label for="content" value="SMS Content" />
                    <textarea id="content" name="content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('content', $defaultContent) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Available tags: @{{teacher_name}}, @{{designation}}</p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr class="text-gray-500">
                                <th class="py-2 px-4"><input type="checkbox" onclick="document.querySelectorAll('.teacher-cb').forEach(c=>c.checked=this.checked)"></th>
                                <th class="py-2 px-4">Name</th>
                                <th class="py-2 px-4">Designation</th>
                                <th class="py-2 px-4">Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($teachers as $teacher)
                                <tr class="border-t">
                                    <td class="py-2 px-4"><input class="teacher-cb" type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}"></td>
                                    <td class="py-2 px-4">{{ $teacher->name }}</td>
                                    <td class="py-2 px-4">{{ $teacher->designation }}</td>
                                    <td class="py-2 px-4">{{ $teacher->phone }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 px-4 text-center text-gray-400">No active staff with a phone number.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4 flex justify-end">
                        <x-primary-button>Send SMS</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
