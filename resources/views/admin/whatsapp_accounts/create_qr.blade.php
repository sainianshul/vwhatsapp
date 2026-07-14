@extends('layouts.app')

@section('title', 'Connect WhatsApp via QR')

@section('content')
<style>
    .qr-container-box {
        position: relative;
        padding: 1.5rem;
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #edf2f7;
    }
    
    .qr-skeleton {
        width: 264px;
        height: 264px;
        background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 0.5rem;
        margin: 0 auto;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .instruction-step {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.25rem;
    }

    .instruction-number {
        width: 28px;
        height: 28px;
        min-width: 28px;
        border-radius: 50%;
        background-color: #f1f5f9;
        color: #3f4254;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        margin-right: 1rem;
        margin-top: 2px;
    }

    .instruction-text {
        font-size: 1.05rem;
        color: #4b5675;
        line-height: 1.5;
    }

    .instruction-text strong {
        color: #181c32;
    }
</style>

<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <!-- Toolbar/Header -->
            <div class="d-flex align-items-center justify-content-between mb-8">
                <div>
                    <h1 class="text-gray-900 fw-bold fs-2x mb-2">Connect to WhatsApp</h1>
                    <div class="text-muted fs-5 fw-semibold">Link your WhatsApp account to enable messaging features</div>
                </div>
                <a href="{{ route('whatsapp_accounts.index') }}" class="btn btn-sm btn-light-primary fw-bold">
                    <i class="ki-outline ki-arrow-left fs-3"></i> Back to Accounts
                </a>
            </div>

            <!-- Main Content Card -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <div class="row g-0">
                        
                        <!-- Left Side: Instructions -->
                        <div class="col-lg-6 px-10 py-12 py-lg-15 border-end border-gray-200">
                            <h2 class="text-gray-900 fw-bolder mb-8 fs-2">To use WhatsApp on this platform:</h2>
                            
                            <div class="instruction-step">
                                <div class="instruction-number">1</div>
                                <div class="instruction-text">Open <strong>WhatsApp</strong> on your phone</div>
                            </div>
                            
                            <div class="instruction-step">
                                <div class="instruction-number">2</div>
                                <div class="instruction-text">
                                    Tap <strong>Menu</strong> <i class="ki-outline ki-dots-vertical fs-4 text-gray-700 mx-1"></i> on Android, or <strong>Settings</strong> <i class="ki-outline ki-setting-2 fs-4 text-gray-700 mx-1"></i> on iPhone
                                </div>
                            </div>
                            
                            <div class="instruction-step">
                                <div class="instruction-number">3</div>
                                <div class="instruction-text">Tap <strong>Linked devices</strong> and then <strong>Link a device</strong></div>
                            </div>
                            
                            <div class="instruction-step">
                                <div class="instruction-number">4</div>
                                <div class="instruction-text">Point your phone to this screen to capture the QR code</div>
                            </div>

                            <!-- Purple Info Alert -->
                            <div class="alert bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row w-100 p-5 mt-10">
                                <i class="ki-outline ki-information-5 fs-2hx text-primary me-4 mb-5 mb-sm-0"></i>
                                <div class="d-flex flex-column pe-0 pe-sm-10">
                                    <h5 class="mb-1 text-primary">Auto Redirect</h5>
                                    <span class="text-gray-800">Please wait on this screen after scanning. You will automatically be redirected to your accounts list once the connection is successful.</span>
                                </div>
                            </div>

                        </div>

                        <!-- Right Side: QR Code Area -->
                        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-light px-10 py-12 py-lg-15 rounded-end-4" style="min-height: 500px;">
                            
                            <div id="qr-container-wrapper" class="text-center w-100">
                                
                                <div class="qr-container-box d-inline-block" id="qr-container">
                                    <div class="qr-skeleton" id="skeleton-loader"></div>
                                </div>
                                
                                <div class="mt-8" id="status-container">
                                    <h3 class="text-gray-800 fw-bold fs-2" id="status-text">Generating Secure Code...</h3>
                                    <p class="text-muted fs-5 mt-2" id="status-subtext">Please ensure your phone is connected to the internet.</p>
                                </div>

                                <!-- Redirect Message (Hidden initially) -->
                                <div id="redirect-message" class="alert alert-success d-none align-items-center justify-content-center mt-6 mx-auto" style="max-width: 400px;">
                                    <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
                                    <div class="fw-semibold text-success fs-5">
                                        Redirecting to your accounts...
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let sessionId = "{{ $sessionId }}";
    let pollInterval;
    let failCount = 0;          // Track consecutive failures
    const MAX_FAILS = 5;        // Only show error after 5 consecutive failures

    function checkStatus() {
        fetch(`{{ route('whatsapp_accounts.qr_status', '') }}/${sessionId}`)
            .then(res => res.json())
            .then(data => {
                // ── Connected ──
                if (data.status === 'connected') {
                    failCount = 0;
                    clearInterval(pollInterval);
                    
                    document.getElementById('status-container').classList.add('d-none');
                    document.getElementById('redirect-message').classList.remove('d-none');
                    document.getElementById('redirect-message').classList.add('d-flex');
                    
                    let qrBox = document.getElementById('qr-container');
                    qrBox.style.background = '#f1faff';
                    qrBox.style.borderColor = '#009ef7';
                    
                    qrBox.innerHTML = `
                        <div class="d-flex flex-column align-items-center justify-content-center animate__animated animate__fadeIn" style="width: 264px; height: 264px;">
                            <i class="ki-outline ki-check-circle text-primary mb-4" style="font-size: 6rem;"></i>
                            <h2 class="text-primary fw-bold mb-0">Connected!</h2>
                        </div>
                    `;
                    
                    setTimeout(() => {
                        window.location.href = "{{ route('whatsapp_accounts.index') }}";
                    }, 2000);
                } 
                // ── QR Code Ready ──
                else if (data.status === 'pending' && data.qr) {
                    failCount = 0;
                    document.getElementById('status-text').innerText = "Ready to Scan";
                    document.getElementById('status-subtext').innerText = "Point your phone's camera at this code";
                    
                    document.getElementById('qr-container').innerHTML = `
                        <div class="animate__animated animate__fadeIn">
                            <img src="${data.qr}" alt="WhatsApp QR Code" class="img-fluid" style="width: 264px; height: 264px;">
                        </div>
                    `;
                }
                // ── Syncing (authenticated, waiting for ready) ──
                else if (data.status === 'syncing') {
                    failCount = 0;
                    document.getElementById('status-text').innerText = "Syncing Data...";
                    document.getElementById('status-subtext').innerText = "Please wait while we sync your chats. This may take up to 30 seconds.";
                    
                    document.getElementById('qr-container').innerHTML = `
                        <div class="d-flex flex-column align-items-center justify-content-center animate__animated animate__fadeIn" style="width: 264px; height: 264px;">
                            <span class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status"></span>
                        </div>
                    `;
                }
                // ── Waiting (transient, keep polling) ──
                else if (data.status === 'waiting') {
                    // Do nothing — just wait for the next poll
                }
                // ── Failed (session truly dead from Node) ──
                else if (data.status === 'failed') {
                    failCount++;
                    if (failCount >= MAX_FAILS) {
                        clearInterval(pollInterval);
                        showError("Connection Failed", "Please try again.");
                    }
                }
            })
            .catch(err => {
                // Network glitch — DON'T show error, just wait for next poll
                console.warn("Polling network error (will retry):", err.message);
            });
    }

    function showError(title, msg) {
        document.getElementById('status-container').classList.add('d-none');
        let qrBox = document.getElementById('qr-container');
        qrBox.style.background = '#fff5f8';
        qrBox.style.borderColor = '#f1416c';
        
        qrBox.innerHTML = `
            <div class="d-flex flex-column align-items-center justify-content-center" style="width: 264px; height: 264px;">
                <i class="ki-outline ki-cross-circle text-danger mb-4" style="font-size: 5rem;"></i>
                <h3 class="text-danger fw-bold">${title}</h3>
                <a href="javascript:location.reload()" class="btn btn-sm btn-danger mt-4 px-6">Try Again</a>
            </div>
        `;
    }

    // Initialize session via AJAX
    fetch("{{ route('whatsapp_accounts.start_session') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ session_id: sessionId })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            pollInterval = setInterval(checkStatus, 3000);
        } else {
            showError("Microservice Offline", data.message || "Failed to initialize.");
        }
    })
    .catch(err => {
        showError("Network Error", "Unable to reach the server.");
        console.error("Init error:", err);
    });

});
</script>
@endpush
