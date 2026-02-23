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
    <!-- Global front-end input validation: ensures phone numbers are digits-only, quantities/stock are non-negative integers, and prices/budgets are non-negative decimals -->
    <script>
        document.addEventListener('input', function(e) {
            const el = e.target;
            if (!el || el.tagName !== 'INPUT') return;

            // Phone inputs: digits only (no letters, no negatives)
            if (el.matches('input[type="tel"], #c_phone, .phone-input')) {
                const pos = el.selectionStart;
                el.value = (el.value || '').replace(/\D+/g, '');
                try { el.setSelectionRange(pos, pos); } catch (err) {}
                return;
            }

            // Integer inputs (quantities, stock): remove non-digits and disallow negative
            if (el.matches('#itemStock, #inQuantity, #outQuantity, .item-qty, .i-qty, input.integer')) {
                const pos = el.selectionStart;
                el.value = (el.value || '').replace(/\D+/g, '');
                try { el.setSelectionRange(pos, pos); } catch (err) {}
                return;
            }

            // Positive decimal inputs (prices, budgets): allow numbers and single dot, no negative
            if (el.matches('#itemPrice, #p_budget, .item-price, .i-price, input.positive')) {
                let v = el.value || '';
                v = v.replace(/[^0-9.]+/g, '');
                const parts = v.split('.');
                if (parts.length > 2) v = parts[0] + '.' + parts.slice(1).join('');
                el.value = v;
                return;
            }
        });
    </script>
    @stack('scripts')
</body>

</html>