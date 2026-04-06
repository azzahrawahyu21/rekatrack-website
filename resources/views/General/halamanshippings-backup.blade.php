@extends('layouts.app')

@section('title', 'Manajemen Pengiriman - RekaTrack')
@section('page-title', 'Manajemen Pengiriman')

@section('content')
  <!-- Stats Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
      <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #0d6efd;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted text-uppercase mb-2">Total Pengiriman</h6>
              <h2 class="mb-0 fw-bold">{{ $totalPengiriman }}</h2>
            </div>
            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
              <i class="bi bi-box-seam fs-3 text-primary"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #ffc107;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted text-uppercase mb-2">Belum Terkirim</h6>
              <h2 class="mb-0 fw-bold">{{ $totalBelumTerkirim }}</h2>
            </div>
            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
              <i class="bi bi-clock-history fs-3 text-warning"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #0dcaf0;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted text-uppercase mb-2">Sedang Dikirim</h6>
              <h2 class="mb-0 fw-bold">{{ $totalSedangDikirim }}</h2>
            </div>
            <div class="bg-info bg-opacity-10 rounded-circle p-3">
              <i class="bi bi-truck fs-3 text-info"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="card stat-card border-0 shadow-sm h-100" style="border-left-color: #198754;">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted text-uppercase mb-2">Terkirim</h6>
              <h2 class="mb-0 fw-bold">{{ $totalTerkirim }}</h2>
            </div>
            <div class="bg-success bg-opacity-10 rounded-circle p-3">
              <i class="bi bi-check-circle fs-3 text-success"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Controls -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="d-flex gap-2">
            <a href="{{ route('shippings.add') }}" class="btn btn-primary">
              <i class="bi bi-plus-circle me-1"></i> Tambah Pengiriman
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
              <i class="bi bi-download me-1"></i> Export
            </button>
          </div>
        </div>

        <div class="col-md-6">
          <div class="d-flex gap-2">
            <select class="form-select" id="statusFilter" style="max-width: 200px;">
              <option value="">Semua Status</option>
              <option value="Belum terkirim">Belum Terkirim</option>
              <option value="Sedang dikirim">Sedang Dikirim</option>
              <option value="Terkirim">Terkirim</option>
            </select>

            <div class="input-group flex-grow-1">
              <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari nomor SJN, tujuan, atau proyek...">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3 text-center" width="60">No</th>
              <th class="px-4 py-3">Nomor SJN</th>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Kepada</th>
              <th class="px-4 py-3">Proyek</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody id="tableBody">
            @forelse($listTravelDocument as $index => $doc)
              <tr
                class="shipping-row"
                data-status="{{ $doc->status }}"
                onclick="window.location='{{ route('shippings.detail', $doc->id) }}'"
                style="cursor: pointer;"
              >
                <td class="px-4 text-center text-muted">{{ $listTravelDocument->firstItem() + $index }}</td>
                <td class="px-4">
                  <span class="fw-semibold text-primary">{{ $doc->no_travel_document ?: '-' }}</span>
                </td>
                <td class="px-4">
                  <span class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $doc->date_no_travel_document ? \Carbon\Carbon::parse($doc->date_no_travel_document)->format('d/m/Y') : '-' }}
                  </span>
                </td>
                <td class="px-4">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt text-danger me-2"></i>
                    <span>{{ $doc->send_to ?: '-' }}</span>
                  </div>
                </td>
                <td class="px-4">{{ $doc->project ?: '-' }}</td>
                <td class="px-4">
                  @if($doc->status == 'Belum terkirim')
                    <span class="badge bg-warning text-dark">
                      <i class="bi bi-clock-history me-1"></i>Belum terkirim
                    </span>
                  @elseif($doc->status == 'Sedang dikirim')
                    <span class="badge bg-info">
                      <i class="bi bi-truck me-1"></i>Sedang dikirim
                    </span>
                  @elseif($doc->status == 'Terkirim')
                    <span class="badge bg-success">
                      <i class="bi bi-check-circle me-1"></i>Terkirim
                    </span>
                  @endif
                </td>
                <td class="px-4 text-center">
                  <div class="btn-group" role="group">
                    <a
                      href="{{ route('shippings.detail', $doc->id) }}"
                      class="btn btn-sm btn-outline-primary"
                      title="Detail"
                      onclick="event.stopPropagation();"
                    >
                      <i class="bi bi-eye"></i>
                    </a>
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-danger"
                      onclick="event.stopPropagation(); confirmDelete({{ $doc->id }})"
                      title="Hapus"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5">
                  <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                  <p class="mb-0 text-muted">Tidak ada data pengiriman</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="card-footer bg-white">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">
          Menampilkan {{ $listTravelDocument->firstItem() ?? 0 }} ke {{ $listTravelDocument->lastItem() ?? 0 }} dari total {{ $listTravelDocument->total() }} data
        </small>
        <div>
          {{ $listTravelDocument->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>
@endsection

@push('modals')
  <!-- Export Modal -->
  <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="{{ route('shippings.export') }}" method="GET">
          <div class="modal-header">
            <h5 class="modal-title" id="exportModalLabel">
              <i class="bi bi-download me-2"></i>Export Data Pengiriman
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="start_date" class="form-label">Tanggal Mulai</label>
              <input type="date" class="form-control" id="start_date" name="start_date">
            </div>
            <div class="mb-3">
              <label for="end_date" class="form-label">Tanggal Akhir</label>
              <input type="date" class="form-control" id="end_date" name="end_date">
            </div>
            <div class="alert alert-info mb-0">
              <i class="bi bi-info-circle me-2"></i>
              <small>Kosongkan tanggal untuk export semua data</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endpush

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const statusFilter = document.getElementById('statusFilter');
      const rows = document.querySelectorAll('.shipping-row');

      function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const status = row.dataset.status;

          const matchesSearch = text.includes(searchTerm);
          const matchesStatus = !statusValue || status === statusValue;

          row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
      }

      searchInput.addEventListener('input', filterTable);
      statusFilter.addEventListener('change', filterTable);
    });

    function confirmDelete(id) {
      if (confirm('Apakah Anda yakin ingin menghapus data pengiriman ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = `/shippings/${id}`;
        form.submit();
      }
    }
  </script>
@endpush
