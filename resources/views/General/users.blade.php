@extends('layouts.app')

@section('title', 'Manajemen Pengguna - RekaTrack')
@php($pageName = 'Manajemen Pengguna')

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Daftar Pengguna</h4>
        <a href="{{ route('users.add') }}" class="btn btn-primary btn-round">
          <i class="fas fa-plus me-1"></i> Tambah Pengguna
        </a>
      </div>
      <div class="card-body">
        <!-- Pencarian -->
        <div class="row mb-4">
          <div class="col-md-6">
            <form action="{{ route('users.index') }}" method="GET" class="d-flex">
              <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control"
                placeholder="Cari pengguna (nama, email, NIP...)"
              />
              <button type="submit" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-search"></i>
              </button>
            </form>
          </div>
        </div>

        <!-- Tabel -->
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th width="5%">No</th>
                <th>NIP</th>
                <th>Nama Pengguna</th>
                <th>Email</th>
                <th>Telepon</th>
                <th width="15%">Role</th>
                <th class="text-center" width="15%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $index => $user)
                <tr>
                  <td>{{ $users->firstItem() + $index }}</td>
                  <td>{{ $user->nip ?? '-' }}</td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{ $user->phone_number ?? '-' }}</td>
                  <td>
                    @if($user->role)
                      <span class="badge badge-info">{{ $user->role->name }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="form-button-action">
                      <a
                        href="{{ route('users.edit', $user->id) }}"
                        class="btn btn-link btn-secondary btn-lg"
                        title="Edit"
                      >
                        <i class="fas fa-edit"></i>
                      </a>
                      <form
                        action="{{ route('users.destroy', $user->id) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Yakin ingin menghapus pengguna \"{{ $user->name }}\"?')"
                      >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link btn-danger btn-lg" title="Hapus">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">
                    <i class="fas fa-users fs-2 d-block mb-2"></i>
                    Tidak ada pengguna ditemukan
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      @if($users->hasPages())
        <div class="card-footer d-flex justify-content-between align-items-center">
          <small class="text-muted">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} data
          </small>
          <nav aria-label="Navigasi halaman">
            {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
          </nav>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
