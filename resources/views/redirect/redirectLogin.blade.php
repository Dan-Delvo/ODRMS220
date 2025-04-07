<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
    <script>

        if (window.history && window.history.pushState) {
            window.history.pushState(null, null, window.location.href); // Push the current state
            window.history.back(); // Go back one step
            window.history.forward(); // Then go forward to the current page
        }

        // JS fallback to prevent back button from returning here
        window.location.replace("{{ route('login') }}?status={{ urlencode(session('status')) }}");
    </script>
</head>
<body>
    <p>Redirecting to login...</p>
</body>
</html>
