const gridOptionsEtiquetas = {
    rowData: [],
    columnDefs: [
        {
            field: "priceTagCode",
            headerName: "Código Etiqueta",
            filter: true,
            sortable: true,
            width: 150
        },
        {
            field: "state",
            headerName: "Estado",
            filter: true,
            sortable: true,
            width: 130,
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
            width: 100,
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
            width: 100,
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
    width: 340,
    cellRenderer: function(params) {
    return `
        <div style="display: flex; gap: 5px;">
            <button class="btn btn-sm btn-primary vincular-plato"
                data-barcode="${params.data.priceTagCode}">
                <i class="fas fa-link"></i> Vincular
            </button>
            <button class="btn btn-sm btn-danger desvincular-plato"
                data-barcode="${params.data.priceTagCode}">
                <i class="fas  mr-1 fa-unlink"></i>DesVincular
            </button>
            <button class="btn btn-sm btn-warning refrescar-etiqueta"
                data-barcode="${params.data.priceTagCode}">
                <i class="fas mr-1 fa-sync"></i>Refrescar
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

// CARGAR ETIQUETAS
function cargarEtiquetas() {
    $.ajax({
        url: '../inc/get_etiquetas.php',
        method: 'GET',
        success: function(respuesta) {
            console.log("Etiquetas:", respuesta);
            gridApiEtiquetas.setGridOption('rowData', respuesta);
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            Swal.fire('Error', 'No se pudieron cargar las etiquetas', 'error');
        }
    });
}
// FORZAR REFRESCO ETIQUETAS FÍSICAS
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
})

// FORZAR REFRESCO DE UNA ETIQUETA
$(document).on('click', '.refrescar-etiqueta', function() {
    const barcode = $(this).data('barcode');

    $.ajax({
        url: '../inc/forzar_refresco.php',
        method: 'POST',
        data: { barcode: barcode },  // ← Manda el barcode específico
        success: function(respuesta) {
            if (respuesta.trim() === 'ok') {
                Swal.fire('¡Listo!', 'Etiqueta actualizada', 'success');
            } else {
                Swal.fire('Error', respuesta, 'error');
            }
        }
    });
});

// MOSTRAR PLATOS EN MODAL
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
                data-barcode="PLATO_${plato.id}"
                style="cursor: pointer; padding: 10px; border-bottom: 1px solid #eee;">
                <strong>${plato.nombre_es}</strong>
            </div>
        `);
    });
}

// ABRIR MODAL VINCULAR
$(document).on('click', '.vincular-plato', function() {
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

// BUSCADOR EN TIEMPO REAL
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

// VINCULAR CON ETIQUETA
$(document).on('click', '.plato-vincular', function() {
    const platoId = $(this).data('id');
    const nombrePlato = $(this).data('nombre');
    const platoBarcode = $(this).data('barcode');
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

// DESVINCULAR ETIQUETA
$(document).on('click', '.desvincular-plato', function(e) {
    e.stopPropagation();
    e.preventDefault();
    
    const barcode = $(this).data('barcode');

    Swal.fire({
        title: '¿Desvincular etiqueta?',
        text: 'La etiqueta quedará sin plato asignado',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, desvincular',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '../inc/desvincular_etiqueta.php',
                method: 'POST',
                data: { barcode: barcode },
                success: function(respuesta) {
                    if (respuesta.trim() === 'ok') {
                        Swal.fire('¡Desvinculada!', 'Etiqueta sin plato asignado', 'success');
                        cargarEtiquetas();
                    } else {
                        Swal.fire('Error', respuesta, 'error');
                    }
                }
            });
        }
    });
});

// REFRESCAR TABLA
$(document).on('click', '#btnRefrescar', function() {
    gridApiEtiquetas.setGridOption('rowData', []);  
    cargarEtiquetas();  
});

// INIT
$(window).on('load', function() {
    const gridDiv = document.querySelector('#myGridEtiquetas');
    if (gridDiv) {
        gridApiEtiquetas = agGrid.createGrid(gridDiv, gridOptionsEtiquetas);
    }
    cargarEtiquetas();
});