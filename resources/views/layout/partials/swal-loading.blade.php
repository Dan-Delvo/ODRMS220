<script>
    document.addEventListener('DOMContentLoaded', function() {

        function attachSwal(form) {
            if (form._swalAttached) return;
            form._swalAttached = true;

            form.addEventListener('submit', function(e) {
                // allow opt-out per-form
                if (form.dataset.swalSkip === "true") return;

                // let native validation show first (if invalid, don't show loading)
                if (!form.checkValidity()) {
                    return;
                }

                // Prevent duplicate submissions
                if (form.dataset.swalSubmitted === "true") {
                    e.preventDefault();
                    return;
                }

                // 🟡 DELETE CONFIRMATION
                if (form.dataset.swalDelete === "true") {
                    e.preventDefault(); // stop the form for now

                    Swal.fire({
                        title: form.dataset.swalDeleteTitle || "Are you sure?",
                        text: form.dataset.swalDeleteText || "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#1f2937',
                        confirmButtonText: form.dataset.swalDeleteConfirm || "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // ✅ Show loading alert while processing
                            Swal.fire({
                                title: form.dataset.swalTitle || "Please wait",
                                html: form.dataset.swalText || "Deleting...",
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            form.dataset.swalSubmitted = "true";
                            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(btn => btn.disabled = true);
                            form.submit();
                        }
                    });

                    return; // stop normal loading alert for delete forms
                }

                // 🟢 NORMAL LOADING ALERT
                const title = form.dataset.swalTitle || 'Please wait';
                const text = form.dataset.swalText || 'Processing...';
                const hideConfirm = form.dataset.swalHideConfirm === "true";

                Swal.fire({
                    title: title,
                    html: text,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: !hideConfirm,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.dataset.swalSubmitted = "true";
                form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(btn => btn.disabled = true);
            }, {
                capture: true
            });
        }

        // Attach to forms
        document.querySelectorAll('form[data-swal-loading="true"], form.swal-loading').forEach(attachSwal);

        // Observe dynamically added forms
        const observer = new MutationObserver(mutations => {
            mutations.forEach(m => {
                m.addedNodes.forEach(node => {
                    if (node.nodeType !== 1) return;
                    if (node.tagName === 'FORM') {
                        if (node.matches('form[data-swal-loading="true"], form.swal-loading')) attachSwal(node);
                    } else {
                        node.querySelectorAll('form[data-swal-loading="true"], form.swal-loading').forEach(attachSwal);
                    }
                });
            });
        });
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // 🟢 SESSION-BASED SWEETALERT HANDLER
        @if(session('success'))
        Swal.fire({
            title: "Success!",
            text: "{{ session('success') }}",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
        @endif

        @if(session('error'))
        Swal.fire({
            title: "Error!",
            text: "{{ session('error') }}",
            icon: "error",
            confirmButtonColor: "#d33",
        });
        @endif
        @if(session('warning'))
        Swal.fire({
            title: "⚠️ Warning!",
            html: `
                <p><strong>{{ session('warning') }}</strong></p>
                @if(session('warning_details'))
                    <hr>
                    <p style="text-align:left;">
                        <b>Details:</b><br>
                        {{ session('warning_details') }}
                    </p>
                @endif
                @if(session('student_name') && session('request_count'))
                    <hr>
                    <p style="text-align:left;">
                        <b>Student:</b> {{ session('student_name') }}<br>
                        <b>Total of Requested Documents:</b> {{ session('request_count') }}
                    </p>
                @endif
            `,
            icon: "warning",
            confirmButtonColor: "#f6c23e",
        });
        @endif

        @if(session('info'))
        Swal.fire({
            title: "Notice",
            text: "{{ session('info') }}",
            icon: "info",
            confirmButtonColor: "#3085d6",
        });
        @endif

    });
</script>