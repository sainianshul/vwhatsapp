<div class="row g-5 g-xxl-8">
    <div class="col-xl-6">
        <!-- Profile Details Card -->
        <div class="card card-bordered shadow-sm border-gray-300 mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Profile Details</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4">
                        <tbody>
                            <tr>
                                <td class="text-gray-600 fw-semibold min-w-150px">Full Name</td>
                                <td class="text-gray-900 fw-bold">{{ $subject->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-semibold">Designation / Role</td>
                                <td class="text-gray-900 fw-bold">{{ $subject->designation ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-semibold">Tracking Status</td>
                                <td>
                                    <span class="badge badge-light-{{ $subject->status_color }} fw-bold">{{ ucfirst($subject->status) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-semibold">Added On</td>
                                <td class="text-gray-900 fw-bold">{{ $subject->created_at->format('d M, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="text-gray-600 fw-semibold">Created By</td>
                                <td class="text-gray-900 fw-bold">{{ $subject->creator->name ?? 'System' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <!-- Network Distribution Card -->
        <div class="card card-bordered shadow-sm border-gray-300 mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">Network Distribution</span>
                </h3>
            </div>
            <div class="card-body py-3">
                @if($subject->socialAccounts->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center h-200px">
                        <i class="ki-outline ki-chart-pie-3 fs-5x text-gray-400 mb-3"></i>
                        <h4 class="text-gray-500 fw-semibold">No social accounts linked yet.</h4>
                        <a href="{{ route('subjects.accounts.create', $subject) }}" class="btn btn-sm btn-light-primary mt-4">
                            Link First Account
                        </a>
                    </div>
                @else
                    <!-- Simple visualization of accounts by platform -->
                    <div class="d-flex flex-column h-100 justify-content-center">
                        @php
                            $platforms = $subject->socialAccounts->groupBy('platform');
                        @endphp
                        @foreach($platforms as $platform => $accounts)
                            @php
                                $account = $accounts->first(); // Just for the helper methods
                            @endphp
                            <div class="d-flex flex-stack mb-5">
                                <div class="d-flex align-items-center me-3">
                                    <div class="symbol symbol-40px me-4">
                                        <span class="symbol-label bg-light-{{ $account->platform_color }}">
                                            <i class="{{ $account->platform_icon }} fs-2 text-{{ $account->platform_color }}"></i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="text-gray-900 text-hover-primary fw-bold fs-5">{{ ucfirst($platform) }}</span>
                                        <span class="text-muted fw-semibold fs-7">{{ $accounts->count() }} Linked Account(s)</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column text-end">
                                    <span class="text-gray-900 fw-bold fs-6">{{ $accounts->sum(fn($a) => $a->posts_count) }} Posts</span>
                                    <span class="text-muted fw-semibold fs-7">Scraped Total</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
