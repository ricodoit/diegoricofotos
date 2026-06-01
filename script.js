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
