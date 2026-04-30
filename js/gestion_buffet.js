const gridOptionsBuffet = {
    rowData: [],
    columnDefs: [
        {
            field: "seccion",
            headerName: "Sección/Posición",
            filter: true,
            sortable: true,
            width: 200,
        },
        {
            field: "desayuno",
            headerName: "Turno Desayuno",
            filter: true,
            sortable: true,
            flex: 1
        },
        {
            field: "comida",
            headerName: "Turno Comida",
            filter: true,
            sortable: true,
            flex: 1
        },
        {
            field: "cena",
            headerName: "Turno Cena",
            filter: true,
            sortable: true,
            flex: 1
        },
        {
            headerName: "Acciones",
            field: "posicion_id",
            width: 200,
            sortable: false,
            filter: false,
            cellRenderer: function(params) {
                if (ROL_USUARIO === 1) {
                    return `
                        <div style="display: flex; gap: 5px;">
                            <button class="btn btn-sm btn-primary editar-posicion" data-posicion-id="${params.data.posicion_id}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger borrar-posicion" data-posicion-id="${params.data.posicion_id}">
                                <i class="fas fa-trash"></i> Limpiar
                            </button>
                        </div>
                    `;
                }
                return '';
            }
        }
    ],
    pagination: true,
    paginationPageSize: 50,
    paginationPageSizeSelector: [10, 20, 50, 100]
};

let gridApiBuffet;

function cargarBuffet() {
    $.ajax({
        url: '../inc/get_buffet_nuevo.php',
        method: 'GET',
        success: function(respuesta) {
            console.log("Buffet cargado:", respuesta);
            const datosFormateados = respuesta.map(item => ({
                posicion_id: item.posicion_id,
                mesa_id: item.mesa_id,
                posicion: item.posicion,
                seccion: item.seccion,
                desayuno: item.turnos[1]?.plato_nombre || '-',
                comida: item.turnos[2]?.plato_nombre || '-',
                cena: item.turnos[3]?.plato_nombre || '-',
                turnos: item.turnos
            }));
            gridApiBuffet.setGridOption('rowData', datosFormateados);
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar buffet:", error);
        }
    });
}

// EDITAR POSICIÓN - SELECCIONAR TURNO
$(document).on('click', '.editar-posicion', function(e) {
    e.preventDefault();
    const posicion_id = $(this).data('posicion-id');
    
    let html = `
        <div class="list-group" id="selectorTurnos">
            <button type="button" class="list-group-item list-group-item-action seleccionar-turno" data-turno-id="1">
                <i class="fas fa-sun mr-2"></i> Editar Desayuno
            </button>
            <button type="button" class="list-group-item list-group-item-action seleccionar-turno" data-turno-id="2">
                <i class="fas fa-utensils mr-2"></i> Editar Comida
            </button>
            <button type="button" class="list-group-item list-group-item-action seleccionar-turno" data-turno-id="3">
                <i class="fas fa-moon mr-2"></i> Editar Cena
            </button>
        </div>
    `;
    
    Swal.fire({
        title: 'Selecciona un Turno',
        html: html,
        icon: 'info',
        showConfirmButton: false,
        width: '500px'
    });
    
    // Evento click en un turno
    $(document).on('click', '.seleccionar-turno', function() {
        const turno_id = $(this).data('turno-id');
        const turnoNombre = $(this).text().trim();
        
        // Carga todos los platos disponibles
        $.ajax({
            url: '../inc/get_todos_platos.php',
            method: 'GET',
            success: function(respuesta) {
                let html = `
                    <form id="formAsignarPlato">
                        <div class="mb-3">
                            <label><strong>Selecciona un plato para ${turnoNombre}:</strong></label>
                            <div class="list-group" style="max-height: 300px; overflow-y: auto;">
                `;
                
                respuesta.forEach(plato => {
                    html += `
                        <div class="list-group-item">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input plato-radio" 
                                    id="plato_${plato.id}" 
                                    name="plato_id"
                                    value="${plato.id}">
                                <label class="custom-control-label" for="plato_${plato.id}">
                                    ${plato.nombre_es}
                                </label>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                            </div>
                        </div>
                        
                        <input type="hidden" name="posicion_id" value="${posicion_id}">
                        <input type="hidden" name="turno_id" value="${turno_id}">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save"></i> Asignar
                        </button>
                    </form>
                `;
                
                Swal.fire({
                    title: turnoNombre,
                    html: html,
                    icon: 'info',
                    showConfirmButton: false,
                    width: '600px'
                });
                
                // Evento submit del form
                $(document).on('submit', '#formAsignarPlato', function(e) {
                    e.preventDefault();
                    
                    const plato_id = $('input[name="plato_id"]:checked').val();
                    
                    if (!plato_id) {
                        Swal.fire('Error', 'Selecciona un plato', 'error');
                        return;
                    }
                    
                    const datos = {
                        posicion_id: $('input[name="posicion_id"]').val(),
                        plato_id: plato_id,
                        turno_id: $('input[name="turno_id"]').val()
                    };
                    
                    console.log("Datos a enviar:", datos);
                    
                    $.ajax({
                        url: '../inc/asignar_plato_posicion.php',
                        method: 'POST',
                        data: datos,
                        success: function(respuesta) {
                            console.log("Respuesta:", respuesta);
                            if (respuesta.trim() === 'ok') {
                                Swal.fire('¡Guardado!', 'Plato asignado correctamente', 'success');
                                cargarBuffet();
                            } else {
                                Swal.fire('Error', respuesta, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error AJAX:", error);
                            Swal.fire('Error', 'No se pudo guardar: ' + xhr.responseText, 'error');
                        }
                    });
                });
            }
        });
    });
});

// BORRAR/LIMPIAR POSICIÓN
$(document).on('click', '.borrar-posicion', function(e) {
    e.preventDefault();
    const posicion_id = $(this).data('posicion-id');
    
    Swal.fire({
        title: "¿Limpiar posición?",
        text: "Se eliminarán todos los platos asignados en esta posición",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, limpiar"
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '../inc/limpiar_posicion.php',
                method: 'POST',
                data: { posicion_id: posicion_id },
                success: function(respuesta) {
                    if(respuesta.trim() === 'ok') {
                        Swal.fire("¡Limpiada!", "", "success");
                        cargarBuffet();
                    } else {
                        Swal.fire("Error", respuesta, "error");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error); 
                }
            })
        }
    })
});

$(window).on('load', function() {
    const gridDiv = document.querySelector('#myGridBuffet');
    if (gridDiv) {
        gridApiBuffet = agGrid.createGrid(gridDiv, gridOptionsBuffet);
    }
    cargarBuffet();
});