<header class="bg-white shadow-sm sticky-top">
  <div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between py-3">
      <button id="toggleSidebar" class="btn btn-link d-md-none" type="button">
        <i class="bi bi-list fs-4"></i>
      </button>

      <span class="d-none d-md-block fw-bold">@yield('page-title', 'Dashboard')</span>

      <div class="dropdown ms-auto">
        <button class="btn btn-link text-decoration-none" type="button" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle fs-4"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="#">Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item">Logout</a></button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>
</header>
