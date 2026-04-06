<div id="preloader" class="fixed inset-0 z-50 flex items-center justify-center bg-white">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>

<script>
  // Hilangkan preloader setelah halaman siap
  document.addEventListener("DOMContentLoaded", function() {
    const preloader = document.getElementById('preloader');
    if (preloader) {
      setTimeout(() => {
        preloader.style.display = 'none';
      }, 800);
    }
  });
</script>
