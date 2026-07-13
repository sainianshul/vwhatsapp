@extends('layouts.app')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'WhatsApp Accounts', 'url' => route('whatsapp_accounts.index')],
        ['label' => 'Edit Account'],
    ]" />

    <div class="card shadow-sm max-w-600px mx-auto mt-10">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">Edit Account Details</span>
                <span class="text-muted fw-semibold fs-7">Update alias for phone number {{ $account->phone_number ?? 'Unknown' }}</span>
            </h3>
        </div>
        
        <form action="{{ route('whatsapp_accounts.update', $account->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body pt-5">
                <div class="mb-5">
                    <label class="form-label fw-bold">Custom Name (Alias)</label>
                    <input type="text" name="name" class="form-control form-control-solid" placeholder="e.g. Sales Team, Support Line" value="{{ old('name', $account->name) }}">
                    <div class="text-muted fs-7 mt-2">Set a custom name to easily identify this account in dropdowns and reports.</div>
                    @error('name')
                        <div class="text-danger mt-1 fs-7">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-5">
                    <label class="form-label fw-bold">WhatsApp Name (Push Name)</label>
                    <input type="text" class="form-control form-control-solid text-gray-500" value="{{ $account->push_name ?? 'N/A' }}" readonly>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold">Status</label>
                    <div>
                        @if($account->status === 'connected')
                            <span class="badge badge-light-success fw-bold border border-success">Connected</span>
                        @else
                            <span class="badge badge-light-danger fw-bold border border-danger">Disconnected</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card-footer d-flex justify-content-end py-6">
                <a href="{{ route('whatsapp_accounts.index') }}" class="btn btn-light btn-active-light-primary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

@endsection
