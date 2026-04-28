const gridOptions = {
    rowData: [],
    columnDefs: [{
        field: "id",
        headerName: "ID",
        filter: true,
        sortable: true,
    },
    {
        field: "username",
        headerName: "Username",
        filter: true,
        sortable: true,
    },
    {
        field: "email",
        headerName: "Email",
        filter: true,
        sortable: true,
    },
    {
        field: "rol",
        headerName: "Rol",
        filter: true,
        sortable: true,
    },
    {
        field: "estado",
        headerName: "Estado",
        filter: true
    },
    {
    headerName: "Acciones",
    field: "id",
    width: 200,
    sortable: false,
    filter: false,
    cellRenderer: function(params) {
        return `
            <div style="display: flex; gap: 5px;">
                <a href="editar_usuario.php?id=${params.data.id}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a id="${params.data.id}" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Borrar
                </a>
            </div>
        `;
    }
}
    ],

    pagination: true,
    paginationPageSize: 10,
    paginationPageSizeSelector: [10, 20, 50, 100]
};

let gridApi;

function cargar_usuarios(){
    $.ajax({
        url: '../inc/get_usuarios.php',
        method: 'GET',
        success: function(respuesta){
            gridApi.setGridOption('rowData', respuesta);
        }
    });
}

$(document).ready(function() {
    const gridDiv = document.querySelector('#myGrid');
    if (gridDiv) {
        gridApi = agGrid.createGrid(gridDiv, gridOptions);
    }
    cargar_usuarios();
});