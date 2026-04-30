const turnos = [
    { id: 1, nombre: 'Desayuno' },
    { id: 2, nombre: 'Comida' },
    { id: 3, nombre: 'Cena' }
];

const alergenos_iconos = {
    'Gluten': '🌾',
    'Lácteos': '🧀',
    'Huevos': '🥚',
    'Pescado': '🐟',
    'Crustáceos': '🦐',
    'Frutos Secos': '🌰'
};

function verAlergenos(alergenos) {
    const lista = alergenos.split(',').map(a => {
        const nombre = a.trim();
        const icono = alergenos_iconos[nombre] || '?';
        return `<li>${icono} ${nombre}</li>`;
    }).join('');
    
    Swal.fire({
        title: 'Alérgenos',
        html: `<ul style="text-align: left; list-style: none;">${lista}</ul>`,
        icon: 'info',
        confirmButtonText: 'Cerrar'
    });
}
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
    },
    {
        field: "nombre_fr",
        headerName: "Nombre_fr",
        filter: true,
        sortable: true,
    },
    {
    field: "alergenos",
    headerName: "Alérgenos",
    filter: true,
    sortable: true,
    flex: 1,
    cellRenderer: function(params) {
        if (!params.data.alergenos || params.data.alergenos.length === 0) {
            return '-';
        }
        
        const alergenos = params.data.alergenos.split(',').map(a => a.trim());
        const iconos = alergenos.map(alergeno => alergenos_iconos[alergeno] || '?').join(' ');
        
        return `<button class="btn btn-lg" onclick="verAlergenos('${params.data.alergenos.replace(/'/g, "\\'")}')">
                    ${iconos}
                </button>`;
    }

    },
{
    headerName: "Acciones",
    field: "id",
    width: 200,
    sortable: false,
    filter: false,
    cellRenderer: function(params) {
        if (ROL_USUARIO === 1) {
            return `
                <div style="display: flex; gap: 5px;">
                    <a href="editar_plato.php?id=${params.data.id}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <button class="btn btn-sm btn-danger borrar-plato" data-id-plato="${params.data.id}">
                        <i class="fas fa-trash"></i> Borrar
                    </button>
                </div>
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

    $(document).on('click', '.borrar-plato', function(e){
    e.preventDefault();
    const plato_id = $(this).data('id-plato');
    console.log('Plato ID:', plato_id);

    Swal.fire({
        title: "¿Eliminar plato?",
        text: "Esta accion no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar"
    }).then(function(result) {
        if (result.isConfirmed) {
            console.log('Enviando:', { plato_id: plato_id });
            $.ajax({
                url: '../inc/borrar_plato.php',
                method: 'POST',
                data: { plato_id: plato_id },
                success: function(respuesta){
                    console.log('Respuesta:', respuesta);
                    if(respuesta.trim() === "ok") {
                        Swal.fire("¡Eliminado!", "", "success");
                        cargar_platos();
                    }
                }
            }) 
        }
    })   
})

});