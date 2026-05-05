<?php
require '../inc/auth_check.php';
validar_acceso([1]);
require "../inc/db.php";
include "../inc/layout/header.php";
include "../inc/layout/sidebar.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid m-3">
            <h1>Gestión de Etiquetas</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Etiquetas Electrónicas</h3>
                    <button class="btn btn-primary ml-auto" id="btnRefrescar">
                        <i class="fas fa-sync"></i> Refrescar Tabla
                    </button>
                    <button class="btn btn-warning ml-2" id="btnRefrescarEtiquetas">
                        <i class="fas fa-sync"></i> Refrescar TODAS Etiquetas
                    </button>
                </div>
                <div class="card-body">
                    <div id="myGridEtiquetas" style="height: 600px; width: 100%;" class="ag-theme-alpine"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL VINCULAR PLATO -->
    <div class="modal fade" id="modalVincularPlato">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Vincular Plato a Etiqueta <strong id="etiquetaCodigo"></strong></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Buscar plato:</strong></label>
                        <input type="text" id="buscadorPlatosEtiqueta" class="form-control mb-3" placeholder="Escribe el nombre del plato...">
                    </div>
                    <div id="listaPlatosEtiqueta" style="max-height: 400px; overflow-y: scroll; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                    </div>
                    <input type="hidden" id="etiquetaBarcode" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ROL_USUARIO = <?php echo $_SESSION['role_id']; ?>;
    </script>
</div>

<?php include "../inc/layout/footer.php"; ?>
<script src="../js/etiquetas.js"></script>