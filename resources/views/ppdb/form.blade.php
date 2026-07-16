@extends('layouts.public')

@section('title', 'Form Pendaftaran PPDB | SMK MADYA DEPOK')

@push('styles')
<style>
  .form-tab {
    flex: 1;
    padding: 0.75rem 1rem;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--muted);
    transition: all 0.2s;
    font-family: inherit;
    position: relative;
  }

  .form-tab:hover {
    color: var(--ink);
  }

  .form-tab.active {
    color: var(--primary);
  }

  .form-tab .tab-count {
    display: inline-flex;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    font-weight: 800;
    background: var(--line);
    color: var(--muted);
    margin-right: 6px;
    transition: 0.2s ease;
  }

  .form-tab.active .tab-count {
    background: var(--primary);
    color: white;
  }

  .form-tab.completed .tab-count {
    background: var(--success);
    color: white;
  }

  .form-tab.completed {
    color: var(--success);
  }

  .step-content {
    display: none;
  }

  .step-content.active {
    display: block;
  }

  .ppdb-input {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border-radius: 0.75rem;
    font-size: 0.9rem;
    outline: none;
    transition: border-color 0.2s;
    background: var(--card);
    border: 1.5px solid var(--line);
    color: var(--ink);
    font-family: inherit;
  }

  .ppdb-input:focus {
    border-color: var(--primary-2);
    box-shadow: 0 0 0 3px rgba(20, 87, 166, 0.1);
  }

  .ppdb-input::placeholder {
    color: var(--muted);
    opacity: 0.6;
  }

  select.ppdb-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%235f6f82' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
  }
</style>
@endpush

@section('content')
<form id="ppdbLogoutForm" method="POST" action="{{ route('logout') }}" style="display:none">
  @csrf
  <input type="hidden" name="redirect_to" value="/">
</form>

<section style="background:var(--bg);padding:1.5rem 0 4rem">
  <div class="container" style="max-width:720px">

    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem">
      <div>
        <div style="font-size:.85rem;color:var(--muted);margin-bottom:.25rem">Beranda / PPDB / Formulir Pendaftaran</div>
        <h1 style="font-size:clamp(1.8rem,4vw,2.5rem);font-family:Calistoga,Georgia,serif;font-weight:400;margin:0;color:var(--ink)">Formulir Pendaftaran</h1>
        <p style="font-size:.9rem;color:var(--muted);margin:.25rem 0 0">Lengkapi data calon siswa dan orang tua/wali.</p>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:0.5rem 1rem;border-radius:10px;border:1px solid var(--line);background:var(--card);color:var(--ink);font-size:.82rem;font-weight:700;cursor:pointer;transition:0.2s;font-family:inherit" onmouseover="this.style.borderColor='var(--primary-2)'" onmouseout="this.style.borderColor='var(--line)'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          Keluar
        </button>
      </form>
    </div>

    <div style="display:flex;border-bottom:1.5px solid var(--line);margin-bottom:2rem">
      <button class="form-tab {{ $currentStep === 1 ? 'active' : '' }} {{ $applicant->completion_step === 'student_data' || $applicant->completion_step === 'parent_data' || $applicant->completion_step === 'documents' || $applicant->completion_step === 'completed' ? 'completed' : '' }}" onclick="goToStep(1)">
        <span class="tab-count">{{ $applicant->completion_step === 'not_started' ? '1' : '✓' }}</span>
        Data Siswa
      </button>
      <button class="form-tab {{ $currentStep === 2 ? 'active' : '' }} {{ $applicant->completion_step === 'parent_data' || $applicant->completion_step === 'documents' || $applicant->completion_step === 'completed' ? 'completed' : '' }}" onclick="goToStep(2)">
        <span class="tab-count">{{ $applicant->completion_step !== 'not_started' && $applicant->completion_step !== 'student_data' ? '✓' : '2' }}</span>
        Data Orang Tua
      </button>
      <button class="form-tab" onclick="goToUpload()" style="flex:0 0 auto;padding-left:1.5rem" id="tabStep3">
        <span class="tab-count">3</span>
        Upload Berkas
      </button>
    </div>

    <div class="ppdb-card" style="padding:2rem">

      <div id="step1" class="step-content {{ $currentStep === 1 ? 'active' : '' }}">
        <form id="formStep1" onsubmit="return false">
          @csrf
          <div style="margin-bottom:2.5rem">
            <h3 style="font-size:1.2rem;font-weight:400;font-family:Calistoga,Georgia,serif;padding-bottom:.5rem;border-bottom:1.5px solid var(--line);margin:0 0 1.25rem;color:var(--ink)">Data Calon Siswa</h3>
            <div class="grid grid-2" style="gap:1rem">
              <div class="field">
                <label for="full_name" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Nama Lengkap <span style="color:var(--danger)">*</span></label>
                <input id="full_name" name="full_name" required value="{{ old('full_name', $applicant->full_name) }}" placeholder="Nama lengkap sesuai ijazah" class="ppdb-input">
              </div>
              <div class="field">
                <label for="nickname" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Nama Panggilan</label>
                <input id="nickname" name="nickname" value="{{ old('nickname', $applicant->nickname) }}" placeholder="Nama panggilan sehari-hari" class="ppdb-input">
              </div>
              <div class="field">
                <label for="birth_place" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Tempat Lahir</label>
                <input id="birth_place" name="birth_place" value="{{ old('birth_place', $applicant->birth_place) }}" placeholder="Contoh: Jakarta" class="ppdb-input">
              </div>
              <div class="field">
                <label for="birth_date" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Tanggal Lahir</label>
                <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', $applicant->birth_date?->format('Y-m-d')) }}" class="ppdb-input">
              </div>
              <div class="field">
                <label for="gender" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Jenis Kelamin</label>
                <select id="gender" name="gender" class="ppdb-input">
                  <option value="">Pilih</option>
                  <option value="L" @selected(old('gender', $applicant->gender) === 'L')>Laki-laki</option>
                  <option value="P" @selected(old('gender', $applicant->gender) === 'P')>Perempuan</option>
                </select>
              </div>
              <div class="field">
                <label for="religion" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Agama</label>
                <select id="religion" name="religion" class="ppdb-input">
                  <option value="">Pilih</option>
                  @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $r)
                  <option value="{{ $r }}" @selected(old('religion', $applicant->religion) === $r)>{{ $r }}</option>
                  @endforeach
                </select>
              </div>
              <div class="field" style="grid-column:1/-1">
                <label for="address" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Alamat</label>
                <textarea id="address" name="address" rows="3" placeholder="Alamat lengkap" class="ppdb-input" style="resize:vertical">{{ old('address', $applicant->address) }}</textarea>
              </div>
              <div class="field">
                <label for="phone" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">No. HP</label>
                <input id="phone" name="phone" value="{{ old('phone', $applicant->phone) }}" placeholder="08xxxxxxxxxx" class="ppdb-input">
              </div>
              <div class="field">
                <label for="asal_sekolah" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Asal Sekolah</label>
                <input id="asal_sekolah" name="asal_sekolah" value="{{ old('asal_sekolah', $applicant->asal_sekolah) }}" placeholder="Nama SMP/MTs asal" class="ppdb-input">
              </div>
              <div class="field">
                <label for="nisn" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">NISN</label>
                <input id="nisn" name="nisn" value="{{ old('nisn', $applicant->nisn) }}" placeholder="Nomor Induk Siswa Nasional" class="ppdb-input">
              </div>
            </div>
          </div>
        </form>

        <div style="display:flex;justify-content:space-between;gap:1rem;margin-top:2rem">
          <a href="{{ route('ppdb.start') }}" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:0.7rem 1.5rem;border-radius:12px;border:1px solid var(--line);background:var(--card);color:var(--ink);font-size:.9rem;font-weight:700;cursor:pointer;transition:0.2s;text-decoration:none;font-family:inherit" onmouseover="this.style.borderColor='var(--primary-2)'" onmouseout="this.style.borderColor='var(--line)'">← Kembali</a>
          <button onclick="saveStep1()" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:0.7rem 1.5rem;border-radius:12px;border:none;font-size:.9rem;font-weight:800;cursor:pointer;transition:opacity .2s;background:var(--primary);color:white;font-family:inherit" onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">Simpan & Lanjut →</button>
        </div>
      </div>

      <div id="step2" class="step-content {{ $currentStep === 2 ? 'active' : '' }}">
        <form id="formStep2" onsubmit="return false">
          @csrf

          <div style="margin-bottom:2.5rem">
            <h3 style="font-size:1.2rem;font-weight:400;font-family:Calistoga,Georgia,serif;padding-bottom:.5rem;border-bottom:1.5px solid var(--line);margin:0 0 1.25rem;color:var(--ink)">Pilihan Program</h3>
            <div class="grid grid-2" style="gap:1rem">
              <div class="field">
                <label for="jenjang" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Jenjang</label>
                <select id="jenjang" name="jenjang" class="ppdb-input" disabled>
                  <option value="SMK" selected>SMK</option>
                </select>
                <input type="hidden" name="jenjang" value="SMK">
              </div>
              <div class="field">
                <label for="program_diminati" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Program Diminati</label>
                <select id="program_diminati" name="program_diminati" class="ppdb-input" required>
                  <option value="">Pilih program</option>
                  <option value="RPL" @selected(old('program_diminati', $applicant->program_diminati) === 'RPL')>RPL (Rekayasa Perangkat Lunak)</option>
                  <option value="DKV" @selected(old('program_diminati', $applicant->program_diminati) === 'DKV')>DKV (Desain Komunikasi Visual)</option>
                  <option value="Akuntansi" @selected(old('program_diminati', $applicant->program_diminati) === 'Akuntansi')>Akuntansi & Keuangan</option>
                  <option value="Bisnis Digital" @selected(old('program_diminati', $applicant->program_diminati) === 'Bisnis Digital')>Bisnis Digital</option>
                </select>
              </div>
            </div>
          </div>

          <div style="margin-bottom:2.5rem">
            <h3 style="font-size:1.2rem;font-weight:400;font-family:Calistoga,Georgia,serif;padding-bottom:.5rem;border-bottom:1.5px solid var(--line);margin:0 0 1.25rem;color:var(--ink)">Data Orang Tua / Wali</h3>

            <p style="font-size:.9rem;font-weight:700;color:var(--muted);margin:0 0 .75rem">Ayah</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">
              <div class="field">
                <label for="ayah_name" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Nama Ayah</label>
                <input id="ayah_name" name="ayah_name" value="{{ old('ayah_name', $applicant->ayah_name) }}" placeholder="Nama lengkap ayah" class="ppdb-input">
              </div>
              <div class="field">
                <label for="ayah_occupation" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Pekerjaan Ayah</label>
                <input id="ayah_occupation" name="ayah_occupation" value="{{ old('ayah_occupation', $applicant->ayah_occupation) }}" placeholder="Pekerjaan" class="ppdb-input">
              </div>
              <div class="field">
                <label for="ayah_phone" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">No. HP Ayah</label>
                <input id="ayah_phone" name="ayah_phone" value="{{ old('ayah_phone', $applicant->ayah_phone) }}" placeholder="08xxxxxxxxxx" class="ppdb-input">
              </div>
            </div>

            <p style="font-size:.9rem;font-weight:700;color:var(--muted);margin:0 0 .75rem">Ibu</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">
              <div class="field">
                <label for="ibu_name" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Nama Ibu</label>
                <input id="ibu_name" name="ibu_name" value="{{ old('ibu_name', $applicant->ibu_name) }}" placeholder="Nama lengkap ibu" class="ppdb-input">
              </div>
              <div class="field">
                <label for="ibu_occupation" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Pekerjaan Ibu</label>
                <input id="ibu_occupation" name="ibu_occupation" value="{{ old('ibu_occupation', $applicant->ibu_occupation) }}" placeholder="Pekerjaan" class="ppdb-input">
              </div>
              <div class="field">
                <label for="ibu_phone" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">No. HP Ibu</label>
                <input id="ibu_phone" name="ibu_phone" value="{{ old('ibu_phone', $applicant->ibu_phone) }}" placeholder="08xxxxxxxxxx" class="ppdb-input">
              </div>
            </div>

            <p style="font-size:.9rem;font-weight:700;color:var(--muted);margin:0 0 .75rem">Email Orang Tua</p>
            <div style="margin-bottom:1.5rem">
              <div class="field">
                <label for="ayah_email" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Email Orang Tua <span style="color:var(--danger)">*</span></label>
                <input id="ayah_email" name="ayah_email" type="email" value="{{ old('ayah_email', $applicant->ayah_email) }}" placeholder="email@contoh.com" required class="ppdb-input">
                <p style="font-size:.8rem;color:var(--muted);margin-top:.3rem">Email ini akan digunakan untuk akun portal orang tua</p>
              </div>
            </div>

            <details style="cursor:pointer">
              <summary style="font-weight:600;color:var(--primary-2);font-size:.9rem;font-family:inherit">+ Tambah data wali (opsional)</summary>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem">
                <div class="field">
                  <label for="wali_name" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Nama Wali</label>
                  <input id="wali_name" name="wali_name" value="{{ old('wali_name', $applicant->wali_name) }}" placeholder="Nama lengkap wali" class="ppdb-input">
                </div>
                <div class="field">
                  <label for="wali_occupation" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Pekerjaan Wali</label>
                  <input id="wali_occupation" name="wali_occupation" value="{{ old('wali_occupation', $applicant->wali_occupation) }}" placeholder="Pekerjaan" class="ppdb-input">
                </div>
                <div class="field">
                  <label for="wali_phone" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">No. HP Wali</label>
                  <input id="wali_phone" name="wali_phone" value="{{ old('wali_phone', $applicant->wali_phone) }}" placeholder="08xxxxxxxxxx" class="ppdb-input">
                </div>
                <div class="field">
                  <label for="wali_email" style="font-size:.85rem;font-weight:700;margin-bottom:.35rem;color:var(--ink)">Email Wali</label>
                  <input id="wali_email" name="wali_email" type="email" value="{{ old('wali_email', $applicant->wali_email) }}" placeholder="email@contoh.com" class="ppdb-input">
                </div>
              </div>
            </details>
          </div>
        </form>

        <div style="display:flex;justify-content:space-between;gap:1rem;margin-top:2rem">
          <button onclick="goToStep(1)" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:0.7rem 1.5rem;border-radius:12px;border:1px solid var(--line);background:var(--card);color:var(--ink);font-size:.9rem;font-weight:700;cursor:pointer;transition:0.2s;font-family:inherit" onmouseover="this.style.borderColor='var(--primary-2)'" onmouseout="this.style.borderColor='var(--line)'">← Kembali</button>
          <button onclick="saveStep2()" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:0.7rem 1.5rem;border-radius:12px;border:none;font-size:.9rem;font-weight:800;cursor:pointer;transition:opacity .2s;background:var(--primary);color:white;font-family:inherit" onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">Simpan & Lanjut →</button>
        </div>
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
    history.replaceState(null, '', '{{ route("ppdb.form") }}?step=' + step);
  }

  function goToUpload() {
    if (currentStep < 2) return;
    var tabs = document.querySelectorAll('.form-tab');
    var completed = tabs[0]?.classList.contains('completed');
    if (completed) {
      window.location.href = '{{ route("ppdb.upload") }}';
    }
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
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          document.querySelectorAll('.form-tab')[0].classList.add('completed');
          document.querySelectorAll('.form-tab')[0].querySelector('.tab-count').textContent = '✓';
          document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
          document.getElementById('step2').classList.add('active');
          document.querySelectorAll('.form-tab').forEach((tab, i) => tab.classList.toggle('active', i === 1));
          currentStep = 2;
          history.replaceState(null, '', '{{ route("ppdb.form") }}?step=2');
        } else {
          alert('Gagal menyimpan: ' + (d.message || 'Unknown error'));
        }
      })
      .catch(e => alert('Gagal menyimpan data. Error: ' + e.message));
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
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          window.location.href = '{{ route("ppdb.upload") }}';
        } else {
          alert('Gagal menyimpan: ' + (d.message || 'Unknown error'));
        }
      })
      .catch(e => alert('Gagal menyimpan data. Error: ' + e.message));
  }

  document.querySelectorAll('.nav-links a, .nav-dropdown-menu a, .nav-dropdown-trigger').forEach(el => {
    el.addEventListener('click', function(e) {
      if (this.closest('.nav-dropdown-trigger')) {
        e.stopPropagation();
        return;
      }
      e.preventDefault();
      const href = this.getAttribute('href');
      if (!href || href === '#') return;
      Swal.fire({
        title: 'Keluar dari formulir?',
        text: 'Progres Anda akan tetap tersimpan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0b3b75',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, keluar',
        cancelButtonText: 'Tetap di sini',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('ppdbLogoutForm').submit();
        }
      });
    });
  });
</script>
@endpush