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
    var datos = $(this).serialize();
    console.log(datos);

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
                //LIMPIAMOS FORM
                $('#formNuevoUsuario')[0].reset();
            }else{
                Swal.fire({
                    title: "Error",
                    text: "Hubo un error al crear el usuario",
                    icon: "error"
                });

                console.error("Detalle tecnico del error", respuesta)

            }
        }

    })
})
});
