<?php
require '../inc/auth_check.php';
validar_acceso([1, 2]);
require "../inc/db.php";
include "../inc/layout/header.php";
include "../inc/layout/sidebar.php";

?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid m-3">
            <h1>Panel de Control del Buffet</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Listado de Platos Existentes</h3>

                    <?php if ($_SESSION['role_id'] === 1): ?>
                        <a href="crear_plato.php" class="btn btn-success" style="margin-left: auto;">
                            <i class="fas fa-plus"></i> Crear Plato
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div id="myGrid" style="height: 600px; width: 100%;" class="ag-theme-alpine"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const ROL_USUARIO = <?php echo $_SESSION['role_id']; ?>;
    </script>
</div>
<?php include "../inc/layout/footer.php"; ?>
<script src="../js/tabla_platos.js"></script>