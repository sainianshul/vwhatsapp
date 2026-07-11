@props(['items' => [], 'badge' => null])

<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 mb-4 mt-2">

    {{-- Home --}}
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary d-flex align-items-center"
            style="transition: color .2s">
            <i class="ki-outline ki-home-2 fs-5"></i>
        </a>
    </li>

    @foreach ($items as $item)

        {{-- Separator --}}
        <li class="breadcrumb-item d-flex align-items-center px-1">
            <i class="ki-outline ki-right fs-8 text-muted opacity-50"></i>
        </li>

        @if (!$loop->last)
            {{-- Linked middle item --}}
            <li class="breadcrumb-item">
                <a href="{{ $item['url'] ?? '#' }}" class="text-muted text-hover-primary fw-semibold"
                    style="transition: color .2s">
                    {{ $item['label'] }}
                </a>
            </li>
        @else
            {{-- Active last item --}}
            <li class="breadcrumb-item d-flex align-items-center gap-2">
                <span class="text-gray-800 fw-bold">{{ $item['label'] }}</span>
                @if ($badge)
                    <span class="badge badge-light-primary fs-9 fw-bold px-3 py-1 ms-1"
                        style="border-radius: 20px; letter-spacing: .02em">
                        {{ $badge }}
                    </span>
                @endif
            </li>
        @endif

    @endforeach

</ul>