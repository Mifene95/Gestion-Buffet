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
        }
    ],
};

let gridApiBuffet;

// CARGAR BUFFET CON FILTRO
function cargarBuffet() {
    aplicarFiltro();
}

// FILTRAR POR MESA
function aplicarFiltro() {
    const mesaSeleccionada = $('#filtroMesa').val();
    
    $.ajax({
        url: '../inc/get_buffet_nuevo.php',
        method: 'GET',
        success: function(respuesta) {
            console.log("Buffet cargado:", respuesta);
            
            let datosFormateados = respuesta.map(item => ({
                posicion_id: item.posicion_id,
                mesa_id: item.mesa_id,
                posicion: item.posicion,
                seccion: item.seccion,
                desayuno: item.turnos[1]?.plato_nombre || '-',
                comida: item.turnos[2]?.plato_nombre || '-',
                cena: item.turnos[3]?.plato_nombre || '-',
                turnos: item.turnos
            }));
            
            // APLICAR FILTRO SI SE SELECCIONA MESA
            if (mesaSeleccionada) {
                datosFormateados = datosFormateados.filter(item => 
                    item.mesa_id == mesaSeleccionada
                );
            }
            
            gridApiBuffet.setGridOption('rowData', datosFormateados);
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar buffet:", error);
        }
    });
}

// EVENTO DEL FILTRO
$(document).on('change', '#filtroMesa', function() {
    aplicarFiltro();
});

// EDITAR POSICIÓN - SELECCIONAR TURNO
$(document).on('click', '.editar-posicion', function(e) {
    e.preventDefault();
    const posicion_id = $(this).data('posicion-id');
    
    let html = `
        <div class="list-group" id="selectorTurnos">
            <button type="button" class="list-group-item list-group-item-action seleccionar-turno" data-turno-id="1" data-posicion-id="${posicion_id}" data-turno-nombre="Desayuno">
                <i class="fas fa-sun mr-2"></i> Editar Desayuno
            </button>
            <button type="button" class="list-group-item list-group-item-action seleccionar-turno" data-turno-id="2" data-posicion-id="${posicion_id}" data-turno-nombre="Comida">
                <i class="fas fa-utensils mr-2"></i> Editar Comida
            </button>
            <button type="button" class="list-group-item list-group-item-action seleccionar-turno" data-turno-id="3" data-posicion-id="${posicion_id}" data-turno-nombre="Cena">
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
});

// EVENTO DEL SELECTOR DE TURNO
$(document).on('click', '.seleccionar-turno', function() {
    const turno_id = $(this).data('turno-id');
    const posicion_id = $(this).data('posicion-id');
    const turnoNombre = $(this).data('turno-nombre');
    
    Swal.close();
    $('#turno_id').val(turno_id);
    $('#posicion_id').val(posicion_id);
    $('#modalTituloTurno').text(turnoNombre);
    
    $.ajax({
        url: '../inc/get_todos_platos.php',
        method: 'GET',
        success: function(respuesta) {
            respuesta.sort((a, b) => a.nombre_es.localeCompare(b.nombre_es));
            window.todosLosPlatos = respuesta;
            
            const listaPlatos = $('#listaPlatos');
            listaPlatos.empty();
            
            respuesta.slice(0, 10).forEach(plato => {
                listaPlatos.append(`
                    <div class="list-group-item" style="padding: 10px; border-bottom: 1px solid #eee;">
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
                `);
            });
            
            $('#modalSeleccionarPlato').modal('show');
        }
    });
});

// Evento del buscador en el modal
$(document).on('keyup', '#buscadorPlatos', function() {
    const termino = $(this).val().toLowerCase();
    const listaPlatos = $('#listaPlatos');
    listaPlatos.empty();
    
    if (termino === '') {
        window.todosLosPlatos.slice(0, 10).forEach(plato => {
            listaPlatos.append(`
                <div class="list-group-item" style="padding: 10px; border-bottom: 1px solid #eee;">
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
            `);
        });
    } else {
        let resultados = 0;
        window.todosLosPlatos.forEach(plato => {
            if (plato.nombre_es.toLowerCase().includes(termino)) {
                listaPlatos.append(`
                    <div class="list-group-item" style="padding: 10px; border-bottom: 1px solid #eee;">
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
                `);
                resultados++;
            }
        });
        
        if (resultados === 0) {
            listaPlatos.append(`<div class="alert alert-info">No se encontraron platos</div>`);
        }
    }
});

// Evento del botón Asignar en el modal
$(document).on('click', '#btnAsignarPlato', function() {
    const plato_id = $('input[name="plato_id"]:checked').val();
    
    if (!plato_id) {
        Swal.fire('Error', 'Selecciona un plato', 'error');
        return;
    }
    
    const datos = {
        posicion_id: $('#posicion_id').val(),
        plato_id: plato_id,
        turno_id: $('#turno_id').val()
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
                $('#modalSeleccionarPlato').modal('hide');
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

// Evento del botón "Cambiar Turno"
$(document).on('click', '#btnCambiarTurno', function() {
    $('#modalSeleccionarPlato').modal('hide');
    const posicion_id = $('#posicion_id').val();
    
    let html = `
        <div class="list-group" id="selectorTurnos">
            <button type="button" class="list-group-item list-group-item-action seleccionar-turno" data-turno-id="1" data-posicion-id="${posicion_id}" data-turno-nombre="Desayuno">
                <i class="fas fa-sun mr-2"></i> Editar Desayuno
            </button>
            <button type="button" class="list-group-item list-group-item-action seleccionar-turno" data-turno-id="2" data-posicion-id="${posicion_id}" data-turno-nombre="Comida">
                <i class="fas fa-utensils mr-2"></i> Editar Comida
            </button>
            <button type="button" class="list-group-item list-group-item-action seleccionar-turno" data-turno-id="3" data-posicion-id="${posicion_id}" data-turno-nombre="Cena">
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
});

$(window).on('load', function() {
    const gridDiv = document.querySelector('#myGridBuffet');
    if (gridDiv) {
        gridApiBuffet = agGrid.createGrid(gridDiv, gridOptionsBuffet);
    }
    cargarBuffet();
});