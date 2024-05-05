<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="nord">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title : config('app.name') }}</title>
    <link rel="icon"
          href="https://www.svgrepo.com/show/156221/medicines.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-mono antialiased">
{{--<x-ui-main full-width>--}}
{{--    <x-slot:content>--}}
        {{ $slot }}
{{--    </x-slot:content>--}}
{{--</x-ui-main>--}}
<x-ui-toast />
</body>

</html>
