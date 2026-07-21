@extends('layouts.guru')
@section('title', 'Bank Soal')
@push('styles')
<style>
.topic-card{border:1.5px solid var(--line);border-radius:16px;overflow:hidden;margin-bottom:12px;transition:border-color .15s}
.topic-card:hover{border-color:var(--primary-3)}
.topic-header{display:flex;align-items:center;gap:12px;padding:14px 18px;cursor:pointer;background:var(--card);user-select:none}
.topic-header:hover{background:color-mix(in srgb,var(--primary-4) 4%,transparent)}
.topic-header svg{flex-shrink:0;color:var(--primary);transition:transform .2s}
.topic-header.open svg{transform:rotate(90deg)}
.topic-header h3{margin:0;font-size:.9rem;font-weight:700;color:var(--ink);flex:1;min-width:0}
.topic-header .topic-count{font-size:.75rem;color:var(--muted);white-space:nowrap;background:var(--bg);padding:3px 10px;border-radius:20px}
.topic-body{display:none;border-top:1px solid var(--line);padding:6px 14px 14px;background:color-mix(in srgb,var(--primary-4) 3%,transparent)}
.topic-body.open{display:block}
</style>
@endpush
@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">LMS</span>
    <h1>Bank Soal</h1>
    <p>Kelola soal per topik.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<div style="display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap">
  <select onchange="window.location='{{ route('guru.bank-soal.index') }}?ta_id='+this.value" style="min-width:260px;padding:10px 14px;border-radius:13px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.85rem">
    <option value="">— Semua Kelas —</option>
    @foreach ($pairs as $pair)
      <option value="{{ $pair['assignment_id'] }}" @selected($selectedTa && $selectedTa->id == $pair['assignment_id'])>
        {{ $pair['class_name'] }} — {{ $pair['subject_name'] }}
      </option>
    @endforeach
  </select>
  @if ($selectedTa)
    <a href="{{ route('guru.bank-soal.create', ['ta_id' => $selectedTa->id]) }}" class="btn btn-primary" style="padding:8px 20px;font-size:.82rem">+ Buat Soal</a>
  @else
    <a href="{{ route('guru.bank-soal.create') }}" class="btn btn-primary" style="padding:8px 20px;font-size:.82rem">+ Buat Soal</a>
  @endif
  <span style="font-size:.82rem;color:var(--muted)">{{ $questions->count() }} soal</span>
</div>

@if ($questions->count())
  @php
    $grouped = $questions->groupBy(fn($q) => $q->topic ?? 'Tanpa Topik');
  @endphp

  <div id="topic-list">
    @foreach ($grouped as $topic => $soals)
      <div class="topic-card">
        <div class="topic-header" onclick="toggleTopic(this)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
          <h3>{{ $topic }}</h3>
          <span class="topic-count">{{ $soals->count() }} soal</span>
          <button onclick="event.stopPropagation();window.location='{{ route('guru.bank-soal.create', ['ta_id' => $selectedTa->id ?? '']) }}&topic='+encodeURIComponent('{{ $topic }}')" style="background:none;border:none;cursor:pointer;color:var(--primary);font-size:.78rem;font-weight:600;padding:4px 8px;border-radius:6px" title="Tambah soal ke topik ini">+</button>
        </div>
        <div class="topic-body">
          @foreach ($soals as $q)
            <div style="display:flex;align-items:flex-start;gap:12px;padding:10px 12px;border-radius:10px;margin-bottom:4px;transition:background .15s" onmouseover="this.style.background='color-mix(in srgb,var(--primary-4) 8%,transparent)'" onmouseout="this.style.background=''">
              <div style="width:32px;height:32px;border-radius:8px;display:grid;place-items:center;flex-shrink:0;font-size:.65rem;font-weight:800;text-transform:uppercase;background:color-mix(in srgb,var(--primary-4) 12%,transparent);color:var(--primary)">
                {{ $q->question_type === 'multiple_choice' ? 'PG' : ($q->question_type === 'true_false' ? 'TF' : 'ES') }}
              </div>
              <div style="flex:1;min-width:0">
                <span style="font-size:.82rem;font-weight:600;color:var(--ink);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ \Illuminate\Support\Str::limit($q->question_text, 120) }}</span>
                <div style="font-size:.72rem;color:var(--muted);margin-top:2px">
                  {{ $q->points }} poin
                  @if ($q->question_type === 'multiple_choice')
                    · {{ count($q->options ?? []) }} opsi · Kunci: {{ $q->correct_answer }}
                  @elseif ($q->question_type === 'true_false')
                    · Kunci: {{ $q->correct_answer === 'true' ? 'True' : 'False' }}
                  @endif
                </div>
              </div>
              <div style="display:flex;gap:4px;flex-shrink:0">
                <button onclick="editSoal({{ $q->id }})" style="width:30px;height:30px;border-radius:7px;display:grid;place-items:center;border:1.5px solid var(--line);color:var(--muted);background:var(--card);cursor:pointer" title="Edit">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                <form method="POST" action="{{ route('guru.bank-soal.destroy', $q) }}" style="display:inline" class="form-delete">
                  @csrf @method('DELETE')
                  <button type="button" onclick="confirmDeleteSoal(this)" style="width:30px;height:30px;border-radius:7px;display:grid;place-items:center;border:1.5px solid var(--danger);color:var(--danger);background:none;cursor:pointer" title="Hapus">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  </button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
@else
  <div class="portal-panel" style="text-align:center;padding:60px 20px">
    <p style="color:var(--muted);font-size:.95rem;margin-bottom:16px">Belum ada soal di bank soal.</p>
    <a href="{{ route('guru.bank-soal.create') }}" class="btn btn-primary">+ Buat Soal</a>
  </div>
@endif

{{-- Edit Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;place-items:center;overflow-y:auto;padding:20px" onclick="if(event.target===this)closeEdit()">
  <div style="background:var(--card);border-radius:16px;padding:28px;max-width:600px;width:100%;margin:auto;box-shadow:0 20px 60px rgba(0,0,0,.2)">
    <h3 style="margin:0 0 18px;font-size:1.05rem">Edit Soal</h3>
    <form id="editForm" method="POST">
      @csrf @method('PUT')
      <div class="field" style="margin-bottom:14px">
        <label>Tipe Soal</label>
        <select name="question_type" id="edit-type" onchange="toggleEditOptions()" required>
          <option value="multiple_choice">Pilihan Ganda</option>
          <option value="true_false">True / False</option>
          <option value="essay">Essay</option>
        </select>
      </div>
      <div class="field" style="margin-bottom:14px">
        <label for="edit-text">Soal</label>
        <textarea id="edit-text" name="question_text" required style="min-height:100px"></textarea>
      </div>
      <div id="edit-options" style="margin-bottom:14px">
        <label style="font-weight:800;font-size:.9rem;display:block;margin-bottom:7px">Opsi Jawaban</label>
        @foreach (['A', 'B', 'C', 'D'] as $label)
          <div class="edit-opt-row" style="display:flex;gap:8px;margin-bottom:8px">
            <span style="width:32px;height:40px;display:grid;place-items:center;font-weight:800;font-size:.85rem;color:var(--muted)">{{ $label }}</span>
            <input type="hidden" name="options[{{ $loop->index }}][label]" value="{{ $label }}">
            <input type="text" name="options[{{ $loop->index }}][text]" placeholder="Opsi {{ $label }}" style="flex:1">
          </div>
        @endforeach
      </div>
      <div id="edit-tf" style="display:none;margin-bottom:14px">
        <label style="font-weight:800;font-size:.9rem;display:block;margin-bottom:7px">Jawaban Benar</label>
        <div style="display:flex;gap:10px">
          <label style="display:flex;align-items:center;gap:6px;padding:10px 16px;border-radius:10px;border:1.5px solid var(--line);cursor:pointer">
            <input type="radio" name="correct_answer" value="true"> True
          </label>
          <label style="display:flex;align-items:center;gap:6px;padding:10px 16px;border-radius:10px;border:1.5px solid var(--line);cursor:pointer">
            <input type="radio" name="correct_answer" value="false"> False
          </label>
        </div>
      </div>
      <div id="edit-correct-field" class="field" style="margin-bottom:14px">
        <label for="edit-correct">Jawaban Benar</label>
        <input id="edit-correct" name="correct_answer" type="text" placeholder="A / B / C / D">
      </div>
      <div class="field" style="margin-bottom:14px">
        <label for="edit-points">Poin</label>
        <input id="edit-points" name="points" type="number" min="0.5" max="999" step="0.5" required>
      </div>
      <div class="field" style="margin-bottom:18px">
        <label for="edit-explanation">Pembahasan (opsional)</label>
        <textarea id="edit-explanation" name="explanation" style="min-height:60px"></textarea>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-primary" type="submit" style="flex:1">Simpan</button>
        <button type="button" onclick="closeEdit()" style="padding:10px 20px;border-radius:13px;border:1.5px solid var(--line);background:var(--card);cursor:pointer;font-size:.85rem;font-weight:600;color:var(--muted)">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleTopic(header) {
  header.classList.toggle('open');
  header.nextElementSibling.classList.toggle('open');
}

function editSoal(id) {
  fetch('/guru/bank-soal/' + id)
    .then(r => r.json())
    .then(q => {
      document.getElementById('editForm').action = '/guru/bank-soal/' + id;
      document.getElementById('edit-type').value = q.question_type;
      document.getElementById('edit-text').value = q.question_text;
      document.getElementById('edit-points').value = q.points;
      document.getElementById('edit-explanation').value = q.explanation || '';

      document.querySelectorAll('.edit-opt-row input[type="text"]').forEach(i => i.value = '');
      if (q.options) {
        q.options.forEach((opt, i) => {
          const row = document.querySelectorAll('.edit-opt-row')[i];
          if (row) row.querySelector('input[type="text"]').value = opt.text;
        });
      }

      if (q.question_type === 'true_false') {
        document.querySelector('#edit-tf input[value="' + q.correct_answer + '"]').checked = true;
      } else {
        document.getElementById('edit-correct').value = q.correct_answer || '';
      }

      toggleEditOptions();
      document.getElementById('editModal').style.display = 'grid';
    });
}

function toggleEditOptions() {
  const type = document.getElementById('edit-type').value;
  document.getElementById('edit-options').style.display = type === 'multiple_choice' ? 'block' : 'none';
  document.getElementById('edit-tf').style.display = type === 'true_false' ? 'block' : 'none';
  document.getElementById('edit-correct-field').style.display = type === 'essay' ? 'block' : 'none';
}

function closeEdit() {
  document.getElementById('editModal').style.display = 'none';
}

function confirmDeleteSoal(btn) {
  const form = btn.closest('.form-delete');
  Swal.fire({
    title: 'Hapus soal?',
    icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444',
    confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal',
  }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endsection
