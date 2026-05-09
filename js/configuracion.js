$(document).on('click', '.btn-guardar-dia', function () {
    const dia = $(this).data('dia');
    const tab = $('#tab-dia-' + dia);
    let turnos = {};

    tab.find('.hora-inicio').each(function () {
        const tid = $(this).data('turno-id');
        if (!turnos[tid]) turnos[tid] = {};
        turnos[tid]['hora_inicio'] = $(this).val();
    });

    tab.find('.hora-fin').each(function () {
        const tid = $(this).data('turno-id');
        if (!turnos[tid]) turnos[tid] = {};
        turnos[tid]['hora_fin'] = $(this).val();
    });

    $.ajax({
        url: '../inc/guardar_configuracion.php',
        method: 'POST',
        data: { dia_semana: dia, turnos: turnos },
        success: function (respuesta) {
            if (respuesta.trim() === 'ok') {
                Swal.fire('¡Guardado!', 'Horarios actualizados correctamente', 'success');
            } else {
                Swal.fire('Error', respuesta, 'error');
            }
        }
    });
});
