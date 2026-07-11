<div class="row g-5 g-xxl-8">
    <!-- Internal Notes (Subject table field) -->
    <div class="col-xl-6">
        <div class="card card-bordered shadow-sm border-gray-300">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Background Notes</span>
                </h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-light btn-active-light-primary" id="edit-notes-btn">
                        <i class="ki-outline ki-pencil fs-4"></i> Edit
                    </button>
                </div>
            </div>
            <div class="card-body py-5">
                <div id="notes-display" class="fs-6 text-gray-800" style="white-space: pre-wrap;">
                    {{ $subject->notes ?: 'No background notes available for this profile.' }}
                </div>
                
                <form id="notes-form" class="d-none" action="{{ route('subjects.update', $subject) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- Hidden required fields just to pass validation if doing a full update, 
                         though a dedicated AJAX route for notes is better. For now, doing simple JS toggle. -->
                    <input type="hidden" name="name" value="{{ $subject->name }}">
                    <input type="hidden" name="status" value="{{ $subject->status }}">
                    <textarea name="notes" class="form-control form-control-solid mb-4" data-kt-autosize="true" rows="5">{{ $subject->notes }}</textarea>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light" id="cancel-notes-btn">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Save Notes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Comments Component -->
    <div class="col-xl-6">
        <x-comments type="App\Models\Subject" :modelId="$subject->id" />
    </div>
</div>

@push('scripts')
<script>
    $('#edit-notes-btn').click(function() {
        $('#notes-display').addClass('d-none');
        $('#notes-form').removeClass('d-none');
        $(this).addClass('d-none');
    });

    $('#cancel-notes-btn').click(function() {
        $('#notes-form').addClass('d-none');
        $('#notes-display').removeClass('d-none');
        $('#edit-notes-btn').removeClass('d-none');
    });
</script>
@endpush
