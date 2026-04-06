@extends('layouts.app')

@section('title', 'Tambah Unit - RekaTrack')
@php($pageName = 'Tambah Data Unit')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h4 class="card-title mb-0">Tambah Unit Baru</h4>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('units.store') }}">
          @csrf

          <div class="mb-3">
            <label for="name" class="form-label">Nama Unit <span class="text-danger">*</span></label>
            <input
              type="text"
              name="name"
              id="name"
              value="{{ old('name') }}"
              class="form-control"
              placeholder="Contoh: PC"
              required
            />
            @error('name')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('units.index') }}" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Simpan Unit
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
