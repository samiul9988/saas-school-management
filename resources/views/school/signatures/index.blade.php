<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Institute Default Signature') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.signatures.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <x-input-label for="title" value="Signature Title" />
                        <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title') }}" required />
                    </div>
                    <div>
                        <x-input-label for="position" value="Select Position" />
                        <select id="position" name="position" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Select Position</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position }}" @selected(old('position') === $position)>{{ $position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="status" value="Sign Status" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="image" value="Signature Image" />
                        <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full text-sm">
                    </div>
                    <div class="md:col-span-4 flex justify-end">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Position Name</th>
                            <th class="py-3 px-4">Signature Title</th>
                            <th class="py-3 px-4">Signature Status</th>
                            <th class="py-3 px-4">Sign</th>
                            <th class="py-3 px-4">Upload Image</th>
                            <th class="py-3 px-4">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($signatures as $signature)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $signature->position }}</td>
                                <td class="py-3 px-4">{{ $signature->title }}</td>
                                <td class="py-3 px-4 capitalize {{ $signature->status === 'active' ? 'text-green-600' : 'text-gray-400' }}">{{ $signature->status }}</td>
                                <td class="py-3 px-4">
                                    @if ($signature->imageUrl())
                                        <img src="{{ $signature->imageUrl() }}" alt="{{ $signature->title }}" class="h-10">
                                    @else
                                        <span class="text-gray-400">No image</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <form method="POST" action="{{ route('school.signatures.upload-image', $signature) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                        @csrf
                                        <input type="file" name="image" accept="image/*" required class="text-xs">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-[#2196f3] rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600">
                                            <i class="bi bi-cloud-upload mr-1"></i> Upload Image
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-4">
                                    <form method="POST" action="{{ route('school.signatures.destroy', $signature) }}" onsubmit="return confirm('Delete this signature?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 px-4 text-center text-gray-400">No signatures yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
