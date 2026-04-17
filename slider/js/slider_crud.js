$(document).ready(function () {

    $("#formSlider").submit(function (e) {

        e.preventDefault();

        let nombre = $("#nombre");
        let imagen = $("#imagen")[0].files[0];

        let formData = new FormData();
        formData.append("nombre", nombre.val());
        formData.append("imagen", imagen);

        $.ajax({
            url: "slider.php?action=create",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,

            success: function (response) {

                response = response.trim();

                // 🔴 Imagen duplicada
                if (response === "imagen_duplicada") {
                    $("#imagen").addClass("is-invalid");
                    $("#imgError").text("Esta imagen ya existe.");
                    return;
                }

                // 🔴 Tipo inválido
                if (response === "tipo_no_valido") {
                    $("#imagen").addClass("is-invalid");
                    $("#imgError").text("Solo se permiten JPG, PNG o WEBP.");
                    return;
                }

                // 🔴 Archivo grande
                if (response === "archivo_grande") {
                    $("#imagen").addClass("is-invalid");
                    $("#imgError").text("La imagen es demasiado grande (máx 2MB).");
                    return;
                }

                // 🔴 Error archivo
                if (response === "error_archivo") {
                    $("#imagen").addClass("is-invalid");
                    $("#imgError").text("Error al subir la imagen.");
                    return;
                }

                // 🟢 Todo correcto
                if (response === "ok") {

                    $("#imagen").removeClass("is-invalid");
                    $("#imagen").addClass("is-valid");

                    alert("Imagen subida correctamente");

                    // limpiar formulario
                    $("#formSlider")[0].reset();

                    // recargar lista si tienes CRUD
                    if (typeof cargar === "function") {
                        cargar();
                    }
                }
            },

            error: function () {
                alert("Error en la petición AJAX");
            }
        });

    });

});