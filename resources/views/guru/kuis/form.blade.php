@extends('layouts.guru')
@section('title', $quiz ? 'Edit Kuis' : 'Buat Kuis')
@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">LMS</span>
    <h1>{{ $quiz ? 'Edit Kuis' : 'Buat Kuis Baru' }}</h1>
    <p>{{ $selectedTa->class_name }} — {{ $selectedTa->subject->name ?? $selectedTa->customSubject->nama ?? '-' }}</p>
  </div>
</div>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div>
      <h2>{{ $quiz ? 'Edit Kuis' : 'Kuis Baru' }}</h2>
      <p>Atur detail kuis dan pilih soal dari bank soal.</p>
    </div>
  </div>

  <form method="POST" action="{{ $quiz ? route('guru.kuis.update', $quiz) : route('guru.kuis.store') }}">
    @csrf
    @if ($quiz) @method('PUT') @endif

    <input type="hidden" name="teaching_assignment_id" value="{{ $selectedTa->id }}">

    <div class="field" style="margin-bottom:14px">
      <label for="title">Judul Kuis</label>
      <input id="title" name="title" type="text" required placeholder="Contoh: Kuis Bab 1" value="{{ old('title', $quiz->title ?? '') }}">
    </div>

    <div class="field" style="margin-bottom:14px">
      <label for="module_id">Modul (opsional)</label>
      <select name="module_id">
        <option value="">— Tanpa Modul —</option>
        @foreach ($modules as $module)
          <option value="{{ $module->id }}" @selected(old('module_id', $quiz->module_id ?? '') == $module->id)>{{ $module->title }}</option>
        @endforeach
      </select>
    </div>

    <div class="field" style="margin-bottom:14px">
      <label for="instructions">Instruksi</label>
      <textarea id="instructions" name="instructions" placeholder="Instruksi pengerjaan kuis..." style="min-height:80px">{{ old('instructions', $quiz->instructions ?? '') }}</textarea>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px">
      <div class="field">
        <label for="time_limit">Batas Waktu (menit, opsional)</label>
        <input id="time_limit" name="time_limit" type="number" min="1" max="999" value="{{ old('time_limit', $quiz->time_limit ?? '') }}">
      </div>
      <div class="field">
        <label for="max_attempts">Max Percobaan</label>
        <input id="max_attempts" name="max_attempts" type="number" min="1" max="10" required value="{{ old('max_attempts', $quiz->max_attempts ?? 1) }}">
      </div>
      <div class="field">
        <label for="start_date">Jadwal Mulai (opsional)</label>
        <input id="start_date" name="start_date" type="datetime-local" value="{{ old('start_date', $quiz?->start_date?->format('Y-m-d\TH:i') ?? '') }}">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
      <div class="field">
        <label for="end_date">Jadwal Selesai (opsional)</label>
        <input id="end_date" name="end_date" type="datetime-local" value="{{ old('end_date', $quiz?->end_date?->format('Y-m-d\TH:i') ?? '') }}">
      </div>
    </div>

    <div style="margin-bottom:18px">
      <label style="font-weight:800;font-size:.9rem;display:block;margin-bottom:10px">Pengaturan</label>
      <div style="display:flex;gap:20px;flex-wrap:wrap">
        <label style="display:flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;color:var(--ink);cursor:pointer">
          <input type="checkbox" name="shuffle_questions" value="1" @checked(old('shuffle_questions', $quiz->shuffle_questions ?? false))> Acak soal
        </label>
        <label style="display:flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;color:var(--ink);cursor:pointer">
          <input type="checkbox" name="shuffle_options" value="1" @checked(old('shuffle_options', $quiz->shuffle_options ?? false))> Acak opsi
        </label>
        <label style="display:flex;align-items:center;gap:6px;font-size:.85rem;font-weight:600;color:var(--ink);cursor:pointer">
          <input type="checkbox" name="show_result_immediately" value="1" @checked(old('show_result_immediately', $quiz->show_result_immediately ?? false))> Tampilkan hasil langsung
        </label>
      </div>
    </div>

    <div style="margin-bottom:18px">
      <label style="font-weight:800;font-size:.9rem;display:block;margin-bottom:10px">Pilih Soal dari Bank Soal</label>
      @if ($bankSoal->count())
        @php
          $grouped = $bankSoal->groupBy(fn($s) => $s->topic ?? 'Tanpa Topik');
        @endphp
        <div style="display:grid;gap:12px" id="soal-list">
          @foreach ($grouped as $topic => $soals)
            <div style="border:1.5px solid var(--line);border-radius:14px;overflow:hidden">
              <div style="padding:10px 14px;background:color-mix(in srgb,var(--primary-4) 8%,transparent);font-weight:700;font-size:.85rem;color:var(--primary);display:flex;align-items:center;gap:8px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                {{ $topic }}
                <span style="font-size:.72rem;color:var(--muted);font-weight:400">({{ $soals->count() }} soal)</span>
              </div>
              <div style="padding:6px 10px">
                @foreach ($soals as $soal)
                  @php
                    $checked = $quiz ? $quiz->questions->contains('question_bank_id', $soal->id) : false;
                    $existingPoints = $checked ? $quiz->questions->firstWhere('question_bank_id', $soal->id)?->points : $soal->points;
                  @endphp
                  <label class="card card-hover" style="display:flex;align-items:center;gap:10px;padding:8px 10px;margin-bottom:4px;cursor:pointer">
                    <input type="checkbox" class="soal-checkbox" value="{{ $soal->id }}" @checked($checked) onchange="toggleSoal(this)" style="width:auto">
                    <div style="flex:1;min-width:0">
                      <span style="font-size:.8rem;font-weight:600;color:var(--ink);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ \Illuminate\Support\Str::limit($soal->question_text, 80) }}</span>
                      <span style="font-size:.7rem;color:var(--muted)">{{ $soal->question_type === 'multiple_choice' ? 'PG' : ($soal->question_type === 'true_false' ? 'TF' : 'Essay') }} · {{ $soal->points }} poin</span>
                    </div>
                    <div class="soal-points" style="display:flex;align-items:center;gap:6px;{{ $checked ? '' : 'display:none' }}">
                      <span style="font-size:.72rem;color:var(--muted)">Poin:</span>
                      <input type="number" name="questions[{{ $soal->id }}][points]" value="{{ $existingPoints }}" min="0.5" max="999" step="0.5" style="width:55px;padding:4px 6px;border-radius:6px;border:1.5px solid var(--line);font-size:.75rem;text-align:center">
                      <input type="hidden" name="questions[{{ $soal->id }}][id]" value="{{ $soal->id }}">
                    </div>
                  </label>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div style="padding:20px;text-align:center;color:var(--muted);border:2px dashed var(--line);border-radius:12px">
          <p style="margin:0 0 8px">Belum ada soal di bank soal.</p>
          <a href="{{ route('guru.bank-soal.create', ['ta_id' => $selectedTa->id]) }}" class="btn btn-primary" style="padding:6px 14px;font-size:.78rem">+ Buat Soal</a>
        </div>
      @endif
    </div>

    <button class="btn btn-primary" type="submit" style="width:100%">
      {{ $quiz ? 'Simpan Perubahan' : 'Buat Kuis' }}
    </button>
    <div style="text-align:center;margin-top:10px">
      <a href="{{ route('guru.kuis.index', ['ta_id' => $selectedTa->id]) }}" style="font-size:.82rem;color:var(--muted);text-decoration:none">Batal</a>
    </div>
  </form>
</section>

<script>
function toggleSoal(checkbox) {
  const card = checkbox.closest('.card');
  const pointsDiv = card.querySelector('.soal-points');
  pointsDiv.style.display = checkbox.checked ? 'flex' : 'none';
  if (!checkbox.checked) {
    card.querySelector('.soal-points input[type="number"]').value = '';
  }
}

document.querySelector('.soal-checkbox')?.addEventListener('change', function() {});
</script>
@endsection
