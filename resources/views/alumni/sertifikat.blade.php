@extends('layouts.alumni')

@section('title', 'Sertifikat Keterampilan')

@section('content')
<div class="portal-heading">
  <div>
    <h1>Sertifikat Keterampilan</h1>
    <p>Kelola dan tunjukkan sertifikasi keahlian, bahasa, atau pelatihan yang Anda miliki.</p>
  </div>
  <div class="portal-actions">
    <button class="btn btn-primary" onclick="openAddModal()">+ Tambah Sertifikat</button>
  </div>
</div>

<div class="portal-panel">
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
    @forelse($certificates as $cert)
      <div style="background: var(--bg); border: 1px solid var(--line); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: color-mix(in srgb, var(--primary) 12%, var(--card)); display: grid; place-items: center; color: var(--primary);">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                <path d="M7 11.5L10 14l7-6"/>
              </svg>
            </div>
            <div style="display: flex; gap: 6px;">
              <button class="btn btn-outline" style="padding: 4px 8px; height: auto; font-size: 0.8rem;" onclick="openEditModal({{ json_encode($cert) }})">Edit</button>
              <form action="{{ route('alumni.sertifikat.destroy', $cert->id) }}" method="POST" onsubmit="return confirm('Hapus sertifikat ini?')" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline" style="padding: 4px 8px; height: auto; font-size: 0.8rem; color: #ef4444; border-color: rgba(239,68,68,0.2);">Hapus</button>
              </form>
            </div>
          </div>
          <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0 0 6px;">{{ $cert->name }}</h3>
          <p style="margin: 0 0 4px; font-size: 0.9rem; font-weight: 600; color: var(--ink);">Penerbit: {{ $cert->issuer }}</p>
          <p style="margin: 0 0 12px; font-size: 0.82rem; color: var(--muted);">Diterbitkan: {{ $cert->issue_date->isoFormat('D MMMM YYYY') }}</p>
        </div>

        <div style="border-top: 1px solid var(--line); padding-top: 12px; margin-top: 12px; display: flex; gap: 10px;">
          @if($cert->certificate_file)
            <a href="{{ asset('storage/' . $cert->certificate_file) }}" target="_blank" class="btn btn-primary" style="flex: 1; text-align: center; justify-content: center; font-size: 0.85rem; height: 36px;">Unduh Sertifikat</a>
          @endif
          @if($cert->certificate_url)
            <a href="{{ $cert->certificate_url }}" target="_blank" class="btn btn-outline" style="flex: 1; text-align: center; justify-content: center; font-size: 0.85rem; height: 36px;">Buka Link</a>
          @endif
        </div>
      </div>
    @empty
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--muted);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 12px; opacity: 0.5;">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        <p style="margin: 0; font-size: 0.95rem; font-style: italic;">Belum ada sertifikat keterampilan yang ditambahkan.</p>
      </div>
    @endforelse
  </div>
</div>

<!-- Modal Form -->
<dialog id="certModal" style="border: none; border-radius: 16px; padding: 24px; width: 90%; max-width: 500px; background: var(--card); color: var(--ink); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 id="modalTitle" style="margin: 0; font-size: 1.3rem;">Tambah Sertifikat</h2>
    <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--muted);">&times;</button>
  </div>

  <form id="certForm" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="methodField" name="_method" value="POST">

    <div class="field" style="margin-bottom: 15px;">
      <label for="modal_name">Nama Sertifikat</label>
      <input type="text" id="modal_name" name="name" required placeholder="Contoh: Junior Web Developer">
    </div>

    <div class="field" style="margin-bottom: 15px;">
      <label for="modal_issuer">Lembaga Penerbit / Penyelenggara</label>
      <input type="text" id="modal_issuer" name="issuer" required placeholder="Contoh: BNSP, Dicoding, Google">
    </div>

    <div class="field" style="margin-bottom: 15px;">
      <label for="modal_issue_date">Tanggal Terbit</label>
      <input type="date" id="modal_issue_date" name="issue_date" required>
    </div>

    <div class="field" style="margin-bottom: 15px;">
      <label for="modal_file">Upload File Sertifikat (PDF, JPG, PNG - Maks. 5MB)</label>
      <input type="file" id="modal_file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
      <small id="fileHelp" style="color: var(--muted); display: block; margin-top: 4px;">Kosongkan jika tidak ingin mengubah file saat edit</small>
    </div>

    <div class="field" style="margin-bottom: 20px;">
      <label for="modal_url">Tautan / Link Kredensial (Opsional)</label>
      <input type="url" id="modal_url" name="certificate_url" placeholder="https://example.com/certificate-verify">
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 10px;">
      <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
  </form>
</dialog>

<script>
  const modal = document.getElementById('certModal');
  const form = document.getElementById('certForm');
  const modalTitle = document.getElementById('modalTitle');
  const methodField = document.getElementById('methodField');

  function openAddModal() {
    modalTitle.textContent = 'Tambah Sertifikat';
    form.action = "{{ route('alumni.sertifikat.store') }}";
    methodField.value = 'POST';
    form.reset();
    document.getElementById('fileHelp').style.display = 'none';
    modal.showModal();
  }

  function openEditModal(cert) {
    modalTitle.textContent = 'Edit Sertifikat';
    form.action = `/alumni/sertifikat/${cert.id}`;
    methodField.value = 'PUT';
    
    document.getElementById('modal_name').value = cert.name;
    document.getElementById('modal_issuer').value = cert.issuer;
    
    // Format date string to YYYY-MM-DD
    const date = new Date(cert.issue_date);
    const formattedDate = date.toISOString().split('T')[0];
    document.getElementById('modal_issue_date').value = formattedDate;
    
    document.getElementById('modal_url').value = cert.certificate_url || '';
    document.getElementById('fileHelp').style.display = 'block';
    
    modal.showModal();
  }

  function closeModal() {
    modal.close();
  }
</script>
@endsection