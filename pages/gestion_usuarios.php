<?php
require '../inc/auth_check.php';
validar_acceso([1]);
require "../inc/db.php";
include "../inc/layout/header.php";
include "../inc/layout/sidebar.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Gestión de Usuarios</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Usuarios</h3>
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
<script src="../js/tabla_usuarios.js"></script>