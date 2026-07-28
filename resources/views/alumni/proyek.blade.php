@extends('layouts.alumni')

@section('title', 'Portofolio Proyek')

@section('content')
<div class="portal-heading">
  <div>
    <h1>Portofolio Proyek</h1>
    <p>Kelola proyek, karya, atau aplikasi yang pernah Anda kembangkan untuk ditunjukkan kepada industri.</p>
  </div>
  <div class="portal-actions">
    <button class="btn btn-primary" onclick="openAddModal()">+ Tambah Proyek</button>
  </div>
</div>

<div class="portal-panel">
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
    @forelse($projects as $project)
      <div style="background: var(--bg); border: 1px solid var(--line); border-radius: 16px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: color-mix(in srgb, var(--primary) 12%, var(--card)); display: grid; place-items: center; color: var(--primary);">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12 2 2 7 12 12 22 7 12 2V2z"/>
                <polyline points="2 17 12 22 22 17"/>
                <polyline points="2 12 12 17 22 12"/>
              </svg>
            </div>
            <div style="display: flex; gap: 6px;">
              <button class="btn btn-outline" style="padding: 4px 8px; height: auto; font-size: 0.8rem;" onclick="openEditModal({{ json_encode($project) }})">Edit</button>
              <form action="{{ route('alumni.proyek.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Hapus proyek ini?')" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline" style="padding: 4px 8px; height: auto; font-size: 0.8rem; color: #ef4444; border-color: rgba(239,68,68,0.2);">Hapus</button>
              </form>
            </div>
          </div>
          <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 8px;">{{ $project->title }}</h3>
          <p style="font-size: 0.88rem; color: var(--muted); margin-bottom: 12px; line-height: 1.5;">{{ $project->description }}</p>
          
          @if($project->start_date)
            <p style="margin: 0 0 10px; font-size: 0.82rem; color: var(--muted);">
              Durasi: {{ $project->start_date->isoFormat('MMMM YYYY') }} - {{ $project->end_date ? $project->end_date->isoFormat('MMMM YYYY') : 'Sekarang' }}
            </p>
          @endif
        </div>

        <div style="border-top: 1px solid var(--line); padding-top: 15px; margin-top: 15px;">
          <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;">
            @if($project->github_url)
              <a href="{{ $project->github_url }}" target="_blank" class="btn btn-outline" style="padding: 4px 10px; height: auto; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <span>GitHub</span>
              </a>
            @endif
            @if($project->project_url)
              <a href="{{ $project->project_url }}" target="_blank" class="btn btn-outline" style="padding: 4px 10px; height: auto; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <span>Demo</span>
              </a>
            @endif
            @if($project->project_file)
              <a href="{{ asset('storage/' . $project->project_file) }}" target="_blank" class="btn btn-outline" style="padding: 4px 10px; height: auto; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                <span>Berkas/PDF</span>
              </a>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--muted);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 12px; opacity: 0.5;">
          <polygon points="12 2 2 7 12 12 22 7 12 2V2z"/>
          <polyline points="2 17 12 22 22 17"/>
          <polyline points="2 12 12 17 22 12"/>
        </svg>
        <p style="margin: 0; font-size: 0.95rem; font-style: italic;">Belum ada proyek portofolio yang ditambahkan.</p>
      </div>
    @endforelse
  </div>
</div>

<!-- Modal Form -->
<dialog id="projectModal" style="border: none; border-radius: 16px; padding: 24px; width: 90%; max-width: 550px; background: var(--card); color: var(--ink); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 id="modalTitle" style="margin: 0; font-size: 1.3rem;">Tambah Proyek</h2>
    <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--muted);">&times;</button>
  </div>

  <form id="projectForm" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" id="methodField" name="_method" value="POST">

    <div class="field" style="margin-bottom: 15px;">
      <label for="modal_title">Judul Proyek</label>
      <input type="text" id="modal_title" name="title" required placeholder="Contoh: Aplikasi E-Commerce Kasir">
    </div>

    <div class="field" style="margin-bottom: 15px;">
      <label for="modal_description">Deskripsi Proyek</label>
      <textarea id="modal_description" name="description" required placeholder="Jelaskan secara singkat fitur, tujuan, atau peran Anda dalam proyek..." style="width: 100%; min-height: 90px; padding: 10px; border-radius: 8px; border: 1px solid var(--line); background: var(--card); color: var(--ink); font-family: inherit; font-size: 0.9rem; resize: vertical;"></textarea>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
      <div class="field">
        <label for="modal_start_date">Tanggal Mulai (Opsional)</label>
        <input type="date" id="modal_start_date" name="start_date">
      </div>
      <div class="field">
        <label for="modal_end_date">Tanggal Selesai (Opsional)</label>
        <input type="date" id="modal_end_date" name="end_date">
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
      <div class="field">
        <label for="modal_github_url">Link Repositori GitHub (Opsional)</label>
        <input type="url" id="modal_github_url" name="github_url" placeholder="https://github.com/username/project">
      </div>
      <div class="field">
        <label for="modal_project_url">Link Demo / Web Live (Opsional)</label>
        <input type="url" id="modal_project_url" name="project_url" placeholder="https://project-demo.com">
      </div>
    </div>

    <div class="field" style="margin-bottom: 20px;">
      <label for="modal_file">Upload Laporan / Dokumentasi File (PDF, ZIP, RAR - Maks. 10MB)</label>
      <input type="file" id="modal_file" name="project_file" accept=".pdf,.zip,.rar">
      <small id="fileHelp" style="color: var(--muted); display: block; margin-top: 4px;">Kosongkan jika tidak ingin mengubah file saat edit</small>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 10px;">
      <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
  </form>
</dialog>

<script>
  const modal = document.getElementById('projectModal');
  const form = document.getElementById('projectForm');
  const modalTitle = document.getElementById('modalTitle');
  const methodField = document.getElementById('methodField');

  function openAddModal() {
    modalTitle.textContent = 'Tambah Proyek';
    form.action = "{{ route('alumni.proyek.store') }}";
    methodField.value = 'POST';
    form.reset();
    document.getElementById('fileHelp').style.display = 'none';
    modal.showModal();
  }

  function openEditModal(project) {
    modalTitle.textContent = 'Edit Proyek';
    form.action = `/alumni/proyek/${project.id}`;
    methodField.value = 'PUT';
    
    document.getElementById('modal_title').value = project.title;
    document.getElementById('modal_description').value = project.description || '';
    
    if (project.start_date) {
      const sDate = new Date(project.start_date);
      document.getElementById('modal_start_date').value = sDate.toISOString().split('T')[0];
    } else {
      document.getElementById('modal_start_date').value = '';
    }
    
    if (project.end_date) {
      const eDate = new Date(project.end_date);
      document.getElementById('modal_end_date').value = eDate.toISOString().split('T')[0];
    } else {
      document.getElementById('modal_end_date').value = '';
    }
    
    document.getElementById('modal_github_url').value = project.github_url || '';
    document.getElementById('modal_project_url').value = project.project_url || '';
    document.getElementById('fileHelp').style.display = 'block';
    
    modal.showModal();
  }

  function closeModal() {
    modal.close();
  }
</script>
@endsection