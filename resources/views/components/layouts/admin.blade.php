<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="winter">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title : config('app.name') }}</title>
    <link rel="icon"
          href="https://www.svgrepo.com/show/474399/3u-server.svg">
    @yield("chart_js")
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200">

{{-- NAVBAR mobile only--}}
<x-ui-nav sticky class="lg:hidden">
    <x-slot:brand>
        <img src="https://www.svgrepo.com/show/156221/medicines.svg" alt="" class="size-20">
    </x-slot:brand>
    <x-slot:actions>
        <label for="main-drawer" class="lg:hidden mr-3">
            <x-ui-icon name="o-bars-3" class="cursor-pointer" />
        </label>
    </x-slot:actions>
</x-ui-nav>

{{-- MAIN--}}
<x-ui-main full-width>
{{--     SIDEBAR--}}
    <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 lg:bg-inherit">

{{--         BRAND--}}
        <div class="ml-5 pt-5 flex justify-center flex-col items-center">
            <img src="https://www.svgrepo.com/show/156221/medicines.svg" alt="" class="size-20">
            <span class="text-3xl font-semibold">S</span>
        </div>

{{--         MENU--}}
        <x-ui-menu activate-by-route>

{{--             User--}}
            @if($user = auth()->user())
                <x-ui-menu-separator />

                <x-ui-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                    <x-slot:actions>
                        <x-ui-button icon="o-cog-6-tooth" class="btn-circle btn-ghost btn-xs" tooltip="Profile" no-wire-navigate link="/administration-panel/Profile" />
                        <x-ui-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip="Logoff" no-wire-navigate link="/logout" spinner/>
                    </x-slot:actions>
                </x-ui-list-item>

                <x-ui-menu-separator />
            @endif

            <x-ui-menu-item title="Dashboard" icon="m-chart-pie" link="/administration-panel/Dashboard" />
            <x-ui-menu-sub title="Products Management" icon="s-building-storefront">
                <x-ui-menu-item title="Products" icon="o-shopping-bag" link="/administration-panel/Products" />
                <x-ui-menu-item title="Brands" icon="o-book-open" link="/administration-panel/Brands" />
                <x-ui-menu-item title="Categories" icon="o-bookmark-square" link="/administration-panel/Categories" />
            </x-ui-menu-sub>
            <x-ui-menu-item title="Users Management" icon="s-user-group" link="/administration-panel/Users" />
            <x-ui-menu-item title="Orders Management" icon="s-truck" link="/administration-panel/Orders" />
        </x-ui-menu>
    </x-slot:sidebar>

{{--     The `$slot` goes here--}}
    <x-slot:content>
        {{ $slot }}
    </x-slot:content>
</x-ui-main>

{{-- Toast--}}
<x-ui-toast />
</body>

</html>
