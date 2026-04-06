<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="{{ route('shippings.index') }}" class="logo">
        <img
          src="{{ asset('images/logo/logo-rekatrack.svg') }}"
          alt="RekaTrack"
          class="navbar-brand"
          height="24"
        />
        <span class="fw-bold text-white ms-2">RekaTrack</span>
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar d-none d-lg-block">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler d-block d-lg-none">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more d-lg-none">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>

  <!-- Sidebar Content (Menu Utama) -->
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        <!-- Menu: Manajemen Pengiriman -->
        <li class="nav-item {{ request()->routeIs('shippings.*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('shippings.index') }}" title="Manajemen Pengiriman">
            <i class="fas fa-boxes"></i>
            <p>Manajemen Pengiriman</p>
          </a>
        </li>

        <!-- Menu: Manajemen Unit -->
        <li class="nav-item {{ request()->routeIs('units.*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('units.index') }}" title="Manajemen Satuan Unit">
            <i class="fas fa-balance-scale"></i>
            <p>Manajemen Unit</p>
          </a>
        </li>

        <!-- Menu: Tracking -->
        <li class="nav-item {{ request()->routeIs('tracking.*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('tracking.index') }}" title="Tracking">
            <i class="fas fa-map-marked-alt"></i>
            <p>Tracking</p>
          </a>
        </li>

        <!-- ✅ Menu: Trash (Hanya untuk Admin & Super Admin) -->

        <li class="nav-item {{ request()->routeIs('shippings.trash') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('shippings.trash') }}" title="Trash Pengiriman">
              <i class="fas fa-recycle"></i>
              <p>Trash</p>
            </a>
        </li>

        <!-- Menu: Manajemen User (Only for Super Admin) -->
        @if(auth()->check() && auth()->user()->role?->name == 'Super Admin')
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">Administrator</h4>
          </li>
          <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('users.index') }}" title="Manajemen User">
              <i class="fas fa-users-cog"></i>
              <p>Manajemen User</p>
            </a>
          </li>
        @endif
      </ul>
    </div>
  </div>
</div>
