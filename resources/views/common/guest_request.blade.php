<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Request Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1dd3b0;
            --dark-bg: #0f172a;
            --card-bg: #1e293b;
            --input-bg: #334155;
            --border-color: #334155;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .form-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 1rem;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 2rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control,
        .form-select {
            background-color: var(--input-bg);
            border: 1px solid #475569;
            color: var(--text-primary);
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #475569;
            color: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        .form-floating > label {
            color: var(--text-secondary);
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label,
        .form-floating > .form-select ~ label {
            color: var(--primary-color);
        }

        .required-label::after {
            content: " *";
            color: #ef4444;
            font-weight: bold;
        }

        /* File Upload Styling */
        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .file-upload-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 2;
        }

        .file-upload-display {
            background-color: var(--input-bg);
            border: 2px dashed #64748b;
            border-radius: 0.75rem;
            padding: 2.5rem 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .file-upload-display:hover {
            background-color: #475569;
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .file-upload-display.has-file {
            border-color: var(--primary-color);
            background-color: rgba(29, 211, 176, 0.1);
        }

        .upload-icon {
            font-size: 3rem;
            color: #64748b;
            margin-bottom: 1rem;
        }

        .upload-text {
            color: var(--text-primary);
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .upload-subtext {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .file-preview {
            display: none;
            align-items: center;
            gap: 1rem;
            width: 100%;
        }

        .preview-icon {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .preview-info {
            flex-grow: 1;
            text-align: left;
        }

        .preview-name {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 0.25rem;
            word-break: break-all;
        }

        .preview-size {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .remove-file {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .remove-file:hover {
            background-color: rgba(239, 68, 68, 0.2);
            transform: scale(1.1);
        }

        /* Validation Feedback */
        .validation-message {
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: none;
            align-items: center;
            gap: 0.5rem;
        }

        .validation-message i {
            font-size: 1rem;
        }

        .validation-message.show {
            display: flex;
        }

        .is-invalid {
            border-color: #ef4444 !important;
        }

        .is-valid {
            border-color: #10b981 !important;
        }

        /* Submit Button */
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), #38d9a9);
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 2.5rem;
            border-radius: 50px;
            border: none;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(29, 211, 176, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(29, 211, 176, 0.6);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Alert Styling */
        .alert {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: var(--text-primary);
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                padding: 2rem 1.5rem;
            }

            .form-title {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1rem;
            }

            .btn-submit {
                width: 100%;
            }
        }

        /* SweetAlert2 Custom Styling */
        .swal2-popup {
            background: var(--card-bg) !important;
            border: 2px solid var(--border-color) !important;
        }

        .swal2-title {
            color: var(--text-primary) !important;
        }

        .swal2-html-container {
            color: var(--text-secondary) !important;
        }

        .swal2-confirm {
            background-color: var(--primary-color) !important;
        }

        .swal2-cancel {
            background-color: #64748b !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-title">
                <i class="fas fa-file-alt me-2"></i>
                Document Request Form
            </div>

            <!-- Error Messages -->
            <div class="alert alert-danger d-none" id="errorAlert">
                <ul class="mb-0" id="errorList"></ul>
            </div>

            <form id="documentRequestForm" method="POST" enctype="multipart/form-data">
                <!-- Document Request Information -->
                <div class="section-title">
                    <i class="fas fa-file-invoice"></i>
                    Document Request Information
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text"
                                   class="form-control"
                                   id="request_schl_entity"
                                   name="request_schl_entity"
                                   placeholder="Requesting School/Entity"
                                   required>
                            <label for="request_schl_entity" class="required-label">Requesting School/Entity</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="document_id" name="document_id" required>
                                <option value="" disabled selected>Select Document Type</option>
                                <option value="1">Form 137</option>
                                <option value="2">Form 138</option>
                                <option value="3">Certificate of Good Moral</option>
                                <option value="4">Diploma</option>
                                <option value="5">Transcript of Records</option>
                            </select>
                            <label for="document_id" class="required-label">Requested Document</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text"
                                   class="form-control"
                                   id="release_mode"
                                   name="release_mode"
                                   value="Pick Up"
                                   readonly
                                   required>
                            <label for="release_mode" class="required-label">Release Mode</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text"
                                   class="form-control"
                                   id="reason"
                                   name="reason"
                                   placeholder="Reason"
                                   required>
                            <label for="reason" class="required-label">Reason for Request</label>
                        </div>
                    </div>
                </div>

                <!-- Student Information -->
                <div class="section-title">
                    <i class="fas fa-user-graduate"></i>
                    Student Information
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text"
                                   class="form-control"
                                   id="student_first_name"
                                   name="student_first_name"
                                   placeholder="First Name"
                                   required>
                            <label for="student_first_name" class="required-label">First Name</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text"
                                   class="form-control"
                                   id="student_last_name"
                                   name="student_last_name"
                                   placeholder="Last Name"
                                   required>
                            <label for="student_last_name" class="required-label">Last Name</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text"
                                   class="form-control"
                                   id="lrn"
                                   name="lrn"
                                   placeholder="LRN"
                                   maxlength="12">
                            <label for="lrn">LRN (Optional)</label>
                        </div>
                        <div class="validation-message text-danger" id="lrn-error">
                            <i class="fas fa-exclamation-circle"></i>
                            LRN must be exactly 12 digits
                        </div>
                        <div class="validation-message text-success" id="lrn-success">
                            <i class="fas fa-check-circle"></i>
                            Valid LRN
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="grade_level" name="grade_level" required>
                                <option value="" disabled selected>Select Grade Level</option>
                                <option value="Grade 7">Grade 7</option>
                                <option value="Grade 8">Grade 8</option>
                                <option value="Grade 9">Grade 9</option>
                                <option value="Grade 10">Grade 10</option>
                                <option value="Grade 11">Grade 11</option>
                                <option value="Grade 12">Grade 12</option>
                            </select>
                            <label for="grade_level" class="required-label">Grade Level</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="student_status" name="student_status" required>
                                <option value="" disabled selected>Select Student Status</option>
                                <option value="Currently Enrolled">Currently Enrolled</option>
                                <option value="Graduate">Graduate</option>
                                <option value="Transferee">Transferee</option>
                            </select>
                            <label for="student_status" class="required-label">Student Status</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text"
                                   class="form-control"
                                   id="last_sy_attended"
                                   name="last_sy_attended"
                                   placeholder="Last SY Attended"
                                   required>
                            <label for="last_sy_attended" class="required-label">Last School Year Attended</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="email"
                                   class="form-control"
                                   id="email_address"
                                   name="email_address"
                                   placeholder="Email Address"
                                   required>
                            <label for="email_address" class="required-label">Email Address</label>
                        </div>
                    </div>
                </div>

                <!-- Supporting Documents -->
                <div class="section-title">
                    <i class="fas fa-paperclip"></i>
                    Supporting Documents
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="file-upload-wrapper">
                            <input type="file"
                                   class="file-upload-input"
                                   id="supporting_document"
                                   name="supporting_document"
                                   accept="image/*,.pdf,.doc,.docx">

                            <div class="file-upload-display">
                                <div class="upload-content">
                                    <div class="upload-icon">📎</div>
                                    <div class="upload-text">Click to upload or drag and drop</div>
                                    <div class="upload-subtext">Images, PDF, DOC, DOCX (Max 10MB)</div>
                                </div>

                                <div class="file-preview">
                                    <div class="preview-icon">📄</div>
                                    <div class="preview-info">
                                        <div class="preview-name">filename.jpg</div>
                                        <div class="preview-size">2.5 MB</div>
                                    </div>
                                    <button type="button" class="remove-file">✕</button>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Upload any supporting documents for your request (Optional)
                        </small>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-submit" id="submitButton">
                        <i class="fas fa-paper-plane me-2"></i>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('documentRequestForm');
            const lrnInput = document.getElementById('lrn');
            const lrnError = document.getElementById('lrn-error');
            const lrnSuccess = document.getElementById('lrn-success');
            const submitButton = document.getElementById('submitButton');
            const fileInput = document.getElementById('supporting_document');
            const fileDisplay = document.querySelector('.file-upload-display');
            const uploadContent = document.querySelector('.upload-content');
            const filePreview = document.querySelector('.file-preview');
            const previewName = document.querySelector('.preview-name');
            const previewSize = document.querySelector('.preview-size');
            const removeBtn = document.querySelector('.remove-file');

            // LRN Validation
            lrnInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '');

                if (this.value.length > 12) {
                    this.value = this.value.slice(0, 12);
                }

                const value = this.value;
                const isValid = /^\d{12}$/.test(value);

                lrnError.classList.remove('show');
                lrnSuccess.classList.remove('show');
                lrnInput.classList.remove('is-invalid', 'is-valid');

                if (value.length === 0) {
                    submitButton.disabled = false;
                } else if (isValid) {
                    lrnSuccess.classList.add('show');
                    lrnInput.classList.add('is-valid');
                    submitButton.disabled = false;
                } else {
                    lrnError.classList.add('show');
                    lrnInput.classList.add('is-invalid');
                    submitButton.disabled = true;
                }
            });

            // File Upload Handling
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: 'Please upload a file smaller than 10MB.',
                            confirmButtonColor: '#1dd3b0'
                        });
                        fileInput.value = '';
                        return;
                    }

                    fileDisplay.classList.add('has-file');
                    uploadContent.style.display = 'none';
                    filePreview.style.display = 'flex';

                    previewName.textContent = file.name;
                    previewSize.textContent = formatFileSize(file.size);
                }
            });

            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.value = '';
                resetFileDisplay();
            });

            function resetFileDisplay() {
                fileDisplay.classList.remove('has-file');
                uploadContent.style.display = 'flex';
                filePreview.style.display = 'none';
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Form Submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate LRN if provided
                const lrnValue = lrnInput.value;
                if (lrnValue.length > 0 && !/^\d{12}$/.test(lrnValue)) {
                    lrnError.classList.add('show');
                    lrnInput.classList.add('is-invalid');
                    lrnInput.focus();
                    return;
                }

                // Show confirmation dialog
                Swal.fire({
                    title: 'Confirm Submission',
                    text: "Are you sure you want to submit this document request?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1dd3b0',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Submit',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitButton.disabled = true;

                        Swal.fire({
                            title: 'Submitting Request...',
                            html: 'Please wait while we process your document request.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Simulate form submission (replace with actual form.submit() in production)
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Request Submitted!',
                                text: 'Your document request has been submitted successfully.',
                                confirmButtonColor: '#1dd3b0'
                            }).then(() => {
                                form.reset();
                                resetFileDisplay();
                                submitButton.disabled = false;
                            });
                        }, 2000);
                    }
                });
            });

            // Initialize Bootstrap tooltips if needed
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
        });
    </script>
</body>
</html>
