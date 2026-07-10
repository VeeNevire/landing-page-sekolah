
const $ = (s, c=document) => c.querySelector(s);
const $$ = (s, c=document) => [...c.querySelectorAll(s)];

const menuBtn = $('#menuBtn');
const navLinks = $('#navLinks');
if(menuBtn && navLinks){
  menuBtn.addEventListener('click', () => navLinks.classList.toggle('open'));
  $$('#navLinks a').forEach(a => a.addEventListener('click', () => navLinks.classList.remove('open')));
}

const themeBtn = $('#themeBtn');
const storedTheme = localStorage.getItem('school-theme');
if(storedTheme === 'dark') document.body.classList.add('dark');
function updateThemeIcon(){
  if(themeBtn) themeBtn.textContent = document.body.classList.contains('dark') ? '☀' : '◐';
}
updateThemeIcon();
themeBtn?.addEventListener('click', () => {
  document.body.classList.toggle('dark');
  localStorage.setItem('school-theme', document.body.classList.contains('dark') ? 'dark' : 'light');
  updateThemeIcon();
});

const backTop = $('#backTop');
window.addEventListener('scroll', () => {
  backTop?.classList.toggle('show', window.scrollY > 500);
});
backTop?.addEventListener('click', () => window.scrollTo({top:0, behavior:'smooth'}));

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if(entry.isIntersecting) entry.target.classList.add('visible');
  });
},{threshold:.12});
$$('.reveal').forEach(el => observer.observe(el));

function animateCounter(el){
  const target = Number(el.dataset.count || 0);
  const suffix = el.dataset.suffix || '';
  let start = 0;
  const duration = 1200;
  const t0 = performance.now();
  function step(now){
    const p = Math.min((now - t0)/duration,1);
    start = Math.floor(target * (1 - Math.pow(1-p,3)));
    el.textContent = start.toLocaleString('id-ID') + suffix;
    if(p<1) requestAnimationFrame(step);
  }
  requestAnimationFrame(step);
}
const statObserver = new IntersectionObserver((entries, obs) => {
  entries.forEach(entry => {
    if(entry.isIntersecting){
      $$('.counter', entry.target).forEach(animateCounter);
      obs.unobserve(entry.target);
    }
  });
});
const statBox = $('.stats-card');
if(statBox) statObserver.observe(statBox);

$$('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    $$('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.dataset.filter;
    $$('.filter-item').forEach(item => {
      item.classList.toggle('hidden', filter !== 'all' && item.dataset.category !== filter);
    });
  });
});

$$('form[data-demo-form]').forEach(form => {
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const notice = $('.notice', form);
    if(notice){
      notice.style.display='block';
      notice.textContent='Terima kasih. Data Anda telah diterima pada mode demo website.';
    }
    form.reset();
  });
});

const current = location.pathname.split('/').pop() || 'index.html';
$$('.nav-links a').forEach(a => {
  if(a.getAttribute('href') === current) a.classList.add('active');
});

$$('.nav-dropdown-trigger').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    btn.nextElementSibling.classList.toggle('show');
  });
});
document.addEventListener('click', () => $$('.nav-dropdown-menu').forEach(m => m.classList.remove('show')));
