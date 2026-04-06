@extends('layouts.app')

@section('title', 'Tambah Pengiriman - RekaTrack')
@php($pageName = 'Tambah Data Pengiriman')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title mb-0">Form Pengiriman Baru</h4>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('shippings.store') }}" id="shippingForm">
          @csrf

          <!-- Nomor SJN dan Tanggal Dokumen -->
          <div class="row mb-4">
            <div class="col-md-6">
              <label class="form-label">Nomor SJN <span class="text-danger">*</span></label>
              <input
                type="text"
                name="numberSJN"
                value="{{ old('numberSJN') }}"
                class="form-control @error('numberSJN') is-invalid @enderror"
                placeholder="Masukkan nomor surat jalan"
                required
              />
              @error('numberSJN')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Tanggal Dokumen</label>
              <input
                type="date"
                name="documentDate"
                value="{{ old('documentDate', date('Y-m-d')) }}"
                class="form-control form-control-lg @error('documentDate') is-invalid @enderror"
              />
              <small class="text-muted">Kosongkan untuk menggunakan tanggal hari ini</small>
              @error('documentDate')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Data Pengiriman Lain -->
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Kepada <span class="text-danger">*</span></label>
              <input
                type="text"
                name="sendTo"
                value="{{ old('sendTo') }}"
                class="form-control @error('sendTo') is-invalid @enderror"
                placeholder="Nama penerima"
                required
              />
              @error('sendTo')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Nomor Ref <span class="text-danger">*</span></label>
              <input
                type="text"
                name="numberRef"
                value="{{ old('numberRef') }}"
                class="form-control @error('numberRef') is-invalid @enderror"
                placeholder="Nomor referensi"
                required
              />
              @error('numberRef')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Proyek <span class="text-danger">*</span></label>
              <input
                type="text"
                name="projectName"
                value="{{ old('projectName') }}"
                class="form-control @error('projectName') is-invalid @enderror"
                placeholder="Nama proyek"
                required
              />
              @error('projectName')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Tanggal Ref</label>
              <input
                type="date"
                name="referenceDate"
                value="{{ old('referenceDate') }}"
                class="form-control @error('referenceDate') is-invalid @enderror"
              />
              @error('referenceDate')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Nomor PO <span class="text-danger">*</span></label>
              <input
                type="text"
                name="poNumber"
                value="{{ old('poNumber') }}"
                class="form-control @error('poNumber') is-invalid @enderror"
                placeholder="Nomor purchase order"
                required
              />
              @error('poNumber')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Jenis Pengiriman <span class="text-danger">*</span></label>
              <select
                name="deliveryType"
                class="form-select @error('deliveryType') is-invalid @enderror"
                required
              >
                <option value="">-- Pilih Jenis --</option>
                <option value="Dalam Kota" {{ old('deliveryType', 'Dalam Kota') == 'Dalam Kota' ? 'selected' : '' }}>
                  Dalam Kota
                </option>
                <option value="Luar Kota" {{ old('deliveryType') == 'Luar Kota' ? 'selected' : '' }}>
                  Luar Kota
                </option>
              </select>
              @error('deliveryType')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Attachment (TEXT ONLY) -->
          <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Lampiran</h5>
              <button type="button" class="btn btn-primary btn-sm" id="addAttachmentBtn">
                <i class="fas fa-plus me-1"></i> Tambah Lampiran
              </button>
            </div>
            <div class="card-body">
              <div id="attachmentContainer">
                @if(is_array(old('attachments')) && count(old('attachments')))
                    @foreach(old('attachments') as $i => $attName)
                    <div class="attachment-row mb-2">
                        <div class="input-group">
                        <input type="text"
                                name="attachments[]"
                                value="{{ $attName }}"
                                class="form-control @error("attachments.$i") is-invalid @enderror"
                                placeholder="Contoh: IS Document" />
                        <button type="button" class="btn btn-outline-danger remove-attachment">
                            <i class="fas fa-trash"></i>
                        </button>
                        </div>
                        @error("attachments.$i")
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    @endforeach
                @else
                    <div class="attachment-row mb-2">
                    <div class="input-group">
                        <input type="text" name="attachments[]" class="form-control"
                            placeholder="Contoh: IS Document" />
                        <button type="button" class="btn btn-outline-danger remove-attachment">
                        <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    </div>
                @endif
              </div>

              <small class="text-muted">
                Isi nama lampiran saja (tanpa upload file). Contoh: IS Document, SPJN Document.
              </small>
            </div>
          </div>

          <!-- Bagian Barang -->
          <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Data Barang</h5>
              <div class="d-flex align-items-center gap-2">
                <span class="text-muted">Total: <span id="totalBarang">1</span> item</span>
                <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                  <i class="fas fa-plus me-1"></i> Tambah Barang
                </button>
              </div>
            </div>
            <div class="card-body">
              <div id="itemsContainer">
                @foreach ($items as $index => $item)
                  <div class="item-row mb-4 p-3 bg-light rounded">
                    <div class="row g-3">
                      <div class="col-md-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input
                          type="text"
                          name="itemName[]"
                          value="{{ old("itemName.$index", $item['itemName']) }}"
                          class="form-control @error("itemName.$index") is-invalid @enderror"
                          placeholder="Contoh: Baut M10"
                          required
                        />
                        @error("itemName.$index")
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-2">
                        <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                        <input
                          type="text"
                          name="itemCode[]"
                          value="{{ old("itemCode.$index", $item['itemCode']) }}"
                          class="form-control @error("itemCode.$index") is-invalid @enderror"
                          placeholder="SKU/Part No"
                          required
                        />
                        @error("itemCode.$index")
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-2">
                        <label class="form-label">Satuan <span class="text-danger">*</span></label>
                        <select name="unitType[]" class="form-select @error("unitType.$index") is-invalid @enderror" required>
                          <option value="">-- pilih --</option>
                          @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" {{ old("unitType.$index", $item['unitType']) == $unit->id ? 'selected' : '' }}>
                              {{ $unit->name }}
                            </option>
                          @endforeach
                        </select>
                        @error("unitType.$index")
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-1">
                        <label class="form-label">Qty Kirim</label>
                        <input
                          type="number"
                          name="quantitySend[]"
                          value="{{ old("quantitySend.$index", $item['quantitySend']) }}"
                          class="form-control @error("quantitySend.$index") is-invalid @enderror"
                          placeholder="0"
                        />
                        @error("quantitySend.$index")
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-1">
                        <label class="form-label">Qty PO</label>
                        <input
                          type="text"
                          name="qtyPreOrder[]"
                          value="{{ old("qtyPreOrder.$index", $item['qtyPreOrder']) }}"
                          class="form-control @error("qtyPreOrder.$index") is-invalid @enderror"
                          placeholder="-"
                        />
                        @error("qtyPreOrder.$index")
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-2">
                        <label class="form-label">Total Kirim <span class="text-danger">*</span></label>
                        <input
                          type="number"
                          name="totalSend[]"
                          value="{{ old("totalSend.$index", $item['totalSend']) }}"
                          class="form-control @error("totalSend.$index") is-invalid @enderror"
                          placeholder="0"
                          required
                        />
                        @error("totalSend.$index")
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-md-3">
                        <label class="form-label">Keterangan</label>
                        <input
                          type="text"
                          name="information[]"
                          value="{{ old("information.$index", $item['information']) }}"
                          class="form-control @error("information.$index") is-invalid @enderror"
                          placeholder="Opsional"
                        />
                        @error("information.$index")
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>

                      {{-- SUB ITEMS --}}
                      <div class="col-12 mt-3">
                        <div class="border rounded bg-white p-3">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">Sub Item</div>
                            <button type="button" class="btn btn-sm btn-outline-primary add-subitem">
                              <i class="fas fa-plus me-1"></i> Tambah Sub Item
                            </button>
                          </div>

                          <div class="subitems-container">
                            @php($oldSubs = old("subItems.$index", []))
                            @if(is_array($oldSubs) && count($oldSubs))
                              @foreach($oldSubs as $sIndex => $sub)
                                <div class="subitem-row row g-2 align-items-end mb-2">
                                  <div class="col-md-3">
                                    <label class="form-label mb-1">Nama</label>
                                    <input type="text"
                                           class="form-control"
                                           name="subItems[{{ $index }}][{{ $sIndex }}][item_name]"
                                           value="{{ $sub['item_name'] ?? '' }}"
                                           placeholder="Nama sub item">
                                  </div>
                                  <div class="col-md-2">
                                    <label class="form-label mb-1">Kode</label>
                                    <input type="text"
                                           class="form-control"
                                           name="subItems[{{ $index }}][{{ $sIndex }}][item_code]"
                                           value="{{ $sub['item_code'] ?? '' }}"
                                           placeholder="Kode sub item">
                                  </div>
                                  <div class="col-md-2">
                                    <label class="form-label mb-1">Satuan</label>
                                    <select class="form-select"
                                            name="subItems[{{ $index }}][{{ $sIndex }}][unit_id]">
                                      <option value="">-- pilih --</option>
                                      @foreach($units as $unit)
                                        <option value="{{ $unit->id }}"
                                          {{ ($sub['unit_id'] ?? '') == $unit->id ? 'selected' : '' }}>
                                          {{ $unit->name }}
                                        </option>
                                      @endforeach
                                    </select>
                                  </div>
                                  <div class="col-md-1">
                                    <label class="form-label mb-1">Qty Kirim</label>
                                    <input type="number"
                                           class="form-control"
                                           name="subItems[{{ $index }}][{{ $sIndex }}][qty_send]"
                                           value="{{ $sub['qty_send'] ?? '' }}"
                                           placeholder="0">
                                  </div>
                                  <div class="col-md-1">
                                    <label class="form-label mb-1">Qty PO</label>
                                    <input type="text"
                                           class="form-control"
                                           name="subItems[{{ $index }}][{{ $sIndex }}][qty_po]"
                                           value="{{ $sub['qty_po'] ?? '' }}"
                                           placeholder="-">
                                  </div>
                                  <div class="col-md-2">
                                    <label class="form-label mb-1">Total Kirim</label>
                                    <input type="number"
                                           class="form-control"
                                           name="subItems[{{ $index }}][{{ $sIndex }}][total_send]"
                                           value="{{ $sub['total_send'] ?? '' }}"
                                           placeholder="0">
                                  </div>
                                  <div class="col-md-1 d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-subitem">
                                      <i class="fas fa-trash"></i>
                                    </button>
                                  </div>

                                  <div class="col-12">
                                    <label class="form-label mb-1">Keterangan</label>
                                    <input type="text"
                                           class="form-control"
                                           name="subItems[{{ $index }}][{{ $sIndex }}][information]"
                                           value="{{ $sub['information'] ?? '' }}"
                                           placeholder="Opsional">
                                  </div>
                                  {{-- <div class="col-12">
                                    <label class="form-label mb-1">Deskripsi</label>
                                    <input type="text"
                                           class="form-control"
                                           name="subItems[{{ $index }}][{{ $sIndex }}][description]"
                                           value="{{ $sub['description'] ?? '' }}"
                                           placeholder="-">
                                  </div> --}}
                                </div>
                              @endforeach
                            @endif
                          </div>

                          <small class="text-muted">
                            Sub item opsional. Kalau diisi, lengkapi minimal Nama, Kode, Satuan, Total kirim.
                          </small>
                        </div>
                      </div>

                      <div class="col-md-12 d-flex justify-content-end mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item"
                          {{ count($items) <= 1 ? 'disabled' : '' }}>
                          <i class="fas fa-trash me-1"></i> Hapus
                        </button>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- Aksi Bawah -->
          <div class="d-flex justify-content-between mt-4 pt-3 border-top">
            <a href="{{ route('shippings.index') }}" class="btn btn-light">
              <i class="fas fa-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-success btn-round">
              <i class="fas fa-save me-1"></i> Simpan Pengiriman
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // ==========================
  // ITEMS + SUB ITEMS
  // ==========================
  const container = document.getElementById('itemsContainer');
  const addItemBtn = document.getElementById('addItemBtn');
  let itemCount = container.querySelectorAll('.item-row').length;
  const units = @json($units);

  function getUnitOptions(selected = '') {
    let html = '<option value="">-- pilih --</option>';
    units.forEach(unit => {
      const sel = String(selected) === String(unit.id) ? 'selected' : '';
      html += `<option value="${unit.id}" ${sel}>${unit.name}</option>`;
    });
    return html;
  }

  function createSubItemRow(itemIndex, subIndex) {
    return `
      <div class="subitem-row row g-2 align-items-end mb-2">
        <div class="col-md-3">
          <label class="form-label mb-1">Nama</label>
          <input type="text" class="form-control"
            name="subItems[${itemIndex}][${subIndex}][item_name]" placeholder="Nama sub item">
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">Kode</label>
          <input type="text" class="form-control"
            name="subItems[${itemIndex}][${subIndex}][item_code]" placeholder="Kode sub item">
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">Satuan</label>
          <select class="form-select"
            name="subItems[${itemIndex}][${subIndex}][unit_id]">
            ${getUnitOptions()}
          </select>
        </div>
        <div class="col-md-1">
          <label class="form-label mb-1">Qty Kirim</label>
          <input type="number" class="form-control"
            name="subItems[${itemIndex}][${subIndex}][qty_send]" placeholder="0">
        </div>
        <div class="col-md-1">
          <label class="form-label mb-1">Qty PO</label>
          <input type="text" class="form-control"
            name="subItems[${itemIndex}][${subIndex}][qty_po]" placeholder="-">
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1">Total Kirim</label>
          <input type="number" class="form-control"
            name="subItems[${itemIndex}][${subIndex}][total_send]" placeholder="0">
        </div>
        <div class="col-md-1 d-flex justify-content-end">
          <button type="button" class="btn btn-outline-danger btn-sm remove-subitem">
            <i class="fas fa-trash"></i>
          </button>
        </div>

        <div class="col-12">
          <label class="form-label mb-1">Keterangan</label>
          <input type="text" class="form-control"
            name="subItems[${itemIndex}][${subIndex}][information]" placeholder="Opsional">
        </div>
      </div>
    `;
  }

  function createNewItemRow() {
    return `
      <div class="item-row mb-4 p-3 bg-light rounded">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="itemName[]" class="form-control" placeholder="Contoh: Baut M10" required />
          </div>
          <div class="col-md-2">
            <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
            <input type="text" name="itemCode[]" class="form-control" placeholder="SKU/Part No" required />
          </div>
          <div class="col-md-2">
            <label class="form-label">Satuan <span class="text-danger">*</span></label>
            <select name="unitType[]" class="form-select" required>
              ${getUnitOptions()}
            </select>
          </div>
          <div class="col-md-1">
            <label class="form-label">Qty Kirim</label>
            <input type="number" name="quantitySend[]" class="form-control" placeholder="0" />
          </div>
          <div class="col-md-1">
            <label class="form-label">Qty PO</label>
            <input type="text" name="qtyPreOrder[]" class="form-control" placeholder="-" />
          </div>
          <div class="col-md-2">
            <label class="form-label">Total Kirim <span class="text-danger">*</span></label>
            <input type="number" name="totalSend[]" class="form-control" placeholder="0" required />
          </div>
          <div class="col-md-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="information[]" class="form-control" placeholder="Opsional" />
          </div>

          <div class="col-12 mt-3">
            <div class="border rounded bg-white p-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-semibold">Sub Item</div>
                <button type="button" class="btn btn-sm btn-outline-primary add-subitem">
                  <i class="fas fa-plus me-1"></i> Tambah Sub Item
                </button>
              </div>
              <div class="subitems-container"></div>
              <small class="text-muted">Sub item opsional.</small>
            </div>
          </div>

          <div class="col-md-12 d-flex justify-content-end mt-2">
            <button type="button" class="btn btn-sm btn-outline-danger remove-item">
              <i class="fas fa-trash me-1"></i> Hapus
            </button>
          </div>
        </div>
      </div>
    `;
  }

  function updateTotalBarang() {
    document.getElementById('totalBarang').textContent =
      container.querySelectorAll('.item-row').length;
  }

  function reindexItems() {
    const itemRows = container.querySelectorAll('.item-row');
    itemRows.forEach((row, itemIndex) => {
      const subRows = row.querySelectorAll('.subitem-row');

      subRows.forEach((subRow, subIndex) => {
        const inputs = subRow.querySelectorAll('input[name], select[name], textarea[name]');
        inputs.forEach(el => {
          const name = el.getAttribute('name');
          if (!name) return;
          const updated = name.replace(/^subItems\[\d+\]\[\d+\]/, `subItems[${itemIndex}][${subIndex}]`);
          el.setAttribute('name', updated);
        });
      });
    });
  }

  addItemBtn.addEventListener('click', function () {
    if (container.querySelectorAll('.item-row').length >= 50) return;
    container.insertAdjacentHTML('beforeend', createNewItemRow());
    itemCount++;
    updateTotalBarang();
    reindexItems();
  });

  container.addEventListener('click', function (e) {
    // Remove item
    if (e.target.closest('.remove-item')) {
      const row = e.target.closest('.item-row');
      if (container.querySelectorAll('.item-row').length > 1) {
        row.remove();
        updateTotalBarang();
        reindexItems();
      }
      return;
    }

    // Add sub item
    if (e.target.closest('.add-subitem')) {
      const itemRow = e.target.closest('.item-row');
      const subContainer = itemRow.querySelector('.subitems-container');
      const itemIndex = Array.from(container.querySelectorAll('.item-row')).indexOf(itemRow);
      const subIndex = subContainer.querySelectorAll('.subitem-row').length;

      subContainer.insertAdjacentHTML('beforeend', createSubItemRow(itemIndex, subIndex));
      reindexItems();
      return;
    }

    // Remove sub item
    if (e.target.closest('.remove-subitem')) {
      const subRow = e.target.closest('.subitem-row');
      subRow.remove();
      reindexItems();
      return;
    }
  });

  updateTotalBarang();
  reindexItems();

  // ==========================
  // ATTACHMENTS (text only)
  // ==========================
  const attachmentContainer = document.getElementById('attachmentContainer');
  const addAttachmentBtn = document.getElementById('addAttachmentBtn');

  function createAttachmentRow() {
    return `
      <div class="attachment-row mb-2">
        <div class="input-group">
          <input type="text" name="attachments[]" class="form-control" placeholder="Contoh: IS Document" />
          <button type="button" class="btn btn-outline-danger remove-attachment">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    `;
  }

  addAttachmentBtn.addEventListener('click', function () {
    attachmentContainer.insertAdjacentHTML('beforeend', createAttachmentRow());
  });

  attachmentContainer.addEventListener('click', function (e) {
    if (e.target.closest('.remove-attachment')) {
      const row = e.target.closest('.attachment-row');
      if (attachmentContainer.querySelectorAll('.attachment-row').length > 1) {
        row.remove();
      } else {
        const input = row.querySelector('input[name="attachments[]"]');
        if (input) input.value = '';
      }
    }
  });
});
</script>
@endpush
