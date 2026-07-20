@extends('layouts.admin')

@section('title', 'Jadwal')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Jadwal</span>
    <h1>Jadwal Mengajar</h1>
    <p>Atur jadwal mengajar per kelas, mapel, hari, dan jam.</p>
  </div>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap;align-items:end">
    <div class="field" style="flex:1;min-width:200px;margin:0">
      <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Semester</label>
      <select name="semester_id" onchange="this.form.submit()" style="min-height:42px">
        @foreach ($periods as $period)
          <option value="{{ $period->id }}" {{ $semesterId == $period->id ? 'selected' : '' }}>
            {{ $period->academic_year }} {{ ucfirst($period->semester) }} {{ $period->is_active ? '(Aktif)' : '' }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="field" style="flex:1;min-width:160px;margin:0">
      <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Kelas</label>
      <select name="class" onchange="this.form.submit()" style="min-height:42px">
        @foreach ($classNames as $cn)
          <option value="{{ $cn }}" {{ $selectedClass == $cn ? 'selected' : '' }}>{{ $cn }}</option>
        @endforeach
      </select>
    </div>
    <div style="align-self:end">
      <button type="button" class="btn btn-primary" onclick="openAddModal()" style="min-height:42px;display:inline-flex;align-items:center;gap:6px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Jadwal
      </button>
    </div>
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" style="min-width:800px">
      <thead>
        <tr>
          <th style="min-width:110px">Jam</th>
          @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
            <th style="text-transform:capitalize;text-align:center">{{ $day }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @php $timeSlots = [1=>'07:00–08:30', 2=>'08:30–10:00', 3=>'10:15–11:45', 4=>'12:30–14:00', 5=>'14:00–15:30']; @endphp
        @foreach ($timeSlots as $slot => $time)
        <tr>
          <td style="font-size:.82rem;font-weight:700;color:var(--muted);white-space:nowrap">{{ $time }}</td>
          @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
            @php $cell = $grid[$day][$slot] ?? null; @endphp
            <td style="text-align:center;min-width:120px;position:relative">
              @if ($cell)
                <div style="padding:10px 8px;border-radius:10px;background:color-mix(in srgb,var(--primary-2) 8%,var(--card));border:1px solid color-mix(in srgb,var(--primary-2) 15%,var(--line))">
                  <div style="font-weight:700;font-size:.88rem;color:var(--primary-2)">{{ $cell['code'] }}</div>
                  <div style="font-size:.78rem;font-weight:600;margin-top:2px">{{ $cell['subject'] }}</div>
                  <div style="font-size:.72rem;color:var(--muted);margin-top:2px">{{ $cell['teacher'] }}</div>
                  <button type="button" onclick="confirmDelete({{ $cell['jadwal_id'] }}, '{{ addslashes($cell['subject']) }}')" title="Hapus jadwal" style="position:absolute;top:4px;right:4px;width:20px;height:20px;border-radius:50%;border:none;background:color-mix(in srgb,var(--danger) 12%,transparent);color:var(--danger);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1">&times;</button>
                </div>
              @else
                <span style="color:var(--line);font-size:.75rem">—</span>
              @endif
            </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

function openAddModal() {
  Swal.fire({
    title: 'Tambah Jadwal',
    html: `
      <form id="jadwalForm" style="text-align:left">
        @csrf
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:#333">Mapel & Guru</label>
          <select id="modalTeachingAssignment" required style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:.9rem;outline:none">
            <option value="">-- Pilih Mapel --</option>
            @foreach ($subjects as $subject)
              <option value="{{ $subject['teaching_assignment_id'] }}">{{ $subject['label'] }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:#333">Hari</label>
          <select id="modalDay" required style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:.9rem;outline:none">
            <option value="">-- Pilih Hari --</option>
            @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
              <option value="{{ $day }}">{{ ucfirst($day) }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:#333">Jam ke-</label>
          <select id="modalSlot" required style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:.9rem;outline:none">
            <option value="">-- Pilih Slot --</option>
            @foreach ([1=>'07:00–08:30', 2=>'08:30–10:00', 3=>'10:15–11:45', 4=>'12:30–14:00', 5=>'14:00–15:30'] as $slot => $time)
              <option value="{{ $slot }}">Slot {{ $slot }} ({{ $time }})</option>
            @endforeach
          </select>
        </div>
      </form>
    `,
    confirmButtonText: 'Simpan',
    confirmButtonColor: '#0b3b75',
    showCancelButton: true,
    cancelButtonText: 'Batal',
    cancelButtonColor: '#6b7280',
    reverseButtons: true,
    preConfirm: () => {
      const taId = document.getElementById('modalTeachingAssignment').value;
      const day = document.getElementById('modalDay').value;
      const timeSlot = document.getElementById('modalSlot').value;
      if (!taId || !day || !timeSlot) {
        Swal.showValidationMessage('Semua field harus diisi');
        return false;
      }
      return { teaching_assignment_id: taId, day, time_slot: timeSlot };
    }
  }).then((result) => {
    if (!result.isConfirmed) return;
    const data = result.value;
    fetch('{{ route("admin.jadwal.store") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1500, showConfirmButton: false })
          .then(() => location.reload());
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Terjadi kesalahan' });
      }
    })
    .catch(() => {
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan server.' });
    });
  });
}

function confirmDelete(jadwalId, subjectName) {
  Swal.fire({
    title: 'Hapus Jadwal?',
    html: `Jadwal <strong>${subjectName}</strong> akan dihapus.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    confirmButtonColor: '#dc2626',
    cancelButtonText: 'Batal',
    cancelButtonColor: '#6b7280',
    reverseButtons: true
  }).then((result) => {
    if (!result.isConfirmed) return;
    fetch(`{{ url('admin/jadwal') }}/${jadwalId}`, {
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json'
      }
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1500, showConfirmButton: false })
          .then(() => location.reload());
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Terjadi kesalahan' });
      }
    })
    .catch(() => {
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan server.' });
    });
  });
}
</script>
@endpush
