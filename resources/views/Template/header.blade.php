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
        <!-- Notifikasi Bell -->
        {{-- <li class="nav-item dropdown me-3">
          <a href="#" class="nav-link" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="notif-bell-wrapper" style="position:relative; display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px;">
                  
                  <i class="fas fa-bell fa-lg" style="color:#f59e0b; font-size:1.65rem;"></i>
                  
                  <span id="notification-badge" 
                        class="notification-badge"
                        style="display:none; 
                              position:absolute; 
                              top:-6px; 
                              right:-6px; 
                              background:#ef4444; 
                              color:white; 
                              font-size:0.75rem; 
                              font-weight:700; 
                              min-width:22px; 
                              height:22px; 
                              display:flex; 
                              align-items:center; 
                              justify-content:center; 
                              border-radius:50%; 
                              border:2px solid #fff; 
                              box-shadow:0 0 0 3px #fff, 0 2px 8px rgba(0,0,0,0.25);
                              transform: translate(30%, -30%);">
                  </span>
              </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow"
              aria-labelledby="notificationDropdown"
              style="width:380px; margin-top:8px; padding:0; border-radius:12px; overflow:hidden;">

            <li class="px-4 py-3 d-flex justify-content-between align-items-center border-bottom bg-light">
              <span class="fw-bold fs-5">
                <i class="fas fa-bell text-warning me-2"></i> Notifikasi
              </span>
              <a href="#" id="mark-all-read" class="text-primary small text-decoration-none">
                Tandai semua dibaca
              </a>
            </li>

            <div id="notification-list" style="max-height:480px; overflow-y:auto;">
              <!-- JS akan mengisi -->
            </div>

            <li class="border-top">
              <a href="{{ route('shippings.index') }}" 
                 class="d-block text-center py-3 small text-primary text-decoration-none fw-medium">
                Lihat semua pengiriman
              </a>
            </li>
          </ul>
        </li> --}}
        
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const badge     = document.getElementById('notification-badge');
    const notifList = document.getElementById('notification-list');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const typeConfig = {
        assigned:        { icon: 'fa-user-check',      color: '#3b82f6', bg: '#dbeafe' },
        fallback:        { icon: 'fa-broadcast-tower',  color: '#8b5cf6', bg: '#ede9fe' },
        pickup:          { icon: 'fa-barcode',          color: '#f59e0b', bg: '#fef3c7' },
        pickup_admin:    { icon: 'fa-barcode',          color: '#f59e0b', bg: '#fef3c7' },
        in_transit:      { icon: 'fa-truck',            color: '#06b6d4', bg: '#cffafe' },
        delivered:       { icon: 'fa-check-circle',     color: '#16a34a', bg: '#dcfce7' },
        delivered_admin: { icon: 'fa-check-circle',     color: '#16a34a', bg: '#dcfce7' },
    };

    function getTypeConfig(type) {
        return typeConfig[type] ?? { icon: 'fa-bell', color: '#64748b', bg: '#f1f5f9' };
    }

    // ── Badge count ───────────────────────────────────────
    function loadUnreadCount() {
        fetch('{{ route("web.notifications.unread-count") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent   = data.count > 99 ? '99+' : data.count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(err => console.error('unread-count error:', err));
    }

    // ── Render item ───────────────────────────────────────
    function renderNotifItem(notif) {
        const cfg      = getTypeConfig(notif.type);
        const isUnread = !notif.is_read;
        const docId    = notif.travel_document?.id;
        const href     = docId ? `{{ url('shippings') }}/${docId}` : '#';

        return `
        <a href="${href}"
           class="d-flex align-items-start gap-2 px-3 py-2 text-decoration-none notification-item ${isUnread ? 'notif-item-unread' : ''}"
           data-id="${notif.id}"
           style="color:inherit; transition:background .15s;">

            <span class="notif-type-icon mt-1"
                  style="background:${cfg.bg}; color:${cfg.color};">
                <i class="fas ${cfg.icon}"></i>
            </span>

            <div style="min-width:0; flex:1;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="${isUnread ? 'fw-bold' : ''}" style="font-size:.875rem;">
                        ${notif.title}
                    </span>
                    ${isUnread
                        ? '<span class="ms-2 rounded-circle bg-warning" style="width:8px;height:8px;flex-shrink:0;display:inline-block;"></span>'
                        : ''}
                </div>
                <small class="text-muted d-block" style="font-size:.78rem; line-height:1.4;">
                    ${notif.message}
                </small>
                <small class="text-primary" style="font-size:.72rem;">${notif.created_at}</small>
            </div>
        </a>
        <div class="dropdown-divider m-0"></div>`;
    }

    // ── Load list ─────────────────────────────────────────
    function loadNotifications() {
        notifList.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-spinner fa-spin"></i> Memuat...
            </div>`;

        fetch('{{ route("web.notifications.index") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => {
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            return r.json();
        })
        .then(data => {
            notifList.innerHTML = '';
            const items = data?.data?.data ?? [];

            if (!items.length) {
                notifList.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                        <small>Tidak ada notifikasi</small>
                    </div>`;
                return;
            }

            items.forEach(notif => {
                notifList.insertAdjacentHTML('beforeend', renderNotifItem(notif));
            });

            // Update badge sekaligus dari response
            if (typeof data.unread_count !== 'undefined') {
                if (data.unread_count > 0) {
                    badge.textContent   = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }

            // Klik item → mark as read
            notifList.querySelectorAll('.notification-item').forEach(el => {
                el.addEventListener('click', function (e) {
                    const id = this.dataset.id;
                    if (!this.classList.contains('notif-item-unread')) return;

                    // Optimistic UI — langsung hapus style unread
                    this.classList.remove('notif-item-unread');
                    this.querySelector('.fw-bold')?.classList.remove('fw-bold');
                    this.querySelector('.bg-warning.rounded-circle')?.remove();

                    fetch(`{{ url('web/notifications') }}/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN':     csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(() => loadUnreadCount())
                    .catch(console.error);
                });
            });
        })
        .catch(err => {
            console.error('loadNotifications error:', err);
            notifList.innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-circle me-1"></i> Gagal memuat notifikasi
                </div>`;
        });
    }

    // ── ✅ Fix utama: listener ke <li>.dropdown, bukan ke <a> ──
    const dropdownToggle = document.getElementById('notificationDropdown');
    if (dropdownToggle) {
        dropdownToggle
            .closest('.dropdown')
            .addEventListener('shown.bs.dropdown', loadNotifications);
    }

    // ── Mark all read ─────────────────────────────────────
    document.getElementById('mark-all-read')?.addEventListener('click', function (e) {
        e.preventDefault();

        fetch('{{ route("web.notifications.read-all") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':     csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(() => {
            notifList.querySelectorAll('.notification-item').forEach(el => {
                el.classList.remove('notif-item-unread');
                el.querySelector('.fw-bold')?.classList.remove('fw-bold');
                el.querySelector('.bg-warning.rounded-circle')?.remove();
            });
            badge.style.display = 'none';
        })
        .catch(console.error);
    });

    // ── Init ──────────────────────────────────────────────
    loadUnreadCount();
    setInterval(loadUnreadCount, 30_000); // polling tiap 30 detik
});
</script>
@endpush