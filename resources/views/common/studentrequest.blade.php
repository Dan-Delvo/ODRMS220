@extends('layout.studentpage')

@section('content')

<style>
    :root {
        --sidebar-width: 270px;
        --sidebar-collapsed-width: 85px;
    }

    body,
    html {
        background-color: #0f172a;
        color: #e2e8f0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    footer,
    .footer {
        display: none !important;
    }

    .main-content {
        margin-left: var(--sidebar-width);
        transition: margin-left 0.4s ease;
        padding: 6rem 2rem 2rem;
    }

    body.sidebar-shrink .main-content {
        margin-left: var(--sidebar-collapsed-width);
    }

    @media (min-width: 1200px) {
        body.sidebar-shrink .form-container {
            max-width: 100%;
            transition: max-width 0.4s ease;
            padding: 3rem 4rem;
        }
    }

    @media (max-width: 1024px) {
        .main-content {
            margin-left: 0 !important;
            padding-top: 7rem;
        }
    }

    @media (max-width: 576px) {
        .main-content {
            padding-top: 8rem;
        }
    }

    .form-container {
        width: 100%;
        max-width: 15000px;
        background-color: #1e293b;
        border: 2px solid #334155;
        border-radius: 1rem;
        padding: 3rem 4rem;
    }

    .form-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #1dd3b0;
        margin-bottom: 2rem;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: bold;
        color: #1dd3b0;
        margin-bottom: 1rem;
    }

    .form-control,
    .form-select {
        background-color: #334155;
        border: none;
        color: #f1f5f9;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #475569;
        color: #fff;
        box-shadow: none;
    }

    /* Custom file upload styling */
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
        background-color: #334155;
        border: 2px dashed #64748b;
        border-radius: 0.5rem;
        padding: 2rem 1rem;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .file-upload-display:hover {
        background-color: #475569;
        border-color: #1dd3b0;
    }

    .file-upload-display.has-file {
        border-color: #1dd3b0;
        background-color: #1e3a32;
    }

    .upload-icon {
        font-size: 2rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .upload-text {
        color: #94a3b8;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .upload-subtext {
        color: #64748b;
        font-size: 0.8rem;
    }

    .file-preview {
        display: none;
        align-items: center;
        gap: 0.75rem;
    }

    .preview-icon {
        font-size: 1.5rem;
        color: #1dd3b0;
    }

    .preview-info {
        flex-grow: 1;
    }

    .preview-name {
        color: #f1f5f9;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .preview-size {
        color: #94a3b8;
        font-size: 0.8rem;
    }

    .remove-file {
        background: none;
        border: none;
        color: #ef4444;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: background-color 0.2s ease;
    }

    .remove-file:hover {
        background-color: rgba(239, 68, 68, 0.1);
    }

    .btn-submit {
        background-color: #1dd3b0;
        color: #1e293b;
        font-weight: 600;
        padding: 0.5rem 1.5rem;
        border-radius: 0.4rem;
        border: none;
    }

    .btn-submit:hover {
        background-color: #38d9a9;
    }

    .text-warning-link {
        color: #1dd3b0;
        text-decoration: none;
    }

    .text-warning-link:hover {
        text-decoration: underline;
    }

    .sidebar {
        z-index: 1050;
    }

    @media (max-width: 1024px) {
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
        }
    }
</style>

<!-- Sidebar Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('sidebarToggle');
        if (toggle) {
            toggle.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-shrink');
            });
        }

        // File upload functionality
        const fileInput = document.getElementById('supporting_document');
        const fileDisplay = document.querySelector('.file-upload-display');
        const uploadContent = document.querySelector('.upload-content');
        const filePreview = document.querySelector('.file-preview');
        const previewName = document.querySelector('.preview-name');
        const previewSize = document.querySelector('.preview-size');
        const removeBtn = document.querySelector('.remove-file');

        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Show file preview
                    fileDisplay.classList.add('has-file');
                    uploadContent.style.display = 'none';
                    filePreview.style.display = 'flex';

                    previewName.textContent = file.name;
                    previewSize.textContent = formatFileSize(file.size);
                } else {
                    resetFileDisplay();
                }
            });

            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.value = '';
                resetFileDisplay();
            });
        }

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
    });
</script>

<div class="main-content">
    <div class="form-title">Document Request Form</div>
    <div class="form-container">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('studentrequest.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 pe-md-4 mb-4">
                    <div class="form-section-title">Document Request Info</div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="request_schl_entity" name="request_schl_entity" placeholder="Requesting Entity">
                        <label for="request_schl_entity">Requesting School/Entity</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select" id="document_id" name="document_id">
                            @foreach($DocType as $doc)
                            <option value="{{ $doc->id }}" 
                                @if($doc->DocType === "Form 137" && $DocRequests->contains('DocType', 'Form 137')) disabled @endif>
                            <!-- @if($DocRequests->contains('id', $doc->id)) disabled @endif>  -->
                                {{ $doc->DocType }}
                            </option>
                            @endforeach
                        </select>
                        <label for="document_id">Requested Document</label>
                    </div>


                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="release_mode" name="release_mode" placeholder="Release Mode" value="Pick Up" readonly>
                        <label for="release_mode">Release Mode</label>
                    </div>
                </div>

                <div class="col-md-6 ps-md-4 mb-4">
                    <div class="form-section-title">Supporting Documents</div>

                    <div class="file-upload-wrapper">
                        <input type="file"
                            class="file-upload-input"
                            id="supporting_document"
                            name="supporting_document"
                            accept="image/*,.pdf,.doc,.docx">

                        <div class="file-upload-display">
                            <div class="upload-content" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
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

                    <small class="text-white">Upload any supporting documents for your request</small>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="dashboard.html" class="text-warning-link">← Back to Dashboard</a>
                <button type="submit" class="btn btn-submit">Submit Request</button>
            </div>
        </form>
    </div>
</div>

@endsection