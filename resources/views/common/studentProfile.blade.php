@extends('layout.studentpage')

@section('content')
@include('layout.partials.message')

<div class="main-content" style="background-color: #0f172a; min-height: 100vh;">
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
                <form id="accountUpdateForm" action="{{ route('student.profile.update', $studInfo->id) }}" method="POST" enctype="multipart/form-data">
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
                                    <img src="{{ asset($studInfo->Id_image) }}"
                                        alt="Student Image"
                                        style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">
                                </div>

                                <!-- Replace Image Button -->
                                <input type="file" id="replaceImageInput" name="Id_image" accept="image/*" class="d-none">
                                <button type="button" id="replaceImageBtn" class="btn btn-sm mt-2"
                                    style="background:#1dd3b0; color:#0f172a;">
                                    Replace Image
                                </button>

                                <!-- Preview (after choosing a new one) -->
                                <div id="replace-image-preview" class="mt-3 d-none">
                                    <div class="mx-auto" style="max-width: 220px; padding: 8px; background:#334155; border-radius: 12px;">
                                        <img id="replacePreviewImg" src="" alt="Preview"
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
                                <input type="text" name="LRN" class="form-control" value="{{ $studInfo->LRN }}">
                            </div>

                            <!-- Grade Level, Status, and Last SY in 3 equal columns -->
                            <div class="mb-3 col-6 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Grade Level</label>
                                <input type="text" name="Grade_level" class="form-control" value="{{ $studInfo->Grade_level }}">
                            </div>
                            <div class="mb-3 col-6 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Status</label>
                                <input type="text" name="Std_status" class="form-control" value="{{ $studInfo->Std_status }}">
                            </div>
                            <div class="mb-3 col-12 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Last School Year Attended</label>
                                <input type="text" name="Last_sy_attended" class="form-control" value="{{ $studInfo->Last_sy_attended ?? '' }}">
                            </div>
                            <div class="mb-3 col-6 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">First Name</label>
                                <input type="text" name="firstname" class="form-control" value="{{ $studInfo->FirstName ?? '' }}">
                            </div>
                            <div class="mb-3 col-6 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Middle Name</label>
                                <input type="text" name="middlename" class="form-control" value="{{ $studInfo->MiddleName ?? '' }}">
                            </div>
                            <div class="mb-3 col-8 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Last Name</label>
                                <input type="text" name="Lastname" class="form-control" value="{{ $studInfo->LastName ?? '' }}">
                            </div>
                            <div class="mb-3 col-4 col-md-4">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Suffix</label>
                                <input type="text" name="suffix" class="form-control" value="{{ $studInfo->Suffix ?? '' }}">
                            </div>
                        </div>

                        <!-- Save Button for Student Info -->
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn" style="background:#1dd3b0; color:#0f172a;">
                                <i class="fas fa-save me-2"></i>Save Student Info
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Mobile Account Information Card -->
            <div class="card shadow-sm border-0 mb-3"
                style="background:#1e293b; border:1px solid #334155;">
                <div class="card-header border-0 py-2" style="background:#1dd3b0; color:#0f172a;">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-user-cog me-2"></i>
                        Account Details
                    </h6>
                </div>
                <form id="accountUpdateForm" action="{{ route('student.profile.verifyUpdate', $studInfo->id) }}" method="POST">
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
                                <input type="email" name="email" class="form-control" value="{{ $studInfo->account->email_address ?? '' }}">
                            </div>
                            <div class="mb-3 col-6">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">New Password:</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
                            </div>
                            <div class="mb-3 col-6">
                                <label class="form-label fw-semibold" style="color:#e2e8f0;">Confirm Password:</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
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
                        <form id="accountUpdateForm" action="{{ route('student.profile.update', $studInfo->id) }}" method="POST" enctype="multipart/form-data">
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
                                            <div style="padding: 8px; background:#334155; border-radius: 12px;">
                                                <img src="{{ asset($studInfo->Id_image) }}"
                                                    alt="Student Image"
                                                    style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;">
                                            </div>

                                            <!-- Replace Image Button -->
                                            <input type="file" id="replaceImageInput" name="Id_image" accept="image/*" class="d-none">
                                            <button type="button" id="replaceImageBtn" class="btn btn-sm mt-2"
                                                style="background:#1dd3b0; color:#0f172a;">
                                                Replace Image
                                            </button>

                                            <!-- Preview (after choosing a new one) -->
                                            <div id="replace-image-preview" class="mt-3 d-none">
                                                <div class="mx-auto" style="max-width: 220px; padding: 8px; background:#334155; border-radius: 12px;">
                                                    <img id="replacePreviewImg" src="" alt="Preview"
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
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Student ID:</label>
                                                    <input type="text" name="id" class="form-control" value="{{ $studInfo->id }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">LRN:</label>
                                                    <input type="text" name="LRN" class="form-control" value="{{ $studInfo->LRN }}">
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Grade Level:</label>
                                                    <input type="text" name="Grade_level" class="form-control" value="{{ $studInfo->Grade_level }}">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Status:</label>
                                                    <input type="text" name="status" class="form-control" value="{{ $studInfo->Std_status }}">
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Last School Year:</label>
                                                    <input type="text" name="Last_sy_attended" class="form-control" value="{{ $studInfo->Last_sy_attended ?? '' }}">
                                                </div>
                                            </div>

                                            <!-- Name Fields -->
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">First Name:</label>
                                                    <input type="text" name="FirstName" class="form-control" value="{{ $studInfo->FirstName }}">
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Middle Name:</label>
                                                    <input type="text" name="MiddleName" class="form-control" value="{{ $studInfo->MiddleName ?? '' }}">
                                                </div>
                                            </div>

                                            <div class="col-8">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Last Name:</label>
                                                    <input type="text" name="LastName" class="form-control" value="{{ $studInfo->LastName }}">
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold" style="color:#e2e8f0;">Suffix:</label>
                                                    <input type="text" name="Suffix" class="form-control" value="{{ $studInfo->Suffix ?? '' }}">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Save button for Student Info -->
                                        <div class="text-end mt-4">
                                            <button type="submit" class="btn" style="background:#1dd3b0; color:#0f172a;">
                                                <i class="fas fa-save me-2"></i>Save Student Information
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <!-- Account Information (Full Width, below Student Info) -->
                <div class="col-12">
                    <div class="card shadow-sm border-0"
                        style="background:#1e293b; border:1px solid #334155;">
                        <div class="card-header border-0" style="background:#1dd3b0; color:#0f172a;">
                            <h5 class="mb-0 d-flex align-items-center">
                                <i class="fas fa-user-cog me-2"></i>
                                Account Details
                            </h5>
                        </div>
                        <form id="accountUpdateForm" action="{{ route('student.profile.verifyUpdate', $studInfo->id) }}" method="POST">
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
                                            <input type="email" name="email" class="form-control" value="{{ $studInfo->account->email_address ?? '' }}">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="color:#e2e8f0;">New Password:</label>
                                            <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="color:#e2e8f0;">Confirm Password:</label>
                                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
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

<style>
    .form-control::placeholder {
        color: #ffffff;
        /* white */
        opacity: 1;
        /* ensure it's not transparent */
    }

    /* Custom responsive adjustments */
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
        }

        .card-body {
            padding: 1rem;
        }

        .fs-2 {
            font-size: 1.5rem !important;
        }

        .card-header h6 {
            font-size: 0.95rem;
        }
    }

    @media (min-width: 768px) and (max-width: 991px) {

        /* Tablet specific adjustments */
        .col-md-6 .card-body {
            padding: 2rem;
        }

        .rounded-circle {
            width: 100px !important;
            height: 100px !important;
        }

        .fs-1 {
            font-size: 2rem !important;
        }
    }

    @media (min-width: 1200px) {

        /* Large screen optimizations */
        .col-xl-6 .row {
            margin-bottom: 1rem;
        }
    }

    /* Ensure equal height cards */
    .h-100 {
        height: 100% !important;
    }

    /* Custom separator styling */
    hr {
        opacity: 0.5;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('accountUpdateForm').addEventListener('submit', function(e) {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we send the verification email.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                background: '#0f172a', // match your card
                color: '#f1f5f9', // light text
                confirmButtonColor: '#1dd3b0', // teal button
            });
        });

        function setupImageUpload(addBtnId, inputId, previewId, imgId, placeholderId, otherPreviewImgId, otherPreviewContainerId, otherPlaceholderId, otherBtnId) {
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
                            // Update current view
                            previewImg.src = e.target.result;
                            previewContainer.classList.remove("d-none");
                            placeholder.classList.add("d-none");
                            addBtn.classList.add("d-none");

                            // Sync with other view
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

        // Desktop -> Mobile sync
        setupImageUpload(
            "desktopAddImageBtn", "desktopImageInput",
            "desktop-image-preview", "desktopPreviewImg", "desktop-placeholder",
            "mobilePreviewImg", "mobile-image-preview", "mobile-placeholder", "mobileAddImageBtn"
        );

        // Mobile -> Desktop sync
        setupImageUpload(
            "mobileAddImageBtn", "mobileImageInput",
            "mobile-image-preview", "mobilePreviewImg", "mobile-placeholder",
            "desktopPreviewImg", "desktop-image-preview", "desktop-placeholder", "desktopAddImageBtn"
        );

        // Password change functionality for mobile
        const mobileChangePasswordBtn = document.getElementById('mobileChangePasswordBtn');
        const mobilePasswordFields = document.getElementById('mobilePasswordChangeFields');

        if (mobileChangePasswordBtn && mobilePasswordFields) {
            mobileChangePasswordBtn.addEventListener('click', function() {
                if (mobilePasswordFields.classList.contains('d-none')) {
                    mobilePasswordFields.classList.remove('d-none');
                    mobileChangePasswordBtn.innerHTML = '<i class="fas fa-times me-1"></i>Cancel';
                    mobileChangePasswordBtn.classList.remove('btn-outline-secondary');
                    mobileChangePasswordBtn.classList.add('btn-outline-danger');
                } else {
                    mobilePasswordFields.classList.add('d-none');
                    mobileChangePasswordBtn.innerHTML = '<i class="fas fa-key me-1"></i>Change Password';
                    mobileChangePasswordBtn.classList.remove('btn-outline-danger');
                    mobileChangePasswordBtn.classList.add('btn-outline-secondary');
                    // Clear password fields
                    mobilePasswordFields.querySelector('input[name="new_password"]').value = '';
                    mobilePasswordFields.querySelector('input[name="password_confirmation"]').value = '';
                }
            });
        }

        // Password change functionality for desktop
        const desktopChangePasswordBtn = document.getElementById('desktopChangePasswordBtn');
        const desktopPasswordFields = document.getElementById('desktopPasswordChangeFields');

        if (desktopChangePasswordBtn && desktopPasswordFields) {
            desktopChangePasswordBtn.addEventListener('click', function() {
                if (desktopPasswordFields.classList.contains('d-none')) {
                    desktopPasswordFields.classList.remove('d-none');
                    desktopChangePasswordBtn.innerHTML = '<i class="fas fa-times me-1"></i>Cancel';
                    desktopChangePasswordBtn.classList.remove('btn-outline-secondary');
                    desktopChangePasswordBtn.classList.add('btn-outline-danger');
                } else {
                    desktopPasswordFields.classList.add('d-none');
                    desktopChangePasswordBtn.innerHTML = '<i class="fas fa-key me-1"></i>Change Password';
                    desktopChangePasswordBtn.classList.remove('btn-outline-danger');
                    desktopChangePasswordBtn.classList.add('btn-outline-secondary');
                    // Clear password fields
                    desktopPasswordFields.querySelector('input[name="new_password"]').value = '';
                    desktopPasswordFields.querySelector('input[name="password_confirmation"]').value = '';
                }
            });
        }

        // Sidebar toggle
        const toggle = document.getElementById('sidebarToggle');
        if (toggle) {
            toggle.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-shrink');
            });
        }
    });
</script>

@endsection