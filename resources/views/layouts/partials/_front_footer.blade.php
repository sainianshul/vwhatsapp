<div class="bg-white py-15 border-top border-gray-200 mt-auto w-100">
    <div class="container">
        <div class="row text-center text-md-start">
            <div class="col-md-6 mb-10 mb-md-0">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-5">
                    <img alt="Logo" src="{{ asset('icon.png') }}" class="h-40px me-3" />
                    <h3 class="m-0 fw-bolder text-gray-900 fs-2">VWhatsApp</h3>
                </div>
                <p class="text-gray-600 fs-6 pe-md-10">
                    The ultimate SaaS platform for scaling your WhatsApp communication, managing multiple accounts, and integrating APIs.
                </p>
            </div>
            <div class="col-md-6 mb-10 mb-md-0 d-flex flex-column align-items-center align-items-md-end">
                <h4 class="fw-bold text-gray-900 mb-5">Quick Links</h4>
                <ul class="list-unstyled text-center text-md-end">
                    <li class="mb-3"><a href="{{ route('home') }}" class="text-gray-600 text-hover-success text-decoration-none">Home</a></li>
                    <li class="mb-3"><a href="{{ route('home') }}#features" class="text-gray-600 text-hover-success text-decoration-none">Features</a></li>
                    <li class="mb-3"><a href="{{ route('login') }}" class="text-gray-600 text-hover-success text-decoration-none">Log In</a></li>
                </ul>
            </div>
        </div>
        <div class="border-top border-gray-200 mt-10 pt-10 text-center">
            <div class="text-gray-500 fw-semibold fs-6">
                &copy; {{ date('Y') }} VWhatsApp. All rights reserved.
            </div>
        </div>
    </div>
</div>
