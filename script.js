// ======================================
// CUENTA ATRÁS
// ======================================

const weddingDate = new Date("July 10, 2027 11:00:00").getTime();

function updateCountdown() {
  const now = new Date().getTime();
  const distance = weddingDate - now;

  const days = Math.floor(distance / (1000 * 60 * 60 * 24));
  const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

  const daysEl = document.getElementById("days");
  const hoursEl = document.getElementById("hours");
  const minutesEl = document.getElementById("minutes");

  if (daysEl) daysEl.innerText = days;
  if (hoursEl) hoursEl.innerText = hours;
  if (minutesEl) minutesEl.innerText = minutes;
}

setInterval(updateCountdown, 1000);
updateCountdown();


// ======================================
// MÚSICA
// ======================================

let audio = new Audio("img/musica.mp3");
audio.loop = true;

function toggleMusic() {
  if (audio.paused) {
    audio.play();
  } else {
    audio.pause();
  }
}


// ======================================
// TABS ANTIGUAS (si existen)
// ======================================

const tabButtons = document.querySelectorAll(".tab-btn");
const tabContents = document.querySelectorAll(".tab-content");

tabButtons.forEach(tab => {
  tab.addEventListener("click", () => {

    tabButtons.forEach(t => t.classList.remove("active"));
    tabContents.forEach(c => c.classList.remove("active"));

    tab.classList.add("active");

    const target = document.getElementById(tab.dataset.tab);

    if (target) {
      target.classList.add("active");
    }
  });
});


// ======================================
// FORMULARIO GOOGLE SHEETS
// ======================================

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

    const response = await fetch(
      "https://script.google.com/macros/s/AKfycbxb1F7TUuNtLBNMIBjijW297IZiFnvvhPD5-4SCxgDowsISCPKMl3PighmQRfTsGLdnzw/exec",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(datos)
      }
    );

    if (!response.ok) {
      throw new Error("Error en la petición");
    }

    alert("¡Solicitud enviada correctamente!");

  } catch (error) {

    console.error(error);
    alert("Error al enviar el formulario");

  }
}


// ======================================
// NAVBAR
// ======================================

const navbar = document.getElementById("navbar");

if (navbar) {
  window.addEventListener("scroll", () => {
    navbar.classList.toggle("scrolled", window.scrollY > 60);
  });
}


// ======================================
// MENÚ MÓVIL
// ======================================

const navToggle = document.getElementById("navToggle");
const navLinks = document.getElementById("navLinks");

if (navToggle && navLinks) {

  navToggle.addEventListener("click", () => {
    navLinks.classList.toggle("open");
  });

  document.querySelectorAll("#navLinks a").forEach(link => {
    link.addEventListener("click", () => {
      navLinks.classList.remove("open");
    });
  });
}


// ======================================
// FILTRO PORTFOLIO
// ======================================

const portfolioTabs = document.querySelectorAll(".portfolio__tab");
const items = document.querySelectorAll(".masonry__item");

portfolioTabs.forEach(tab => {

  tab.addEventListener("click", () => {

    portfolioTabs.forEach(t => t.classList.remove("active"));

    tab.classList.add("active");

    const filtro = tab.dataset.filter;

    items.forEach(item => {

      item.style.display =
        filtro === "all" || item.dataset.category === filtro
          ? ""
          : "none";

    });

  });

});


// ======================================
// LIGHTBOX
// ======================================

let lbImages = [];
let lbIdx = 0;

const lb = document.getElementById("lightbox");
const lbImg = document.getElementById("lightboxImg");

if (items.length && lb && lbImg) {

  items.forEach(item => {

    item.addEventListener("click", () => {

      lbImages = [
        ...document.querySelectorAll(
          '.masonry__item:not([style*="display: none"]) img'
        )
      ];

      lbIdx = lbImages.indexOf(item.querySelector("img"));

      lbImg.src = lbImages[lbIdx].src.replace("w=600", "w=1400");

      lb.classList.add("active");

      document.body.style.overflow = "hidden";
    });

  });
}

function closeLightbox() {

  if (!lb) return;

  lb.classList.remove("active");
  document.body.style.overflow = "";
}

function navLightbox(direction) {

  if (!lbImages.length) return;

  lbIdx = (lbIdx + direction + lbImages.length) % lbImages.length;

  lbImg.src = lbImages[lbIdx].src.replace("w=600", "w=1400");
}

if (lb) {

  lb.addEventListener("click", e => {

    if (e.target === lb) {
      closeLightbox();
    }

  });
}

document.addEventListener("keydown", e => {

  if (!lb || !lb.classList.contains("active")) return;

  if (e.key === "Escape") closeLightbox();
  if (e.key === "ArrowLeft") navLightbox(-1);
  if (e.key === "ArrowRight") navLightbox(1);

});


// ======================================
// VÍDEO MODAL
// ======================================

function openVideoModal(url) {

  const frame = document.getElementById("videoFrame");
  const modal = document.getElementById("videoModal");

  if (!frame || !modal) return;

  frame.src = url;

  modal.classList.add("active");

  document.body.style.overflow = "hidden";
}

function closeVideoModal() {

  const frame = document.getElementById("videoFrame");
  const modal = document.getElementById("videoModal");

  if (!frame || !modal) return;

  frame.src = "";

  modal.classList.remove("active");

  document.body.style.overflow = "";
}

const videoModal = document.getElementById("videoModal");

if (videoModal) {

  videoModal.addEventListener("click", e => {

    if (e.target === videoModal) {
      closeVideoModal();
    }

  });

}


// ======================================
// FORMULARIO CONDICIONAL
// ======================================

function toggleConditionalFields() {

  const servicio = document.getElementById("servicio")?.value;

  document.querySelectorAll(".form__conditional").forEach(el => {
    el.classList.remove("visible");
  });

  if (!servicio) return;

  const target = document.getElementById("fields-" + servicio);

  if (target) {
    target.classList.add("visible");
  }
}


// ======================================
// FAQ
// ======================================

document.querySelectorAll(".faq-item__question").forEach(btn => {

  btn.addEventListener("click", () => {

    const item = btn.parentElement;

    const estabaAbierto = item.classList.contains("active");

    document.querySelectorAll(".faq-item").forEach(i => {
      i.classList.remove("active");
    });

    if (!estabaAbierto) {
      item.classList.add("active");
    }

  });

});


// ======================================
// ANIMACIONES SCROLL
// ======================================

const observer = new IntersectionObserver(
  entries => {

    entries.forEach(entry => {

      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
      }

    });

  },
  {
    threshold: 0.1
  }
);

document.querySelectorAll(".fade-in").forEach(el => {
  observer.observe(el);
});
