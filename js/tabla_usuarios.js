const gridOptions = {
    rowData: [],
    columnDefs: [{
        field: "id",
        headerName: "ID",
        width: 100,
        filter: true,
        sortable: true,
        flex: 1
    },
    {
        field: "username",
        headerName: "Username",
        filter: true,
        sortable: true,
        flex: 1
    },
    {
        field: "email",
        headerName: "Email",
        filter: true,
        sortable: true,
        flex: 1
    },
    {
        field: "rol",
        headerName: "Rol",
        filter: true,
        sortable: true,
        flex: 1
    },
    {
        field: "estado",
        headerName: "Estado",
        filter: true,
        flex: 1
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
                <a href="editar_usuario.php?id=${params.data.id}" class="btn btn-sm btn-primary editar-usuario">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a data-usuario-id="${params.data.id}" class="btn btn-sm btn-danger borrar-usuario">
                    <i class="fas fa-trash"></i> Borrar
                </a>
            </div>
        `;
    }
}
    ],
};

let gridApi;

function cargar_usuarios(){
    $.ajax({
        url: '../inc/get_usuarios.php',
        method: 'GET',
        success: function(respuesta){
            gridApi.setGridOption('rowData', respuesta);
            console.log(respuesta);
        },
        error: function(xhr, status, error) {
            console.error("Error AJAX:", error);
            Swal.fire('Error', 'No se pudo guardar: ' + xhr.responseText, 'error');
            console.log(respuesta)
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

$(document).on('click', '.borrar-usuario', function(e){
    e.preventDefault();
    const usuario_id = $(this).data('usuario-id');

    Swal.fire({
        title: "¿Eliminar usuario?",
        text: "Esta accion no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar"
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                url: '../inc/borrar_usuario.php',
                method: 'POST',
                data: { usuario_id: usuario_id },
                success: function(respuesta){
                    if(respuesta.trim() === "ok") {
                        Swal.fire("¡Eliminado!", "", "success");
                        cargar_usuarios();
                    }
                }
            }) 
        }
    })   
})