<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <form method="POST">
        @csrf
        <input type="text" name="test" value="hello">
        <button type="submit">Test</button>
    </form>
</body>
</html>
