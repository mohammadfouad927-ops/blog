<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My Blog')</title>
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="bg-gray-100 min-h-screen">

{{-- Navigation --}}
<x-nav-website />

{{-- Page Content --}}
<main class="max-w-7xl mx-auto px-4 py-6">
    @yield('content')
</main>

</body>
</html>
