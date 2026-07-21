@extends('layouts.guru')
@section('title', 'Buat Soal')
@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Bank Soal</span>
    <h1>Buat Soal Baru</h1>
    <p>Buat banyak soal sekaligus untuk kuis.</p>
  </div>
</div>

<a href="{{ route('guru.bank-soal.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:.82rem;color:var(--muted);text-decoration:none;border:1.5px solid var(--line);border-radius:10px;margin-bottom:16px">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
  Kembali ke Bank Soal
</a>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div>
      <h2>Buat Soal Baru</h2>
      <p>Pilih kelas, atur jumlah soal, lalu isi form.</p>
    </div>
  </div>

  {{-- Step 1: TA + Topik --}}
  <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:18px">
    <div class="field" style="min-width:280px">
      <label for="ta-select">Kelas & Mata Pelajaran</label>
      <select id="ta-select" required>
        <option value="">— Pilih —</option>
        @foreach ($pairs as $pair)
          <option value="{{ $pair['assignment_id'] }}">{{ $pair['class_name'] }} — {{ $pair['subject_name'] }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="field" style="margin-bottom:18px">
    <label for="topik">Topik <span style="color:var(--danger)">*</span></label>
    <input id="topik" type="text" required placeholder="Contoh: Matematika - Bab 1: Bilangan Bulat" maxlength="255" value="{{ $presetTopic ?? '' }}">
    <span style="font-size:.75rem;color:var(--muted);margin-top:4px">Semua soal di batch ini akan memiliki topik yang sama. Gunakan untuk mengelompokkan soal per tema.</span>
  </div>

  {{-- Step 2: Counts --}}
  <div style="display:flex;gap:14px;align-items:end;flex-wrap:wrap;margin-bottom:18px;padding:18px;border-radius:14px;background:color-mix(in srgb,var(--primary-4) 6%,transparent)">
    <div class="field" style="width:140px">
      <label>Pilihan Ganda</label>
      <input id="count-pg" type="number" min="0" max="50" value="5" style="text-align:center">
    </div>
    <div class="field" style="width:140px">
      <label>True / False</label>
      <input id="count-tf" type="number" min="0" max="50" value="0" style="text-align:center">
    </div>
    <div class="field" style="width:140px">
      <label>Essay</label>
      <input id="count-essay" type="number" min="0" max="50" value="0" style="text-align:center">
    </div>
    <button onclick="generateForm()" class="btn btn-primary" style="padding:10px 28px;height:44px">Generate Form</button>
  </div>

  {{-- Step 3: Generated Form --}}
  <form method="POST" action="{{ route('guru.bank-soal.store') }}" id="bulk-form" style="display:none">
    @csrf
    <input type="hidden" name="teaching_assignment_id" id="form-ta-id" value="">
    <input type="hidden" name="topic" id="form-topic" value="">

    <div id="form-container"></div>

    <div style="display:flex;gap:10px;margin-top:24px;padding-top:18px;border-top:1.5px solid var(--line)">
      <button class="btn btn-primary" type="submit" style="flex:1;padding:14px;font-size:.9rem">Simpan Semua Soal</button>
      <button type="button" onclick="resetForm()" style="padding:14px 24px;border-radius:13px;border:1.5px solid var(--line);background:var(--card);cursor:pointer;font-size:.85rem;font-weight:600;color:var(--muted)">Reset</button>
    </div>
  </form>
</section>

<script>
function generateForm() {
  const taId = document.getElementById('ta-select').value;
  if (!taId) { return alert('Pilih kelas & mata pelajaran terlebih dahulu.'); }

  const topic = document.getElementById('topik').value.trim();
  if (!topic) { return alert('Topik harus diisi untuk mengelompokkan soal.'); }

  const pg = parseInt(document.getElementById('count-pg').value) || 0;
  const tf = parseInt(document.getElementById('count-tf').value) || 0;
  const essay = parseInt(document.getElementById('count-essay').value) || 0;
  const total = pg + tf + essay;

  if (total === 0) { return alert('Minimal 1 soal.'); }
  if (total > 50) { return alert('Maksimal 50 soal dalam satu batch.'); }

  document.getElementById('form-ta-id').value = taId;
  document.getElementById('form-topic').value = topic;

  let html = '';
  let idx = 0;

  for (let i = 1; i <= pg; i++) {
    html += `
      <div class="card card-hover" style="padding:16px 18px;margin-bottom:12px">
        <h4 style="margin:0 0 12px;font-size:.9rem;color:var(--primary)">Pilihan Ganda #${i}</h4>
        <input type="hidden" name="questions[${idx}][type]" value="multiple_choice">
        <div class="field" style="margin-bottom:10px">
          <label>Soal</label>
          <textarea name="questions[${idx}][question_text]" required placeholder="Tulis soal..." style="min-height:70px"></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
          ${['A','B','C','D'].map(l => `
            <div style="display:flex;align-items:center;gap:6px">
              <span style="font-weight:800;font-size:.8rem;color:var(--muted);min-width:18px">${l}.</span>
              <input type="text" name="questions[${idx}][options][${l}]" placeholder="Opsi ${l}" required style="flex:1">
            </div>
          `).join('')}
        </div>
        <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap">
          <div style="display:flex;align-items:center;gap:6px">
            <span style="font-size:.8rem;font-weight:600;color:var(--muted)">Jawaban:</span>
            <select name="questions[${idx}][correct_answer]" required style="width:80px;padding:6px 8px;border-radius:7px;border:1.5px solid var(--line);font-size:.8rem">
              <option value="">Pilih</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>
          <div style="display:flex;align-items:center;gap:6px">
            <span style="font-size:.8rem;font-weight:600;color:var(--muted)">Poin:</span>
            <input type="number" name="questions[${idx}][points]" value="1" min="0.5" max="999" step="0.5" style="width:60px;padding:6px 8px;border-radius:7px;border:1.5px solid var(--line);font-size:.8rem;text-align:center">
          </div>
        </div>
        <div class="field" style="margin-top:8px">
          <label style="font-size:.78rem">Pembahasan (opsional)</label>
          <textarea name="questions[${idx}][explanation]" placeholder="Penjelasan jawaban..." style="min-height:40px;font-size:.8rem"></textarea>
        </div>
      </div>
    `;
    idx++;
  }

  for (let i = 1; i <= tf; i++) {
    html += `
      <div class="card card-hover" style="padding:16px 18px;margin-bottom:12px">
        <h4 style="margin:0 0 12px;font-size:.9rem;color:var(--primary)">True / False #${i}</h4>
        <input type="hidden" name="questions[${idx}][type]" value="true_false">
        <div class="field" style="margin-bottom:10px">
          <label>Soal</label>
          <textarea name="questions[${idx}][question_text]" required placeholder="Tulis soal..." style="min-height:70px"></textarea>
        </div>
        <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;margin-bottom:10px">
          <div style="display:flex;align-items:center;gap:6px">
            <span style="font-size:.8rem;font-weight:600;color:var(--muted)">Jawaban:</span>
            <label style="display:flex;align-items:center;gap:4px;padding:6px 12px;border-radius:7px;border:1.5px solid var(--line);cursor:pointer;font-size:.8rem">
              <input type="radio" name="questions[${idx}][correct_answer]" value="true"> True
            </label>
            <label style="display:flex;align-items:center;gap:4px;padding:6px 12px;border-radius:7px;border:1.5px solid var(--line);cursor:pointer;font-size:.8rem">
              <input type="radio" name="questions[${idx}][correct_answer]" value="false"> False
            </label>
          </div>
          <div style="display:flex;align-items:center;gap:6px">
            <span style="font-size:.8rem;font-weight:600;color:var(--muted)">Poin:</span>
            <input type="number" name="questions[${idx}][points]" value="1" min="0.5" max="999" step="0.5" style="width:60px;padding:6px 8px;border-radius:7px;border:1.5px solid var(--line);font-size:.8rem;text-align:center">
          </div>
        </div>
        <div class="field" style="margin-top:4px">
          <label style="font-size:.78rem">Pembahasan (opsional)</label>
          <textarea name="questions[${idx}][explanation]" placeholder="Penjelasan jawaban..." style="min-height:40px;font-size:.8rem"></textarea>
        </div>
      </div>
    `;
    idx++;
  }

  for (let i = 1; i <= essay; i++) {
    html += `
      <div class="card card-hover" style="padding:16px 18px;margin-bottom:12px">
        <h4 style="margin:0 0 12px;font-size:.9rem;color:var(--primary)">Essay #${i}</h4>
        <input type="hidden" name="questions[${idx}][type]" value="essay">
        <div class="field" style="margin-bottom:10px">
          <label>Soal</label>
          <textarea name="questions[${idx}][question_text]" required placeholder="Tulis soal..." style="min-height:70px"></textarea>
        </div>
        <div class="field" style="margin-bottom:10px">
          <label>Kunci Jawaban</label>
          <textarea name="questions[${idx}][correct_answer]" placeholder="Jawaban referensi..." style="min-height:50px;font-size:.8rem"></textarea>
        </div>
        <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap">
          <div style="display:flex;align-items:center;gap:6px">
            <span style="font-size:.8rem;font-weight:600;color:var(--muted)">Poin:</span>
            <input type="number" name="questions[${idx}][points]" value="2" min="0.5" max="999" step="0.5" style="width:60px;padding:6px 8px;border-radius:7px;border:1.5px solid var(--line);font-size:.8rem;text-align:center">
          </div>
        </div>
        <div class="field" style="margin-top:8px">
          <label style="font-size:.78rem">Pembahasan (opsional)</label>
          <textarea name="questions[${idx}][explanation]" placeholder="Penjelasan jawaban..." style="min-height:40px;font-size:.8rem"></textarea>
        </div>
      </div>
    `;
    idx++;
  }

  document.getElementById('form-container').innerHTML = html;
  document.getElementById('bulk-form').style.display = 'block';
  document.getElementById('bulk-form').scrollIntoView({behavior:'smooth'});
}

function resetForm() {
  document.getElementById('form-container').innerHTML = '';
  document.getElementById('bulk-form').style.display = 'none';
}
</script>
@endsection
