@extends('layouts.app')

@section('title', 'Manajemen Unit - RekaTrack')
@php($pageName = 'Manajemen Unit')

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Daftar Satuan Unit</h4>
        <a href="{{ route('units.add') }}" class="btn btn-primary btn-round">
          <i class="fas fa-plus me-1"></i> Tambah Unit
        </a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th width="10%">No</th>
                <th>Nama Unit</th>
                <th class="text-center" width="20%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($units as $index => $unit)
                <tr>
                  <td>{{ $units->firstItem() + $index }}</td>
                  <td>{{ $unit->name }}</td>
                  <td class="text-center">
                    <div class="form-button-action">
                      <a
                        href="{{ route('units.edit', $unit->id) }}"
                        class="btn btn-link btn-primary btn-lg"
                        title="Edit"
                      >
                        <i class="fas fa-edit"></i>
                      </a>
                      <form
                        action="{{ route('units.destroy', $unit->id) }}"
                        method="POST"
                        class="d-inline delete-form"
                        onsubmit="return confirm('Yakin ingin menghapus unit \"{{ $unit->name }}\"?')"
                      >
                        @csrf
                        @method('DELETE')
                      <button
                        type="button"
                        class="btn btn-link btn-danger btn-lg"
                        title="Hapus"
                        data-bs-toggle="modal"
                        data-bs-target="#confirmDeleteModal"
                        data-name="{{ $unit->name }}"
                        data-url="{{ route('units.destroy', $unit->id) }}"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted">
                    <i class="fas fa-cube fs-2 d-block mb-2"></i>
                    Tidak ada satuan unit
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if ($units->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center">
          <small class="text-muted">
            Menampilkan {{ $units->firstItem() }}–{{ $units->lastItem() }} dari {{ $units->total() }} data
          </small>
          <div>
            {{ $units->links('pagination::bootstrap-5') }}
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus satuan unit <strong id="unit-name"></strong>?<br>
        Tindakan ini tidak dapat dikembalikan.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form id="delete-form" method="POST" style="display:inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('confirmDeleteModal');
    const deleteForm = document.getElementById('delete-form');
    const unitNameEl = document.getElementById('unit-name');

    deleteModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const name = button.getAttribute('data-name');
      const url = button.getAttribute('data-url');

      unitNameEl.textContent = name;
      deleteForm.action = url;
    });
  });
</script>
@endpush
