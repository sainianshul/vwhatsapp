@extends('layouts.app')

@section('title', 'Send WhatsApp Message')

@section('content')

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">

            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                    <i class="ki-outline ki-check fs-2hx text-success me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-success">Success</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                    <i class="ki-outline ki-cross-circle fs-2hx text-danger me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-danger">Error</h4>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <form id="quick-send-form" method="POST" action="{{ route('whatsapp_messages.store') }}" enctype="multipart/form-data" class="form d-flex flex-column flex-lg-row">
                @csrf
                
                <!--begin::Aside column-->
                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                    
                    <!--begin::Sender Account-->
                    <div class="card card-flush py-4 card-bordered border-gray-300 shadow-sm">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Sender Account</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <label class="required form-label text-gray-900 fw-semibold">From</label>
                            <select name="whatsapp_account_id" class="form-select text-gray-900 border border-gray-300 bg-transparent @error('whatsapp_account_id') is-invalid @enderror" data-control="select2" data-hide-search="true" required>
                                <option value="">Select Account</option>
                                @foreach ($activeAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('whatsapp_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->push_name ?? $account->phone_number ?? 'Unknown' }} ({{ $account->phone_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('whatsapp_account_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="text-gray-600 fs-7 mt-2">Choose the active WhatsApp account to send the message from.</div>
                        </div>
                    </div>
                    <!--end::Sender Account-->
                    
                    <div class="d-flex flex-column gap-3">
                        <button type="submit" class="btn btn-primary fw-bold w-100 shadow-sm" id="btn-send-msg">
                            <i class="ki-outline ki-send fs-4 me-1"></i> Send Message
                        </button>
                    </div>
                </div>
                <!--end::Aside column-->

                <!--begin::Main column-->
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="card card-flush py-4 card-bordered border-gray-300 shadow-sm">
                        <div class="card-header">
                            <div class="card-title">
                                <h2 class="fs-5 fw-bold text-gray-900 m-0">Message Details</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            
                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="required form-label fw-semibold">Receiver Numbers</label>
                                <input class="form-control" value="{{ old('receiver_numbers') }}" name="receiver_numbers" id="receiver_numbers" placeholder="Enter up to 10 numbers (e.g. 919876543210)" required/>
                                <div class="text-muted fs-7 mt-2">Type a number and press Enter, Comma, or Space. You can also paste multiple numbers separated by commas. Max 10 numbers allowed. For more, use Bulk Campaigns.</div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="required form-label text-gray-900 fw-semibold">Message Text</label>
                                <textarea name="message_text" class="form-control text-gray-900 bg-transparent @error('message_text') is-invalid border-danger @else border border-gray-300 @enderror" rows="5" placeholder="Type your message here..." required>{{ old('message_text') }}</textarea>
                                @error('message_text')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-7">
                                <label class="form-label text-gray-900 fw-semibold">Attach Media (Optional)</label>
                                <input type="file" name="media_file" id="mediaFile" class="form-control" accept="image/*,video/mp4,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" />
                                <div class="text-muted fs-7 mt-2">Attach an image, video, audio, or document (Max 16MB).</div>
                            </div>
                            <!--end::Input group-->

                        </div>
                    </div>
                </div>
                <!--end::Main column-->

            </form>
        </div>
    </div>
    <!--end::Content-->

@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
        $(document).ready(function() {
            var tagifyInstance = new Tagify(document.querySelector("#receiver_numbers"), {
                maxTags: 10,
                pattern: /^[0-9+]+$/,
                delimiters: ",| |\n|\r",
                pasteAsTags: true,
                placeholder: "Enter numbers and press Enter",
                dropdown: { enabled: 0 }
            });

            $('#quick-send-form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let btn = $('#btn-send-msg');
                let originalText = btn.html();

                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending...');

                let formData = new FormData(this);

                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if(response.success) {
                            // Clear form
                            tagifyInstance.removeAllTags();
                            form.find('textarea[name="message_text"]').val('');
                            form.find('input[name="media_file"]').val('');

                            Swal.fire({
                                toast: true,
                                position: 'top',
                                showConfirmButton: false,
                                timer: 2000,
                                icon: 'success',
                                title: response.message || 'Messages sent successfully'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Something went wrong!'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endpush
