@extends('layouts.admin')

@section('title', 'Tagihan')

@push('styles')
<style>
.billing-status { padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;display:inline-block }
.billing-status.lunas { background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success) }
.billing-status.belum { background:color-mix(in srgb,#ef4444 12%,var(--card));color:#ef4444 }
</style>
@endpush

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Keuangan</span>
    <h1>Tagihan</h1>
    <p>Kelola tagihan SPP, seragam, dan biaya lainnya untuk siswa.</p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19" />
      <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
    Tambah Tagihan
  </button>
</div>

<section class="portal-kpis" style="margin-bottom:20px">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Tagihan</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></span></div>
    <strong class="portal-kpi-value">Rp{{ number_format($totalAmount, 0, ',', '.') }}</strong>
    <span class="portal-kpi-note">{{ $billings->total() }} item</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Sudah Dibayar</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span></div>
    <strong class="portal-kpi-value" style="color:var(--success)">Rp{{ number_format($paidAmount, 0, ',', '.') }}</strong>
    <span class="portal-kpi-note good">Lunas</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Belum Dibayar</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span></div>
    <strong class="portal-kpi-value" style="color:var(--danger)">Rp{{ number_format($unpaidAmount, 0, ',', '.') }}</strong>
    <span class="portal-kpi-note" style="color:var(--danger)">Menunggu pembayaran</span>
  </article>
</section>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="field" style="flex:1;min-width:200px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama tagihan atau siswa...">
    </div>
    <div class="field" style="min-width:160px">
      <select name="class">
        <option value="">Semua Kelas</option>
        @foreach ($classes as $c)
        <option value="{{ $c }}" {{ request('class') === $c ? 'selected' : '' }}>{{ $c }}</option>
        @endforeach
      </select>
    </div>
    <div class="field" style="min-width:140px">
      <select name="status">
        <option value="">Semua Status</option>
        <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Dibayar</option>
        <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
      </select>
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Filter</button>
    @if (request('search') || request('class') || request('status'))
    <a href="{{ route('admin.billing.index') }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Siswa</th>
          <th>Kelas</th>
          <th>Tagihan</th>
          <th>Jumlah</th>
          <th>Jatuh Tempo</th>
          <th>Status</th>
          <th>Tgl Bayar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($billings as $b)
        <tr id="row-{{ $b->id }}"
          data-id="{{ $b->id }}"
          data-name="{{ $b->name }}"
          data-amount="{{ $b->amount }}"
          data-due_date="{{ $b->due_date->format('Y-m-d') }}"
          data-status="{{ $b->status }}"
          data-paid_date="{{ $b->paid_date?->format('Y-m-d') ?? '' }}">
          <td>{{ $loop->iteration + ($billings->currentPage() - 1) * $billings->perPage() }}</td>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <span style="width:30px;height:30px;border-radius:8px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:800;font-size:.75rem;flex-shrink:0">{{ strtoupper(substr($b->student->full_name ?? '?', 0, 1)) }}</span>
              <strong style="font-size:.88rem">{{ $b->student->full_name ?? '-' }}</strong>
            </div>
          </td>
          <td style="font-size:.85rem;color:var(--muted)">{{ $b->student->class_name ?? '-' }}</td>
          <td><strong>{{ $b->name }}</strong></td>
          <td>Rp{{ number_format($b->amount, 0, ',', '.') }}</td>
          <td style="font-size:.85rem">{{ $b->due_date->format('d M Y') }}</td>
          <td>
            <span class="billing-status {{ $b->status }}">{{ $b->status === 'lunas' ? 'Lunas' : 'Belum' }}</span>
          </td>
          <td style="font-size:.85rem">{{ $b->paid_date?->format('d M Y') ?? '-' }}</td>
          <td>
            <div style="display:flex;gap:4px">
              <button type="button" class="btn btn-outline" onclick="openEditModal({{ $b->id }})" style="min-height:30px;min-width:30px;padding:0;display:inline-flex;align-items:center;justify-content:center" title="Edit">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
              </button>
              <button type="button" class="btn btn-outline" onclick="confirmDelete({{ $b->id }}, '{{ addslashes($b->name) }}')" style="min-height:30px;min-width:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" title="Hapus">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" style="text-align:center;padding:30px;color:var(--muted)">Belum ada tagihan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $billings->links('vendor.pagination.admin') }}</div>
</section>

{{-- Create Modal --}}
<div class="admin-modal-overlay" id="createModal">
  <div class="admin-modal-box" style="max-width:500px">
    <div class="admin-modal-header">
      <h2>Tambah Tagihan</h2>
      <button class="admin-modal-close" onclick="closeCreateModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
      </button>
    </div>
    <form id="createForm" onsubmit="submitCreate(event)">
      <div class="admin-modal-body">
        @csrf
        <div class="field">
          <label>Nama Tagihan <span style="color:#ef4444">*</span></label>
          <input type="text" name="name" required placeholder="Contoh: SPP Bulan Agustus, Seragam, Baju Olahraga">
        </div>
        <div class="field" style="margin-top:14px">
          <label>Jumlah <span style="color:#ef4444">*</span></label>
          <input type="number" name="amount" required min="0" placeholder="500000">
        </div>
        <div class="field" style="margin-top:14px">
          <label>Jatuh Tempo <span style="color:#ef4444">*</span></label>
          <input type="date" name="due_date" required value="{{ date('Y-m-d', strtotime('+1 month')) }}">
        </div>
        <div class="field" style="margin-top:14px">
          <label>Sasaran <span style="color:#ef4444">*</span></label>
          <select name="target_type" id="targetType" onchange="toggleTargetClass()">
            <option value="all">Semua siswa</option>
            <option value="class">Per Kelas</option>
          </select>
        </div>
        <div class="field" style="margin-top:14px;display:none" id="targetClassField">
          <label>Kelas <span style="color:#ef4444">*</span></label>
          <select name="target_class">
            <option value="">Pilih kelas</option>
            @foreach ($classes as $c)
            <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
          </select>
        </div>
        <div class="field" style="margin-top:14px">
          <label>Status <span style="color:#ef4444">*</span></label>
          <select name="status" onchange="togglePaidDate(this)">
            <option value="belum">Belum Dibayar</option>
            <option value="lunas">Lunas</option>
          </select>
        </div>
        <div class="field" style="margin-top:14px;display:none" id="paidDateField">
          <label>Tanggal Bayar</label>
          <input type="date" name="paid_date" value="{{ date('Y-m-d') }}">
        </div>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeCreateModal()">Batal</button>
        <button type="submit" class="btn btn-primary" id="createSubmitBtn">Buat Tagihan</button>
      </div>
    </form>
  </div>
</div>

{{-- Edit Modal --}}
<div class="admin-modal-overlay" id="editModal">
  <div class="admin-modal-box" style="max-width:500px">
    <div class="admin-modal-header">
      <h2>Edit Tagihan</h2>
      <button class="admin-modal-close" onclick="closeEditModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
      </button>
    </div>
    <form id="editForm" onsubmit="submitEdit(event)">
      <div class="admin-modal-body">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" id="editId" value="">
        <div class="field">
          <label>Nama Tagihan <span style="color:#ef4444">*</span></label>
          <input type="text" name="name" id="editName" required>
        </div>
        <div class="field" style="margin-top:14px">
          <label>Jumlah <span style="color:#ef4444">*</span></label>
          <input type="number" name="amount" id="editAmount" required min="0">
        </div>
        <div class="field" style="margin-top:14px">
          <label>Jatuh Tempo <span style="color:#ef4444">*</span></label>
          <input type="date" name="due_date" id="editDueDate" required>
        </div>
        <div class="field" style="margin-top:14px">
          <label>Status <span style="color:#ef4444">*</span></label>
          <select name="status" id="editStatus" onchange="toggleEditPaidDate(this)">
            <option value="belum">Belum Dibayar</option>
            <option value="lunas">Lunas</option>
          </select>
        </div>
        <div class="field" style="margin-top:14px;display:none" id="editPaidDateField">
          <label>Tanggal Bayar</label>
          <input type="date" name="paid_date" id="editPaidDate">
        </div>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeEditModal()">Batal</button>
        <button type="submit" class="btn btn-primary" id="editSubmitBtn">Simpan</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';

function toggleTargetClass() {
  const val = document.getElementById('targetType').value;
  document.getElementById('targetClassField').style.display = val === 'class' ? 'block' : 'none';
}

function togglePaidDate(sel) {
  document.getElementById('paidDateField').style.display = sel.value === 'lunas' ? 'block' : 'none';
}

function toggleEditPaidDate(sel) {
  document.getElementById('editPaidDateField').style.display = sel.value === 'lunas' ? 'block' : 'none';
}

function openCreateModal() {
  document.getElementById('createForm').reset();
  document.getElementById('paidDateField').style.display = 'none';
  document.getElementById('targetClassField').style.display = 'none';
  document.getElementById('createModal').classList.add('open');
}

function closeCreateModal() {
  document.getElementById('createModal').classList.remove('open');
}

function openEditModal(id) {
  const row = document.querySelector('#row-' + id);
  document.getElementById('editId').value = id;
  document.getElementById('editName').value = row.dataset.name;
  document.getElementById('editAmount').value = row.dataset.amount;
  document.getElementById('editDueDate').value = row.dataset.due_date;
  document.getElementById('editStatus').value = row.dataset.status;
  document.getElementById('editPaidDate').value = row.dataset.paid_date;
  document.getElementById('editPaidDateField').style.display = row.dataset.status === 'lunas' ? 'block' : 'none';
  document.getElementById('editModal').classList.add('open');
}

function closeEditModal() {
  document.getElementById('editModal').classList.remove('open');
}

function submitCreate(e) {
  e.preventDefault();
  const form = document.getElementById('createForm');
  const formData = new FormData(form);
  const btn = document.getElementById('createSubmitBtn');
  const original = btn.textContent;
  btn.disabled = true;
  btn.textContent = 'Menyimpan...';

  fetch('{{ route("admin.billing.store") }}', {
    method: 'POST',
    body: formData,
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
  })
  .then(r => r.json().then(j => ({ ok: r.ok, json: j })))
  .then(({ ok, json }) => {
    if (ok && json.success) {
      closeCreateModal();
      Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 3000 })
        .then(() => { location.reload(); });
    } else if (json.errors) {
      let msg = Object.values(json.errors).flat().join(', ');
      Swal.fire('Gagal', msg, 'error');
    } else {
      Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
    }
  })
  .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'))
  .finally(() => { btn.disabled = false; btn.textContent = original; });
}

function submitEdit(e) {
  e.preventDefault();
  const id = document.getElementById('editId').value;
  const form = document.getElementById('editForm');
  const formData = new FormData(form);
  const btn = document.getElementById('editSubmitBtn');
  const original = btn.textContent;
  btn.disabled = true;
  btn.textContent = 'Menyimpan...';

  fetch('/admin/billing/' + id, {
    method: 'POST',
    body: formData,
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
  })
  .then(r => r.json().then(j => ({ ok: r.ok, json: j })))
  .then(({ ok, json }) => {
    if (ok && json.success) {
      closeEditModal();
      Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 3000 })
        .then(() => { location.reload(); });
    } else if (json.errors) {
      let msg = Object.values(json.errors).flat().join(', ');
      Swal.fire('Gagal', msg, 'error');
    } else {
      Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
    }
  })
  .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'))
  .finally(() => { btn.disabled = false; btn.textContent = original; });
}

function confirmDelete(id, name) {
  Swal.fire({
    title: 'Hapus Tagihan?',
    text: 'Tagihan "' + name + '" akan dihapus.',
    icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
    confirmButtonText: 'Hapus', cancelButtonText: 'Batal'
  }).then(result => {
    if (result.isConfirmed) {
      fetch('/admin/billing/' + id, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          document.getElementById('row-' + id)?.remove();
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: d.message, showConfirmButton: false, timer: 2000 });
        } else {
          Swal.fire('Gagal', d.message, 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
    }
  });
}

document.getElementById('createModal')?.addEventListener('click', function(e) { if (e.target === this) closeCreateModal(); });
document.getElementById('editModal')?.addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });
</script>
@endpush
@endsection
