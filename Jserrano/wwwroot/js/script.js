function validarNombre(nombre) {
    // Solo letras, espacios y acentos
    return /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre);
}

function validarCorreo(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validarPassword(password) {
    // Mínimo 8 caracteres, 1 mayúscula, 1 minúscula y 1 número
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(password);
}

function mostrarValido(input) {
    input.removeClass("is-invalid");
    input.addClass("is-valid");
}

function mostrarInvalido(input, mensaje, errorId) {
    input.removeClass("is-valid");
    input.addClass("is-invalid");
    $(errorId).text(mensaje);
}

function registroUsuarios() {
    let nombre = $("#nombre");
    let email = $("#email");
    let password = $("#pwd");

    let nombreValido = validarNombre(nombre.val());
    let emailValido = validarCorreo(email.val());
    let passwordValida = validarPassword(password.val());

    // Validar nombre
    if (!nombreValido) {
        mostrarInvalido(nombre, "El nombre no puede contener números ni caracteres inválidos.", "#nombreError");
    } else {
        mostrarValido(nombre);
    }

    // Validar email
    if (!emailValido) {
        mostrarInvalido(email, "Ingresa un correo con formato válido.", "#emailError");
    } else {
        mostrarValido(email);
    }

    // Validar contraseña
    if (!passwordValida) {
        mostrarInvalido(password, "La contraseña es muy débil.", "#pwdError");
    } else {
        mostrarValido(password);
    }

    // Si algún campo no es válido, NO registrar
    if (!nombreValido || !emailValido || !passwordValida) {
        // Se detiene completamente el registro
        return false;
    }

    // Simulación de correos ya existentes
    const correosExistentes = [
        "admin@gmail.com",
        "usuario@test.com",
        "josue@gmail.com"
    ];

    if (correosExistentes.includes(email.val().toLowerCase())) {
        mostrarInvalido(email, "Este correo ya está registrado.", "#emailError");

        // No continuar con el registro si el correo ya existe
        return false;
    }

    // Envío AJAX si todo está correcto
    let formData = new FormData();
    formData.append("nombre", nombre.val());
    formData.append("email", email.val());
    formData.append("pwd", password.val());

    $.ajax({
        url: "usuarios.php",
        data: formData,
        processData: false,
        contentType: false,
        type: "POST",
        cache: false,
success: function(result) {

    if (result.trim() === "correo_existente") {
        mostrarInvalido(email, "Este correo ya está registrado.", "#emailError");
        return false;
    }

    if (result.trim() === "registro_correcto") {
        alert("Usuario registrado correctamente");
        window.location.href = "login.html";
    }
		},
        error: function(xhr, status) {
            alert("Ocurrió un error al registrar el usuario.");
        }
    });
}

// Validación en tiempo real
$(document).ready(function () {

    $("#nombre").on("input", function () {
        if (validarNombre($(this).val())) {
            mostrarValido($(this));
        } else {
            mostrarInvalido($(this), "El nombre no puede contener números.", "#nombreError");
        }
    });

    $("#email").on("input", function () {
        if (validarCorreo($(this).val())) {
            mostrarValido($(this));
        } else {
            mostrarInvalido($(this), "Correo no válido.", "#emailError");
        }
    });

    $("#pwd").on("input", function () {
        if (validarPassword($(this).val())) {
            mostrarValido($(this));
        } else {
            mostrarInvalido($(this), "La contraseña es débil.", "#pwdError");
        }
    });
});

function login() {

    let email = $("#email");
    let password = $("#pwd");

    // Quitar errores anteriores
    email.removeClass("is-invalid");
    password.removeClass("is-invalid");

    let formData = new FormData();
    formData.append("email", email.val());
    formData.append("pwd", password.val());

    $.ajax({
        url: "login.php",
        data: formData,
        processData: false,
        contentType: false,
        type: "POST",
        cache: false,

        success: function(result) {

            result = result.trim();

            // Correo no registrado
            if (result === "correo_no_registrado") {
                email.addClass("is-invalid");
                $("#emailLoginError").text("El correo no está registrado.");
                return false;
            }

            // Contraseña incorrecta
            if (result === "password_incorrecta") {
                password.addClass("is-invalid");
                $("#pwdLoginError").text("La contraseña y el correo no coinsiden.");
                return false;
            }

            // Login correcto
            if (result === "login_correcto") {
                window.location.href = "../index.html";
            }
        },

        error: function() {
            alert("Ocurrió un error al iniciar sesión.");
        }
    });
}