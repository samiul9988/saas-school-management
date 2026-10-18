<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Institute Update') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.profile.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label value="Institute Logo" />
                        <div class="mt-1 border border-dashed border-gray-300 rounded-lg p-4 flex flex-col items-center gap-3">
                            @if ($school->logo)
                                <img src="{{ Storage::url($school->logo) }}" alt="Logo" class="w-24 h-24 object-contain rounded-full border">
                            @else
                                <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">No Logo</div>
                            @endif
                            <input type="file" name="logo" accept="image/*" class="text-sm">
                        </div>
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-input-label for="name" value="Institute Name" />
                            <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $school->name) }}" required />
                        </div>
                        <div>
                            <x-input-label for="admin_name" value="Admin Name" />
                            <x-text-input id="admin_name" name="admin_name" class="mt-1 block w-full" value="{{ old('admin_name', $school->admin_name) }}" />
                        </div>
                        <div>
                            <x-input-label for="contact_number" value="Contact Number" />
                            <x-text-input id="contact_number" name="contact_number" class="mt-1 block w-full" value="{{ old('contact_number', $school->contact_number) }}" />
                        </div>
                        <div>
                            <x-input-label for="academic_year_id" value="Academic Year" />
                            <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Select --</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}" @selected(old('academic_year_id', $school->academic_year_id) == $year->id)>{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label for="address" value="Institute Address" />
                            <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('address', $school->address) }}</textarea>
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <x-primary-button>Update</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
