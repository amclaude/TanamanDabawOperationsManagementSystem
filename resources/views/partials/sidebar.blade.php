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
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <div class="sidebar-footer">
            <button type="submit" class="sign-out-link" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer; font-size: 1rem;">
                <i class="fas fa-sign-out-alt"></i> <span class="label">Sign Out</span>
            </button>
        </div>
    </form>
</aside>