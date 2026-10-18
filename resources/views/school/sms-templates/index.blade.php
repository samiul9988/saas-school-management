<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('SMS Template Settings') }}</h2>
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
                <h3 class="font-semibold mb-4">Add Template</h3>
                <form method="POST" action="{{ route('school.sms-templates.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <x-input-label for="category" value="Category" />
                        <select id="category" name="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @foreach (\App\Models\SmsTemplate::CATEGORIES as $category)
                                <option value="{{ $category }}">{{ ucfirst(str_replace('_', ' ', $category)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" class="mt-1 block w-full" required />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="content" value="Content (use @{{tag}} for merge fields)" />
                        <x-text-input id="content" name="content" class="mt-1 block w-full" required />
                    </div>
                    <div class="md:col-span-4 flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_default" value="1"> Set as default for this category
                        </label>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>

            @foreach ($templates as $category => $categoryTemplates)
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-semibold capitalize">{{ str_replace('_', ' ', $category) }}</div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr class="text-gray-500">
                                <th class="py-2 px-4">Title</th>
                                <th class="py-2 px-4">Content</th>
                                <th class="py-2 px-4">Default</th>
                                <th class="py-2 px-4">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categoryTemplates as $template)
                                <tr class="border-t">
                                    <td class="py-2 px-4">{{ $template->title }}</td>
                                    <td class="py-2 px-4 text-gray-500">{{ $template->content }}</td>
                                    <td class="py-2 px-4">{{ $template->is_default ? 'Yes' : 'No' }}</td>
                                    <td class="py-2 px-4">
                                        <form method="POST" action="{{ route('school.sms-templates.destroy', $template) }}" onsubmit="return confirm('Delete this template?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
