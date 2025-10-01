import '../css/app.css'

// Placeholder for Echo setup; actual keys configured via .env and bootstrap.
import '../bootstrap/js/echo'

// Theme toggle
const toggle = document.getElementById('theme-toggle')
if (toggle) {
  const apply = (v)=> document.documentElement.classList.toggle('dark', v === 'dark')
  const saved = localStorage.getItem('theme') || 'system'
  if (saved !== 'system') apply(saved)
  toggle.addEventListener('click', ()=>{
    const current = document.documentElement.classList.contains('dark') ? 'dark' : 'light'
    const next = current === 'dark' ? 'light' : 'dark'
    localStorage.setItem('theme', next)
    apply(next)
  })
}

