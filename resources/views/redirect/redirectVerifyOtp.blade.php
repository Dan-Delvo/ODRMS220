<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
    <meta http-equiv="refresh" content="0;url={{ route('verifyotp') }}">
    <script>
        // JS fallback to prevent back button from returning here
        window.location.replace("{{ route('verifyotp') }}");
    </script>
</head>
<body>
    <p>Redirecting to verify...</p>
</body>
</html>
