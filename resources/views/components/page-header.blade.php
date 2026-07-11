@props(['title', 'description' => ''])

<div class="mb-4">
    <h1 class="fw-bold text-gray-900 fs-2 mb-2">{{ $title }}</h1>
    @if ($description)
        <span class="text-dark fw-semibold fs-7">{{ $description }}</span>
    @endif
</div>