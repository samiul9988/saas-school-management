<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Plans') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('super-admin.plans.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Plan
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Monthly</th>
                            <th class="py-3 px-4">Yearly</th>
                            <th class="py-3 px-4">Features</th>
                            <th class="py-3 px-4">Active</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plans as $plan)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $plan->name }}</td>
                                <td class="py-3 px-4">{{ number_format($plan->monthly_price, 2) }}</td>
                                <td class="py-3 px-4">{{ number_format($plan->yearly_price, 2) }}</td>
                                <td class="py-3 px-4 text-xs text-gray-500">{{ implode(', ', $plan->features ?? []) }}</td>
                                <td class="py-3 px-4">{{ $plan->is_active ? 'Yes' : 'No' }}</td>
                                <td class="py-3 px-4 space-x-2">
                                    <a href="{{ route('super-admin.plans.edit', $plan) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Delete this plan?')">
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
        </div>
    </div>
</x-app-layout>
