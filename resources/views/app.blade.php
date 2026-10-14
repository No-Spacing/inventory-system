<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="{{ asset('images/v-logo.png') }}">
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    @inertiaHead
</head>
<body>
    @inertia
</body>
<script src="http://localhost:8098"></script>
</html>