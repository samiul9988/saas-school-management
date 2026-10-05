<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit School') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('super-admin.schools.update', $school) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="name" value="School Name" />
                        <x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name', $school->name) }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" type="email" name="email" class="mt-1 block w-full" value="{{ old('email', $school->email) }}" />
                    </div>
                    <div>
                        <x-input-label for="phone" value="Phone" />
                        <x-text-input id="phone" name="phone" class="mt-1 block w-full" value="{{ old('phone', $school->phone) }}" />
                    </div>
                    <div>
                        <x-input-label for="address" value="Address" />
                        <x-text-input id="address" name="address" class="mt-1 block w-full" value="{{ old('address', $school->address) }}" />
                    </div>
                    <div>
                        <x-input-label for="plan_id" value="Plan" />
                        <select id="plan_id" name="plan_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">— No plan —</option>
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" @selected($school->plan_id === $plan->id)>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @foreach (['pending', 'trial', 'active', 'suspended', 'expired'] as $status)
                                <option value="{{ $status }}" @selected($school->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Update School</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
