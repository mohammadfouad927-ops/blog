<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My Blog')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

{{-- Navigation --}}
<x-nav-website />

{{-- Page Content --}}
    @yield('content')


</body>
</html>
