const weddingDate = new Date("July 10, 2027 11:00:00").getTime();

function updateCountdown() {
  const now = new Date().getTime();
  const distance = weddingDate - now;

  const days = Math.floor(distance / (1000 * 60 * 60 * 24));
  const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

  document.getElementById("days").innerText = days;
  document.getElementById("hours").innerText = hours;
  document.getElementById("minutes").innerText = minutes;
}

setInterval(updateCountdown, 1000);
updateCountdown();
let audio = new Audio("img/musica.mp3");
audio.loop = true;

function toggleMusic() {
  if (audio.paused) {
    audio.play();
  } else {
    audio.pause();
  }
}
function openLightbox(img) {
  const lightbox = document.getElementById("lightbox");
  const lightboxImg = document.getElementById("lightbox-img");

  lightbox.style.display = "flex";
  lightboxImg.src = img.src;
}

function closeLightbox() {
  document.getElementById("lightbox").style.display = "none";
}


const tabs = document.querySelectorAll(".tab-btn");
const contents = document.querySelectorAll(".tab-content");

tabs.forEach(tab => {
  tab.addEventListener("click", () => {

    tabs.forEach(t => t.classList.remove("active"));
    contents.forEach(c => c.classList.remove("active"));

    tab.classList.add("active");

    document.getElementById(tab.dataset.tab).classList.add("active");

  });
});

async function enviarFormulario() {

  const datos = {
    nombre: document.getElementById("nombre")?.value || "",
    email: document.getElementById("email")?.value || "",
    telefono: document.getElementById("telefono")?.value || "",
    servicio: document.getElementById("servicio")?.value || "",
    mensaje: document.getElementById("mensaje")?.value || "",

    fechaEvento: document.getElementById("fechaEvento")?.value || "",
    localizacion: document.getElementById("localizacion")?.value || "",
    estilo: document.getElementById("estilo")?.value || "",

    fechaComunion: document.getElementById("fechaComunion")?.value || "",
    localizacionComunion: document.getElementById("localizacionComunion")?.value || "",

    empresa: document.getElementById("empresa")?.value || "",
    cif: document.getElementById("cif")?.value || "",
    horario: document.getElementById("horario")?.value || "",
    requerimientos: document.getElementById("requerimientos")?.value || "",

    fechaEventoCorporativo: document.getElementById("fechaEventoCorporativo")?.value || "",
    localizacionEvento: document.getElementById("localizacionEvento")?.value || "",
    tipoEvento: document.getElementById("tipoEvento")?.value || ""
  };

  try {

    await fetch(
      "https://script.google.com/macros/s/AKfycbxb1F7TUuNtLBNMIBjijW297IZiFnvvhPD5-4SCxgDowsISCPKMl3PighmQRfTsGLdnzw/exec",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(datos)
      }
    );

    alert("¡Solicitud enviada correctamente!");

  } catch(error) {

    console.error(error);
    alert("Error al enviar el formulario");

  }

}


 // Navbar scroll
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 60));

    // Mobile menu
    document.getElementById('navToggle').addEventListener('click', () => document.getElementById('navLinks').classList.toggle('open'));
    document.querySelectorAll('#navLinks a').forEach(l => l.addEventListener('click', () => document.getElementById('navLinks').classList.remove('open')));

    // Portfolio filter
    const tabs = document.querySelectorAll('.portfolio__tab');
    const items = document.querySelectorAll('.masonry__item');
    tabs.forEach(tab => tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const f = tab.dataset.filter;
      items.forEach(item => item.style.display = (f === 'all' || item.dataset.category === f) ? '' : 'none');
    }));

    // Lightbox
    let lbImages = [], lbIdx = 0;
    const lb = document.getElementById('lightbox'), lbImg = document.getElementById('lightboxImg');
    items.forEach(item => item.addEventListener('click', () => {
      lbImages = [...document.querySelectorAll('.masonry__item:not([style*="display: none"]) img')];
      lbIdx = lbImages.indexOf(item.querySelector('img'));
      lbImg.src = lbImages[lbIdx].src.replace('w=600', 'w=1400');
      lb.classList.add('active'); document.body.style.overflow = 'hidden';
    }));
    function closeLightbox() { lb.classList.remove('active'); document.body.style.overflow = ''; }
    function navLightbox(d) { lbIdx = (lbIdx + d + lbImages.length) % lbImages.length; lbImg.src = lbImages[lbIdx].src.replace('w=600', 'w=1400'); }
    lb.addEventListener('click', e => { if (e.target === lb) closeLightbox(); });
    document.addEventListener('keydown', e => { if (!lb.classList.contains('active')) return; if (e.key==='Escape') closeLightbox(); if (e.key==='ArrowLeft') navLightbox(-1); if (e.key==='ArrowRight') navLightbox(1); });

    // Video Modal
    function openVideoModal(url) { document.getElementById('videoFrame').src = url; document.getElementById('videoModal').classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeVideoModal() { document.getElementById('videoFrame').src = ''; document.getElementById('videoModal').classList.remove('active'); document.body.style.overflow = ''; }
    document.getElementById('videoModal').addEventListener('click', e => { if (e.target === document.getElementById('videoModal')) closeVideoModal(); });

    // Conditional form fields
    function toggleConditionalFields() {
      const v = document.getElementById('servicio').value;
      document.querySelectorAll('.form__conditional').forEach(el => el.classList.remove('visible'));
      if (v) { const t = document.getElementById('fields-' + v); if (t) t.classList.add('visible'); }
    }

    // FAQ accordion
    document.querySelectorAll('.faq-item__question').forEach(btn => btn.addEventListener('click', () => {
      const item = btn.parentElement, was = item.classList.contains('active');
      document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
      if (!was) item.classList.add('active');
    }));

    // Scroll fade-in
    const obs = new IntersectionObserver(entries => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }), { threshold: 0.1 });
    document.querySelectorAll('.fade-in').forEach(el => obs.observe(el));
