@extends('layouts.app')

@section('title', 'API Documentation')

@section('content')

    <x-breadcrumb :items="[
        ['label' => 'Developer API', 'url' => route('admin.developer_settings.index')],
        ['label' => 'API Documentation'],
    ]" />

    <div class="card">
        <div class="card-body">
            
            <h4 class="mb-5">WhatsApp Sending API Documentation</h4>
            <p>Use the following API to send WhatsApp messages from your external CRM or application.</p>
            
            <hr>

            <h5 class="mt-5">1. Endpoint URL</h5>
            <p>Make a <strong>POST</strong> request to the following URL:</p>
            <code>{{ url('/api/v1/messages/send') }}</code>

            <h5 class="mt-5">2. Required Headers</h5>
            <p>You must provide the following HTTP headers in your request:</p>
            <ul>
                <li><strong>Content-Type:</strong> <code>application/json</code></li>
                <li><strong>Authorization:</strong> <code>Bearer YOUR_API_KEY_HERE</code></li>
            </ul>

            <h5 class="mt-5">3. JSON Request Body</h5>
            <p>Provide the data in JSON format:</p>
            <pre class="bg-light p-3 rounded" style="max-width: 500px;"><code>{
  "to": "919876543210",
  "text": "Hello from CRM!"
}</code></pre>

            <p class="mt-5 mb-0"><strong>Note:</strong> The "to" field must include the country code without any + or spaces.</p>

        </div>
    </div>
@endsection
