<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manual Attendance Process') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="POST" action="{{ route('school.student-attendance.settings.manual-process.store') }}" class="space-y-4 max-w-md">
                @csrf
                <div>
                    <x-input-label value="Institute ID" />
                    <x-text-input class="mt-1 block w-full bg-gray-50" value="{{ $school->id }}" disabled />
                </div>
                <div>
                    <x-input-label value="From Date" />
                    <x-text-input type="date" name="from_date" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label value="To Date" />
                    <x-text-input type="date" name="to_date" class="mt-1 block w-full" required />
                </div>
                <button type="submit" class="w-full px-6 py-2.5 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    Save
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
