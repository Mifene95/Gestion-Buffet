//CREACION NUEVO PLATO
$(document).ready(function(){
$('#formNuevoPlato').submit(function(e){
    e.preventDefault();

    //SERIALIZE JUNTA TODO
    var datos = $(this).serialize();
    console.log(datos);

    $.ajax ({
        url: '../inc/creacion_platos.php',
        method: 'POST',
        data: datos,

        success: function(respuesta){
            
            if(respuesta === "ok"){
                Swal.fire({
                    title: "¡Guardado!",
                    text: "El plato se ha creado correctamente",
                    icon: "success",
                    confirmButtonText: "Genial"
                });
                //LIMPIAMOS FORM
                $('#formNuevoPlato')[0].reset();
            }else{
                Swal.fire({
                    title: "Error",
                    text: "Hubo un error al crear un plato",
                    icon: "error"
                });

                console.error("Detalle tecnico del error", respuesta)

            }
        }
    })

});

    $('.toggle-password').click(function() {
    const target = $($(this).data('target'));
    const type = target.attr('type') === 'password' ? 'text' : 'password';
    target.attr('type', type);
});


//CREAR NUEVO USUARIO
$('#formNuevoUsuario').submit(function(e){
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
    
    $.ajax({
        url: '../inc/crear_usuario.php',
        method: 'POST',
        data: datos,
        success: function(respuesta){
            if(respuesta === "ok"){
                Swal.fire({
                    title: "¡Guardado!",
                    text: "El Usuario se ha creado correctamente",
                    icon: "success",
                    confirmButtonText: "Genial"
                });
                $('#formNuevoUsuario')[0].reset();
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
                    text: "Hubo un error al crear el usuario",
                    icon: "error"
                });
            }
        }
    });
});

$('#icono-platos').click(function(){
    window.location.href = "../pages/tabla_platos.php";
})

$('#icono-usuarios').click(function(){
    window.location.href = "../pages/gestion_usuarios.php";
})

});
