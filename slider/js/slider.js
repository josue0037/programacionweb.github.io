let id_actual = 0;

// CARGAR PRIMERA IMAGEN
window.onload = function () {
    cargarInicial();
};

function cargarInicial() {

    fetch("slider.php?action=mostrar_inicial")
    .then(res => res.json())
    .then(data => {

        if (data.status === "ok") {

            id_actual = data.id;

            document.getElementById("imagenSlider").src = data.ruta;
            document.getElementById("imagenSlider").style.display = "block";

            document.getElementById("sinImagenes").style.display = "none";

        } else {

            document.getElementById("imagenSlider").style.display = "none";
            document.getElementById("sinImagenes").style.display = "block";
        }
    });
}

function cargarImagen(direccion) {

    fetch(`slider.php?action=mostrar&id=${id_actual}&dir=${direccion}`)
    .then(res => res.json())
    .then(data => {

        if (data.status === "ok") {

            id_actual = data.id;

            document.getElementById("imagenSlider").src = data.ruta;

        } else {

            document.getElementById("imagenSlider").style.display = "none";
            document.getElementById("sinImagenes").style.display = "block";
        }

    })
    .catch(err => console.error(err));
}

function siguiente() {
    cargarImagen("next");
}

function anterior() {
    cargarImagen("prev");
}