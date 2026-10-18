<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Send Dynamic SMS (By Excel)') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">
                    Upload a .csv, .xls, or .xlsx file. The first row must be column headers, with one column named
                    "Phone" or "Mobile". Every other column header becomes a merge tag you can use in the SMS content below,
                    e.g. a "Name" column becomes @{{name}}.
                </p>
                <form method="POST" action="{{ route('school.sms-send.dynamic.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="file" value="Choose File" />
                        <input type="file" id="file" name="file" accept=".csv,.xls,.xlsx" required class="mt-1 block w-full text-sm">
                    </div>
                    <div>
                        <x-input-label for="content" value="SMS Content" />
                        <textarea id="content" name="content" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('content', $defaultContent) }}</textarea>
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Process &amp; Send</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
