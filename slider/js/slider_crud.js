$(document).ready(function () {
    // Ocultar formulario si NO es admin
    if (USER_ROLE !== "admin") {
        $("#formSlider").hide();
    }
    // CARGAR LISTA AL INICIO
    cargar();

    $("#formSlider").submit(function (e) {

        e.preventDefault();

        let nombre = $("#nombre");
        let imagen = $("#imagen")[0].files[0];

        let formData = new FormData();
        formData.append("nombre", nombre.val());
        formData.append("imagen", imagen);

        $.ajax({
            url: "../slider.php?action=create", // IMPORTANTE
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,

            success: function (response) {

                response = response.trim();

                if (response === "no_sesion") {
                    alert("Sesión expirada");
                    window.location.href = "../Jserrano/login.html";
                    return;
                }

                if (response === "no_autorizado") {
                    alert("No tienes permisos");
                    return;
                }

                if (response === "imagen_duplicada") {
                    $("#imagen").addClass("is-invalid");
                    $("#imgError").text("Esta imagen ya existe.");
                    return;
                }

                if (response === "tipo_no_valido") {
                    $("#imagen").addClass("is-invalid");
                    $("#imgError").text("Solo JPG, PNG o WEBP.");
                    return;
                }

                if (response === "archivo_grande") {
                    $("#imagen").addClass("is-invalid");
                    $("#imgError").text("Máx 2MB.");
                    return;
                }

                if (response === "error_archivo") {
                    $("#imagen").addClass("is-invalid");
                    $("#imgError").text("Error al subir.");
                    return;
                }

                if (response === "ok") {

                    alert("Imagen subida correctamente");

                    $("#formSlider")[0].reset();

                    cargar(); // recargar lista
                }

                if (response === "reactivada") {
                    alert("Imagen reactivada correctamente");
                    $("#formSlider")[0].reset();
                    cargar();
                }
            }
        });

    });

});

//  CARGAR IMÁGENES
function cargar() {

    fetch("slider.php?action=read")
    .then(res => res.json())
    .then(data => {

        let html = "";

        data.forEach(item => {

            html += `
                <div class="mb-3 border p-2">

                    <img src="${item.ruta}" width="200">
            `;

            // SOLO ADMIN PUEDE EDITAR
            if (USER_ROLE === "admin") {

                html += `
                    <input 
                        type="text" 
                        value="${item.nombre}" 
                        class="form-control mt-2"
                        onchange="editar(${item.id}, this.value)"
                    >

                    <button class="btn btn-danger mt-2" 
                        onclick="desactivar(${item.id})">
                        Eliminar
                    </button>
                `;
            }

            html += `</div>`;
        });

        document.getElementById("lista").innerHTML = html;
    });
}

function editar(id, nombre) {

    let formData = new FormData();
    formData.append("id", id);
    formData.append("nombre", nombre);

    fetch("slider.php?action=update", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(res => {

        if (res.trim() !== "ok") {
            alert("Error al actualizar");
        }
    });
}

function desactivar(id) {

    let formData = new FormData();
    formData.append("id", id);
    formData.append("modo", "soft");

    fetch("slider.php?action=delete", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(res => {
        if (res.includes("desactivado")) {
            cargar();
        }
    });
}