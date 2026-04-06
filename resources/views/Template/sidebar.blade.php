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

        <!-- Main Section -->
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">Menu Utama</h4>
        </li>

        <!-- Menu: Manajemen Pengiriman -->
        <li class="nav-item {{ request()->routeIs('shippings.*') && !request()->routeIs('shippings.trash') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('shippings.index') }}" data-tooltip="Manajemen Pengiriman">
            <i class="fas fa-boxes"></i>
            <p>Manajemen Pengiriman</p>
          </a>
        </li>

        <!-- Menu: Manajemen Unit -->
        <li class="nav-item {{ request()->routeIs('units.*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('units.index') }}" data-tooltip="Manajemen Unit">
            <i class="fas fa-balance-scale"></i>
            <p>Manajemen Unit</p>
          </a>
        </li>

        <!-- Menu: Tracking -->
        <li class="nav-item {{ request()->routeIs('tracking.*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('tracking.index') }}" data-tooltip="Tracking Pengiriman">
            <i class="fas fa-map-marked-alt"></i>
            <p>Tracking Pengiriman</p>
          </a>
        </li>

        <!-- Utilities Section -->
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">Utilities</h4>
        </li>

        <!-- Menu: Trash -->
        <li class="nav-item {{ request()->routeIs('shippings.trash') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('shippings.trash') }}" data-tooltip="Recycle Bin">
            <i class="fa-solid fa-trash-arrow-up"></i>
            <p>Recycle Bin</p>
            @if(isset($trashedCount) && $trashedCount > 0)
              <span class="badge badge-danger">{{ $trashedCount }}</span>
            @endif
          </a>
        </li>

        <!-- Administrator Section (Only for Super Admin) -->
        @if(auth()->check() && auth()->user()->role?->name == 'Super Admin')
          <li class="nav-section">
            <span class="sidebar-mini-icon">
              <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">Administrator</h4>
          </li>

          <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('users.index') }}" data-tooltip="Manajemen User">
              <i class="fas fa-users-cog"></i>
              <p>Manajemen User</p>
            </a>
          </li>
        @endif

      </ul>
    </div>
  </div>

  <!-- Sidebar Footer (Optional) -->
  <div class="sidebar-footer">
    <div class="user-box">
      <div class="avatar-sm float-start me-2">
        @if(auth()->user()->avatar)
          <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="user-img" class="avatar-title rounded-circle">
        @else
          <span class="avatar-title rounded-circle bg-soft-primary text-primary">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </span>
        @endif
      </div>
      <div class="user-info">
        <h6 class="mb-0">{{ auth()->user()->name }}</h6>
        <p class="text-muted mb-0">{{ auth()->user()->role?->name ?? 'User' }}</p>
      </div>
    </div>
  </div>
</div>
