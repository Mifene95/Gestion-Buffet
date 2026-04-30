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
    field: "id",
    width: 200,
    sortable: false,
    filter: false,
    cellRenderer: function(params) {
        if (ROL_USUARIO === 1 && params.data.plato_ids && params.data.plato_ids.length > 0) {
            return `
                <div style="display: flex; gap: 5px;">
                    <a href="editar_buffet.php?ids=${params.data.plato_ids.join(',')}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button class="btn btn-sm btn-danger borrar-plato" data-plato-ids="${params.data.plato_ids.join(',')}">
                        <i class="fas fa-trash"></i> Borrar
                    </button>
                </div>
            `;
        } else if (ROL_USUARIO === 1) {
            return `<span class="badge badge-warning">Sin platos</span>`;
        }
        return '';
    }
}
    ],
    pagination: true,
    paginationPageSize: 10,
    paginationPageSizeSelector: [10, 20, 50, 100]
};

let gridApiBuffet;

function cargarBuffet() {
    $.ajax({
        url: '../inc/get_buffet.php',
        method: 'GET',
        success: function(respuesta) {
            const datosFormateados = respuesta.map(item => ({
                mesa_id: item.mesa_id,
                posicion: item.posicion,
                seccion: item.mesa + ' / ' + item.posicion,
                desayuno: item.turnos[1]?.plato_nombre || '-',
                comida: item.turnos[2]?.plato_nombre || '-',
                cena: item.turnos[3]?.plato_nombre || '-',
                plato_ids: item.plato_ids
            }));
            gridApiBuffet.setGridOption('rowData', datosFormateados);
        }
    });
}

$(document).ready(function() {
    $('#formEditarBuffet').on('submit', function(e) {
        e.preventDefault();
        var datos = $(this).serialize();

        $.ajax({
            url: '../inc/editar_buffet.php',
            method: 'POST',
            data: datos,
            success: function(respuesta) {
                if(respuesta.trim() === 'ok') {
                    Swal.fire({
                        title: "¡Guardado!",
                        text: "La posición se ha actualizado",
                        icon: "success",
                        confirmButtonText: "Genial"
                    }).then(function() {
                        window.location.href = "gestionar_buffet.php";
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: respuesta,
                        icon: "error"
                    });
                }
            }
        })
    });
});

$(document).on('click', '.editar-buffet', function(e) {
    e.preventDefault();
    const $btn = $(this);
    const plato_ids = $btn.data('plato-ids');
    const mesa_id = $btn.closest('tr').find('[data-mesa-id]').data('mesa-id');
    const posicion = $btn.closest('tr').find('[data-posicion]').data('posicion');
    
    // Obtener mesa_id y posicion del primer plato_id
    const first_plato_id = plato_ids.split(',')[0];
    
    $.ajax({
        url: '../inc/get_platos_posicion.php',
        method: 'GET',
        data: { plato_ids: plato_ids },
        success: function(respuesta) {
            let html = '<ul class="list-group">';
            respuesta.forEach(plato => {
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>${plato.nombre_es}</span>
                        <a href="editar_buffet.php?ids=${plato.id}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </li>
                `;
            });
            html += '</ul>';
            
            Swal.fire({
                title: 'Platos en esta posición',
                html: html,
                icon: 'info',
                confirmButtonText: 'Cerrar'
            });
        }
    });
});

$(document).on('click', '.borrar-plato', function(e) {
    e.preventDefault();
    const plato_ids = $(this).data('plato-ids');
    
    console.log('Platos a borrar:', plato_ids); 

    Swal.fire({
        title: "¿Eliminar platos?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar"
    }).then(function(result) {
        console.log('Resultado del SweetAlert:', result);
        
        if (result.isConfirmed) {
            console.log('Enviando AJAX...'); 
            
            $.ajax({
                url: '../inc/borrar_buffet.php',
                method: 'POST',
                data: { plato_ids: plato_ids },
                success: function(respuesta) {
                    console.log('Respuesta:', respuesta); 
                    
                    if(respuesta.trim() === 'ok') {
                        Swal.fire("¡Eliminado!", "", "success");
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