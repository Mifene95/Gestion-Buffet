$(document).on('click', '#btnGuardarConfig', function() {
    // Recoger todos los horarios
    let turnos = {};

    $('.hora-inicio').each(function() {
        const turno_id = $(this).data('turno-id');
        if (!turnos[turno_id]) turnos[turno_id] = {};
        turnos[turno_id]['hora_inicio'] = $(this).val();
    });

    $('.hora-fin').each(function() {
        const turno_id = $(this).data('turno-id');
        if (!turnos[turno_id]) turnos[turno_id] = {};
        turnos[turno_id]['hora_fin'] = $(this).val();
    });

    $.ajax({
        url: '../inc/guardar_configuracion.php',
        method: 'POST',
        data: { turnos: turnos },
        success: function(respuesta) {
            if (respuesta.trim() === 'ok') {
                Swal.fire('¡Guardado!', 'Horarios actualizados correctamente', 'success');
            } else {
                Swal.fire('Error', respuesta, 'error');
            }
        }
    });
});