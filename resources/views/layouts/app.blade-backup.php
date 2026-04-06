<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'RekaTrack')</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('images/logo/logo-rekatrack.png') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    body {
      font-family: 'Inter', system-ui, sans-serif;
    }
    .sidebar {
      min-height: 100vh;
      background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
    }
    .main-content {
      margin-left: 250px;
      min-height: 100vh;
      background-color: #f8f9fa;
    }
    .stat-card {
      border-left: 4px solid;
      transition: transform 0.2s;
    }
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    .table-responsive {
      border-radius: 0.5rem;
      overflow: hidden;
    }
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        position: fixed;
        z-index: 1050;
      }
      .sidebar.show {
        transform: translateX(0);
      }
      .main-content {
        margin-left: 0;
      }
    }
  </style>

  @stack('styles')
</head>

<body>
  <!-- Preloader -->
  {{-- @include('partials.preloader') --}}

  <div class="d-flex">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      @include('Template.sidebar')
    </aside>

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
      <!-- Header -->
      <header class="bg-white shadow-sm sticky-top">
        @include('Template.header')
      </header>

      <!-- Content -->
      <main class="container-fluid p-4">
        <!-- Breadcrumb -->
        @if (isset($breadcrumbs))
          <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
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

        <!-- Konten Halaman -->
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Modal -->
  @stack('modals')

  <!-- Delete Form -->
  <form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
  </form>

  <script>
    // Sidebar toggle for mobile
    document.querySelector('[data-bs-toggle="sidebar"]')?.addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('show');
    });
  </script>

  @stack('scripts')
</body>
</html>
