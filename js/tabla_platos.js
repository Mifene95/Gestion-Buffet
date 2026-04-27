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
                field: "turnos",
                headerName: "Turno",
                filter: true,
                editable: true
            },
            {
                field: "posicion",
                headerName: "Posición"
            },
        ],

        //Paginacion
        pagination: true,
        paginationPageSize: 10,
        paginationPageSizeSelector: [10, 20, 50, 100]
    };

    let gridApi;

    function cargar_platos(){
        $.ajax({
            url: ('../inc/get_platos.php'),
            method: 'GET',

            success: function(respuesta){
                gridApi.setGridOption('rowData', respuesta)
                
            }
        })
    }

    window.onload = () => {
        const gridDiv = document.querySelector('#myGrid');
        if (gridDiv) {
            gridApi = agGrid.createGrid(gridDiv, gridOptions);
        }
        cargar_platos();

        $('#selector_idioma').change(function() {
        const idioma = $(this).val();
        console.log(idioma);

        gridApi.setColumnsVisible(['nombre_es'], false); 
        gridApi.setColumnsVisible(['nombre_en'], false); 
        gridApi.setColumnsVisible(['nombre_fr'], false);
        gridApi.setColumnsVisible([idioma], true);
    })
    };

    

















