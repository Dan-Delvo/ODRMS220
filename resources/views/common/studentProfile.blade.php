@extends('layout.studentpage')

@section('content')
@include('layout.partials.studentMessage')

<style>
    /* Modal Styling */
    .modal-content {
        background: #1e293b;
        border: 1px solid rgba(100, 116, 139, 0.3);
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        color: #f1f5f9;
    }

    .modal-header {
        background: #1dd3b0;
        color: #0f172a;
        border-radius: 16px 16px 0 0;
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.125rem;
        display: flex;
        align-items: center;
    }

    .modal-title i {
        margin-right: 0.5rem;
    }

    .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-body p {
        color: #cbd5e1;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1.25rem;
    }

    /* Form Controls */
    .form-control {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(100, 116, 139, 0.3);
        color: #f1f5f9;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        background: rgba(15, 23, 42, 0.7);
        border-color: #1dd3b0;
        box-shadow: 0 0 0 3px rgba(29, 211, 176, 0.1);
        color: #f1f5f9;
        outline: none;
    }

    .form-control::placeholder {
        color: #64748b;
    }

    .form-label {
        color: #e2e8f0;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    /* OTP Input */
    #otpCode {
        text-align: center;
        font-size: 1.25rem;
        letter-spacing: 0.25rem;
        font-weight: 600;
    }

    /* Feedback Messages */
    #otpFeedback {
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.5rem;
        border-radius: 6px;
        margin-top: 0.5rem;
    }

    #otpFeedback:not(:empty) {
        display: block;
    }

    /* Password Toggle */
    .toggle-password {
        cursor: pointer;
        color: #94a3b8;
        transition: color 0.2s ease;
    }

    .toggle-password:hover {
        color: #1dd3b0;
    }

    /* Password Strength */
    #passwordStrength {
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.25rem 0;
    }

    /* Password Rules */
    #passwordRules small {
        font-size: 0.8rem;
        display: block;
        margin: 0.25rem 0;
        transition: color 0.2s ease;
    }

    #passwordRules small.text-success {
        color: #10b981 !important;
    }

    #passwordRules small.text-danger {
        color: #f87171 !important;
    }

    /* Password Match */
    #passwordMatch {
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.25rem 0;
    }

    /* Invalid Feedback */
    .invalid-feedback {
        color: #fca5a5;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    /* Modal Footer */
    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(100, 116, 139, 0.2);
    }

    /* Buttons */
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-primary-custom {
        background: #1dd3b0;
        color: #0f172a;
    }

    .btn-primary-custom:hover {
        background: #14b8a6;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(29, 211, 176, 0.3);
    }

    .btn-outline-light {
        background: transparent;
        color: #cbd5e1;
        border: 1px solid rgba(203, 213, 225, 0.3);
    }

    .btn-outline-light:hover {
        background: rgba(203, 213, 225, 0.1);
        color: #f1f5f9;
        border-color: rgba(203, 213, 225, 0.5);
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Loading Spinner */
    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="main-content mt-4" style="background-color: #0f172a; min-height: 100vh;">
    <div class="container-fluid py-4 text-light">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center flex-wrap">
                    <div class="me-3 mb-2">
                        <i class="fas fa-user-graduate fs-2" style="color:#1dd3b0;"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 fw-bold" style="color:#f1f5f9;">Student Profile</h2>
                        <p class="mb-0" style="color:#e2e8f0;">View and manage your personal information</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile View (visible only on small screens) -->
        <div class="d-block d-md-none">
            <!-- Combined Mobile Profile and Student Information Card -->
            <div class="card shadow-sm border-0 mb-4"
                style="background:#1e293b; border:1px solid #334155;">
                <div class="card-header border-0 py-2" style="background:#1dd3b0; color:#0f172a;">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-id-card me-2"></i>
                        Student Information
                    </h6>
                </div>
                <form class="accountUpdateForm" action="{{ route('student.profile.update', $studInfo->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="student_info">

                    <!-- Profile Section -->
                    <div class="card-body py-4 text-center" style="border-bottom: 1px solid #334155;">
                        <!-- Avatar / Image -->
                        <div class="mb-3 text-center">
                            @if(!empty($studInfo->Id_image))
                            <div class="mx-auto text-center" style="max-width: 220px;">
                                <div style="padding: 8px; background:#334155; border-radius: 12px;">
                                    <img src="/public/{{trim($studInfo->Id_image)}}"
                                        alt="Student Image"
                                        class="mobile-profile-img"
                                        style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">

                                </div>

                                <!-- Replace Image Button -->
                                <input type="file" id="mobileReplaceImageInput" name="Id_image" accept="image/*" class="d-none">
                                <button type="button" id="mobileReplaceImageBtn" class="btn btn-sm mt-2"
                                    style="background:#1dd3b0; color:#0f172a;">
                                    Replace Image
                                </button>

                                <div id="mobile-replace-preview" class="mt-3 d-none">
                                    <div class="mx-auto" style="max-width: 180px; padding: 8px; background:#334155; border-radius: 12px;">
                                        <img id="mobileReplacePreviewImg" src="" alt="Preview"
                                            style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">
                                    </div>
                                </div>

                            </div>
                            @else
                            <!-- Placeholder -->
                            <div id="mobile-placeholder" class="mb-3">
                                <div class="d-flex align-items-center justify-content-center mx-auto"
                                    style="width: 180px; height: 220px; background:#334155; border-radius: 12px;">
                                    <i class="fas fa-user fa-2x" style="color:#1dd3b0;"></i>
                                </div>
                            </div>

                            <!-- Hidden input -->
                            <input type="file" id="mobileImageInput" name="Id_image"
                                accept="image/*" class="d-none">

                            <!-- Button -->
                            <button type="button" id="mobileAddImageBtn" class="btn btn-sm"
                                style="background:#1dd3b0; color:#0f172a;">
                                Add Image
                            </button>

                            <!-- Preview -->
                            <div id="mobile-image-preview" class="mt-3 d-none">
                                <div class="mx-auto" style="max-width: 180px; padding: 8px; background:#334155; border-radius: 12px;">
                                    <img id="mobilePreviewImg" src="" alt="Preview"
                                        style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">
                                </div>
                            </div>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-2" style="color:#f1f5f9;">{{ $studInfo->FirstName }} {{ $studInfo->LastName }}</h4>
                        <p class="mb-3" style="color:#cbd5e1;">{{ $studInfo->Grade_level }} • {{ $studInfo->Std_status }}</p>
                        @if(trim($studInfo->Std_status) === 'Regular')
                        <span class="badge mb-3" style="background:#1dd3b0; color:#0f172a;">Active Student</span>
                        @elseif(trim($studInfo->Std_status) === 'Alumni')
                        <span class="badge mb-3" style="background:#1dd3b0; color:#ffffff;">Alumni</span>
                        @else
                        <span class="badge mb-3" style="background:#f87171; color:#0f172a;">{{ $studInfo->Std_status }}</span>
                        @endif
                    </div>

                    <!-- Student Information Fields Section -->
                    <div class="card-body py-3" style="color:#f1f5f9;">
                        <div class="row g-3">
                            <!-- Student ID & LRN side by side -->
                            <div class="mb-3 col-6">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Student ID</label>
                                <input type="text" name="id" class="form-control" value="{{ $studInfo->id }}" readonly>
                            </div>

                            <div class="mb-3 col-6">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">LRN</label>
                                <input
                                    type="text"
                                    name="LRN"
                                    id="lrn-mobile"
                                    class="form-control @error('LRN') is-invalid @enderror"
                                    value="{{ old('LRN', $studInfo->LRN) }}"
                                    maxlength="12"
                                    pattern="\d{12}">
                                @error('LRN')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                                <div id="lrn-error-mobile" class="text-danger mt-1" style="font-size: 0.875rem; display: none;">
                                    <i class="fas fa-exclamation-circle me-1"></i>LRN must be exactly 12 digits
                                </div>
                                <div id="lrn-success-mobile" class="text-success mt-1" style="font-size: 0.875rem; display: none;">
                                    <i class="fas fa-check-circle me-1"></i>Valid LRN
                                </div>
                            </div>

                            <!-- Grade Level, Status, and Last SY -->
                            <div class="mb-3 col-6 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Grade Level</label>
                                <select name="Grade_level" class="form-select white-dropdown @error('Grade_level') is-invalid @enderror">
                                    <option value="" disabled selected>Select Grade</option>
                                    @foreach($grade as $g)
                                    <option value="{{ $g }}" {{ old('Grade_level', $studInfo->Grade_level) == $g ? 'selected' : '' }}>
                                        {{ $g }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('Grade_level')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3 col-6 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Status</label>
                                <select name="Std_status" class="form-select white-dropdown @error('Std_status') is-invalid @enderror">
                                    <option value="" disabled selected>Select Status</option>
                                    @foreach($stat as $s)
                                    <option value="{{ $s }}" {{ old('Std_status', $studInfo->Std_status) == $s ? 'selected' : '' }}>
                                        {{ $s }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('Std_status')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3 col-12 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Last School Year Attended</label>
                                <input
                                    type="text"
                                    name="Last_sy_attended"
                                    class="form-control @error('Last_sy_attended') is-invalid @enderror"
                                    value="{{ old('Last_sy_attended', $studInfo->Last_sy_attended) }}">
                                @error('Last_sy_attended')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Name Fields -->
                            <div class="mb-3 col-6 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">First Name</label>
                                <input
                                    type="text"
                                    name="FirstName"
                                    class="form-control @error('FirstName') is-invalid @enderror"
                                    value="{{ old('FirstName', $studInfo->FirstName) }}">
                                @error('FirstName')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3 col-6 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Middle Name</label>
                                <input
                                    type="text"
                                    name="MiddleName"
                                    class="form-control @error('MiddleName') is-invalid @enderror"
                                    value="{{ old('MiddleName', $studInfo->MiddleName) }}">
                                @error('MiddleName')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3 col-8 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Last Name</label>
                                <input
                                    type="text"
                                    name="LastName"
                                    class="form-control @error('LastName') is-invalid @enderror"
                                    value="{{ old('LastName', $studInfo->LastName) }}">
                                @error('LastName')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3 col-4 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Suffix</label>
                                <input
                                    type="text"
                                    name="Suffix"
                                    class="form-control @error('Suffix') is-invalid @enderror"
                                    value="{{ old('Suffix', $studInfo->Suffix) }}">
                                @error('Suffix')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>


                        <!-- Save Button for Student Info -->
                        <div class="mt-3 text-end">
                            <button type="submit" id="submit-btn-mobile" class="btn" style="background:#1dd3b0; color:#0f172a;">
                                <i class="fas fa-save me-2"></i>Save Student Info
                            </button>s
                        </div>
                    </div>
                </form>
            </div>

            <!-- Mobile Account Information Card -->
            <div class="card shadow-sm border-0 mb-3" style="background:#1e293b; border:1px solid #334155;">
                <div class="card-header border-0 py-2" style="background:#1dd3b0; color:#0f172a;">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-user-cog me-2"></i>
                        Account Details
                    </h6>
                </div>
                <form class="accountUpdateForm" action="{{ route('student.profile.verifyUpdate', $studInfo->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="update_type" value="account_info">
                    <div class="card-body py-3" style="color:#f1f5f9;">
                        <div class="row g-3">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Username:</label>
                                <input type="text" name="username" class="form-control" value="{{ $studInfo->account->username }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ $studInfo->account->email_address ?? '' }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Password:</label>
                                <button type="button" class="btn btn-outline-light w-100" id="mobileChangePasswordBtn">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </div>
                        </div>

                        <!-- Save Button for Account Info -->
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn" style="background:#1dd3b0; color:#0f172a;">
                                <i class="fas fa-save me-2"></i>Save Account Details
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tablet & Desktop View (hidden on small screens) -->
        <div class="d-none d-md-block">
            <div class="row g-4">
                <!-- Student Information (Full Width) -->
                <div class="col-12">
                    <div class="card shadow-sm border-0 h-100"
                        style="background:#1e293b; border:1px solid #334155;">
                        <form class="accountUpdateForm" action="{{ route('student.profile.update', $studInfo->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-0">
                                <div class="card-header border-0" style="background:#1dd3b0; color:#0f172a;">
                                    <h5 class="mb-0 d-flex align-items-center">
                                        <i class="fas fa-id-card me-2"></i>
                                        Student Information
                                    </h5>
                                </div>

                                <!-- Profile Section -->
                                <div class="col-12 col-md-4 text-center d-flex align-items-center">
                                    <div class="card-body py-2 w-100">
                                        <!-- Avatar / Image -->
                                        @if(!empty($studInfo->Id_image))
                                        <div class="mx-auto text-center" style="max-width: 220px;">
                                            <div style="background:#334155; border-radius: 12px;">
                                                <img src="/public/{{trim($studInfo->Id_image)}}"
                                                    alt="Student Image"
                                                    class="desktop-profile-img"
                                                    style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">
                                            </div>

                                            <!-- Replace Image Button -->
                                            <input type="file" id="desktopReplaceImageInput" name="Id_image" accept="image/*" class="d-none">
                                            <button type="button" id="desktopReplaceImageBtn" class="btn btn-sm mt-2"
                                                style="background:#1dd3b0; color:#0f172a;">
                                                Replace Image
                                            </button>

                                            <!-- Preview (after choosing a new one) -->
                                            <div id="desktop-replace-preview" class="mt-3 d-none">
                                                <div class="mx-auto" style="max-width: 220px; padding: 8px; background:#334155; border-radius: 12px;">
                                                    <img id="desktopReplacePreviewImg" src="" alt="Preview"
                                                        style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">
                                                </div>
                                            </div>

                                        </div>
                                        @else
                                        <!-- Placeholder -->
                                        <div id="desktop-placeholder" class="mb-3">
                                            <div class="d-flex align-items-center justify-content-center mx-auto"
                                                style="width: 220px; height: 280px; background:#334155; border-radius: 12px;">
                                                <i class="fas fa-user fa-3x" style="color:#1dd3b0;"></i>
                                            </div>
                                        </div>

                                        <input type="file" id="desktopImageInput" name="Id_image"
                                            accept="image/*" class="d-none">

                                        <button type="button" id="desktopAddImageBtn" class="btn btn-sm"
                                            style="background:#1dd3b0; color:#0f172a;">
                                            Add Image
                                        </button>

                                        <!-- Preview -->
                                        <div id="desktop-image-preview" class="mt-3 d-none">
                                            <div class="mx-auto" style="max-width: 220px; padding: 8px; background:#334155; border-radius: 12px;">
                                                <img id="desktopPreviewImg" src="" alt="Preview"
                                                    style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">
                                            </div>
                                        </div>
                                        @endif


                                        <h4 class="fw-bold mb-2" style="color:#f1f5f9;">{{ $studInfo->FirstName }} {{ $studInfo->LastName }}</h4>
                                        <p class="mb-3" style="color:#cbd5e1;">{{ $studInfo->Grade_level }} • {{ $studInfo->Std_status }}</p>

                                        @if(trim($studInfo->Std_status) === 'Regular')
                                        <span class="badge mb-4" style="background:#1dd3b0; color:#0f172a;">Active Student</span>
                                        @elseif(trim($studInfo->Std_status) === 'Alumni')
                                        <span class="badge mb-4" style="background:#1dd3b0; color:#ffffff;">Alumni</span>
                                        @else
                                        <span class="badge mb-4" style="background:#f87171; color:#0f172a;">{{ $studInfo->Std_status }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Student Information Section -->
                                <div class="col-12 col-md-8">

                                    <input type="hidden" name="update_type" value="student_info">
                                    <div class="card-body" style="color:#f1f5f9;">
                                        <div class="row g-3">
                                            <!-- Student ID -->
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Student ID:</label>
                                                    <input type="text" name="id" class="form-control" value="{{ $studInfo->id }}" readonly>
                                                </div>
                                            </div>

                                            <!-- LRN -->
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">LRN:</label>
                                                    <input
                                                        type="text"
                                                        name="LRN"
                                                        id="lrn-desktop"
                                                        class="form-control @error('LRN') is-invalid @enderror"
                                                        value="{{ old('LRN', $studInfo->LRN) }}"
                                                        maxlength="12"
                                                        pattern="\d{12}">
                                                    @error('LRN')
                                                    <div class="invalid-feedback">
                                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                    </div>
                                                    @enderror
                                                    <div id="lrn-error-desktop" class="text-danger mt-1" style="font-size: 0.875rem; display: none;">
                                                        <i class="fas fa-exclamation-circle me-1"></i>LRN must be exactly 12 digits
                                                    </div>
                                                    <div id="lrn-success-desktop" class="text-success mt-1" style="font-size: 0.875rem; display: none;">
                                                        <i class="fas fa-check-circle me-1"></i>Valid LRN
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Grade Level -->
                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Grade Level:</label>
                                                    <select name="Grade_level" class="form-select white-dropdown @error('Grade_level') is-invalid @enderror">
                                                        <option value="" disabled selected>Select Grade</option>
                                                        @foreach($grade as $g)
                                                        <option value="{{ $g }}" {{ old('Grade_level', $studInfo->Grade_level) == $g ? 'selected' : '' }}>
                                                            {{ $g }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('Grade_level')
                                                    <div class="invalid-feedback">
                                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Status:</label>
                                                    <select name="Std_status" class="form-select white-dropdown @error('Std_status') is-invalid @enderror">
                                                        <option class="text-white" value="" disabled selected>Select Status</option>
                                                        @foreach($stat as $s)
                                                        <option value="{{ $s }}" {{ old('Std_status', $studInfo->Std_status) == $s ? 'selected' : '' }}>
                                                            {{ $s }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('Std_status')
                                                    <div class="invalid-feedback">
                                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Last School Year -->
                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Last School Year:</label>
                                                    <input
                                                        type="text"
                                                        name="Last_sy_attended"
                                                        class="form-control @error('Last_sy_attended') is-invalid @enderror"
                                                        value="{{ old('Last_sy_attended', $studInfo->Last_sy_attended) }}">
                                                    @error('Last_sy_attended')
                                                    <div class="invalid-feedback">
                                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- First Name -->
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">First Name:</label>
                                                    <input
                                                        type="text"
                                                        name="FirstName"
                                                        class="form-control @error('FirstName') is-invalid @enderror"
                                                        value="{{ old('FirstName', $studInfo->FirstName) }}">
                                                    @error('FirstName')
                                                    <div class="invalid-feedback">
                                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Middle Name -->
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Middle Name:</label>
                                                    <input
                                                        type="text"
                                                        name="MiddleName"
                                                        class="form-control @error('MiddleName') is-invalid @enderror"
                                                        value="{{ old('MiddleName', $studInfo->MiddleName) }}">
                                                    @error('MiddleName')
                                                    <div class="invalid-feedback">
                                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Last Name -->
                                            <div class="col-8">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Last Name:</label>
                                                    <input
                                                        type="text"
                                                        name="LastName"
                                                        class="form-control @error('LastName') is-invalid @enderror"
                                                        value="{{ old('LastName', $studInfo->LastName) }}">
                                                    @error('LastName')
                                                    <div class="invalid-feedback">
                                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Suffix -->
                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Suffix:</label>
                                                    <input
                                                        type="text"
                                                        name="Suffix"
                                                        class="form-control @error('Suffix') is-invalid @enderror"
                                                        value="{{ old('Suffix', $studInfo->Suffix) }}">
                                                    @error('Suffix')
                                                    <div class="invalid-feedback">
                                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Save button for Student Info -->
                                        <div class="text-end mt-4">
                                            <button type="submit" id="submit-btn-desktop" class="btn" style="background:#1dd3b0; color:#0f172a;">
                                                <i class="fas fa-save me-2"></i>Save Student Information
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- Desktop Account Information Card -->
                <div class="col-12">
                    <div class="card shadow-sm border-0" style="background:#1e293b; border:1px solid #334155;">
                        <div class="card-header border-0" style="background:#1dd3b0; color:#0f172a;">
                            <h5 class="mb-0 d-flex align-items-center">
                                <i class="fas fa-user-cog me-2"></i>
                                Account Details
                            </h5>
                        </div>
                        <form class="accountUpdateForm" action="{{ route('student.profile.verifyUpdate', $studInfo->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="update_type" value="account_info">
                            <div class="card-body" style="color:#f1f5f9;">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="color:#e2e8f0;">Username:</label>
                                            <input type="text" name="username" class="form-control" value="{{ $studInfo->account->username }}">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="color:#e2e8f0;">Email Address:</label>
                                            <input type="email" name="email" class="form-control" value="{{ $studInfo->account->email_address ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="color:#e2e8f0;">Password:</label>
                                            <button type="button" class="btn btn-outline-light" id="desktopChangePasswordBtn">
                                                <i class="fas fa-key me-2"></i>Change Password
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Save button for Account Info -->
                                <div class="text-end">
                                    <button type="submit" class="btn" style="background:#1dd3b0; color:#0f172a;">
                                        <i class="fas fa-save me-2"></i>Save Account Details
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- OTP Verification Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otpModalLabel"><i class="fas fa-envelope"></i>Verify OTP Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>An OTP code has been sent to your registered email. Please enter it below to continue.</p>
                <input type="text" id="otpCode" class="form-control mb-3" placeholder="Enter OTP Code" maxlength="6">
                <div id="otpFeedback" class="fw-semibold"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary-custom" id="verifyOtpBtn">Verify OTP</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel"><i class="fas fa-key"></i>Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePasswordForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Password:</label>
                        <div class="position-relative">
                            <input type="password" name="current_password" id="currentPassword"
                                class="form-control"
                                style="padding-right: 2.5rem;"
                                required>
                            <i class="fas fa-eye-slash position-absolute toggle-password"
                                id="toggleCurrentPassword"
                                style="top: 50%; right: 12px; transform: translateY(-50%); z-index: 10; pointer-events: auto;"></i>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password:</label>
                        <div class="position-relative">
                            <input type="password" name="new_password" id="newPassword"
                                class="form-control"
                                style="padding-right: 2.5rem;"
                                required minlength="8">
                            <i class="fas fa-eye-slash position-absolute toggle-password"
                                id="toggleNewPassword"
                                style="top: 50%; right: 12px; transform: translateY(-50%); z-index: 10; pointer-events: auto;"></i>
                        </div>
                        <div id="passwordStrength" class="mt-1 small"></div>

                        <!-- Password Rules -->
                        <div id="passwordRules" class="mt-2 d-none">
                            <small id="ruleLength" class="text-danger">✖ 8-20 characters</small>
                            <small id="ruleLetter" class="text-danger">✖ Contains a letter</small>
                            <small id="ruleNumber" class="text-danger">✖ Contains a number</small>
                            <small id="ruleSpecial" class="text-danger">✖ Contains a special character</small>
                            <small id="ruleNoSpaces" class="text-danger">✖ No spaces allowed</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password:</label>
                        <div class="position-relative">
                            <input type="password" name="new_password_confirmation" id="confirmPassword"
                                class="form-control"
                                style="padding-right: 2.5rem;"
                                required>
                            <i class="fas fa-eye-slash position-absolute toggle-password"
                                id="toggleConfirmPassword"
                                style="top: 50%; right: 12px; transform: translateY(-50%); z-index: 10; pointer-events: auto;"></i>
                        </div>
                        <div id="passwordMatch" class="mt-1 small"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="saveAccountBtn" class="btn btn-primary-custom">Save Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar toggle
        const toggle = document.getElementById('sidebarToggle');
        if (toggle) {
            toggle.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-shrink');
            });
        }
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 1024) {
                document.body.classList.remove('sidebar-shrink');
            }
        });

        function validateLRN(inputId, errorId, successId, submitBtnId) {
            const lrnInput = document.getElementById(inputId);
            const errorMsg = document.getElementById(errorId);
            const successMsg = document.getElementById(successId);
            const submitBtn = document.getElementById(submitBtnId);

            if (!lrnInput) return; // Exit if element doesn't exist in current view

            lrnInput.addEventListener('input', function(e) {
                // Remove non-digit characters
                this.value = this.value.replace(/\D/g, '');

                const value = this.value;
                const isValid = /^\d{12}$/.test(value);

                if (value.length === 0) {
                    // Empty field - hide all messages
                    errorMsg.style.display = 'none';
                    successMsg.style.display = 'none';
                    lrnInput.classList.remove('is-invalid', 'is-valid');
                    submitBtn.disabled = false;
                } else if (isValid) {
                    // Valid LRN
                    errorMsg.style.display = 'none';
                    successMsg.style.display = 'block';
                    lrnInput.classList.remove('is-invalid');
                    lrnInput.classList.add('is-valid');
                    submitBtn.disabled = false;
                } else {
                    // Invalid LRN
                    errorMsg.style.display = 'block';
                    successMsg.style.display = 'none';
                    lrnInput.classList.remove('is-valid');
                    lrnInput.classList.add('is-invalid');
                    submitBtn.disabled = true;
                }
            });

            // Prevent form submission if LRN is invalid
            const form = lrnInput.closest('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const value = lrnInput.value;
                    if (value.length > 0 && !/^\d{12}$/.test(value)) {
                        e.preventDefault();
                        errorMsg.style.display = 'block';
                        lrnInput.classList.add('is-invalid');
                        lrnInput.focus();
                    }
                });
            }
        }

        document.querySelectorAll('.accountUpdateForm').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your information.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: '#334155',
                    color: '#f1f5f9',
                    confirmButtonColor: '#1dd3b0',
                });
            });
        });

        // Function to setup image upload for new images
        function setupImageUpload(addBtnId, inputId, previewId, imgId, placeholderId,
            otherPreviewImgId, otherPreviewContainerId, otherPlaceholderId, otherBtnId) {
            const addBtn = document.getElementById(addBtnId);
            const fileInput = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewId);
            const previewImg = document.getElementById(imgId);
            const placeholder = document.getElementById(placeholderId);

            const otherPreviewImg = document.getElementById(otherPreviewImgId);
            const otherPreviewContainer = document.getElementById(otherPreviewContainerId);
            const otherPlaceholder = document.getElementById(otherPlaceholderId);
            const otherBtn = document.getElementById(otherBtnId);

            if (addBtn && fileInput) {
                addBtn.addEventListener("click", () => fileInput.click());

                fileInput.addEventListener("change", (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            // show preview in current view
                            previewImg.src = e.target.result;
                            previewContainer.classList.remove("d-none");
                            placeholder.classList.add("d-none");
                            addBtn.classList.add("d-none");

                            // mirror the image to the other view
                            if (otherPreviewImg && otherPreviewContainer && otherPlaceholder && otherBtn) {
                                otherPreviewImg.src = e.target.result;
                                otherPreviewContainer.classList.remove("d-none");
                                otherPlaceholder.classList.add("d-none");
                                otherBtn.classList.add("d-none");
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        // Replace existing image script (unchanged)
        function setupReplaceImage(replaceBtnId, replaceInputId, replacePreviewId, replacePreviewImgId, originalImgSelector) {
            const replaceBtn = document.getElementById(replaceBtnId);
            const replaceInput = document.getElementById(replaceInputId);
            const replacePreview = document.getElementById(replacePreviewId);
            const replacePreviewImg = document.getElementById(replacePreviewImgId);
            const originalImg = document.querySelector(originalImgSelector);

            if (!replaceBtn || !replaceInput || !originalImg) return;

            replaceBtn.addEventListener("click", () => replaceInput.click());

            replaceInput.addEventListener("change", (event) => {
                const file = event.target.files && event.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    if (replacePreviewImg) replacePreviewImg.src = e.target.result;
                    if (replacePreview) replacePreview.classList.remove("d-none");
                    originalImg.style.display = "none";
                };
                reader.readAsDataURL(file);
            });
        }

        // Initialize validation for both views
        validateLRN('lrn-mobile', 'lrn-error-mobile', 'lrn-success-mobile', 'submit-btn-mobile');
        validateLRN('lrn-desktop', 'lrn-error-desktop', 'lrn-success-desktop', 'submit-btn-desktop');

        // Setup connections between desktop & mobile
        setupImageUpload(
            "desktopAddImageBtn", "desktopImageInput",
            "desktop-image-preview", "desktopPreviewImg", "desktop-placeholder",
            "mobilePreviewImg", "mobile-image-preview", "mobile-placeholder", "mobileAddImageBtn"
        );

        setupImageUpload(
            "mobileAddImageBtn", "mobileImageInput",
            "mobile-image-preview", "mobilePreviewImg", "mobile-placeholder",
            "desktopPreviewImg", "desktop-image-preview", "desktop-placeholder", "desktopAddImageBtn"
        );

        // Replace existing image functionality
        setupReplaceImage(
            "desktopReplaceImageBtn",
            "desktopReplaceImageInput",
            "desktop-replace-preview",
            "desktopReplacePreviewImg",
            ".desktop-profile-img"
        );

        setupReplaceImage(
            "mobileReplaceImageBtn",
            "mobileReplaceImageInput",
            "mobile-replace-preview",
            "mobileReplacePreviewImg",
            ".mobile-profile-img"
        );
        const desktopChangeBtn = document.getElementById('desktopChangePasswordBtn');
        const mobileChangeBtn = document.getElementById('mobileChangePasswordBtn');
        const otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
        const changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));

        const verifyOtpBtn = document.getElementById('verifyOtpBtn');
        const otpFeedback = document.getElementById('otpFeedback');

        // --- Step 1: Send OTP on button click (ERROR HANDLERS FIXED) ---
        [desktopChangeBtn, mobileChangeBtn].forEach(btn => {
            if (btn) {
                btn.addEventListener('click', async () => {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending OTP...';

                    try {
                        const response = await fetch('{{ route("student.password.sendOtp", $studInfo->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        });

                        const data = await response.json();
                        if (data.success) {
                            // Show success notification with button AND auto-close
                            Swal.fire({
                                icon: 'success',
                                title: 'Notice!',
                                text: data.message || 'OTP has been sent to your email address.',
                                confirmButtonColor: '#1dd3b0',
                                confirmButtonText: 'OK',
                                background: '#1F2937',
                                color: '#fff',
                                timer: 3000,
                                timerProgressBar: true
                            });

                            // Show the OTP modal
                            otpModal.show();
                        } else {
                            // Show error notification (user needs to read this)
                            Swal.fire({
                                icon: 'error',
                                title: 'Notice!',
                                text: data.message || 'Failed to send OTP.',
                                confirmButtonColor: '#1dd3b0',
                                background: '#1F2937',
                                color: '#fff',
                            });
                        }
                    } catch (error) {
                        // Show error notification for network issues (user needs to read this)
                        Swal.fire({
                            icon: 'error',
                            title: 'Notice!',
                            text: 'Error sending OTP. Please try again.',
                            confirmButtonColor: '#1dd3b0',
                            background: '#1F2937',
                            color: '#fff',
                        });
                    } finally {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-key me-2"></i>Change Password';
                    }
                });
            }
        });

        // --- Step 2: Verify OTP ---
        verifyOtpBtn.addEventListener('click', async () => {
            const code = document.getElementById('otpCode').value.trim();
            otpFeedback.textContent = '';

            if (!code) {
                otpFeedback.textContent = 'Please enter the OTP code.';
                otpFeedback.style.color = '#f87171';
                return;
            }

            verifyOtpBtn.disabled = true;
            verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';

            try {
                const response = await fetch('{{ route("student.password.verifyOtp", $studInfo->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        otp: code
                    })
                });

                const data = await response.json();
                if (data.verified) {
                    // Show success notification with button AND auto-close
                    Swal.fire({
                        icon: 'success',
                        title: 'Verified!',
                        text: 'OTP verified successfully. You can now change your password.',
                        confirmButtonColor: '#1dd3b0',
                        confirmButtonText: 'OK',
                        background: '#1F2937',
                        color: '#fff',
                        timer: 3000,
                        timerProgressBar: true
                    });

                    // Hide OTP modal and show password change modal
                    otpModal.hide();
                    changePasswordModal.show();
                } else {
                    // Show error notification
                    Swal.fire({
                        icon: 'error',
                        title: 'Verification Failed',
                        text: data.message || 'Invalid or expired OTP.',
                        confirmButtonColor: '#1dd3b0',
                        background: '#1F2937',
                        color: '#fff',
                    });

                    otpFeedback.textContent = data.message || 'Invalid or expired OTP.';
                    otpFeedback.style.color = '#f87171';
                }
            } catch (error) {
                // Show error notification for network issues
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error verifying OTP. Please try again.',
                    confirmButtonColor: '#1dd3b0',
                    background: '#1F2937',
                    color: '#fff',
                });

                otpFeedback.textContent = 'Error verifying OTP.';
                otpFeedback.style.color = '#f87171';
            } finally {
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.innerHTML = 'Verify OTP';
            }
        });

        // --- Step 3: Update Password (CONVERTED TO SWAL) ---
        const changePasswordForm = document.getElementById('changePasswordForm');

        changePasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const saveBtn = document.getElementById('saveAccountBtn');

            // Get values directly from inputs
            const currentPassword = document.querySelector('[name="current_password"]').value;
            const newPassword = document.querySelector('[name="new_password"]').value;
            const passwordConfirmation = document.querySelector('[name="new_password_confirmation"]').value;

            // Clear previous errors
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';

            try {
                const payload = {
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: passwordConfirmation
                };

                const response = await fetch('{{ route("student.password.update", $studInfo->id) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    changePasswordModal.hide();

                    // Show success notification with button AND auto-close
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Password updated successfully!',
                        confirmButtonColor: '#1dd3b0',
                        confirmButtonText: 'OK',
                        background: '#1F2937',
                        color: '#fff',
                        timer: 3000,
                        timerProgressBar: true
                    });

                    changePasswordForm.reset();
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = document.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback d-block';
                                errorDiv.textContent = data.errors[field][0];
                                input.parentElement.appendChild(errorDiv);
                            }
                        });
                    } else {
                        // Show error with SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'An error occurred.',
                            confirmButtonColor: '#1dd3b0',
                            background: '#1F2937',
                            color: '#fff',
                        });
                    }
                }
            } catch (error) {
                console.error('Error:', error);

                // Show error with SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error updating password. Please try again.',
                    confirmButtonColor: '#1dd3b0',
                    background: '#1F2937',
                    color: '#fff',
                });
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save Password';
            }
        });

        // Toggle password visibility function
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        // Use event delegation for eye icons - attach to document
        document.addEventListener('click', function(e) {
            // Check if clicked element is one of the toggle icons
            if (e.target && e.target.id === 'toggleCurrentPassword') {
                togglePasswordVisibility('currentPassword', 'toggleCurrentPassword');
            }
            if (e.target && e.target.id === 'toggleNewPassword') {
                togglePasswordVisibility('newPassword', 'toggleNewPassword');
            }
            if (e.target && e.target.id === 'toggleConfirmPassword') {
                togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword');
            }
        });

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password) && password.length >= 8) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            if (strength === 0) return {
                text: 'None',
                color: '#f1f5f9',
                level: 0
            };
            if (strength === 1) return {
                text: 'Weak',
                color: '#f87171',
                level: 1
            };
            if (strength === 2) return {
                text: 'Medium',
                color: '#facc15',
                level: 2
            };
            return {
                text: 'Strong',
                color: '#4ade80',
                level: 3
            };
        }

        // Validate password rules
        function validatePasswordRules(password) {
            const rules = {
                length: password.length >= 8 && password.length <= 20,
                letter: /[a-zA-Z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password),
                noSpaces: !/\s/.test(password)
            };

            // Update rule indicators
            updateRuleIndicator('ruleLength', rules.length);
            updateRuleIndicator('ruleLetter', rules.letter);
            updateRuleIndicator('ruleNumber', rules.number);
            updateRuleIndicator('ruleSpecial', rules.special);
            updateRuleIndicator('ruleNoSpaces', rules.noSpaces);

            return Object.values(rules).every(rule => rule);
        }

        function updateRuleIndicator(elementId, isValid) {
            const element = document.getElementById(elementId);
            if (isValid) {
                element.style.color = '#4ade80';
                element.innerHTML = element.innerHTML.replace('✖', '✓');
            } else {
                element.style.color = '#f87171';
                element.innerHTML = element.innerHTML.replace('✓', '✖');
            }
        }

        function validatePasswordForm(form) {
            const newPass = form.querySelector('input[name="new_password"]').value.trim();
            const confirmPass = form.querySelector('input[name="new_password_confirmation"]').value.trim();
            const saveBtn = form.querySelector('#saveAccountBtn');
            const strength = checkPasswordStrength(newPass);

            if (!saveBtn) return;

            if (newPass === '' && confirmPass === '') {
                saveBtn.disabled = false;
                return;
            }

            const rulesValid = validatePasswordRules(newPass);

            if (strength.level < 3 || confirmPass !== newPass || !rulesValid) {
                saveBtn.disabled = true;
            } else {
                saveBtn.disabled = false;
            }
        }

        const newPasswordInput = document.querySelector('input[name="new_password"]');
        const confirmPasswordInput = document.querySelector('input[name="new_password_confirmation"]');
        const strengthFeedback = document.getElementById('passwordStrength');
        const matchFeedback = document.getElementById('passwordMatch');
        const passwordRules = document.getElementById('passwordRules');

        newPasswordInput.addEventListener('focus', function() {
            passwordRules.classList.remove('d-none');
        });

        newPasswordInput.addEventListener('blur', function() {
            if (this.value === '') {
                passwordRules.classList.add('d-none');
            }
        });

        newPasswordInput.addEventListener('input', function() {
            const result = checkPasswordStrength(this.value);
            strengthFeedback.innerHTML = `Password Strength: <span style="color:${result.color};">${result.text}</span>`;

            if (this.value !== '') {
                passwordRules.classList.remove('d-none');
                validatePasswordRules(this.value);
            } else {
                passwordRules.classList.add('d-none');
            }

            validatePasswordForm(this.closest('form'));
        });

        confirmPasswordInput.addEventListener('input', function() {
            const newPass = newPasswordInput.value;
            if (this.value === '') {
                matchFeedback.textContent = '';
            } else if (this.value !== newPass) {
                matchFeedback.textContent = 'Passwords do not match';
                matchFeedback.style.color = '#f87171';
            } else {
                matchFeedback.textContent = 'Passwords match';
                matchFeedback.style.color = '#4ade80';
            }
            validatePasswordForm(this.closest('form'));
        });

        // // On page load, disable all save buttons initially
        // document.querySelectorAll('form#accountUpdateForm button[type="submit"]').forEach((btn) => {
        //     btn.disabled = true;
        // });

    });
</script>

@endsection