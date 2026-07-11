{{-- _table-skeleton.blade.php --}}
@php $skelId = $id ?? 'table-skeleton'; @endphp
<div id="{{ $skelId }}" class="placeholder-glow">
    @for($i = 0; $i < 6; $i++)
        <div class="d-flex align-items-center gap-3 py-4 border-bottom border-gray-200">
            <span class="placeholder bg-secondary rounded" style="width:35px;height:35px;"></span>
            <div class="flex-grow-1">
                <span class="placeholder bg-secondary rounded col-3 mb-2 d-block"></span>
                <span class="placeholder bg-secondary rounded col-2 d-block"></span>
            </div>
            <span class="placeholder bg-secondary rounded col-1"></span>
            <span class="placeholder bg-secondary rounded col-1"></span>
            <span class="placeholder bg-secondary rounded col-2"></span>
        </div>
    @endfor
</div>