<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Chart of Accounts') }}</h2>
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
                <h3 class="font-semibold mb-4">Add Account</h3>
                <form method="POST" action="{{ route('school.accounts.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <x-input-label for="code" value="Code" />
                        <x-text-input id="code" name="code" class="mt-1 block w-full" value="{{ old('code') }}" required />
                    </div>
                    <div>
                        <x-input-label for="name" value="Account Name" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name') }}" required />
                    </div>
                    <div>
                        <x-input-label for="type" value="Type" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @foreach (\App\Models\ChartOfAccount::TYPES as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-primary-button>Add</x-primary-button>
                    </div>
                </form>
            </div>

            @foreach (\App\Models\ChartOfAccount::TYPES as $type)
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-semibold capitalize">{{ $type }}</div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50">
                            <tr class="text-gray-500">
                                <th class="py-2 px-4">Code</th>
                                <th class="py-2 px-4">Name</th>
                                <th class="py-2 px-4">System</th>
                                <th class="py-2 px-4">Active</th>
                                <th class="py-2 px-4">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($accounts->get($type, collect()) as $account)
                                <tr class="border-t">
                                    <td class="py-2 px-4">{{ $account->code }}</td>
                                    <td class="py-2 px-4">{{ $account->name }}</td>
                                    <td class="py-2 px-4">{{ $account->is_system ? 'Yes' : 'No' }}</td>
                                    <td class="py-2 px-4">{{ $account->is_active ? 'Yes' : 'No' }}</td>
                                    <td class="py-2 px-4">
                                        @unless ($account->is_system)
                                            <form method="POST" action="{{ route('school.accounts.destroy', $account) }}" onsubmit="return confirm('Delete this account?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                            </form>
                                        @endunless
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-4 px-4 text-center text-gray-400">No accounts yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
