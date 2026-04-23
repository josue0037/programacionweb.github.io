let id_actual = 1; // ID inicial

function cargarImagen(direccion) {
    fetch(`slider.php?action=mostrar&id=${id_actual}&dir=${direccion}`)
    .then(res => res.json())
    .then(data => {

        if (data.status === "ok") {
            id_actual = data.id;

            document.getElementById("imagenSlider").src = data.ruta;
        } else {
            console.log("No hay más imágenes");
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