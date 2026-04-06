<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('shippings.index') }}">lala</a></li>
    @if(isset($pageName))
      <li class="breadcrumb-item active" aria-current="page">{{ $pageName }}</li>
    @endif
  </ol>
</nav>
