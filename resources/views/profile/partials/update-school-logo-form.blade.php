@php($school = $user->school)

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('School Logo') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Upload your school's logo. It appears in the sidebar for everyone at your school.") }}
        </p>
    </header>

    @if (session('status') === 'logo-updated')
        <p class="mt-2 text-sm text-green-600">{{ __('Logo updated.') }}</p>
    @endif

    <form method="POST" action="{{ route('profile.school-logo.update') }}" enctype="multipart/form-data" class="mt-6 flex items-center gap-6">
        @csrf

        <div class="flex items-center justify-center w-16 h-16 rounded-lg bg-gray-100 overflow-hidden shrink-0">
            @if ($school->logo)
                <img src="{{ Storage::url($school->logo) }}" alt="{{ $school->name }}" class="w-full h-full object-cover">
            @else
                <i class="bi bi-building text-gray-400 text-2xl"></i>
            @endif
        </div>

        <div class="flex-1">
            <input type="file" name="logo" accept="image/*" class="block w-full text-sm text-gray-600
                file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                hover:file:bg-indigo-100" required>
            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
        </div>

        <x-primary-button>{{ __('Upload') }}</x-primary-button>
    </form>
</section>
