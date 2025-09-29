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

                const title = form.dataset.swalTitle || 'Please wait';
                const text = form.dataset.swalText || 'Processing...';
                const hideConfirm = form.dataset.swalHideConfirm === "true";

                // Show SweetAlert2 loading modal
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

                // mark as submitted to prevent double submit
                form.dataset.swalSubmitted = "true";

                // disable submit inputs/buttons to be safe
                form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function(btn) {
                    btn.disabled = true;
                });

                // NOTE: If your form submits via AJAX, you should call Swal.close() when done.
            }, {
                capture: true
            });
        }

        // Attach to existing forms that opt-in
        document.querySelectorAll('form[data-swal-loading="true"], form.swal-loading').forEach(attachSwal);

        // Observe DOM for forms inserted later (optional)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(m) {
                m.addedNodes.forEach(function(node) {
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
    });
</script>