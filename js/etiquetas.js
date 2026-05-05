const gridOptionsEtiquetas = {
    rowData: [],
    columnDefs: [
        {
            field: "ubicacion",
            headerName: "Mesa/Posición",
            filter: true,
            sortable: true,
            width: 200,
        },
        {
            field: "state",
            headerName: "Estado",
            filter: true,
            sortable: true,
            width: 200,
            cellRenderer: function(params) {
                if (params.value === 1) {
                    return '<span class="badge badge-success p-2">🟢 Conectada</span>';
                } else {
                    return '<span class="badge badge-danger p-2">🔴 Desconectada</span>';
                }
            }
        },
        {
            field: "battery",
            headerName: "Batería",
            filter: true,
            sortable: true,
            width: 200,
            cellRenderer: function(params) {
                const nivel = params.value;
                let color = 'success';
                if (nivel < 30) color = 'danger';
                else if (nivel < 60) color = 'warning';
                return `<span class="badge badge-${color} p-2">🔋 ${nivel}%</span>`;
            }
        },
        {
            field: "apSignal",
            headerName: "Señal",
            filter: true,
            sortable: true,
            width: 150,
            cellRenderer: function(params) {
                return params.value ? `📶 ${params.value}` : '-';
            }
        },
        {
            field: "itemTitle",
            headerName: "Plato Asignado",
            filter: true,
            sortable: true,
            flex: 1,
            cellRenderer: function(params) {
                return params.value ? params.value : '<span class="text-muted">Sin asignar</span>';
            }
        },
        {
            headerName: "Acciones",
            sortable: false,
            filter: false,
            flex: 1,
            cellRenderer: function(params) {
                return `
                    <div style="display: flex; gap: 5px;">
                        <button class="btn btn-sm btn-primary vincular-plato"
                            data-barcode="${params.data.priceTagCode}">
                            <i class="fas fa-link mr-1"></i>Vincular
                        </button>
                        <button class="btn btn-sm btn-danger quitar-plato"
                            data-barcode="${params.data.priceTagCode}">
                            <i class="fas fa-unlink mr-1"></i>Quitar Plato
                        </button>
                        <button class="btn btn-sm btn-warning refrescar-etiqueta"
                            data-barcode="${params.data.priceTagCode}">
                            <i class="fas fa-sync mr-1"></i>Refrescar
                        </button>
                    </div>
                `;
            }
        }
    ],
    pagination: true,
    paginationPageSize: 20
};

let gridApiEtiquetas;

// CARGAR ETIQUETAS (solo una vez definida)
function cargarEtiquetas() {
    $.ajax({
        url: '../inc/get_etiquetas.php',
        method: 'GET',
        success: function(respuesta) {
            console.log("Etiquetas:", respuesta);
            window.todasLasEtiquetas = respuesta;
            gridApiEtiquetas.setGridOption('rowData', respuesta);
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            Swal.fire('Error', 'No se pudieron cargar las etiquetas', 'error');
        }
    });
}

// MOSTRAR PLATOS EN MODAL VINCULAR
function mostrarPlatosEtiqueta(platos) {
    const lista = $('#listaPlatosEtiqueta');
    lista.empty();

    if (platos.length === 0) {
        lista.append('<div class="alert alert-info">No se encontraron platos</div>');
        return;
    }

    platos.forEach(plato => {
        lista.append(`
            <div class="list-group-item list-group-item-action plato-vincular"
                data-id="${plato.id}"
                data-nombre="${plato.nombre_es}"
                style="cursor: pointer; padding: 10px; border-bottom: 1px solid #eee;">
                <strong>${plato.nombre_es}</strong>
            </div>
        `);
    });
}

// MOSTRAR ETIQUETAS EN MODAL ASIGNAR POSICIÓN
function mostrarEtiquetasModal(etiquetas) {
    const lista = $('#listaEtiquetas');
    lista.empty();

    if (!etiquetas || etiquetas.length === 0) {
        lista.append('<div class="alert alert-info">No se encontraron etiquetas</div>');
        return;
    }

    etiquetas.forEach(etiqueta => {
        const estado = etiqueta.state === 1
            ? '<span class="badge badge-success">🟢 Conectada</span>'
            : '<span class="badge badge-danger">🔴 Desconectada</span>';

        const plato = etiqueta.itemTitle
            ? etiqueta.itemTitle
            : '<span class="text-muted">Sin plato</span>';

        lista.append(`
            <div class="list-group-item list-group-item-action etiqueta-seleccionar"
                data-barcode="${etiqueta.priceTagCode}"
                style="cursor: pointer; padding: 10px; border-bottom: 1px solid #eee;">
                <div class="d-flex justify-content-between">
                    <strong>${etiqueta.priceTagCode}</strong>
                    ${estado}
                </div>
                <small class="text-muted">Plato: ${plato}</small>
            </div>
        `);
    });
}

// REFRESCAR TABLA
$(document).on('click', '#btnRefrescar', function() {
    gridApiEtiquetas.setGridOption('rowData', []);
    cargarEtiquetas();
});

// FORZAR REFRESCO TODAS LAS ETIQUETAS
$(document).on('click', '#btnRefrescarEtiquetas', function() {
    Swal.fire({
        title: '¿Forzar refresco?',
        text: 'Se actualizarán todas las etiquetas físicas',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, refrescar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '../inc/forzar_refresco.php',
                method: 'POST',
                success: function(respuesta) {
                    if (respuesta.trim() === 'ok') {
                        Swal.fire('¡Listo!', 'Etiquetas actualizadas', 'success');
                        cargarEtiquetas();
                    } else {
                        Swal.fire('Error', respuesta, 'error');
                    }
                }
            });
        }
    });
});

// FORZAR REFRESCO DE UNA ETIQUETA
$(document).on('click', '.refrescar-etiqueta', function(e) {
    e.stopPropagation();
    const barcode = $(this).data('barcode');

    $.ajax({
        url: '../inc/forzar_refresco.php',
        method: 'POST',
        data: { barcode: barcode },
        success: function(respuesta) {
            if (respuesta.trim() === 'ok') {
                Swal.fire('¡Listo!', 'Etiqueta ' + barcode + ' actualizada', 'success');
            } else {
                Swal.fire('Error', respuesta, 'error');
            }
        }
    });
});

// ABRIR MODAL VINCULAR PLATO
$(document).on('click', '.vincular-plato', function(e) {
    e.stopPropagation();
    const barcode = $(this).data('barcode');
    $('#etiquetaBarcode').val(barcode);
    $('#etiquetaCodigo').text(barcode);
    $('#buscadorPlatosEtiqueta').val('');

    $.ajax({
        url: '../inc/get_todos_platos.php',
        method: 'GET',
        success: function(respuesta) {
            respuesta.sort((a, b) => a.nombre_es.localeCompare(b.nombre_es));
            window.todosLosPlatosEtiqueta = respuesta;
            mostrarPlatosEtiqueta(respuesta.slice(0, 10));
            $('#modalVincularPlato').modal('show');
        }
    });
});

// BUSCADOR PLATOS EN MODAL VINCULAR
$(document).on('keyup', '#buscadorPlatosEtiqueta', function() {
    const termino = $(this).val().toLowerCase();

    if (termino === '') {
        mostrarPlatosEtiqueta(window.todosLosPlatosEtiqueta.slice(0, 10));
    } else {
        const filtrados = window.todosLosPlatosEtiqueta.filter(plato =>
            plato.nombre_es.toLowerCase().includes(termino)
        );
        mostrarPlatosEtiqueta(filtrados);
    }
});

// CLICK EN PLATO → VINCULAR
$(document).on('click', '.plato-vincular', function() {
    const platoId = $(this).data('id');
    const nombrePlato = $(this).data('nombre');
    const etiquetaBarcode = $('#etiquetaBarcode').val();

    Swal.fire({
        title: '¿Vincular plato?',
        html: `¿Asignar <strong>${nombrePlato}</strong> a la etiqueta <strong>${etiquetaBarcode}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, vincular',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '../inc/vincular_etiqueta.php',
                method: 'POST',
                data: {
                    plato_id: platoId,
                    etiqueta_barcode: etiquetaBarcode
                },
                success: function(respuesta) {
                    if (respuesta.trim() === 'ok') {
                        Swal.fire('¡Vinculado!', `${nombrePlato} asignado correctamente`, 'success');
                        $('#modalVincularPlato').modal('hide');
                        cargarEtiquetas();
                    } else {
                        Swal.fire('Error', respuesta, 'error');
                    }
                }
            });
        }
    });
});

// QUITAR PLATO
$(document).on('click', '.quitar-plato', function(e) {
    e.stopPropagation();
    const barcode = $(this).data('barcode');

    Swal.fire({
        title: '¿Quitar plato?',
        text: 'La etiqueta mostrará "Sin Plato"',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '../inc/quitar_plato_etiqueta.php',
                method: 'POST',
                data: { etiqueta_barcode: barcode },
                success: function(respuesta) {
                    if (respuesta.trim() === 'ok') {
                        Swal.fire('¡Listo!', 'Etiqueta actualizada', 'success');
                        cargarEtiquetas();
                    } else {
                        Swal.fire('Error', respuesta, 'error');
                    }
                }
            });
        }
    });
});

// ABRIR MODAL 1 - ASIGNAR POSICIÓN
$(document).on('click', '#btnAsignarPosicion', function() {
    $('#buscadorEtiquetas').val('');
    mostrarEtiquetasModal(window.todasLasEtiquetas);
    $('#modalSeleccionarEtiqueta').modal('show');
});

// BUSCADOR ETIQUETAS EN MODAL 1
$(document).on('keyup', '#buscadorEtiquetas', function() {
    const termino = $(this).val().toLowerCase();

    if (termino === '') {
        mostrarEtiquetasModal(window.todasLasEtiquetas);
    } else {
        const filtradas = window.todasLasEtiquetas.filter(e =>
            e.priceTagCode.toLowerCase().includes(termino) ||
            (e.itemTitle && e.itemTitle.toLowerCase().includes(termino))
        );
        mostrarEtiquetasModal(filtradas);
    }
});

// CLICK EN ETIQUETA → ABRIR MODAL 2
$(document).on('click', '.etiqueta-seleccionar', function() {
    const barcode = $(this).data('barcode');
    $('#etiquetaBarcodeAsignar').val(barcode);
    $('#etiquetaSeleccionada').text(barcode);
    $('#selectMesa').val('');
    $('#selectPosicion').html('<option value="">Selecciona primero una mesa</option>');

    $('#modalSeleccionarEtiqueta').modal('hide');
    $('#modalAsignarPosicion').modal('show');
});

// VOLVER AL MODAL 1
$(document).on('click', '#btnVolverEtiquetas', function() {
    $('#modalAsignarPosicion').modal('hide');
    $('#modalSeleccionarEtiqueta').modal('show');
});

// CARGAR POSICIONES AL CAMBIAR MESA
$(document).on('change', '#selectMesa', function() {
    const mesa_id = $(this).val();
    const select = $('#selectPosicion');
    select.html('<option value="">Cargando...</option>');

    if (!mesa_id) {
        select.html('<option value="">Selecciona primero una mesa</option>');
        return;
    }

    $.ajax({
        url: '../inc/get_posiciones_mesa.php',
        method: 'GET',
        data: { mesa_id: mesa_id },
        success: function(respuesta) {
            select.html('<option value="">Selecciona una posición</option>');
            respuesta.forEach(function(pos) {
                select.append(`<option value="${pos.id}">${pos.posicion}</option>`);
            });
        }
    });
});

// GUARDAR POSICIÓN
$(document).on('click', '#btnGuardarPosicion', function() {
    const barcode = $('#etiquetaBarcodeAsignar').val();
    const mesa_id = $('#selectMesa').val();
    const posicion_id = $('#selectPosicion').val();
    const mesa_nombre = $('#selectMesa option:selected').text();
    const posicion_nombre = $('#selectPosicion option:selected').text();

    if (!mesa_id || !posicion_id) {
        Swal.fire('Error', 'Selecciona mesa y posición', 'error');
        return;
    }

    $.ajax({
        url: '../inc/asignar_posicion_etiqueta.php',
        method: 'POST',
        data: {
            barcode: barcode,
            posicion_id: posicion_id,
            nombre_posicion: mesa_nombre.replace(/ /g, '_') + '_' + posicion_nombre
        },
        success: function(respuesta) {
            if (respuesta.trim() === 'ok') {
                Swal.fire('¡Guardado!', 'Posición asignada correctamente', 'success');
                $('#modalAsignarPosicion').modal('hide');
                cargarEtiquetas();
            } else {
                Swal.fire('Error', respuesta, 'error');
            }
        }
    });
});

// INIT
$(window).on('load', function() {
    const gridDiv = document.querySelector('#myGridEtiquetas');
    if (gridDiv) {
        gridApiEtiquetas = agGrid.createGrid(gridDiv, gridOptionsEtiquetas);
    }
    cargarEtiquetas();
});