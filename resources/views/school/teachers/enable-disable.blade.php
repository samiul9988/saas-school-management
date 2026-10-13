@php($tab = $tab ?? 'disable')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Enable / Disable') }}</h2>
    </x-slot>

    <div class="flex gap-2 bg-[#d6f0fb] rounded-lg p-2">
        <a href="{{ route('school.teachers.enable-disable', ['tab' => 'disable']) }}"
           class="px-4 py-2 rounded-md text-sm font-semibold {{ $tab === 'disable' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
            To Disable
        </a>
        <a href="{{ route('school.teachers.enable-disable', ['tab' => 'enable']) }}"
           class="px-4 py-2 rounded-md text-sm font-semibold {{ $tab === 'enable' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}">
            To Enable
        </a>
    </div>

    <div class="space-y-4 mt-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b font-semibold">
                {{ $tab === 'disable' ? 'Staff Disable' : 'Staff Enable' }}
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="flex justify-end mb-2 text-sm text-gray-600">
                Total Count: {{ $teachers->count() }}
            </div>

            <form method="POST" action="{{ route('school.teachers.enable-disable.store') }}">
                @csrf
                <input type="hidden" name="action" value="{{ $tab === 'disable' ? 'disable' : 'enable' }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-blue-100">
                            <tr class="text-gray-700">
                                <th class="py-3 px-4 w-10">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Designation</th>
                                <th class="py-3 px-4">Mobile No.</th>
                                <th class="py-3 px-4">Gender</th>
                                <th class="py-3 px-4">Employment Date</th>
                                <th class="py-3 px-4">Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($teachers as $teacher)
                                <tr class="border-t">
                                    <td class="py-2 px-4">
                                        <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" class="row-checkbox">
                                    </td>
                                    <td class="py-2 px-4">{{ $teacher->staff_serial ?: $teacher->id }}</td>
                                    <td class="py-2 px-4">{{ $teacher->name }}</td>
                                    <td class="py-2 px-4">{{ $teacher->designation }}</td>
                                    <td class="py-2 px-4">{{ $teacher->phone }}</td>
                                    <td class="py-2 px-4">{{ $teacher->gender ? ucfirst($teacher->gender) : '' }}</td>
                                    <td class="py-2 px-4">{{ $teacher->joining_date?->format('Y-m-d') }}</td>
                                    <td class="py-2 px-4">
                                        <div class="w-9 h-9 rounded-full bg-gray-100 overflow-hidden flex items-center justify-center">
                                            @if ($teacher->photo)
                                                <img src="{{ asset('storage/'.$teacher->photo) }}" class="w-full h-full object-cover" alt="">
                                            @else
                                                <i class="bi bi-person text-gray-400"></i>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="py-6 text-center text-gray-400">No staff found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($teachers->isNotEmpty())
                    <button type="submit" class="mt-4 px-5 py-2 bg-blue-600 text-white rounded-md font-semibold text-sm hover:bg-blue-700">
                        {{ $tab === 'disable' ? 'Disable' : 'Enable' }}
                    </button>
                @endif
            </form>
        </div>
    </div>

    <script>
        document.getElementById('select-all')?.addEventListener('change', function () {
            document.querySelectorAll('.row-checkbox').forEach(function (cb) {
                cb.checked = this.checked;
            }.bind(this));
        });
    </script>
</x-app-layout>
