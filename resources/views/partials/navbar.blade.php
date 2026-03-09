<nav class="navbar">
    <div class="nav-left">
        <button id="menu-toggle" title="Toggle sidebar" aria-label="Toggle sidebar" style="font-size: 1.2rem; cursor: pointer; margin-right: 6px; background: none; border: none; color: inherit;">
            <i class="fas fa-bars"></i>
        </button>

        <div class="nav-brand">
        <div class="logo-container">
            <img src="{{ asset('images/TanamanLogo.png') }}" alt="Logo" class="nav-logo">
        </div>
        <span class="company-name">Tanaman</span>
        </div>
    </div>

    <div class="nav-profile" id="profile-trigger">
        <div class="profile-text">
            <span class="profile-name">{{ implode(' ', array_slice(explode(' ', trim(Auth::user()->name ?? '')), 0, 2)) }}</span>
            <span class="profile-role">{{ Auth::user()->role }}</span>
        </div>
        <img src="{{ asset('images/images.jpg') }}" class="profile-pic">
        <i class="fas fa-chevron-down dropdown-icon"></i>

        <div class="dropdown-menu" id="profile-dropdown">
            <div class="dropdown-header">
                <div class="user-name">{{ implode(' ', array_slice(explode(' ', trim(Auth::user()->name ?? '')), 0, 2)) }}</div>
                <div class="user-email">{{ Auth::user()->email }}</div>
            </div>
            <hr>
            <a href="{{ route('profile') }}" class="dropdown-item">
                <i class="far fa-user"></i> My Profile
            </a>
            <a href="#" class="dropdown-item">
                <i class="fas fa-cog"></i> Account Setting
            </a>
            <hr>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item logout-item" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </button>
            </form>
        </div>
    </div>
</nav>
@push('scripts')
<script>
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (menuToggle && sidebar) {
        const setSidebarCollapsed = (collapsed) => {
            if (collapsed) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
            }
            try { localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0'); } catch (e) {}
            try {
                var maxAge = 60*60*24*365; // 1 year
                document.cookie = 'sidebarCollapsed=' + (collapsed ? '1' : '0') + '; path=/; max-age=' + maxAge + ';';
            } catch (e) {}
            try {
                if (collapsed) {
                    document.documentElement.setAttribute('data-sidebar-collapsed', '1');
                } else {
                    document.documentElement.removeAttribute('data-sidebar-collapsed');
                }
            } catch (e) {}
        };

        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            if (window.innerWidth < 768) {
                const isActive = sidebar.classList.toggle('active');
                if (overlay) overlay.classList.toggle('show', isActive);
                document.documentElement.classList.toggle('sidebar-open', isActive);
                document.body.classList.toggle('sidebar-open', isActive);
                return;
            }
            sidebar.classList.toggle('collapsed');
            setSidebarCollapsed(sidebar.classList.contains('collapsed'));
        });

        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('active') &&
                !sidebar.contains(e.target) &&
                e.target !== menuToggle) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('show');
                document.documentElement.classList.remove('sidebar-open');
                document.body.classList.remove('sidebar-open');
            }
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        const profileTrigger = document.getElementById('profile-trigger');
        const dropdownMenu = document.getElementById('profile-dropdown');
        const dropdownIcon = document.querySelector('.dropdown-icon');

        try {
            const persisted = localStorage.getItem('sidebarCollapsed');
            if (persisted === '1' && window.innerWidth >= 768) {
                sidebar.classList.add('collapsed');
            } else if (persisted === '0' && window.innerWidth >= 768) {
                sidebar.classList.remove('collapsed');
            } else if (window.innerWidth < 768) {
                document.documentElement.removeAttribute('data-sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', '0');
                var maxAge = 60*60*24*365;
                document.cookie = 'sidebarCollapsed=0; path=/; max-age=' + maxAge + ';';
            }
        } catch (e) {}

        if (profileTrigger && dropdownMenu) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');

                if (dropdownIcon) {
                    dropdownIcon.style.transform = dropdownMenu.classList.contains('show') ?
                        'rotate(180deg)' :
                        'rotate(0deg)';
                }
            });

            window.addEventListener('click', function(e) {
                if (!profileTrigger.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                    if (dropdownIcon) {
                        dropdownIcon.style.transform = 'rotate(0deg)';
                    }
                }
            });
        }
        if (overlay) {
            overlay.addEventListener('click', function() {
                if (sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('show');
                    document.documentElement.classList.remove('sidebar-open');
                    document.body.classList.remove('sidebar-open');
                }
            });
        }
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                if (sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
                if (overlay) overlay.classList.remove('show');
                document.documentElement.classList.remove('sidebar-open');
                document.body.classList.remove('sidebar-open');
            }
        });
    });
</script>
@endpush
