<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tanaman')</title>

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Sidebar Collapse Transitions */
        .dashboard-container {
            display: flex;
        }

        .sidebar {
            width: 250px; /* Default width */
            transition: width 0.3s ease;
            overflow: hidden;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* Collapsed State */
        .dashboard-container.collapsed .sidebar {
            width: 70px;
        }

        .dashboard-container.collapsed .sidebar span {
            display: none;
        }

        .dashboard-container.collapsed .sidebar .sidebar-menu li a,
        .dashboard-container.collapsed .sidebar .sidebar-footer .sign-out-link {
            justify-content: center;
            text-align: center !important;
            padding-left: 0;
            padding-right: 0;
        }

        .dashboard-container.collapsed .sidebar i {
            margin-right: 0;
        }

        /* Prevent transitions on page load */
        .no-transition * {
            transition: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="dashboard-body no-transition">

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