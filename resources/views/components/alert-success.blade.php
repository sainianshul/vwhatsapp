@if (session('success'))
    <div class="alert bg-light-success border border-success border-dashed d-flex align-items-center p-3 mb-6 rounded">
        <i class="ki-outline ki-check-circle fs-3 text-success me-3"></i>
        <div class="d-flex flex-column">
            <span class="text-success fw-medium fs-7">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn btn-icon ms-auto m-0 p-0" data-bs-dismiss="alert" style="width: 24px; height: 24px;">
            <i class="ki-outline ki-cross fs-4 text-success"></i>
        </button>
    </div>
@endif
