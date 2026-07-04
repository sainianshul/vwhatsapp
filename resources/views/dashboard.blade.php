@extends('layouts.adminlte')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-primary" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div class="inner p-4">
                <h3>150</h3>
                <p>New Posts Fetched</p>
            </div>
            <div class="small-box-icon" style="opacity: 0.5;">
                <i class="bi bi-facebook fs-1"></i>
            </div>
            <a href="#" class="small-box-footer" style="border-radius: 0 0 12px 12px; background: rgba(0,0,0,0.15);">
                More info <i class="bi bi-arrow-right-circle-fill"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-success" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div class="inner p-4">
                <h3>53<sup style="font-size: 20px">%</sup></h3>
                <p>Positive Sentiment</p>
            </div>
            <div class="small-box-icon" style="opacity: 0.5;">
                <i class="bi bi-graph-up-arrow fs-1"></i>
            </div>
            <a href="#" class="small-box-footer" style="border-radius: 0 0 12px 12px; background: rgba(0,0,0,0.15);">
                More info <i class="bi bi-arrow-right-circle-fill"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-warning" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div class="inner p-4 text-white">
                <h3>44</h3>
                <p>Mentions Today</p>
            </div>
            <div class="small-box-icon" style="opacity: 0.5;">
                <i class="bi bi-twitter fs-1"></i>
            </div>
            <a href="#" class="small-box-footer" style="border-radius: 0 0 12px 12px; background: rgba(0,0,0,0.15);">
                <span class="text-white">More info <i class="bi bi-arrow-right-circle-fill"></i></span>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-danger" style="border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div class="inner p-4">
                <h3>65</h3>
                <p>Action Required</p>
            </div>
            <div class="small-box-icon" style="opacity: 0.5;">
                <i class="bi bi-exclamation-triangle fs-1"></i>
            </div>
            <a href="#" class="small-box-footer" style="border-radius: 0 0 12px 12px; background: rgba(0,0,0,0.15);">
                More info <i class="bi bi-arrow-right-circle-fill"></i>
            </a>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h3 class="card-title fw-bold">Recent Comments</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>Platform</th>
                                <th>User</th>
                                <th>Comment</th>
                                <th>AI Suggestion</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="bi bi-facebook text-primary fs-5"></i></td>
                                <td><div class="d-flex align-items-center"><div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">A</div> <span>Rahul K.</span></div></td>
                                <td><span class="text-truncate d-inline-block" style="max-width: 150px;">Good initiative by sir!</span></td>
                                <td><span class="badge text-bg-success bg-opacity-10 text-success p-2">Thanks Rahul! We appreciate your support.</span></td>
                                <td><button class="btn btn-sm btn-primary">Reply</button></td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-twitter text-info fs-5"></i></td>
                                <td><div class="d-flex align-items-center"><div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">S</div> <span>@sam_sharma</span></div></td>
                                <td><span class="text-truncate d-inline-block" style="max-width: 150px;">The roads are still broken in ward 4.</span></td>
                                <td><span class="badge text-bg-warning bg-opacity-10 text-warning p-2">We have noted this and forwarded to PWD.</span></td>
                                <td><button class="btn btn-sm btn-primary">Reply</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4 shadow-sm border-0 bg-primary text-white">
            <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                <h3 class="card-title fw-bold">System Status</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Facebook API Sync</span>
                    <span class="badge bg-white text-primary">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Twitter API Sync</span>
                    <span class="badge bg-white text-primary">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Background Queue</span>
                    <span class="badge bg-white text-success">0 Jobs Pending</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
