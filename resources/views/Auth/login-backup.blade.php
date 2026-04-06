<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sign In | RekaTrack</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('images/logo/logo-rekatrack.png') }}">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    html, body {
      height: 100%;
    }
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-form {
      max-width: 400px;
      width: 100%;
    }
    .login-side {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #fff;
      display: none;
    }
    @media (min-width: 992px) {
      .login-side {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
      }
    }
  </style>
</head>
<body class="bg-light">

  <!-- Alert Flash Message -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="login-container">
    <div class="row g-0 w-100">
      <!-- Form Login -->
      <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center">
        <div class="login-form p-4 p-md-5">
          <div class="text-center mb-4">
            <h2 class="fw-bold">Masuk</h2>
            <p class="text-muted">Masukkan email dan password untuk masuk!</p>
          </div>

          <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                name="email"
                placeholder="info@gmail.com"
                value="{{ old('email') }}"
                required
              >
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <input
                  type="password"
                  class="form-control @error('password') is-invalid @enderror"
                  id="password"
                  name="password"
                  placeholder="Masukkan password anda"
                  required
                >
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Masuk</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Sisi Kanan (Logo & Slogan) -->
      <div class="col-12 col-lg-6 login-side text-center">
        <div>
          <div class="mb-4">
            <img src="{{ asset('images/logo/logo-rekatrack.png') }}" alt="RekaTrack" class="img-fluid" style="max-height: 60px;">
          </div>
          <p class="text-white-50">
            Sistem Manajemen Distribusi Barang PT. Rekaindo Global Jasa.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function () {
      const password = document.getElementById('password');
      const icon = this.querySelector('i');
      if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
  </script>
</body>
</html>
