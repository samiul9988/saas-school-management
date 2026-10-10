<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Multiple Student') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            @include('school.students._import-panel', [
                'mode' => 'with-id',
                'title' => 'Student Excel Registration With ID',
                'preview' => $previewWithId,
                'columns' => ['Student ID', 'Roll No', 'Name', 'Gender', 'Religion', "Father's Name", "Mother's Name", 'Mobile No.'],
                'collapseOpen' => true,
            ])

            @include('school.students._import-panel', [
                'mode' => 'without-id',
                'title' => 'Student Excel Registration Without ID',
                'preview' => $previewWithoutId,
                'columns' => ['Roll No', 'Name', 'Gender', 'Religion', "Father's Name", "Mother's Name", 'Mobile No.'],
                'collapseOpen' => false,
            ])
        </div>
    </div>
</x-app-layout>
