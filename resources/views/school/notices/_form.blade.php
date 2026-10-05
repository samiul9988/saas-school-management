@php($notice = $notice ?? null)

<div>
    <x-input-label for="title" value="Title" />
    <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title', $notice?->title) }}" required />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>
<div>
    <x-input-label for="body" value="Body" />
    <textarea id="body" name="body" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('body', $notice?->body) }}</textarea>
    <x-input-error :messages="$errors->get('body')" class="mt-2" />
</div>
<div>
    <x-input-label for="audience" value="Audience" />
    <select id="audience" name="audience" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="all" @selected(old('audience', $notice?->audience ?? 'all') === 'all')>Everyone</option>
        <option value="students" @selected(old('audience', $notice?->audience) === 'students')>Students</option>
        <option value="teachers" @selected(old('audience', $notice?->audience) === 'teachers')>Teachers</option>
        <option value="parents" @selected(old('audience', $notice?->audience) === 'parents')>Parents</option>
    </select>
</div>
<div>
    <x-input-label for="published_at" value="Publish At" />
    <x-text-input id="published_at" type="datetime-local" name="published_at" class="mt-1 block w-full"
        value="{{ old('published_at', $notice?->published_at?->format('Y-m-d\TH:i')) }}" />
</div>
