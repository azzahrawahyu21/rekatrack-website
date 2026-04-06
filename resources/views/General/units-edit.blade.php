@extends('layouts.app')

@section('title', 'Edit Unit - RekaTrack')
@php($pageName = 'Edit Unit')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h4 class="card-title mb-0">Edit Data Unit</h4>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('units.update', $unit->id) }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label for="name" class="form-label">Nama Unit <span class="text-danger">*</span></label>
            <input
              type="text"
              name="name"
              id="name"
              value="{{ old('name', $unit->name) }}"
              class="form-control"
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
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save me-1"></i> Update Unit
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
