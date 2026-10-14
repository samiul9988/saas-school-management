<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Attendance Time Config') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="POST" action="{{ route('school.student-attendance.settings.time-config.store') }}" id="time-config-form" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @csrf
                <div>
                    <x-input-label value="Select Section" />
                    <select name="section_id" id="f_section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}">
                                {{ $sectionOption->schoolClass?->name }} -{{ $sectionOption->shift?->name ?? 'N/A' }}-{{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Select Period" />
                    <select name="period_id" id="f_period_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="Send SMS" />
                    <select name="send_sms_status" id="f_send_sms_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                    </select>
                </div>

                <div>
                    <x-input-label value="Start Time" />
                    <x-text-input type="time" name="start_time" id="f_start_time" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label value="End Time" />
                    <x-text-input type="time" name="end_time" id="f_end_time" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label value="Delay Time" />
                    <x-text-input type="time" name="delay_time" id="f_delay_time" class="mt-1 block w-full" />
                </div>

                <div>
                    <x-input-label value="Process Time" />
                    <x-text-input type="time" name="process_time" id="f_process_time" class="mt-1 block w-full" />
                </div>
                <div class="sm:col-span-2 flex items-end">
                    <button type="submit" class="w-full px-6 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                        Save
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="font-semibold text-lg mb-4">Configuration List</div>
            <div class="overflow-x-auto border rounded-md">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4">Class</th>
                            <th class="py-3 px-4">Shift</th>
                            <th class="py-3 px-4">Section</th>
                            <th class="py-3 px-4">Period</th>
                            <th class="py-3 px-4">Start Time</th>
                            <th class="py-3 px-4">End Time</th>
                            <th class="py-3 px-4">Delay Time</th>
                            <th class="py-3 px-4">Process Time</th>
                            <th class="py-3 px-4">Send SMS</th>
                            <th class="py-3 px-4">Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($configs as $config)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $config->schoolClass?->name }}</td>
                                <td class="py-2 px-4">{{ $config->section?->shift?->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4">{{ $config->section?->name }}</td>
                                <td class="py-2 px-4">{{ $config->period?->name }}</td>
                                <td class="py-2 px-4">{{ $config->start_time }}</td>
                                <td class="py-2 px-4">{{ $config->end_time }}</td>
                                <td class="py-2 px-4">{{ $config->delay_time }}</td>
                                <td class="py-2 px-4">{{ $config->process_time }}</td>
                                <td class="py-2 px-4">{{ ucfirst($config->send_sms_status) }}</td>
                                <td class="py-2 px-4">
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-btn"
                                            data-section="{{ $config->section_id }}"
                                            data-period="{{ $config->period_id }}"
                                            data-sms="{{ $config->send_sms_status }}"
                                            data-start="{{ $config->start_time }}"
                                            data-end="{{ $config->end_time }}"
                                            data-delay="{{ $config->delay_time }}"
                                            data-process="{{ $config->process_time }}">
                                        Update
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="py-6 text-center text-gray-400">No configuration yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.edit-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('f_section_id').value = btn.dataset.section;
                document.getElementById('f_period_id').value = btn.dataset.period;
                document.getElementById('f_send_sms_status').value = btn.dataset.sms;
                document.getElementById('f_start_time').value = btn.dataset.start || '';
                document.getElementById('f_end_time').value = btn.dataset.end || '';
                document.getElementById('f_delay_time').value = btn.dataset.delay || '';
                document.getElementById('f_process_time').value = btn.dataset.process || '';
                document.getElementById('time-config-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
</x-app-layout>
