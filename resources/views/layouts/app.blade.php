<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'RekaTrack')</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('images/logo/logo-rekatrack.png') }}">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- Fonts and icons (mengikuti Kaiadmin) -->
  <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    WebFont.load({
      google: { families: ["Public Sans:300,400,500,600,700"] },
      custom: {
        families: [
          "Font Awesome 5 Solid",
          "Font Awesome 5 Regular",
          "Font Awesome 5 Brands",
          "simple-line-icons",
        ],
        urls: ["{{ asset('assets/css/fonts.min.css') }}"],
      },
      active: function () {
        sessionStorage.fonts = true;
      },
    });
  </script>

  <!-- CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
  {{-- <link rel="stylesheet" href="{{ asset('assets/css/sidebar.css') }}" /> --}}


  <!-- Custom CSS (jika ada tambahan di masa depan) -->
  @stack('styles')
</head>

<body>
  <div class="wrapper">
    <!-- Sidebar -->
    @include('Template.sidebar')

    <!-- Main Panel -->
    <div class="main-panel">
      <!-- Main Header -->
      @include('Template.header')

      <!-- Page Content -->
      <div class="container">
        <div class="page-inner">
          <!-- Judul Halaman (opsional) -->
          @if (isset($pageName) || (isset($breadcrumbs) && count($breadcrumbs) > 1))
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
              <div>
                @if (isset($pageName))
                  <h3 class="fw-bold mb-3">{{ $pageName }}</h3>
                @endif
                <!-- Breadcrumb opsional (jika butuh multi-level) -->
                @if (isset($breadcrumbs))
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      @foreach ($breadcrumbs as $breadcrumb)
                        @if ($loop->last)
                          <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['label'] }}</li>
                        @else
                          <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none">{{ $breadcrumb['label'] }}</a>
                          </li>
                        @endif
                      @endforeach
                    </ol>
                  </nav>
                @endif
              </div>
              <!-- Area tombol aksi (opsional per halaman) -->
              @hasSection('page-actions')
                <div class="ms-md-auto py-2 py-md-0">
                  @yield('page-actions')
                </div>
              @endif
            </div>
          @endif

          <!-- Alert -->
          @include('partials.alert.alert')

          <!-- Konten Utama -->
          @yield('content')
        </div>
      </div>

      <!-- Footer -->
      <footer class="footer">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <div class="container-fluid d-flex justify-content-between">
          <div class="copyright">
            &copy; {{ date('Y') }} RekaTrack by <b>TI Rekaindo</b>. All rights reserved.
          </div>
        </div>
      </footer>
    </div>
  </div>

  <!-- Modal -->
  @stack('modals')

  <!-- Delete Form (global) -->
  <form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
  </form>

  <!-- Core JS Files -->
  <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

  <!-- Custom JS -->
  <script>
    // Sidebar toggle mobile
    document.querySelector('[data-bs-toggle="sidebar"]')?.addEventListener('click', function () {
      document.querySelector('.sidebar').classList.toggle('toggled');
    });

    // Global delete confirmation (opsional)
    window.deleteItem = function (url) {
      if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = url;
        form.submit();
      }
    };
  </script>

  @stack('scripts')
</body>
</html>
