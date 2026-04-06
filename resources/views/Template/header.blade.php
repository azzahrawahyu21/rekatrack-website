<div class="main-header">
  <div class="main-header-logo">
    <!-- Logo Header (identik dengan sidebar untuk konsistensi) -->
    <div class="logo-header" data-background-color="dark">
      <a href="{{ route('shippings.index') }}" class="logo">
        <img
          src="{{ asset('images/logo/logo-rekatrack.png') }}"
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
        <button class="btn btn-toggle sidenav-toggler d-block d-lg-none" data-bs-toggle="sidebar">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more d-lg-none">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
  </div>

  <!-- Navbar Header -->
  <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
      <!-- Judul Halaman (opsional, bisa dikosongkan) -->
      {{-- <span class="d-none d-lg-block fw-bold">@yield('page-title', 'Dashboard')</span> --}}

      <!-- User Menu (kanan) -->
      <ul class="navbar-nav topbar-nav ms-auto align-items-center">
        <li class="nav-item topbar-user dropdown hidden-caret">
          <a
            class="dropdown-toggle profile-pic"
            data-bs-toggle="dropdown"
            href="#"
            aria-expanded="false"
          >
            <div class="avatar-sm">
              <img
                src="{{ auth()->user()->avatar_url }}"
                alt="Profile"
                class="avatar-img rounded-circle"
              />
            </div>
            <span class="profile-username">
              <span class="op-7">Hi,</span>
              <span class="fw-bold">{{ auth()->user()->name ?? 'User' }}</span>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                    <img
                      src="{{ auth()->user()->avatar_url }}"
                      alt="Profile"
                      class="avatar-img rounded"
                    />
                  </div>
                  <div class="u-text">
                    <h4>{{ auth()->user()->name ?? 'User' }}</h4>
                    <p class="text-muted">{{ auth()->user()->email ?? '' }}</p>
                  </div>
                </div>
              </li>
              <li>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('profile') }}">Profile</a>
                <a class="dropdown-item" href="#">Settings</a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">Logout</button>
                </form>
              </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
  <!-- End Navbar -->
</div>
