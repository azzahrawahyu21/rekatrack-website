@extends('layouts.app')

@section('title', 'Trash - Manajemen Pengiriman')
@php($pageName = 'Trash Pengiriman')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Trash Pengiriman</h4>
                <a href="{{ route('shippings.index') }}" class="btn btn-outline-secondary btn-round">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($trashedDocuments->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-recycle fs-1 d-block mb-3 text-muted"></i>
                        <p class="text-muted mb-0">Tidak ada data di trash</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor SJN</th>
                                    <th>Kepada</th>
                                    <th>Proyek</th>
                                    <th>Status</th>
                                    <th>Dihapus pada</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trashedDocuments as $index => $doc)
                                    <tr>
                                        <td>{{ $trashedDocuments->firstItem() + $index }}</td>
                                        <td>{{ $doc->no_travel_document ?? '-' }}</td>
                                        <td>{{ $doc->send_to ?? '-' }}</td>
                                        <td>{{ $doc->project ?? '-' }}</td>
                                        <td>
                                            @if ($doc->status == 'Belum terkirim')
                                                <span class="badge badge-warning">Belum terkirim</span>
                                            @elseif($doc->status == 'Sedang dikirim')
                                                <span class="badge badge-info">Sedang dikirim</span>
                                            @elseif($doc->status == 'Terkirim')
                                                <span class="badge badge-success">Terkirim</span>
                                            @else
                                                <span class="badge badge-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($doc->deleted_at)
                                                {{ $doc->deleted_at->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('shippings.restore', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Kembalikan data ini ke daftar utama?')">
                                                @csrf
                                                <button type="submit" class="btn btn-link btn-success" title="Kembalikan">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $trashedDocuments->links('pagination::simple-bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
