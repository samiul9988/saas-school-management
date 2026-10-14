<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Period Setup') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <div class="font-semibold text-center mb-4">Select From Global Period List</div>
                    <div class="border rounded-lg p-4 space-y-3">
                        @foreach (\App\Http\Controllers\School\AttendanceSettingsController::GLOBAL_PERIODS as $name)
                            <form method="POST" action="{{ route('school.student-attendance.settings.period.store') }}">
                                @csrf
                                <input type="hidden" name="name" value="{{ $name }}">
                                <button type="submit" @disabled(in_array($name, $addedNames))
                                        class="w-full py-2.5 rounded-md font-semibold text-sm text-white {{ in_array($name, $addedNames) ? 'bg-blue-300 cursor-not-allowed' : 'bg-[#2196f3] hover:bg-blue-600' }}">
                                    {{ $name }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <div class="border rounded-lg overflow-hidden overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-blue-100">
                                <tr class="text-gray-700">
                                    <th class="py-3 px-4">Period</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($periods as $period)
                                    <tr class="border-t">
                                        <td class="py-2 px-4">{{ $period->name }}</td>
                                        <td class="py-2 px-4">
                                            <form method="POST" action="{{ route('school.students.initial-setup.toggle', ['period', $period->id]) }}">
                                                @csrf
                                                <button type="submit" class="{{ $period->status === 'enable' ? 'text-green-600' : 'text-red-600' }} font-semibold hover:underline">
                                                    {{ $period->status === 'enable' ? 'Enabled' : 'Disabled' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="py-2 px-4">
                                            <form action="{{ route('school.students.initial-setup.destroy', ['period', $period->id]) }}" method="POST" onsubmit="return confirm('Remove this period?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-6 text-center text-gray-400">No periods added yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
