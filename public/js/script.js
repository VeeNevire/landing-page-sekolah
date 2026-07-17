
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
  if(!themeBtn) return;
  const dark = document.body.classList.contains('dark');
  themeBtn.innerHTML = dark
    ? '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>'
    : '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>';
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
  entries.forEach((entry, i) => {
    if(entry.isIntersecting){
      setTimeout(() => entry.target.classList.add('visible'), entry.target.dataset.delay || 0);
      observer.unobserve(entry.target);
    }
  });
},{threshold:.08, rootMargin:'0px 0px -40px 0px'});
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
