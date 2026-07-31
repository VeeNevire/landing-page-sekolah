@extends('layouts.admin')

@section('title', 'Audit Log')

@php
$currentRole = request('role', '');
$roleColors = ['admin' => '#4338ca', 'teacher' => '#0369a1', 'homeroom' => '#0d9488', 'parent' => '#b45309', 'principal' => '#7c3aed'];
$roleLabels = ['admin' => 'Admin', 'teacher' => 'Guru', 'homeroom' => 'Wali Kelas', 'parent' => 'Orang Tua', 'principal' => 'Kepsek'];

$entityLabels = [
'User' => 'Akun', 'Student' => 'Siswa', 'Subject' => 'Mapel',
'Quiz' => 'Kuis', 'Assignment' => 'Tugas', 'Material' => 'Materi',
'AcademicPeriod' => 'Periode', 'TeachingAssignment' => 'Penugasan',
'Jadwal' => 'Jadwal', 'Assessment' => 'Nilai',
'Attendance' => 'Absensi', 'TeacherNote' => 'Catatan',
'CourseModule' => 'Modul', 'QuestionBank' => 'Soal',
'Applicant' => 'Pendaftar', 'Jurusan' => 'Jurusan',
'ParentStudent' => 'Orang Tua-Siswa', 'Submission' => 'Tugas',
'QuizAttempt' => 'Percobaan Kuis', 'JurusanCustomSubject' => 'Mapel Custom',
];

$entityColors = [
    'User' => '#6366f1', 'Student' => '#22c55e', 'Subject' => '#06b6d4',
    'Quiz' => '#a855f7', 'Assignment' => '#f97316', 'Material' => '#ec4899',
    'AcademicPeriod' => '#14b8a6', 'TeachingAssignment' => '#d97706',
    'Jadwal' => '#64748b', 'Assessment' => '#10b981',
    'Attendance' => '#0ea5e9', 'TeacherNote' => '#eab308',
    'CourseModule' => '#8b5cf6', 'QuestionBank' => '#f43f5e',
    'Applicant' => '#d946ef', 'Jurusan' => '#0891b2',
    'ParentStudent' => '#84cc16', 'Submission' => '#f97316',
    'QuizAttempt' => '#c026d3', 'JurusanCustomSubject' => '#06b6d4',
];

$actionLabels = [
'auth.login' => 'Login',
'auth.logout' => 'Logout',
'user.create' => 'Menambahkan akun',
'user.update' => 'Mengedit akun',
'user.toggle' => 'Mengubah status akun',
'user.reset-password' => 'Mereset password akun',
'user.delete' => 'Menghapus akun',
'student.create' => 'Menambahkan data siswa',
'student.update' => 'Mengedit data siswa',
'student.delete' => 'Menghapus data siswa',
'student.reset-password' => 'Mereset password siswa',
'student.import' => 'Import data siswa',
'student.bulk_graduate' => 'Meluluskan siswa',
'student.create-from-applicant' => 'Menerima pendaftar jadi siswa',
'subject.create' => 'Menambahkan mata pelajaran',
'subject.update' => 'Mengedit mata pelajaran',
'subject.delete' => 'Menghapus mata pelajaran',
'subject.assign' => 'Menugaskan mata pelajaran',
'subject.assign-cs' => 'Menambahkan mapel custom',
'period.create' => 'Menambahkan periode',
'period.update' => 'Mengedit periode',
'period.delete' => 'Menghapus periode',
'period.activate' => 'Mengaktifkan periode',
'teaching.create' => 'Menambahkan penugasan',
'teaching.update' => 'Mengedit penugasan',
'teaching.delete' => 'Menghapus penugasan',
'jurusan.create' => 'Menambahkan jurusan',
'jurusan.update' => 'Mengedit jurusan',
'jurusan.delete' => 'Menghapus jurusan',
'jadwal.create' => 'Menambahkan jadwal',
'jadwal.update' => 'Mengedit jadwal',
'jadwal.delete' => 'Menghapus jadwal',
'assessment.create' => 'Input nilai',
'assessment.update' => 'Mengedit penilaian',
'assessment.delete' => 'Menghapus penilaian',
'assessment.publish' => 'Mempublikasi nilai',
'attendance.record' => 'Input absensi',
'teacher-note.create' => 'Menulis catatan',
'grade.publish' => 'Mempublikasi nilai',
'material.create' => 'Upload materi',
'material.delete' => 'Menghapus materi',
'module.create' => 'Menambahkan modul',
'module.update' => 'Mengedit modul',
'module.delete' => 'Menghapus modul',
'quiz.create' => 'Menambahkan kuis',
'quiz.update' => 'Mengedit kuis',
'quiz.delete' => 'Menghapus kuis',
'quiz.publish' => 'Mempublikasi kuis',
'quiz.grade_essay' => 'Menilai essay',
'assignment.create' => 'Menambahkan tugas',
'assignment.update' => 'Mengedit tugas',
'assignment.delete' => 'Menghapus tugas',
'assignment.publish' => 'Mempublikasi tugas',
'submission.grade' => 'Menilai tugas',
'parent-student.create' => 'Menghubungkan orang tua-siswa',
'parent-student.delete' => 'Memutuskan orang tua-siswa',
'question_bank.bulk_create' => 'Menambahkan soal',
'question_bank.update' => 'Mengedit soal',
'question_bank.delete' => 'Menghapus soal',
'applicant.status_update' => 'Mengubah status pendaftar',
'applicant.bulk-accept' => 'Menerima pendaftar',
'applicant.bulk-status' => 'Mengubah status pendaftar massal',
'applicant.deleted' => 'Menghapus pendaftar',
'billing.create' => 'Membuat tagihan',
'billing.update' => 'Mengedit tagihan',
'billing.delete' => 'Menghapus tagihan',
'alumni.update' => 'Mengedit data alumni',
'alumni.cv_upload' => 'Upload CV alumni',
];

$actionColors = [
'auth.login' => 'var(--success)', 'auth.logout' => 'var(--muted)',
'user.create' => 'var(--success)', 'user.update' => 'var(--primary-2)',
'user.delete' => '#ef4444', 'user.toggle' => '#b45309', 'user.reset-password' => '#b45309',
'student.create' => 'var(--success)', 'student.update' => 'var(--primary-2)',
'student.delete' => '#ef4444', 'student.reset-password' => '#b45309',
'student.import' => 'var(--success)', 'student.bulk_graduate' => '#b45309',
'student.create-from-applicant' => 'var(--success)',
'subject.create' => 'var(--success)', 'subject.update' => 'var(--primary-2)',
'subject.delete' => '#ef4444', 'subject.assign' => 'var(--primary-2)', 'subject.assign-cs' => 'var(--primary-2)',
'period.create' => 'var(--success)', 'period.update' => 'var(--primary-2)',
'period.delete' => '#ef4444', 'period.activate' => '#b45309',
'teaching.create' => 'var(--success)', 'teaching.update' => 'var(--primary-2)',
'teaching.delete' => '#ef4444',
'jurusan.create' => 'var(--success)', 'jurusan.update' => 'var(--primary-2)',
'jurusan.delete' => '#ef4444',
'jadwal.create' => 'var(--success)', 'jadwal.update' => 'var(--primary-2)',
'jadwal.delete' => '#ef4444',
'assessment.create' => 'var(--primary-2)', 'assessment.update' => 'var(--primary-2)',
'assessment.delete' => '#ef4444', 'assessment.publish' => '#b45309',
'attendance.record' => 'var(--primary-2)',
'teacher-note.create' => 'var(--primary-2)', 'grade.publish' => '#b45309',
'material.create' => 'var(--success)', 'material.delete' => '#ef4444',
'module.create' => 'var(--success)', 'module.update' => 'var(--primary-2)',
'module.delete' => '#ef4444',
'quiz.create' => 'var(--success)', 'quiz.update' => 'var(--primary-2)',
'quiz.delete' => '#ef4444', 'quiz.publish' => '#b45309', 'quiz.grade_essay' => '#b45309',
'assignment.create' => 'var(--success)', 'assignment.update' => 'var(--primary-2)',
'assignment.delete' => '#ef4444', 'assignment.publish' => '#b45309',
'submission.grade' => '#b45309',
'parent-student.create' => 'var(--success)', 'parent-student.delete' => '#ef4444',
'question_bank.bulk_create' => 'var(--success)', 'question_bank.update' => 'var(--primary-2)',
'question_bank.delete' => '#ef4444',
'applicant.status_update' => '#b45309', 'applicant.bulk-accept' => 'var(--success)',
'applicant.bulk-status' => '#b45309', 'applicant.deleted' => '#ef4444',
'billing.create' => 'var(--success)', 'billing.update' => 'var(--primary-2)',
'billing.delete' => '#ef4444',
'alumni.update' => 'var(--primary-2)', 'alumni.cv_upload' => 'var(--primary-2)',
];

$domainGroups = [
'auth' => 'Sesi', 'user' => 'Akun', 'student' => 'Siswa', 'subject' => 'Mapel',
'period' => 'Periode', 'teaching' => 'Penugasan', 'jurusan' => 'Jurusan',
'jadwal' => 'Jadwal', 'assessment' => 'Nilai', 'attendance' => 'Absensi',
'teacher-note' => 'Catatan', 'grade' => 'Publikasi', 'material' => 'Materi',
'module' => 'Modul', 'quiz' => 'Kuis', 'assignment' => 'Tugas',
'submission' => 'Pengumpulan', 'parent-student' => 'Orang Tua–Siswa',
'question_bank' => 'Bank Soal', 'applicant' => 'PPDB',
'billing' => 'Tagihan', 'alumni' => 'Alumni',
];

$publishCombined = ['grade.publish', 'assessment.publish'];
$actionGroups = [];
foreach ($actionLabels as $key => $label) {
    if (in_array($key, $publishCombined, true)) {
        continue;
    }
    $domain = explode('.', $key, 2)[0];
    $group = $domainGroups[$domain] ?? ucfirst($domain);
    $actionGroups[$group][$key] = $label;
}
$actionGroups['Nilai']['publish'] = 'Mempublikasi nilai';

$filters = array_filter([
    'user_id' => request('user_id'),
    'action' => request('action'),
    'q' => request('q'),
    'from' => request('from'),
    'to' => request('to'),
]);

function parseUserAgent(?string $ua): string {
    if (!$ua) return '—';

    if (preg_match('/Edg\/(\d+)/', $ua, $m)) $browser = 'Edge ' . $m[1];
    elseif (preg_match('/Chrome\/(\d+)/', $ua, $m)) $browser = 'Chrome ' . $m[1];
    elseif (preg_match('/Firefox\/(\d+)/', $ua, $m)) $browser = 'Firefox ' . $m[1];
    elseif (preg_match('/Safari\//', $ua)) $browser = 'Safari';
    elseif (preg_match('/OPR\/(\d+)/', $ua, $m)) $browser = 'Opera ' . $m[1];
    else $browser = '?';

    if (preg_match('/Windows NT (\d+\.\d+)/', $ua, $m)) {
        $versions = ['10.0' => 'Win 10/11', '6.3' => 'Win 8.1', '6.2' => 'Win 8', '6.1' => 'Win 7'];
        $os = $versions[$m[1]] ?? 'Win ' . $m[1];
    } elseif (preg_match('/Mac OS X (\d+)[_\.](\d+)/', $ua, $m)) {
        $os = 'macOS ' . $m[1] . '.' . $m[2];
    } elseif (preg_match('/Android (\d+)/', $ua, $m)) {
        $os = 'Android ' . $m[1];
    } elseif (preg_match('/iPhone; CPU iPhone OS (\d+)_(\d+)/', $ua, $m)) {
        $os = 'iOS ' . $m[1] . '.' . $m[2];
    } elseif (preg_match('/Linux/', $ua)) {
        $os = 'Linux';
    } else {
        $os = '?';
    }

    return $browser . ' · ' . $os;
}

$hasFilter = request()->hasAny(['user_id', 'action', 'q', 'from', 'to']);
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Keamanan sistem</span>
    <h1>Audit Log</h1>
    <p>Pantau semua aktivitas dan perubahan data dalam sistem.</p>
  </div>
</div>

<div class="tabs audit-tabs">
  <a href="{{ route('admin.audit.index', $filters) }}"
    class="tab-btn {{ $currentRole === '' ? 'active' : '' }}">
    Semua <span class="tab-count">{{ $tabCounts['all'] }}</span>
  </a>
  <a href="{{ route('admin.audit.index', ['role' => 'admin'] + $filters) }}"
    class="tab-btn {{ $currentRole === 'admin' ? 'active' : '' }}">
    Admin <span class="tab-count">{{ $tabCounts['admin'] }}</span>
  </a>
  <a href="{{ route('admin.audit.index', ['role' => 'guru'] + $filters) }}"
    class="tab-btn {{ $currentRole === 'guru' ? 'active' : '' }}">
    Guru <span class="tab-count">{{ $tabCounts['guru'] }}</span>
  </a>
  <a href="{{ route('admin.audit.index', ['role' => 'parent'] + $filters) }}"
    class="tab-btn {{ $currentRole === 'parent' ? 'active' : '' }}">
    Orang Tua <span class="tab-count">{{ $tabCounts['parent'] }}</span>
  </a>
</div>

<div class="admin-toolbar audit-toolbar">
  <form method="GET" class="audit-filter-form" id="auditFilterForm">
    @if ($currentRole)
    <input type="hidden" name="role" value="{{ $currentRole }}">
    @endif
    <div class="field audit-field-role">
      <label for="audit-user">Pengguna</label>
      <select name="user_id" id="audit-user">
        <option value="">Semua pengguna</option>
        @foreach ($users as $user)
        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name ?: $user->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="field audit-field-role">
      <label for="audit-action">Aksi</label>
      <select name="action" id="audit-action">
        <option value="">Semua aksi</option>
        @foreach ($actionGroups as $groupLabel => $actions)
        <optgroup label="{{ $groupLabel }}">
          @foreach ($actions as $key => $label)
          <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </optgroup>
        @endforeach
      </select>
    </div>
    <div class="field audit-field-grow">
      <label for="audit-q">Pencarian bebas</label>
      <input type="text" name="q" id="audit-q" value="{{ request('q') }}" placeholder="Nama, entitas, atau aksi…">
    </div>
    <div class="field audit-field-date">
      <label for="audit-from">Dari</label>
      <input type="date" name="from" id="audit-from" value="{{ request('from') }}">
    </div>
    <div class="field audit-field-date">
      <label for="audit-to">Sampai</label>
      <input type="date" name="to" id="audit-to" value="{{ request('to') }}">
    </div>
    <div class="audit-toolbar-actions">
      <button class="btn btn-primary" type="submit" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0" aria-hidden="true" tabindex="-1">Filter</button>
      @if ($hasFilter)
      <a href="{{ route('admin.audit.index', array_filter(['role' => $currentRole])) }}" class="btn btn-outline">Reset</a>
      @endif
    </div>
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap audit-table-wrap">
    <table class="grade-table audit-table">
      <thead>
        <tr>
          <th class="audit-col-time">Waktu</th>
          <th>Pengguna</th>
          <th>Aksi</th>
          <th>Entitas</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($logs as $log)
        @php
          $entityName = $log->entity_identifier;
          $entityRoleLabel = null;
          if ($log->entity_identifier) {
            if (preg_match('/^(.+?)\s*\((.+?)\)$/', $log->entity_identifier, $m)) {
              $entityName = trim($m[1]);
              $entityRoleLabel = $m[2];
            }
            elseif (str_contains($log->entity_identifier, '|')) {
              $parts = explode('|', $log->entity_identifier, 2);
              $entityName = $parts[0];
              $entityRoleLabel = $roleLabels[$parts[1]] ?? $parts[1];
            }
          }
          $actionLabel = $actionLabels[$log->action] ?? $log->action;
          if ($entityRoleLabel && str_starts_with($log->action, 'user.')) {
            if (in_array($log->action, ['user.create', 'user.update', 'user.delete'])) {
              $actionLabel .= ' ' . strtolower($entityRoleLabel);
            }
          }

          $carbon = $log->created_at;
          if ($carbon->isToday()) {
            $timeText = 'Hari ini · ' . $carbon->format('H:i');
          } elseif ($carbon->isYesterday()) {
            $timeText = 'Kemarin · ' . $carbon->format('H:i');
          } else {
            $timeText = $carbon->format('d M') . ($carbon->year !== now()->year ? ' Y' : '') . ' · ' . $carbon->format('H:i');
          }
          $timeFull = $carbon->format('d M Y H:i');
          $userRole = $log->user->role ?? 'parent';
          $userColor = $roleColors[$userRole] ?? '#5f6f82';
          $entityClass = class_basename($log->entity_type);
          $entityColor = $entityColors[$entityClass] ?? '#5f6f82';
        @endphp
        <tr>
          <td>
            <span class="audit-time" title="{{ $timeFull }}">
              <span class="audit-time-dot {{ $carbon->isToday() ? 'today' : ($carbon->isYesterday() ? 'yesterday' : '') }}"></span>
              <span>{{ $timeText }}</span>
            </span>
          </td>
          <td>
            <div class="audit-user">
              <span class="audit-avatar" style="--chip:{{ $userColor }}">{{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}</span>
              <div class="audit-user-meta">
                <span class="audit-user-name">{{ $log->user->name ?? 'System' }}</span>
                @if ($log->user)
                <span class="audit-chip" style="--chip:{{ $userColor }}">{{ $roleLabels[$userRole] ?? $userRole }}</span>
                @endif
              </div>
            </div>
          </td>
          <td>
            <span class="audit-chip" style="--chip:{{ $actionColors[$log->action] ?? 'var(--primary-2)' }}">{{ $actionLabel }}</span>
          </td>
          <td>
            @if (str_starts_with($log->action, 'auth.'))
              <span class="audit-meta">{{ parseUserAgent($log->user_agent) }}</span>
            @elseif ($log->entity_identifier)
              @if ($entityRoleLabel)
                <span class="audit-entity-name">{{ $entityName }} <span class="audit-entity-role">({{ $entityRoleLabel }})</span></span>
              @else
                <span class="audit-entity">
                  <span class="audit-entity-name">{{ $entityName }}</span>
                  <span class="audit-chip" style="--chip:{{ $entityColor }}">{{ $entityLabels[$entityClass] ?? $entityClass }}</span>
                </span>
              @endif
            @else
              <span class="audit-chip" style="--chip:{{ $entityColor }}">{{ $entityLabels[$entityClass] ?? $entityClass }}</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4">
            <div class="audit-empty">
              <span class="audit-empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              </span>
              <h3>@if ($hasFilter) Tidak ada aktivitas yang cocok @else Belum ada aktivitas tercatat @endif</h3>
              <p>@if ($hasFilter) Log tidak ditemukan untuk filter ini. Coba ubah atau hapus filternya. @else Aktivitas sistem akan muncul di sini saat ada perubahan data. @endif</p>
              @if ($hasFilter)
              <a href="{{ route('admin.audit.index', array_filter(['role' => $currentRole])) }}" class="btn btn-outline">Reset filter</a>
              @endif
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="audit-pagination">{{ $logs->links('vendor.pagination.admin') }}</div>
</section>
@endsection

@push('scripts')
<script>
(function () {
  var form = document.getElementById('auditFilterForm');
  if (!form) return;

  var qInput = document.getElementById('audit-q');
  var debounceTimer = null;

  ['audit-user', 'audit-action', 'audit-from', 'audit-to'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.addEventListener('change', function () { form.submit(); });
  });

  if (qInput) {
    qInput.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () { form.submit(); }, 600);
    });
    qInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        clearTimeout(debounceTimer);
        e.preventDefault();
        form.submit();
      }
    });
  }
})();
</script>
@endpush
