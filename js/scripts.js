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

});
