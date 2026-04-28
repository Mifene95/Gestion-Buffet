const turnos = [
    { id: 1, nombre: 'Desayuno' },
    { id: 2, nombre: 'Comida' },
    { id: 3, nombre: 'Cena' }
];

const gridOptions = {
    rowData: [],
    columnDefs: [{
        field: "nombre_es",
        headerName: "Nombre_es",
        filter: true,
        sortable: true,
    },
    {
        field: "nombre_en",
        headerName: "Nombre_en",
        filter: true,
        sortable: true,
        hide: true,
    },
    {
        field: "nombre_fr",
        headerName: "Nombre_fr",
        filter: true,
        sortable: true,
        hide: true,
    },
    {
        field: "alergenos",
        headerName: "Alergenos",
        filter: true,
        sortable: true,
    },
    {
        field: "mesa",
        headerName: "Mesa / Sección",
        filter: true
    },
    {
        field: "posicion",
        headerName: "Posición"
    },
    {
        field: "turnos",
        headerName: "Turno",
        filter: true,
        editable: true
    },
    {
    headerName: "Acciones",
    field: "id",
    width: 200,
    sortable: false,
    filter: false,
    cellRenderer: function(params) {
        // Si es Admin (Rol 1)
        if (ROL_USUARIO === 1) {
            return `
                <div style="display: flex; gap: 5px;">
                <a href"" class="btn btn-sm btn-primary edit-admin" data-plato-id="${params.data.id}">
                    <i class="fas fa-tools"></i> Editar
                </a>
                <a data-usuario-id="${params.data.id}" class="btn btn-sm btn-danger borrar-usuario">
                    <i class="fas fa-trash"></i> Borrar
                </a>
                </div>
            `;
        } 
        // Si es Usuario (u otro rol)
        else {
            return `
                <button class="btn btn-sm btn-primary edit-plato" data-plato-id="${params.data.id}" data-plato-nombre="${params.data.nombre_es}" data-plato-turnos="${params.data.turnos || ''}">
                    <i class="fas fa-edit"></i> Editar Turno
                </button>
            `;
        }
    }
}
    ],
    pagination: true,
    paginationPageSize: 10,
    paginationPageSizeSelector: [10, 20, 50, 100]
};

let gridApi;

function cargar_platos(){
    $.ajax({
        url: '../inc/get_platos.php',
        method: 'GET',
        success: function(respuesta){
            gridApi.setGridOption('rowData', respuesta);
        }
    });
}

$(window).on('load', function() {
    const gridDiv = document.querySelector('#myGrid');
    if (gridDiv) {
        gridApi = agGrid.createGrid(gridDiv, gridOptions);
    }
    cargar_platos();

    $('#selector_idioma').change(function() {
        const idioma = $(this).val();
        gridApi.setColumnsVisible(['nombre_es'], false); 
        gridApi.setColumnsVisible(['nombre_en'], false); 
        gridApi.setColumnsVisible(['nombre_fr'], false);
        gridApi.setColumnsVisible([idioma], true);
    });

    $(document).on('click', '.edit-plato', function(){
        const platoId = $(this).data('plato-id');
        const nombrePlato = $(this).data('plato-nombre');
        const turnosActuales = $(this).data('plato-turnos');
        
        const turnosIds = turnosActuales 
            ? turnosActuales.split(', ').map(nombreTurno => 
                turnos.find(t => t.nombre === nombreTurno).id
            )
            : [];
        
        $('#nombrePlatoModal').text(nombrePlato);
        $('#btnGuardarTurnos').data('plato-id', platoId);
        
        let checkboxesHTML = '';
        turnos.forEach(turno => {
            const checked = turnosIds.includes(turno.id) ? 'checked' : '';
            checkboxesHTML += `<div class="col-md-4 col-sm-6 mb-2">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" name="turno_modal[]" id="turno_${turno.id}" value="${turno.id}" ${checked}>
                    <label class="custom-control-label" for="turno_${turno.id}">
                        ${turno.nombre}
                    </label>
                </div>
            </div>`;
        });
        
        $('#checkboxesTurnos').html('<div class="row">' + checkboxesHTML + '</div>');
        $('#modalTurnos').modal('show');
    });

    $('#btnGuardarTurnos').click(function(){
        let platoId = $(this).data('plato-id');
        let turnosSeleccionados = $('input[name="turno_modal[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: '../inc/guardar_turnos.php',
            method: 'POST',
            data: {
                plato_id: platoId,
                turnos: turnosSeleccionados
            },
            success: function(respuesta){
                if(respuesta.trim() === "ok"){
                    cargar_platos();
                    $('#modalTurnos').modal('hide');
                    Swal.fire({
                        title: "¡Turno actualizado!",
                        text: "Se actualizo el turno",
                        icon: "success",
                        confirmButtonText: "Genial"
                    });
                }else{
                    Swal.fire({
                        title: "Error",
                        text: "Hubo un error al actualizar el plato",
                        icon: "error"
                    });
                }
            }
        })
    })
});