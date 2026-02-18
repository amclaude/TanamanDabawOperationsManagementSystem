<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tanaman')</title>

    <script>
        // Apply persisted sidebar-collapsed state before styles render to avoid layout jump
        (function(){
            try {
                if (localStorage.getItem('sidebarCollapsed') === '1') {
                    document.documentElement.setAttribute('data-sidebar-collapsed', '1');
                }
            } catch (e) {}
        })();
    </script>

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

<body class="dashboard-body">

    @include('partials.navbar')

    <div class="dashboard-container">
        @if(in_array(auth()->user()->role, ['Admin', 'Operations Manager']))
            @include('partials.sidebar')
        @endif
        <main class="main-content" style="padding: 30px 25px;">
            @yield('content')
        </main>
    </div>

    @include('partials.footer')
    @stack('scripts')
</body>

</html>