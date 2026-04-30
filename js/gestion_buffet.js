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
        } else {
            return `
                <button class="btn btn-sm btn-primary edit-plato" data-plato-id="${params.data.id}" data-plato-nombre="${params.data.nombre_es}" data-plato-turnos="${params.data.turnos || ''}">
                    <i class="fas fa-clock"></i> Editar Turno
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

let gridApiBuffet;

function cargarBuffet() {
    $.ajax({
        url: '../inc/get_buffet.php',
        method: 'GET',
        success: function(respuesta) {
            const datosFormateados = respuesta.map(item => ({
                mesa_id: item.mesa_id,
                posicion: item.posicion,
                seccion: item.mesa + ' ' + item.posicion,
                desayuno: item.turnos[1]?.plato_nombre || '-',
                comida: item.turnos[2]?.plato_nombre || '-',
                cena: item.turnos[3]?.plato_nombre || '-',
                plato_ids: Object.values(item.turnos).map(t => t.plato_id).filter(id => id)
            }));
            gridApiBuffet.setGridOption('rowData', datosFormateados);
        }
    });
}

$(window).on('load', function() {
    const gridDiv = document.querySelector('#myGridBuffet');
    if (gridDiv) {
        gridApiBuffet = agGrid.createGrid(gridDiv, gridOptionsBuffet);
    }
    cargarBuffet();
});