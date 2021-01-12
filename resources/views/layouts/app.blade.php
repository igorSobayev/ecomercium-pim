<!doctype html>
<html class="fixed">

<head>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>{{ config('app.name', 'Ecomercium PIM') }}</title>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <link rel="stylesheet" href="{{ asset('css/font-awesome/css/all.min.css') }}" />

</head>

<body>


    {{-- Comprobación inicial de si el usuario ha iniciado sesión o no --}}
    @if (!Auth::check())
    @yield('verification')
    {{-- @include('auth.login') --}}
    @endif
    {{-- Fin comprobación --}}

    @if (Auth::check())
    <section class="body" id="app">

        <!-- start: header -->
        @include('partials.nav')
        <!-- end: header -->

        <div class="inner-wrapper">

            {{-- Main --}}
            @include('partials.main')
            {{-- End main --}}
        </div>

    </section>
    @endif

    @if (Auth::check())
    {{-- Scripts --}}
    @include('partials.scripts')
    {{-- Fin scripts --}}
    @endif

</body>

</html>