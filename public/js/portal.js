
const portalSidebar = document.getElementById('portalSidebar');
const guruSidebar = document.getElementById('guruSidebar');

document.getElementById('portalMenuButton')?.addEventListener('click', () => {
  portalSidebar?.classList.toggle('open');
});

document.getElementById('guruMenuButton')?.addEventListener('click', () => {
  guruSidebar?.classList.toggle('open');
});

document.addEventListener('click', (event) => {
  if (
    (portalSidebar?.classList.contains('open') &&
     !portalSidebar.contains(event.target) &&
     !event.target.closest('#portalMenuButton')) ||
    (guruSidebar?.classList.contains('open') &&
     !guruSidebar.contains(event.target) &&
     !event.target.closest('#guruMenuButton'))
  ) {
    portalSidebar?.classList.remove('open');
    guruSidebar?.classList.remove('open');
  }
});

document.getElementById('studentSwitcher')?.addEventListener('change', (event) => {
  const url = new URL(window.location.href);
  url.searchParams.set('student_id', event.target.value);
  window.location.href = url.toString();
});

document.getElementById('printReport')?.addEventListener('click', () => window.print());

document.getElementById('subjectFilter')?.addEventListener('change', (event) => {
  const selected = event.target.value;
  document.querySelectorAll('[data-subject-row]').forEach(row => {
    row.hidden = selected !== 'all' && row.dataset.subjectRow !== selected;
  });
  document.querySelectorAll('[data-subject-detail]').forEach(row => {
    row.hidden = selected !== 'all' && row.dataset.subjectDetail !== selected;
  });
});

function updateClock() {
  var now = new Date();
  var h = String(now.getHours()).padStart(2,'0');
  var m = String(now.getMinutes()).padStart(2,'0');
  var el = document.getElementById('portalClock');
  if(el) el.textContent = h+':'+m;
}
setInterval(updateClock,1000);
updateClock();

document.querySelectorAll('[data-toggle-password]').forEach(button => {
  button.addEventListener('click', () => {
    const input = document.getElementById(button.dataset.togglePassword);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    button.textContent = input.type === 'password' ? 'Tampilkan' : 'Sembunyikan';
  });
});
