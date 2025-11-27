<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HackZone') }} - @yield('title', 'Eventos de Programación')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Compartido -->
    <link rel="stylesheet" href="{{ asset('css/shared.css') }}">

    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

    @stack('styles')
</head>
<body>
    <!-- Navbar Compartido -->
    @include('components.navbar')

    @yield('content')

    <!-- JavaScript Personalizado -->
    <script src="{{ asset('js/inicio.js') }}"></script>

    @stack('scripts')
</body>
</html>
