@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Ticket #{{ $ticket->id }}</h1>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light fw-semibold">
                <i class="ki-outline ki-arrow-left fs-4 me-1"></i>Back
            </a>
            @if(auth()->user()->role === 'admin')
                <form action="{{ route('tickets.status', $ticket) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $ticket->status === 'open' ? 'closed' : 'open' }}">
                    <button type="submit" class="btn btn-sm btn-{{ $ticket->status === 'open' ? 'danger' : 'success' }}">
                        {{ $ticket->status === 'open' ? 'Close Ticket' : 'Reopen Ticket' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">{{ $ticket->subject }}</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">
                        Submitted by {{ $ticket->user->name }} on {{ $ticket->created_at->format('d M Y, H:i') }}
                        - Status: <span class="badge badge-light-{{ $ticket->status === 'open' ? 'success' : 'danger' }}">{{ ucfirst($ticket->status) }}</span>
                    </span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="fs-5 fw-normal text-gray-800 bg-light p-5 rounded">
                    {{ $ticket->message }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
