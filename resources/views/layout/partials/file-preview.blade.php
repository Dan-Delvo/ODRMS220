@switch($ext)
{{-- IMAGE PREVIEW WITH ZOOM --}}
@case('jpg')
@case('jpeg')
@case('png')
@case('gif')
@case('webp')
<div class="text-center w-100">
    <img src="/public/{{trim($filePath)}}"
    
        alt="File Preview"
        class="img-fluid rounded zoomable"
        style="max-height: 65vh; object-fit: contain; cursor: zoom-in;"
        onclick="this.classList.toggle('zoomed')"
        onerror="this.onerror=null; this.src='{{ asset('images/no-image-placeholder.png') }}';">
    <p class="mt-2 text-muted">
        <strong>{{ strtoupper($ext) }}</strong> — {{ basename($filePath) }}
    </p>
    <div class="mt-3">
        <a href="{{ asset($filePath) }}"
            class="btn btn-sm btn-primary me-2"
            download>
            <i class="fas fa-download me-1"></i> Download
        </a>
        <a href="{{ asset($filePath) }}"
            class="btn btn-sm btn-outline-secondary"
            target="_blank">
            <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
        </a>
    </div>
</div>
@break

{{-- PDF PREVIEW --}}
@case('pdf')
<div class="text-center w-100">
    <iframe src="/public/{{trim($filePath)}}"
        width="100%"
        height="400px"
        style="border:1px solid #ddd;"></iframe>
    <p class="mt-2 text-muted">
        <i class="fas fa-file-pdf text-danger"></i>
        <strong>PDF</strong> — {{ basename($filePath) }}
    </p>
    <div class="mt-3">
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-primary me-2"
            download>
            <i class="fas fa-download me-1"></i> Download
        </a>
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-outline-secondary"
            target="_blank">
            <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
        </a>
    </div>
</div>
@break

{{-- WORD --}}
@case('doc')
@case('docx')
<div class="text-center">
    <i class="fas fa-file-word text-primary" style="font-size:3rem;"></i>
    <p class="mt-2"><strong>WORD</strong> — {{ basename($filePath) }}</p>
    <div class="mt-3">
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-primary me-2"
            download>
            <i class="fas fa-download me-1"></i> Download
        </a>
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-outline-secondary"
            target="_blank">
            <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
        </a>
    </div>
</div>
@break

{{-- EXCEL --}}
@case('xls')
@case('xlsx')
<div class="text-center">
    <i class="fas fa-file-excel text-success" style="font-size:3rem;"></i>
    <p class="mt-2"><strong>EXCEL</strong> — {{ basename($filePath) }}</p>
    <div class="mt-3">
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-primary me-2"
            download>
            <i class="fas fa-download me-1"></i> Download
        </a>
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-outline-secondary"
            target="_blank">
            <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
        </a>
    </div>
</div>
@break

{{-- POWERPOINT --}}
@case('ppt')
@case('pptx')
<div class="text-center">
    <i class="fas fa-file-powerpoint text-warning" style="font-size:3rem;"></i>
    <p class="mt-2"><strong>POWERPOINT</strong> — {{ basename($filePath) }}</p>
    <div class="mt-3">
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-primary me-2"
            download>
            <i class="fas fa-download me-1"></i> Download
        </a>
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-outline-secondary"
            target="_blank">
            <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
        </a>
    </div>
</div>
@break

{{-- TEXT FILE --}}
@case('txt')
<div class="text-center">
    <i class="fas fa-file-alt text-secondary" style="font-size:3rem;"></i>
    <p class="mt-2"><strong>TEXT</strong> — {{ basename($filePath) }}</p>
    <div class="mt-3">
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-primary me-2"
            download>
            <i class="fas fa-download me-1"></i> Download
        </a>
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-outline-secondary"
            target="_blank">
            <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
        </a>
    </div>
</div>
@break

{{-- DEFAULT --}}
@default
<div class="text-center">
    <i class="fas fa-file text-muted" style="font-size:3rem;"></i>
    <p class="mt-2"><strong>{{ strtoupper($ext) }}</strong> — {{ basename($filePath) }}</p>
    <div class="mt-3">
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-primary me-2"
            download>
            <i class="fas fa-download me-1"></i> Download
        </a>
        <a href="/public/{{trim($filePath)}}"
            class="btn btn-sm btn-outline-secondary"
            target="_blank">
            <i class="fas fa-external-link-alt me-1"></i> Open in New Tab
        </a>
    </div>
</div>
@endswitch