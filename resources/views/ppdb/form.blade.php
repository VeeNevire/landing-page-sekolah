@php
$steps = [
    1 => ['label' => 'Data Siswa', 'icon' => '👤'],
    2 => ['label' => 'Data Orang Tua', 'icon' => '👨‍👩‍👧'],
    3 => ['label' => 'Upload Berkas', 'icon' => '📁'],
];
@endphp

@extends('layouts.public')

@section('title', 'Form Pendaftaran PPDB | SMK MADYA DEPOK')

@push('styles')
<style>
.form-section { margin-bottom: 2.5rem; }
.form-section h3 { margin-bottom: 1.25rem; padding-bottom: .5rem; border-bottom: 2px solid var(--primary); }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-grid .full { grid-column: 1 / -1; }
.field label { display: block; font-weight: 600; margin-bottom: .35rem; font-size: .9rem; }
.field input, .field select, .field textarea {
  width: 100%; padding: .65rem .85rem; border: 1.5px solid var(--border);
  border-radius: 8px; background: var(--card); color: var(--text);
  font-size: .95rem; transition: border-color .2s;
}
.field input:focus, .field select:focus, .field textarea:focus {
  outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}

.tabs {
  display: flex;
  gap: 0;
  margin-bottom: 2rem;
  border-bottom: 2px solid var(--border);
}
.tab {
  flex: 1;
  padding: 1rem;
  background: none;
  border: none;
  cursor: pointer;
  font-size: .95rem;
  font-weight: 600;
  color: var(--muted);
  transition: all .2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-bottom: 3px solid transparent;
  margin-bottom: -2px;
}
.tab:hover {
  color: var(--text);
  background: var(--card);
}
.tab.active {
  color: var(--primary);
  border-bottom-color: var(--primary);
}
.tab .step-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--border);
  color: var(--muted);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: .85rem;
}
.tab.active .step-num {
  background: var(--primary);
  color: #fff;
}
.tab.completed .step-num {
  background: #10b981;
  color: #fff;
}
.tab.completed {
  color: #10b981;
}

.step-content {
  display: none;
}
.step-content.active {
  display: block;
}

.step-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
  justify-content: space-between;
}
</style>
@endpush

@section('content')
<section class="page-hero">
  <div class="container" style="display:flex;justify-content:space-between;align-items:center">
    <div>
      <div class="breadcrumb">Beranda / PPDB / Formulir Pendaftaran</div>
      <h1>Formulir Pendaftaran</h1>
      <p class="lead">Lengkapi data calon siswa dan orang tua/wali.</p>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:6px;font-size:.85rem;white-space:nowrap">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Keluar
      </button>
    </form>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:720px">
    <div class="tabs" id="tabs">
      <button class="tab {{ $currentStep === 1 ? 'active' : '' }} {{ $applicant->completion_step === 'student_data' || $applicant->completion_step === 'parent_data' || $applicant->completion_step === 'documents' || $applicant->completion_step === 'completed' ? 'completed' : '' }}" onclick="goToStep(1)">
        <span class="step-num">{{ $applicant->completion_step === 'not_started' ? '1' : '✓' }}</span>
        <span>Data Siswa</span>
      </button>
      <button class="tab {{ $currentStep === 2 ? 'active' : '' }} {{ $applicant->completion_step === 'parent_data' || $applicant->completion_step === 'documents' || $applicant->completion_step === 'completed' ? 'completed' : '' }}" onclick="goToStep(2)">
        <span class="step-num">{{ $applicant->completion_step !== 'not_started' && $applicant->completion_step !== 'student_data' ? '✓' : '2' }}</span>
        <span>Data Orang Tua</span>
      </button>
      <button class="tab {{ $currentStep === 3 ? 'active' : '' }}" onclick="window.location.href='{{ route('ppdb.upload') }}'">
        <span class="step-num">3</span>
        <span>Upload Berkas</span>
      </button>
    </div>

    <div id="step1" class="step-content {{ $currentStep === 1 ? 'active' : '' }}">
      <form id="formStep1" onsubmit="return false">
        @csrf
        <div class="form-section">
          <h3>Data Calon Siswa</h3>
          <div class="form-grid">
            <div class="field">
              <label for="full_name">Nama Lengkap <span style="color:var(--danger)">*</span></label>
              <input id="full_name" name="full_name" required value="{{ old('full_name', $applicant->full_name) }}" placeholder="Nama lengkap sesuai ijazah">
            </div>
            <div class="field">
              <label for="nickname">Nama Panggilan</label>
              <input id="nickname" name="nickname" value="{{ old('nickname', $applicant->nickname) }}" placeholder="Nama panggilan sehari-hari">
            </div>
            <div class="field">
              <label for="birth_place">Tempat Lahir</label>
              <input id="birth_place" name="birth_place" value="{{ old('birth_place', $applicant->birth_place) }}" placeholder="Contoh: Jakarta">
            </div>
            <div class="field">
              <label for="birth_date">Tanggal Lahir</label>
              <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', $applicant->birth_date?->format('Y-m-d')) }}">
            </div>
            <div class="field">
              <label for="gender">Jenis Kelamin</label>
              <select id="gender" name="gender">
                <option value="">Pilih</option>
                <option value="L" @selected(old('gender', $applicant->gender) === 'L')>Laki-laki</option>
                <option value="P" @selected(old('gender', $applicant->gender) === 'P')>Perempuan</option>
              </select>
            </div>
            <div class="field">
              <label for="religion">Agama</label>
              <select id="religion" name="religion">
                <option value="">Pilih</option>
                @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $r)
                  <option value="{{ $r }}" @selected(old('religion', $applicant->religion) === $r)>{{ $r }}</option>
                @endforeach
              </select>
            </div>
            <div class="field full">
              <label for="address">Alamat</label>
              <textarea id="address" name="address" rows="3" placeholder="Alamat lengkap">{{ old('address', $applicant->address) }}</textarea>
            </div>
            <div class="field">
              <label for="phone">No. HP</label>
              <input id="phone" name="phone" value="{{ old('phone', $applicant->phone) }}" placeholder="08xxxxxxxxxx">
            </div>
            <div class="field">
              <label for="asal_sekolah">Asal Sekolah</label>
              <input id="asal_sekolah" name="asal_sekolah" value="{{ old('asal_sekolah', $applicant->asal_sekolah) }}" placeholder="Nama SMP/MTs asal">
            </div>
            <div class="field">
              <label for="nisn">NISN</label>
              <input id="nisn" name="nisn" value="{{ old('nisn', $applicant->nisn) }}" placeholder="Nomor Induk Siswa Nasional">
            </div>
          </div>
        </div>
      </form>

      <div class="step-actions">
        <a href="{{ route('ppdb.start') }}" class="btn btn-outline">Kembali</a>
        <button class="btn btn-primary" onclick="saveStep1()">Simpan & Lanjut →</button>
      </div>
    </div>

    <div id="step2" class="step-content {{ $currentStep === 2 ? 'active' : '' }}">
      <form id="formStep2" onsubmit="return false">
        @csrf
        <div class="form-section">
          <h3>Pilihan Program</h3>
          <div class="form-grid">
            <div class="field">
              <label for="jenjang">Jenjang</label>
              <select id="jenjang" name="jenjang">
                <option value="">Pilih jenjang</option>
                <option value="SMA" @selected(old('jenjang', $applicant->jenjang) === 'SMA')>SMA</option>
                <option value="SMK" @selected(old('jenjang', $applicant->jenjang) === 'SMK')>SMK</option>
              </select>
            </div>
            <div class="field">
              <label for="program_diminati">Program Diminati</label>
              <select id="program_diminati" name="program_diminati">
                <option value="">Pilih program</option>
                <option value="SMA Sains & Teknologi" @selected(old('program_diminati', $applicant->program_diminati) === 'SMA Sains & Teknologi')>SMA Sains & Teknologi</option>
                <option value="SMA Sosial & Humaniora" @selected(old('program_diminati', $applicant->program_diminati) === 'SMA Sosial & Humaniora')>SMA Sosial & Humaniora</option>
                <option value="RPL" @selected(old('program_diminati', $applicant->program_diminati) === 'RPL')>RPL (Rekayasa Perangkat Lunak)</option>
                <option value="DKV" @selected(old('program_diminati', $applicant->program_diminati) === 'DKV')>DKV (Desain Komunikasi Visual)</option>
                <option value="Akuntansi" @selected(old('program_diminati', $applicant->program_diminati) === 'Akuntansi')>Akuntansi</option>
                <option value="Bisnis Digital" @selected(old('program_diminati', $applicant->program_diminati) === 'Bisnis Digital')>Bisnis Digital</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-section">
          <h3>Data Orang Tua / Wali</h3>

          <h4 style="margin-bottom:.75rem;color:var(--muted);font-size:.95rem">Ayah</h4>
          <div class="form-grid" style="margin-bottom:1.5rem">
            <div class="field">
              <label for="ayah_name">Nama Ayah</label>
              <input id="ayah_name" name="ayah_name" value="{{ old('ayah_name', $applicant->ayah_name) }}" placeholder="Nama lengkap ayah">
            </div>
            <div class="field">
              <label for="ayah_occupation">Pekerjaan Ayah</label>
              <input id="ayah_occupation" name="ayah_occupation" value="{{ old('ayah_occupation', $applicant->ayah_occupation) }}" placeholder="Pekerjaan">
            </div>
            <div class="field">
              <label for="ayah_phone">No. HP Ayah</label>
              <input id="ayah_phone" name="ayah_phone" value="{{ old('ayah_phone', $applicant->ayah_phone) }}" placeholder="08xxxxxxxxxx">
            </div>
          </div>

          <h4 style="margin-bottom:.75rem;color:var(--muted);font-size:.95rem">Ibu</h4>
          <div class="form-grid" style="margin-bottom:1.5rem">
            <div class="field">
              <label for="ibu_name">Nama Ibu</label>
              <input id="ibu_name" name="ibu_name" value="{{ old('ibu_name', $applicant->ibu_name) }}" placeholder="Nama lengkap ibu">
            </div>
            <div class="field">
              <label for="ibu_occupation">Pekerjaan Ibu</label>
              <input id="ibu_occupation" name="ibu_occupation" value="{{ old('ibu_occupation', $applicant->ibu_occupation) }}" placeholder="Pekerjaan">
            </div>
            <div class="field">
              <label for="ibu_phone">No. HP Ibu</label>
              <input id="ibu_phone" name="ibu_phone" value="{{ old('ibu_phone', $applicant->ibu_phone) }}" placeholder="08xxxxxxxxxx">
            </div>
          </div>

          <h4 style="margin-bottom:.75rem;color:var(--muted);font-size:.95rem">Email Orang Tua</h4>
          <div class="form-grid" style="margin-bottom:1.5rem">
            <div class="field full">
              <label for="ayah_email">Email Orang Tua <span style="color:var(--danger)">*</span></label>
              <input id="ayah_email" name="ayah_email" type="email" value="{{ old('ayah_email', $applicant->ayah_email) }}" placeholder="email@contoh.com" required>
              <small style="color:var(--muted)">Email ini akan digunakan untuk akun portal orang tua</small>
            </div>
          </div>

          <details style="cursor:pointer;color:var(--primary)">
            <summary style="font-weight:600">+ Tambah data wali (opsional)</summary>
            <div class="form-grid" style="margin-top:1rem">
              <div class="field">
                <label for="wali_name">Nama Wali</label>
                <input id="wali_name" name="wali_name" value="{{ old('wali_name', $applicant->wali_name) }}" placeholder="Nama lengkap wali">
              </div>
              <div class="field">
                <label for="wali_occupation">Pekerjaan Wali</label>
                <input id="wali_occupation" name="wali_occupation" value="{{ old('wali_occupation', $applicant->wali_occupation) }}" placeholder="Pekerjaan">
              </div>
              <div class="field">
                <label for="wali_phone">No. HP Wali</label>
                <input id="wali_phone" name="wali_phone" value="{{ old('wali_phone', $applicant->wali_phone) }}" placeholder="08xxxxxxxxxx">
              </div>
              <div class="field">
                <label for="wali_email">Email Wali</label>
                <input id="wali_email" name="wali_email" type="email" value="{{ old('wali_email', $applicant->wali_email) }}" placeholder="email@contoh.com">
              </div>
            </div>
          </details>
        </div>
      </form>

      <div class="step-actions">
        <button class="btn btn-outline" onclick="goToStep(1)">← Kembali</button>
        <button class="btn btn-primary" onclick="saveStep2()">Simpan & Lanjut →</button>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
let currentStep = {{ $currentStep }};

function goToStep(step) {
  if (step > currentStep) return;
  document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
  document.getElementById('step' + step).classList.add('active');
  currentStep = step;
}

function saveStep1() {
  const form = document.getElementById('formStep1');
  const data = new FormData(form);

  fetch('{{ route("ppdb.form.step1") }}', {
    method: 'POST',
    body: data,
    headers: { 
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
    }
  })
  .then(r => {
    console.log('Response status:', r.status);
    return r.json();
  })
  .then(d => {
    console.log('Response data:', d);
    if (d.success) {
      document.querySelectorAll('.tab')[0].classList.add('completed');
      document.querySelectorAll('.tab')[0].querySelector('.step-num').textContent = '✓';
      document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
      document.getElementById('step2').classList.add('active');
      document.querySelectorAll('.tab').forEach((tab, i) => tab.classList.toggle('active', i === 1));
      currentStep = 2;
    } else {
      alert('Gagal menyimpan: ' + (d.message || 'Unknown error'));
    }
  })
  .catch(e => {
    console.error('Fetch error:', e);
    alert('Gagal menyimpan data. Cek console untuk detail. Error: ' + e.message);
  });
}

function saveStep2() {
  const form = document.getElementById('formStep2');
  const data = new FormData(form);

  fetch('{{ route("ppdb.form.step2") }}', {
    method: 'POST',
    body: data,
    headers: { 
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
    }
  })
  .then(r => {
    console.log('Response status:', r.status);
    return r.json();
  })
  .then(d => {
    console.log('Response data:', d);
    if (d.success) {
      window.location.href = '{{ route("ppdb.upload") }}';
    } else {
      alert('Gagal menyimpan: ' + (d.message || 'Unknown error'));
    }
  })
  .catch(e => {
    console.error('Fetch error:', e);
    alert('Gagal menyimpan data. Cek console untuk detail. Error: ' + e.message);
  });
}
</script>
@endpush
