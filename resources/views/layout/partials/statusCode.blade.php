<script>
    @if (session('swal_error_title'))
        Swal.fire({
            title: "{{ session('swal_error_title') }}",
            text: "{{ session('swal_error_text') }}",
            icon: "{{ session('swal_error_icon') }}",
            confirmButtonColor: "#1dd3b0"
        });
    @endif

    @if (session('swal_success_title'))
        Swal.fire({
            title: "{{ session('swal_success_title') }}",
            text: "{{ session('swal_success_text') }}",
            icon: "{{ session('swal_success_icon') }}",
            confirmButtonColor: "#1dd3b0"
        });
    @endif
</script>
