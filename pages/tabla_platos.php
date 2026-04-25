<?php
session_start();
require "../inc/db.php";
include "../inc/layout/header.php";
include "../inc/layout/sidebar.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Panel de Control del Buffet</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Platos Existentes</h3>
                </div>
                <div class="card-body">
                    <div id="myGrid" style="height: 400px; width: 100%;" class="ag-theme-alpine"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../inc/layout/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>

<script>
    const gridOptions = {
        rowData: [{
                nombre: "Paella",
                mesa: "Calientes",
                posicion: 1,
                precio: "15€"
            },
            {
                nombre: "Ensalada César",
                mesa: "Fríos",
                posicion: 4,
                precio: "10€"
            },
            {
                nombre: "Paella Valenciana",
                alergenos: "cosas",
                mesa: "Calientes",
                turno: "Almuerzo",
                posicion: 1,
                precio: "15€"
            },
            {
                nombre: "Tarta de Queso",
                mesa: "Postres",
                posicion: 2,
                precio: "6€"
            }
        ],
        columnDefs: [{
                field: "nombre",
                headerName: "Nombre del Plato",
                filter: true,
                sortable: true,
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
                field: "turno",
                headerName: "Turno",
                filter: true,
                editable: true
            },
            {
                field: "posicion",
                headerName: "Posición"
            },
            {
                field: "precio",
                headerName: "Precio"
            }
        ],

        //Paginacion
        pagination: true,
        paginationPageSize: 10,
        paginationPageSizeSelector: [10, 20, 50, 100]
    };

    //Al cargar la pagina
    window.onload = () => {
        const gridDiv = document.querySelector('#myGrid');
        if (gridDiv) {
            agGrid.createGrid(gridDiv, gridOptions);
        }
    };
</script>