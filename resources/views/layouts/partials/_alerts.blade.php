@if (session('success'))
    <div class="alert alert-dismissible bg-light-success d-flex flex-column flex-sm-row p-5 mb-10">
        <i class="ki-outline ki-notification-bing fs-2hx text-success me-4 mb-5 mb-sm-0"></i>
        <div class="d-flex flex-column pe-0 pe-sm-10">
            <h4 class="fw-semibold">Success</h4>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
            <i class="ki-outline ki-cross fs-1 text-success"></i>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-dismissible bg-light-danger d-flex flex-column flex-sm-row p-5 mb-10">
        <i class="ki-outline ki-message-text-2 fs-2hx text-danger me-4 mb-5 mb-sm-0"></i>
        <div class="d-flex flex-column pe-0 pe-sm-10">
            <h4 class="fw-semibold">Error</h4>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
            <i class="ki-outline ki-cross fs-1 text-danger"></i>
        </button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-dismissible bg-light-warning d-flex flex-column flex-sm-row p-5 mb-10">
        <i class="ki-outline ki-warning fs-2hx text-warning me-4 mb-5 mb-sm-0"></i>
        <div class="d-flex flex-column pe-0 pe-sm-10">
            <h4 class="fw-semibold">Warning</h4>
            <span>{{ session('warning') }}</span>
        </div>
        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
            <i class="ki-outline ki-cross fs-1 text-warning"></i>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-dismissible bg-light-danger d-flex flex-column flex-sm-row p-5 mb-10">
        <i class="ki-outline ki-message-text-2 fs-2hx text-danger me-4 mb-5 mb-sm-0"></i>
        <div class="d-flex flex-column pe-0 pe-sm-10">
            <h4 class="fw-semibold">Please fix the following errors</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
            <i class="ki-outline ki-cross fs-1 text-danger"></i>
        </button>
    </div>
@endif
