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