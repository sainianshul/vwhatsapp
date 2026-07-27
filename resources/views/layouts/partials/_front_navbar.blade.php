<div class="d-flex align-items-center justify-content-between px-10 py-5 bg-white border-bottom border-gray-200 position-fixed w-100 z-index-3" style="top: 0; left: 0; z-index: 999;">
    <div class="d-flex align-items-center">
        <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
            <img alt="Logo" src="{{ asset('icon.png') }}" class="h-30px me-3" />
            <h3 class="m-0 fw-bolder text-gray-900 fs-2">VWhatsApp</h3>
        </a>
    </div>
    <div>
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-success fw-bolder">Go to Dashboard</a>
        @else
            @if(Route::currentRouteName() != 'login')
                <a href="{{ route('login') }}" class="btn btn-success fw-bolder px-8 py-3">Log In</a>
            @endif
        @endauth
    </div>
</div>
