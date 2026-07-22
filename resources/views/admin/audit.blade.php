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
];

$actionColors = [
'auth.login' => 'var(--success)', 'auth.logout' => 'var(--muted)',
'user.create' => 'var(--success)', 'user.update' => 'var(--primary-2)',
'user.delete' => '#ef4444', 'user.toggle' => '#b45309', 'user.reset-password' => '#b45309',
'student.create' => 'var(--success)', 'student.update' => 'var(--primary-2)',
'student.delete' => '#ef4444', 'student.reset-password' => '#b45309',
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
'assessment.create' => 'var(--primary-2)', 'attendance.record' => 'var(--primary-2)',
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
];

function parseUserAgent(?string $ua): string {
    if (!$ua) return '—';
    
    // Browser detection
    if (preg_match('/Edg\/(\d+)/', $ua, $m)) $browser = 'Edge ' . $m[1];
    elseif (preg_match('/Chrome\/(\d+)/', $ua, $m)) $browser = 'Chrome ' . $m[1];
    elseif (preg_match('/Firefox\/(\d+)/', $ua, $m)) $browser = 'Firefox ' . $m[1];
    elseif (preg_match('/Safari\//', $ua)) $browser = 'Safari';
    elseif (preg_match('/OPR\/(\d+)/', $ua, $m)) $browser = 'Opera ' . $m[1];
    else $browser = '?';
    
    // OS detection
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
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Keamanan sistem</span>
    <h1>Audit Log</h1>
    <p>Pantau semua aktivitas dan perubahan data dalam sistem.</p>
  </div>
</div>

<div class="tabs" style="margin:0 0 20px">
  <a href="{{ route('admin.audit.index', array_filter(['user_id' => request('user_id'), 'action' => request('action'), 'from' => request('from'), 'to' => request('to')])) }}"
    class="tab-btn {{ $currentRole === '' ? 'active' : '' }}">
    Semua <span class="tab-count">{{ $tabCounts['all'] }}</span>
  </a>
  <a href="{{ route('admin.audit.index', array_filter(['role' => 'admin', 'user_id' => request('user_id'), 'action' => request('action'), 'from' => request('from'), 'to' => request('to')])) }}"
    class="tab-btn {{ $currentRole === 'admin' ? 'active' : '' }}">
    Admin <span class="tab-count">{{ $tabCounts['admin'] }}</span>
  </a>
  <a href="{{ route('admin.audit.index', array_filter(['role' => 'teacher', 'user_id' => request('user_id'), 'action' => request('action'), 'from' => request('from'), 'to' => request('to')])) }}"
    class="tab-btn {{ $currentRole === 'teacher' ? 'active' : '' }}">
    Guru <span class="tab-count">{{ $tabCounts['guru'] }}</span>
  </a>
  <a href="{{ route('admin.audit.index', array_filter(['role' => 'parent', 'user_id' => request('user_id'), 'action' => request('action'), 'from' => request('from'), 'to' => request('to')])) }}"
    class="tab-btn {{ $currentRole === 'parent' ? 'active' : '' }}">
    Orang Tua <span class="tab-count">{{ $tabCounts['parent'] }}</span>
  </a>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    @if ($currentRole)
    <input type="hidden" name="role" value="{{ $currentRole }}">
    @endif
    <div class="field" style="flex:1;min-width:180px">
      <select name="user_id">
        <option value="">Semua Pengguna</option>
        @foreach ($users as $user)
        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name ?: $user->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="field" style="flex:1;min-width:180px">
      <input type="text" name="action" value="{{ request('action') }}" placeholder="Cari aksi...">
    </div>
    <div class="field" style="flex:0;min-width:150px">
      <input type="date" name="from" value="{{ request('from') }}">
    </div>
    <div class="field" style="flex:0;min-width:150px">
      <input type="date" name="to" value="{{ request('to') }}">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Filter</button>
    @if (request()->hasAny(['user_id', 'action', 'from', 'to']))
    <a href="{{ route('admin.audit.index', array_filter(['role' => $currentRole])) }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Waktu</th>
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
            // New format: "Nama (Role)"
            if (preg_match('/^(.+?)\s*\((.+?)\)$/', $log->entity_identifier, $m)) {
              $entityName = trim($m[1]);
              $entityRoleLabel = $m[2];
            }
            // Old format fallback: "Nama|role_key"
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
        @endphp
        <tr>
          @php
          $carbon = $log->created_at;
          $now = now();
          @endphp
          <td style="font-size:.85rem;white-space:nowrap">
            @if ($carbon->isToday())
              <span>{{ $carbon->format('H:i') }} <span style="color:var(--muted)">WIB</span></span>
            @elseif ($carbon->isYesterday())
              <span>{{ $carbon->format('d M H:i') }}</span>
            @else
              <span>{{ $carbon->format('d M Y H:i') }}</span>
            @endif
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <span style="width:28px;height:28px;border-radius:8px;display:grid;place-items:center;background:color-mix(in srgb,{{ $roleColors[$log->user->role ?? 'parent'] ?? '#666' }} 12%,var(--card));color:{{ $roleColors[$log->user->role ?? 'parent'] ?? '#666' }};font-weight:800;font-size:.7rem;flex-shrink:0">{{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}</span>
              <div>
                <span style="font-size:.88rem;display:block">{{ $log->user->name ?? 'System' }}</span>
                @if ($log->user)
                <span style="padding:2px 6px;border-radius:6px;font-size:.68rem;font-weight:700;background:color-mix(in srgb,{{ $roleColors[$log->user->role] ?? '#666' }} 12%,var(--card));color:{{ $roleColors[$log->user->role] ?? '#666' }}">{{ $roleLabels[$log->user->role] ?? $log->user->role }}</span>
                @endif
              </div>
            </div>
          </td>
          <td>
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ $actionColors[$log->action] ?? 'var(--primary-2)' }} 12%,var(--card));color:{{ $actionColors[$log->action] ?? 'var(--primary-2)' }}">{{ $actionLabel }}</span>
          </td>
          <td style="font-size:.82rem">
            @if (str_starts_with($log->action, 'auth.'))
              <span style="color:var(--muted);font-size:.72rem;white-space:nowrap">{{ parseUserAgent($log->user_agent) }}</span>
            @elseif ($log->entity_identifier)
              @if ($entityRoleLabel)
                <span style="color:var(--s-ink);font-weight:500">{{ $entityName }} <span style="color:var(--muted);font-weight:400">({{ $entityRoleLabel }})</span></span>
              @else
                <div style="display:flex;align-items:center;gap:6px">
                  <span style="color:var(--s-ink);font-weight:500">{{ $entityName }}</span>
                  <span style="padding:2px 8px;border-radius:6px;font-size:.7rem;font-weight:600;background:color-mix(in srgb,{{ $entityColors[class_basename($log->entity_type)] ?? '#666' }} 12%,var(--card));color:{{ $entityColors[class_basename($log->entity_type)] ?? '#666' }}">{{ $entityLabels[class_basename($log->entity_type)] ?? class_basename($log->entity_type) }}</span>
                </div>
              @endif
            @else
              <span style="padding:2px 8px;border-radius:6px;font-size:.7rem;font-weight:600;background:color-mix(in srgb,{{ $entityColors[class_basename($log->entity_type)] ?? '#666' }} 12%,var(--card));color:{{ $entityColors[class_basename($log->entity_type)] ?? '#666' }}">{{ $entityLabels[class_basename($log->entity_type)] ?? class_basename($log->entity_type) }}</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" style="text-align:center;padding:30px;color:var(--muted)">Tidak ada log ditemukan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $logs->links('vendor.pagination.admin') }}</div>
</section>
@endsection


