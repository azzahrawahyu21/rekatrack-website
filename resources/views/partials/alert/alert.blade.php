@if (session('success') || session('error') || session('warning') || session('info'))
  @php
    $types = ['success', 'error', 'warning', 'info'];
    $sessionType = collect($types)->first(fn($type) => session()->has($type));
    $message = session($sessionType);
    $bootstrapType = [
      'success' => 'success',
      'error' => 'danger',
      'warning' => 'warning',
      'info' => 'info',
    ][$sessionType] ?? 'info';
  @endphp

  <div class="alert alert-{{ $bootstrapType }} alert-dismissible fade show" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Terjadi kesalahan validasi:</strong>
    <ul class="mb-0 mt-2 ps-3">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
