$('#formEditarUsuario').submit(function(e){
    e.preventDefault();

    var pass = $('#password').val();
    var passConfirm = $('#password_confirm').val();

    if (pass !== passConfirm) {
        Swal.fire({
            title: "Las contraseñas no coinciden",
            text: "Por favor, verifica que ambas contraseñas sean iguales.",
            icon: "warning",
            confirmButtonText: "Reintentar"
        });
        return; 
    }

    var datos = $(this).serialize();
    console.log(datos)
    $.ajax({
        url: '../inc/editar_usuario.php',
        method: 'POST',
        data: datos,

        success: function(respuesta){
            if(respuesta === "ok"){
                Swal.fire({
                    title: "¡Guardado!",
                    text: "El Usuario se ha editado correctamente",
                    icon: "success",
                    confirmButtonText: "Genial"
                }).then(function() {
            window.location.href = "gestion_usuarios.php";
        });
                $('#formEditarUsuario')[0].reset();
                
            } else if (res === "pass_mismatch") {
                
                Swal.fire({
                    title: "Las contraseñas no coinciden",
                    text: "Por favor, asegúrate de escribir la misma contraseña en ambos campos.",
                    icon: "warning",
                    confirmButtonText: "Corregir"
                });
            }else {
                Swal.fire({
                    title: "Error",
                    text: "Hubo un error al editar el usuario",
                    icon: "error"
                });
            }
        }
    })
});

