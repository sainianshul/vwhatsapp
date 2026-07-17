@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Create Bulk Campaign</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.bulk_campaigns.index') }}" class="text-muted text-hover-primary">Bulk Campaigns</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-gray-900">Create</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.bulk_campaigns.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Left Col -->
                    <div class="col-lg-8">
                        <div class="card mb-5 mb-xl-10">
                            <div class="card-header border-0 cursor-pointer">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Campaign Details</h3>
                                </div>
                            </div>
                            <div class="card-body border-top p-9">
                                
                                <div class="mb-7">
                                    <label class="required form-label fw-semibold">Campaign Name</label>
                                    <input type="text" name="campaign_name" class="form-control" placeholder="e.g., Summer Sale 2026" required value="{{ old('campaign_name') }}" />
                                </div>

                                <div class="mb-7">
                                    <label class="required form-label fw-semibold">Select WhatsApp Account</label>
                                    <select name="whatsapp_account_id" class="form-select" data-control="select2" required>
                                        <option value="">Select an account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('whatsapp_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->phone_number }} ({{ $account->pushname }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-7">
                                    <div class="d-flex justify-content-between mb-2">
                                        <label class="required form-label fw-semibold mb-0">Upload Contacts CSV</label>
                                        <a href="{{ route('admin.bulk_campaigns.sample_csv') }}" class="text-primary fs-7 fw-bold"><i class="fa fa-download me-1"></i> Sample CSV</a>
                                    </div>
                                    <input type="file" name="csv_file" id="csvFile" class="form-control" accept=".csv" required />
                                </div>

                                <div class="mb-7">
                                    <div class="d-flex justify-content-between mb-3">
                                        <label class="form-label fw-semibold mb-0">Attach Media (Optional)</label>
                                    </div>
                                    
                                    <!-- Radio Buttons for Media Selection -->
                                    <div class="d-flex flex-wrap gap-6 mb-4">
                                        <div class="form-check form-check-custom">
                                            <input class="form-check-input" type="radio" name="media_type" value="none" id="media_type_none" checked />
                                            <label class="form-check-label text-gray-800" for="media_type_none">No Media</label>
                                        </div>
                                        <div class="form-check form-check-custom">
                                            <input class="form-check-input" type="radio" name="media_type" value="single" id="media_type_single" />
                                            <label class="form-check-label text-gray-800" for="media_type_single">Single File</label>
                                        </div>
                                        <div class="form-check form-check-custom">
                                            <input class="form-check-input" type="radio" name="media_type" value="dynamic" id="media_type_dynamic" />
                                            <label class="form-check-label text-gray-800" for="media_type_dynamic">Dynamic Group</label>
                                        </div>
                                    </div>

                                    <!-- Single File Upload Area -->
                                    <div id="singleFileArea" class="d-none">
                                        <input type="file" name="media_file" id="mediaFile" class="form-control" accept="image/*,video/mp4,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" />
                                        <!-- Media Preview -->
                                        <div id="mediaPreview" class="mt-3 d-none">
                                            <img id="mediaPreviewImg" src="" class="rounded border d-none" style="max-height: 120px; max-width: 200px;" />
                                            <div id="mediaPreviewFile" class="d-none badge badge-light-primary border border-primary px-3 py-2 fs-7">
                                                <i class="ki-outline ki-file fs-5 me-1"></i> <span id="mediaPreviewFileName"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dynamic Media Group Area -->
                                    <div id="dynamicMediaArea" class="d-none">
                                        <select name="media_group_id" class="form-select" data-control="select2" data-placeholder="Select a Media Group">
                                            <option value=""></option>
                                            @foreach($mediaGroups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="required form-label fw-semibold mb-0">Message Template</label>
                                        <span id="charCount" class="text-muted fs-8">0 / 4096</span>
                                    </div>
                                    <textarea name="message_template" id="messageTemplate" class="form-control" rows="6" placeholder="Hi @{{name}}, here is your special offer!" required>{{ old('message_template') }}</textarea>
                                </div>

                                <!-- Variable Buttons (appear after CSV upload) -->
                                <div id="variableButtons" class="mb-7 d-none">
                                    <label class="form-label fw-semibold text-muted fs-7">Click to insert variable:</label>
                                    <div id="variableButtonsContainer" class="d-flex flex-wrap gap-2"></div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Right Col -->
                    <div class="col-lg-4">
                        <div class="card mb-5">
                            <div class="card-header border-0 cursor-pointer">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Settings & Preview</h3>
                                </div>
                            </div>
                            <div class="card-body border-top p-9">
                                
                                <!-- Schedule At -->
                                <h4 class="fs-5 fw-bold text-gray-800 mb-4">Schedule</h4>
                                <div class="mb-7">
                                    <label class="form-label fw-semibold fs-7">Schedule At (Optional)</label>
                                    <input type="datetime-local" name="scheduled_at" id="scheduledAt" class="form-control form-control-sm" value="{{ old('scheduled_at') }}" />
                                </div>

                                <!-- Anti Ban Settings -->
                                <h4 class="fs-5 fw-bold text-gray-800 mb-4">Anti-Ban Delays</h4>
                                <div class="row mb-7">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold fs-7">Min Delay (sec)</label>
                                        <input type="number" name="delay_min" class="form-control form-control-sm" value="35" min="1" required />
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold fs-7">Max Delay (sec)</label>
                                        <input type="number" name="delay_max" class="form-control form-control-sm" value="60" min="2" required />
                                    </div>
                                </div>

                                <!-- Live Preview -->
                                <h4 class="fs-5 fw-bold text-gray-800 mb-4">Live Preview</h4>
                                <div class="p-5 mb-5 rounded" style="background-color: #efeae2; min-height: 200px; border-radius: 8px;">
                                    <div class="d-flex justify-content-end">
                                        <div class="p-3 shadow-sm position-relative" style="background-color: #d9fdd3; color: #111b21; border-radius: 8px 0px 8px 8px; max-width: 85%;">
                                            <!-- Message Tail (Fake CSS triangle) -->
                                            <div style="position: absolute; top: 0; right: -8px; width: 0; height: 0; border-top: 0px solid transparent; border-bottom: 12px solid transparent; border-left: 10px solid #d9fdd3;"></div>
                                            
                                            <div id="previewBox" class="text-wrap text-break fs-6" style="white-space: pre-wrap; margin-bottom: 15px;">Select a CSV and type a message to see the preview here...</div>
                                            
                                            <div class="text-end position-absolute bottom-0 end-0 p-2" style="font-size: 11px; color: #667781;">
                                                <span class="me-1">12:00</span>
                                                <i class="ki-solid ki-check-double text-info fs-7"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <button type="submit" id="submitBtn" class="btn btn-primary">Start Campaign</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('csvFile');
        const mediaInput = document.getElementById('mediaFile');
        const messageInput = document.getElementById('messageTemplate');
        const previewBox = document.getElementById('previewBox');
        const charCount = document.getElementById('charCount');
        const variableButtons = document.getElementById('variableButtons');
        const variableButtonsContainer = document.getElementById('variableButtonsContainer');
        const scheduledAtInput = document.getElementById('scheduledAt');
        const submitBtn = document.getElementById('submitBtn');
        let csvHeaders = [];
        let firstRowVariables = {};

        // ─── CSV Upload: Parse headers + first row ───
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                const text = event.target.result;
                // Use a proper CSV line parser that handles quoted values
                const rows = parseCSVLines(text);
                if (rows.length > 1) {
                    csvHeaders = rows[0].map(h => h.trim().toLowerCase());
                    const firstData = rows[1];
                    
                    firstRowVariables = {};
                    csvHeaders.forEach((header, index) => {
                        if (firstData[index] !== undefined) {
                            firstRowVariables[header] = firstData[index].trim();
                        }
                    });

                    // Render variable buttons
                    renderVariableButtons(csvHeaders);
                    updatePreview();
                }
            };
            reader.readAsText(file);
        });

        // ─── Proper CSV parser (handles quoted commas) ───
        function parseCSVLines(text) {
            const rows = [];
            let current = [];
            let field = '';
            let inQuotes = false;

            for (let i = 0; i < text.length; i++) {
                const ch = text[i];
                const next = text[i + 1];

                if (inQuotes) {
                    if (ch === '"' && next === '"') {
                        field += '"';
                        i++; // skip escaped quote
                    } else if (ch === '"') {
                        inQuotes = false;
                    } else {
                        field += ch;
                    }
                } else {
                    if (ch === '"') {
                        inQuotes = true;
                    } else if (ch === ',') {
                        current.push(field);
                        field = '';
                    } else if (ch === '\n' || (ch === '\r' && next === '\n')) {
                        current.push(field);
                        field = '';
                        if (current.length > 0) rows.push(current);
                        current = [];
                        if (ch === '\r') i++; // skip \n in \r\n
                    } else {
                        field += ch;
                    }
                }
            }
            // Push last row
            if (field || current.length > 0) {
                current.push(field);
                rows.push(current);
            }
            return rows;
        }

        // ─── Render variable buttons ───
        function renderVariableButtons(headers) {
            variableButtonsContainer.innerHTML = '';
            headers.forEach(header => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm btn-light-primary border border-primary fw-semibold';
                btn.innerHTML = '<i class="ki-outline ki-plus fs-7 me-1"></i>' + header;
                btn.addEventListener('click', function() {
                    insertAtCursor(messageInput, '@{{' + header + '}}');
                    updatePreview();
                });
                variableButtonsContainer.appendChild(btn);
            });
            variableButtons.classList.remove('d-none');
        }

        // ─── Insert text at cursor position ───
        function insertAtCursor(textarea, text) {
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const before = textarea.value.substring(0, start);
            const after = textarea.value.substring(end);
            textarea.value = before + text + after;
            // Place cursor after inserted text
            textarea.selectionStart = textarea.selectionEnd = start + text.length;
            textarea.focus();
            // Trigger input event for char counter
            textarea.dispatchEvent(new Event('input'));
        }

        // ─── Message Input: Update preview + char counter ───
        messageInput.addEventListener('input', function() {
            updateCharCount();
            updatePreview();
        });

        function updateCharCount() {
            const len = messageInput.value.length;
            charCount.textContent = len + ' / 4096';
            charCount.classList.toggle('text-danger', len > 4096);
            charCount.classList.toggle('text-muted', len <= 4096);
        }

        function updatePreview() {
            let text = messageInput.value;
            if (!text) {
                previewBox.innerHTML = "Select a CSV and type a message to see the preview here...";
                return;
            }

            if (Object.keys(firstRowVariables).length > 0) {
                for (const [key, value] of Object.entries(firstRowVariables)) {
                    const regex = new RegExp(`@\\{\\{${key}\\}\\}`, 'gi');
                    text = text.replace(regex, `<span class="badge badge-light-primary px-1">${value}</span>`);
                }
            }
            previewBox.innerHTML = text;
        }

        // ─── Media Preview ───
        mediaInput.addEventListener('change', function() {
            const file = this.files[0];
            const mediaPreview = document.getElementById('mediaPreview');
            const mediaPreviewImg = document.getElementById('mediaPreviewImg');
            const mediaPreviewFile = document.getElementById('mediaPreviewFile');
            const mediaPreviewFileName = document.getElementById('mediaPreviewFileName');

            if (!file) {
                mediaPreview.classList.add('d-none');
                return;
            }

            mediaPreview.classList.remove('d-none');

            if (file.type.startsWith('image/')) {
                // Show image thumbnail
                const reader = new FileReader();
                reader.onload = function(e) {
                    mediaPreviewImg.src = e.target.result;
                    mediaPreviewImg.classList.remove('d-none');
                    mediaPreviewFile.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                // Show filename badge
                mediaPreviewImg.classList.add('d-none');
                mediaPreviewFile.classList.remove('d-none');
                mediaPreviewFileName.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(1) + ' MB)';
            }
        });

        // ─── Schedule At: Dynamic button text ───
        scheduledAtInput.addEventListener('change', function() {
            if (this.value) {
                submitBtn.textContent = 'Schedule Campaign';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-warning');
            } else {
                submitBtn.textContent = 'Start Campaign';
                submitBtn.classList.remove('btn-warning');
                submitBtn.classList.add('btn-primary');
            }
        });

        // Init char counter
        updateCharCount();
        // ─── Radio Button Logic ───
        const mediaRadios = document.querySelectorAll('input[name="media_type"]');
        const singleFileArea = document.getElementById('singleFileArea');
        const dynamicMediaArea = document.getElementById('dynamicMediaArea');
        
        mediaRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'none') {
                    singleFileArea.classList.add('d-none');
                    dynamicMediaArea.classList.add('d-none');
                } else if (this.value === 'single') {
                    singleFileArea.classList.remove('d-none');
                    dynamicMediaArea.classList.add('d-none');
                } else if (this.value === 'dynamic') {
                    singleFileArea.classList.add('d-none');
                    dynamicMediaArea.classList.remove('d-none');
                }
            });
        });

    });
</script>
@endpush
