$(document).ready(function() {
    $('#formEditarPlato').on('submit', function(e){
        e.preventDefault();
        var datos = $(this).serialize();

        $.ajax({
        url: '../inc/editar_plato.php',
        method: 'POST',
        data: datos,

        success: function(respuesta){
            if(respuesta === "ok"){
                Swal.fire({
                    title: "¡Guardado!",
                    text: "El plato se ha editado correctamente",
                    icon: "success",
                    confirmButtonText: "Genial"
                }).then(function() {
            window.location.href = "tabla_platos.php";
        });
                $('#formEditarPlato')[0].reset();
            }else {
                Swal.fire({
                    title: "Error",
                    text: "Hubo un error al editar el plato",
                    icon: "error"
                });
            }
        }
    })
    });
});