
$(document).on('click', '#btnGuardarConfig', function(){
    let turnos = {};
    
    $('.hora-inicio').each(function(){
        const turno_id = $(this).data('turno-id');
        console.log(turno_id);
    })
})
