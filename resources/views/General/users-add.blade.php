@extends('layouts.app')

@section('title', 'Tambah Pengguna - RekaTrack')
@php($pageName = 'Tambah Pengguna')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h4 class="card-title mb-0">Data Pengguna</h4>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}">
          @csrf

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input
                type="text"
                name="fullname"
                value="{{ old('fullname') }}"
                class="form-control @error('fullname') is-invalid @enderror"
                placeholder="Nama lengkap"
              />
              @error('fullname')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">NIP <span class="text-danger">*</span></label>
              <input
                type="text"
                name="nip"
                value="{{ old('nip') }}"
                class="form-control @error('nip') is-invalid @enderror"
                placeholder="Nomor Induk Pegawai"
              />
              @error('nip')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">E-mail <span class="text-danger">*</span></label>
              <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="contoh@email.com"
              />
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
              <input
                type="text"
                name="telephone"
                value="{{ old('telephone') }}"
                class="form-control @error('telephone') is-invalid @enderror"
                placeholder="081234567890"
              />
              @error('telephone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-12">
              <label class="form-label">Pilih Role <span class="text-danger">*</span></label>
              <select
                name="role"
                class="form-select @error('role') is-invalid @enderror"
              >
                <option value="">-- Pilih Role --</option>
                @foreach ($roles as $role)
                  <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                  </option>
                @endforeach
              </select>
              @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-user-plus me-1"></i> Tambah Pengguna
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
