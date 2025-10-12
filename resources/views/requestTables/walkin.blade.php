@extends('layout.blankpage')

@section('content')

<style>
    .tooltip {
        position: relative;
        display: inline-block;
    }

    .tooltip .tooltip-text {
        visibility: hidden;
        width: 160px;
        background-color: black;
        color: #fff;
        text-align: center;
        padding: 6px;
        border-radius: 4px;

        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);

        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .required-label::after {
        content: " *";
        color: red;
    }

    .sync-panel {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-top: 15px;
    }

    .sync-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 15px;
        transition: background 0.2s;
    }

    .sync-header:hover {
        background: #e9ecef;
    }

    .toggle-icon {
        transition: transform 0.3s;
        font-size: 20px;
        font-weight: bold;
    }

    .toggle-icon.open {
        transform: rotate(180deg);
    }

    .sync-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .sync-content.open {
        max-height: 500px;
    }

    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
        background: #dc3545;
    }

    .status-indicator.online {
        background: #28a745;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .sync-button {
        width: 100%;
        padding: 12px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        margin-top: 10px;
    }

    .sync-button:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }

    .sync-button:hover:not(:disabled) {
        background: #0056b3;
    }

    .pending-count {
        background: #ffc107;
        color: #000;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: bold;
        display: inline-block;
        margin-top: 8px;
    }

    .sync-details {
        margin-top: 10px;
        font-size: 13px;
        color: #666;
    }

    .sync-details div {
        margin: 4px 0;
    }
</style>

@include('layout.partials.message')
<div class="row justify-content-space-evenly">
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 rounded-lg mt-3">
            <div class="card-header text-white" style="background-color: #1f2937;">
                <h3 class="my-2">Document Request Form</h3>
            </div>
            <div class="card-body p-4">

                <form action="{{ route('walkin.store') }}" method="POST" id="walkinForm">
                    @csrf

                    <!-- Document Request Info -->
                    <h5 class="mb-3">📄 Document Request Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('request_schl_entity') is-invalid @enderror"
                                    id="inputRequestSchlEntity" type="text" name="request_schl_entity"
                                    value="{{ old('request_schl_entity') }}"
                                    placeholder="Enter Requesting School/Entity" required
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Enter the name of the school requesting the document.">
                                <label for="inputRequestSchlEntity" class="required-label">Requesting School/Entity</label>
                                @error('request_schl_entity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('document_id') is-invalid @enderror"
                                    id="inputDocumentId" name="document_id" required>
                                    <option value="" disabled selected>Select Document Type</option>
                                    @foreach($DocType as $doc)
                                    <option value="{{ $doc->id }}" {{ old('document_id') == $doc->id ? 'selected' : '' }}>
                                        {{ $doc->DocType }}
                                    </option>
                                    @endforeach
                                </select>
                                <label for="inputDocumentId" class="required-label">Requested Document</label>
                                @error('document_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control" id="inputReleaseMode" type="text"
                                    value="Pickup" name="release_mode" readonly>
                                <label for="inputReleaseMode" class="required-label">Release Mode</label>
                            </div>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <h5 class="mt-4 mb-3">👩‍🎓 Student Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('student_first_name') is-invalid @enderror"
                                    id="inputStudentFirstName" type="text" name="student_first_name"
                                    value="{{ old('student_first_name') }}"
                                    placeholder="Enter Student's First Name" required>
                                <label for="inputStudentFirstName" class="required-label">First Name</label>
                                @error('student_first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('student_last_name') is-invalid @enderror"
                                    id="inputStudentLastName" type="text" name="student_last_name"
                                    value="{{ old('student_last_name') }}"
                                    placeholder="Enter Student's Last Name" required>
                                <label for="inputStudentLastName" class="required-label">Last Name</label>
                                @error('student_last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('lrn') is-invalid @enderror"
                                    id="inputLRN" type="text" name="lrn"
                                    value="{{ old('lrn') }}"
                                    placeholder="Enter LRN"
                                    maxlength="12">
                                <label for="inputLRN">LRN (12-digit)</label>
                                @error('lrn')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Real-time validation messages -->
                            <div id="lrnValidation" class="mt-1 small">
                                <div id="lrnRuleNumbers" class="text-muted">❌ Only numbers allowed</div>
                                <div id="lrnRuleLength" class="text-muted">❌ Must be exactly 12 digits</div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('grade_level') is-invalid @enderror"
                                    id="inputGradeLevel" name="grade_level" required>
                                    <option value="" disabled selected>Select Grade Level</option>
                                    @foreach($grade as $g)
                                    <option value="{{ $g }}" {{ old('grade_level') == $g ? 'selected' : '' }}>{{ $g }}</option>
                                    @endforeach
                                </select>
                                <label for="inputGradeLevel" class="required-label">Grade Level</label>
                                @error('grade_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select @error('student_status') is-invalid @enderror"
                                    id="inputStudentStatus" name="student_status" required>
                                    <option value="" disabled selected>Select Student Status</option>
                                    @foreach($stat as $s)
                                    <option value="{{ $s }}" {{ old('student_status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                <label for="inputStudentStatus" class="required-label">Student Status</label>
                                @error('student_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control @error('last_sy_attended') is-invalid @enderror"
                                    id="inputLastSYAttended" type="text" name="last_sy_attended"
                                    value="{{ old('last_sy_attended') }}"
                                    placeholder="Enter Last SY Attended" required>
                                <label for="inputLastSYAttended" class="required-label">Last School Year Attended</label>
                                @error('last_sy_attended')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating">
                                <input class="form-control @error('email_address') is-invalid @enderror"
                                    id="email_address" type="email" name="email_address"
                                    value="{{ old('email_address') }}"
                                    placeholder="Enter Email Address" required>
                                <label for="email_address" class="required-label">Email Address</label>
                                @error('email_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-4 text-center">
                        <button class="btn btn-lg text-white fw-semibold px-5 py-2 rounded-pill"
                            style="background-color: #1dd3b0; box-shadow: 0 4px 10px rgba(29, 211, 176, 0.5);"
                            id="submitButton"
                            type="button">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mt-3 d-none d-lg-block">
        <!-- QR Code Card -->
        <div class="card" style="width: 18rem;">
            <img src="{{ asset('images/qrCode.png') }}" class="card-img-top" alt="ubnhsLogo">
            <div class="card-body">
                <p class="card-text">Thank you for using our Online Document Request and Management System! After completing your request,
                    Please scan the Qr Code to answer a quick survey and help us improve the system for our research.</p>
            </div>
        </div>

        <!-- Sync Panel -->
        <div class="sync-panel">
            <div class="sync-header" onclick="toggleSyncPanel()">
                <div>
                    <span class="status-indicator" id="status-indicator"></span>
                    <strong id="connection-status">Checking...</strong>
                </div>
                <span class="toggle-icon" id="toggle-icon">▼</span>
            </div>

            <div class="sync-content" id="sync-content">
                <div class="sync-details" id="sync-details">
                    <div>Pending Students: <strong id="pending-students">0</strong></div>
                    <div>Pending Accounts: <strong id="pending-accounts">0</strong></div>
                    <div>Pending Requests: <strong id="pending-requests">0</strong></div>
                </div>

                <div>
                    <span class="pending-count" id="total-pending">0 pending</span>
                </div>

                <button class="sync-button" id="sync-btn" onclick="syncData()" disabled>
                    Sync Now
                </button>

                <div style="margin-top: 10px; font-size: 12px; color: #666;">
                    Last checked: <span id="last-checked">Never</span>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Toggle sync panel
    function toggleSyncPanel() {
        const content = document.getElementById('sync-content');
        const icon = document.getElementById('toggle-icon');

        content.classList.toggle('open');
        icon.classList.toggle('open');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const lrnInput = document.getElementById('inputLRN');
        const ruleNumbers = document.getElementById('lrnRuleNumbers');
        const ruleLength = document.getElementById('lrnRuleLength');

        lrnInput.addEventListener('input', function () {
            // Remove non-numeric chars
            this.value = this.value.replace(/\D/g, '');

            // Check if only numbers
            if (/^\d*$/.test(this.value)) {
                ruleNumbers.textContent = "✅ Only numbers allowed";
                ruleNumbers.className = "text-success";
            } else {
                ruleNumbers.textContent = "❌ Only numbers allowed";
                ruleNumbers.className = "text-danger";
            }

            // Check length
            if (this.value.length === 12) {
                ruleLength.textContent = "✅ Exactly 12 digits";
                ruleLength.className = "text-success";
            } else {
                ruleLength.textContent = "❌ Must be exactly 12 digits";
                ruleLength.className = "text-danger";
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        document.getElementById('submitButton').addEventListener('click', function() {
            const button = this;
            button.disabled = true;

            Swal.fire({
                title: 'Confirm Submission',
                text: "Are you sure you want to submit this request?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1dd3b0',
                cancelButtonColor: '#1f2937',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Submitting...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('walkinForm').submit();
                } else {
                    button.disabled = false;
                }
            });
        });

    });

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Check connection every 30 seconds
    setInterval(checkConnection, 30000);

    // Initial check
    checkConnection();
    loadSyncStatus();

    /**
     * Check if online database is reachable
     */
    function checkConnection() {
        fetch('/sync/check-connection')
            .then(response => response.json())
            .then(data => {
                const indicator = document.getElementById('status-indicator');
                const statusText = document.getElementById('connection-status');
                const syncBtn = document.getElementById('sync-btn');
                const lastChecked = document.getElementById('last-checked');

                if (data.online) {
                    indicator.classList.add('online');
                    statusText.textContent = 'Online';
                    syncBtn.disabled = false;
                } else {
                    indicator.classList.remove('online');
                    statusText.textContent = 'Offline';
                    syncBtn.disabled = true;
                }

                lastChecked.textContent = new Date().toLocaleTimeString();
                loadSyncStatus();
            })
            .catch(error => {
                console.error('Connection check failed:', error);
                document.getElementById('connection-status').textContent = 'Error';
            });
    }

    /**
     * Load pending sync count
     */
    function loadSyncStatus() {
        fetch('/sync/status')
            .then(response => response.json())
            .then(data => {
                document.getElementById('pending-students').textContent = data.pending_students;
                document.getElementById('pending-accounts').textContent = data.pending_accounts;
                document.getElementById('pending-requests').textContent = data.pending_requests;
                document.getElementById('total-pending').textContent = data.total_pending + ' pending';
            })
            .catch(error => {
                console.error('Status load failed:', error);
            });
    }


    /**
     * Trigger sync
     */
    function syncData() {
        const syncBtn = document.getElementById('sync-btn');
        syncBtn.disabled = true;
        syncBtn.textContent = 'Syncing...';

        fetch('/sync/trigger', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            syncBtn.textContent = 'Sync Now';

            console.log('Sync response:', data); // Debug log

            // The response structure is { message: '...', data: { ... } }
            const syncData = data.data;

            if (syncData.success) {
                const studentsCount = syncData.pushed?.students || 0;
                const accountsCount = syncData.pushed?.accounts || 0;
                const requestsCount = syncData.pushed?.requests || 0;
                const failedCount = syncData.failed || 0;

                alert(`✅ Sync Completed!\n\nStudents synced: ${studentsCount}\nAccounts synced: ${accountsCount}\nRequests synced: ${requestsCount}\nFailed: ${failedCount}`);
            } else {
                const errors = syncData.errors || ['Unknown error'];
                alert(`⚠️ Sync completed with errors:\n\n${errors.join('\n')}`);
            }

            loadSyncStatus();
            checkConnection();
        })
        .catch(error => {
            syncBtn.textContent = 'Sync Now';
            syncBtn.disabled = false;
            alert('❌ Sync failed: ' + error.message);
            console.error('Sync failed:', error);
        });
    }

    // Show notification when form is submitted offline
    @if(session('Success'))
        alert('{{ session('Success') }}');
    @endif

    @if(session('Error'))
        alert('{{ session('Error') }}');
    @endif
</script>
@endsection
