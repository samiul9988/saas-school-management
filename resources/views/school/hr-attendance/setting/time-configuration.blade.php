<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Staff Attendance Time Configuration') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="POST" action="{{ route('school.hr-attendance.setting.time-configuration.store') }}">
                @csrf
                <div class="font-semibold text-lg mb-4">Day &amp; Time Configuration</div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 overflow-x-auto border rounded-md">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-blue-100">
                                <tr class="text-gray-700">
                                    <th class="py-3 px-4"><input type="checkbox" id="select-all" class="h-4 w-4"></th>
                                    <th class="py-3 px-4">ID</th>
                                    <th class="py-3 px-4">Name</th>
                                    <th class="py-3 px-4">Designation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($teachers as $teacher)
                                    <tr class="border-t">
                                        <td class="py-2 px-4"><input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" class="teacher-checkbox h-4 w-4"></td>
                                        <td class="py-2 px-4">{{ $teacher->staff_serial ?? $teacher->id }}</td>
                                        <td class="py-2 px-4">{{ $teacher->name }}</td>
                                        <td class="py-2 px-4">{{ $teacher->designation }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-6 text-center text-gray-400">No active staff found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        <p class="text-xs text-gray-500 p-2">Leave all unchecked to save this as the default configuration for every staff member.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <x-input-label value="In Time" />
                            <x-text-input type="time" name="in_time" value="{{ $config?->in_time }}" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Out Time" />
                            <x-text-input type="time" name="out_time" value="{{ $config?->out_time }}" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Delay Time" />
                            <x-text-input type="time" name="delay_time" value="{{ $config?->delay_time }}" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Input Process Time" />
                            <x-text-input type="time" name="input_process_time" value="{{ $config?->input_process_time }}" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Output Process Time" />
                            <x-text-input type="time" name="output_process_time" value="{{ $config?->output_process_time }}" class="mt-1 block w-full" />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            @foreach (\App\Http\Controllers\School\StaffAttendanceController::WEEKDAYS as $day)
                                @php($checked = $config && in_array($day, $config->days ?? []))
                                <label class="day-toggle flex items-center justify-center py-2 rounded-md text-sm font-medium cursor-pointer {{ $checked ? 'bg-[#2196f3] text-white' : 'bg-gray-100 text-gray-700' }}">
                                    <input type="checkbox" name="days[]" value="{{ $day }}" class="hidden" {{ $checked ? 'checked' : '' }}>
                                    {{ ucfirst($day) }}
                                </label>
                            @endforeach
                        </div>

                        <button type="submit" class="w-full px-6 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('select-all')?.addEventListener('change', function () {
            document.querySelectorAll('.teacher-checkbox').forEach(function (cb) { cb.checked = this.checked; }.bind(this));
        });

        document.querySelectorAll('.day-toggle').forEach(function (label) {
            label.addEventListener('click', function () {
                setTimeout(function () {
                    const input = label.querySelector('input');
                    label.className = 'day-toggle flex items-center justify-center py-2 rounded-md text-sm font-medium cursor-pointer ' + (input.checked ? 'bg-[#2196f3] text-white' : 'bg-gray-100 text-gray-700');
                }, 0);
            });
        });
    </script>
</x-app-layout>
