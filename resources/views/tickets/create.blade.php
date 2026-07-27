@extends('layouts.app')

@section('title', 'Create Ticket')

@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Create Support Ticket</h1>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light fw-semibold">
                <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
            </a>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('tickets.store') }}" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label class="required form-label">Subject</label>
                        <input type="text" name="subject" class="form-control mb-2" required placeholder="What do you need help with?" />
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Message</label>
                        <textarea name="message" class="form-control" rows="5" required placeholder="Describe your issue in detail..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
