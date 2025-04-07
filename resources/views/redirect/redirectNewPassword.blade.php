<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
    <meta http-equiv="refresh" content="0;url={{ route('newpassword') }}">
    <script>
        // JS fallback to prevent back button from returning here
        window.location.replace("{{ route('newpassword') }}");
    </script>
</head>
<body>
    <p>Redirecting to password...</p>
</body>
</html>
