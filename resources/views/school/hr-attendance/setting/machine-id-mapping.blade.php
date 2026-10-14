<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Enabled Staff Machine ID Map') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex gap-2 mb-6">
                <a href="{{ route('school.hr-attendance.setting.machine-id-mapping', ['tab' => 'enabled']) }}"
                   class="px-6 py-2.5 rounded-md font-semibold text-sm {{ $tab === 'enabled' ? 'bg-[#2196f3] text-white' : 'bg-gray-100 text-gray-700' }}">
                    Enabled Staff
                </a>
                <a href="{{ route('school.hr-attendance.setting.machine-id-mapping', ['tab' => 'disabled']) }}"
                   class="px-6 py-2.5 rounded-md font-semibold text-sm {{ $tab === 'disabled' ? 'bg-[#2196f3] text-white' : 'bg-gray-100 text-gray-700' }}">
                    Disabled Staff
                </a>
            </div>

            @if ($tab === 'enabled')
                <p class="text-sm text-red-600 mb-4">Note: Check For Staff ID As Machine ID</p>

                <form method="POST" action="{{ route('school.hr-attendance.setting.machine-id-mapping.batch-update') }}">
                    @csrf
                    <div class="overflow-x-auto border rounded-md">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-blue-100">
                                <tr class="text-gray-700">
                                    <th class="py-3 px-4"><input type="checkbox" id="select-all" class="h-4 w-4"></th>
                                    <th class="py-3 px-4">Staff ID</th>
                                    <th class="py-3 px-4">Name</th>
                                    <th class="py-3 px-4">Designation</th>
                                    <th class="py-3 px-4">User ID</th>
                                    <th class="py-3 px-4">Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($teachers as $teacher)
                                    <tr class="border-t">
                                        <td class="py-2 px-4"><input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" class="teacher-checkbox h-4 w-4"></td>
                                        <td class="py-2 px-4">{{ $teacher->staff_serial ?? $teacher->id }}</td>
                                        <td class="py-2 px-4">{{ $teacher->name }}</td>
                                        <td class="py-2 px-4">{{ $teacher->designation }}</td>
                                        <td class="py-2 px-4">
                                            <input type="text" name="machine_id[{{ $teacher->id }}]" value="{{ $teacher->machine_id }}" class="border-gray-300 rounded-md shadow-sm w-32">
                                        </td>
                                        <td class="py-2 px-4">
                                            <button type="button" class="btn btn-sm btn-outline-secondary single-update"
                                                    data-url="{{ route('school.hr-attendance.setting.machine-id-mapping.update', $teacher) }}">
                                                Update
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-6 text-center text-gray-400">No Data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($teachers->isNotEmpty())
                        <div class="mt-4">
                            <button type="submit" class="px-6 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                                Batch Update
                            </button>
                        </div>
                    @endif
                </form>

                <form id="single-update-form" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="machine_id" id="single-update-value">
                </form>
            @else
                <div class="border rounded-md divide-y">
                    @forelse ($teachers as $teacher)
                        <div class="px-4 py-3">{{ $teacher->staff_serial ?? $teacher->id }}. {{ $teacher->name }} @if($teacher->designation) ({{ $teacher->designation }}) @endif</div>
                    @empty
                        <div class="px-4 py-3 text-gray-400">DisabledStaff</div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('select-all')?.addEventListener('change', function () {
            document.querySelectorAll('.teacher-checkbox').forEach(function (cb) { cb.checked = this.checked; }.bind(this));
        });

        document.querySelectorAll('.single-update').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const row = btn.closest('tr');
                const value = row.querySelector('input[type="text"]').value;
                const form = document.getElementById('single-update-form');
                form.action = btn.dataset.url;
                document.getElementById('single-update-value').value = value;
                form.submit();
            });
        });
    </script>
</x-app-layout>
