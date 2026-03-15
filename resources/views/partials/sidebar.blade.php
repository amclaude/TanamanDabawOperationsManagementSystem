<aside class="sidebar {{ request()->cookie('sidebarCollapsed') === '1' ? 'collapsed' : '' }}">
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> <span class="label">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="{{ route('clients') }}"
                class="{{ request()->routeIs('clients*') ? 'active' : '' }}">
                <i class="fas fa-user-friends"></i> <span class="label">Clients</span>
            </a>
        </li>

        <li>
            <a href="{{ route('projects') }}"
                class="{{ request()->routeIs('projects*') ? 'active' : '' }}">
                <i class="fas fa-briefcase"></i> <span class="label">Projects</span>
            </a>
        </li>

        <li>
            <a href="{{ route('employees') }}"
                class="{{ request()->routeIs('employees*') ? 'active' : '' }}">
                <i class="fas fa-user-check"></i> <span class="label">Employees</span>
            </a>
        </li>

        <li>
            <a href="{{ route('quotes') }}"
                class="{{ request()->routeIs('quotes*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> <span class="label">Quotes</span>
            </a>
        </li>

        <li>
            <a href="{{ route('invoices') }}"
                class="{{ request()->routeIs('invoices*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> <span class="label">Invoices</span>
            </a>
        </li>

        <li>
            <a href="{{ route('inventory') }}"
                class="{{ request()->routeIs('inventory*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> <span class="label">Inventory</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-bottom">
        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form">
            @csrf
            <div class="sidebar-footer">
                <button type="submit" class="sign-out-link">
                    <i class="fas fa-sign-out-alt"></i> <span class="label sign-out-text">Sign Out</span>
                </button>
            </div>
        </form>
    </div>
</aside>
