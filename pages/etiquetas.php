<?php
require '../inc/auth_check.php';
validar_acceso([1]);
require "../inc/db.php";

$page_title = 'Etiquetas';

include "../inc/layout/header.php";
include "../inc/layout/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= htmlspecialchars($page_title) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a></li>
                        <li class="breadcrumb-item active">Etiquetas</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Etiquetas Electrónicas</h3>
                    <div class="flex ml-auto">
                        <button class="btn btn-success ml-auto" id="btnAsignarPosicion">
                            <i class="fas fa-map-marker-alt"></i> Asignar Posición
                        </button>
                        <button class="btn btn-primary " id="btnRefrescar">
                            <i class="fas fa-sync"></i> Refrescar Tabla
                        </button>
                        <button class="btn btn-warning " id="btnRefrescarEtiquetas">
                            <i class="fas fa-sync"></i> Refrescar TODAS Etiquetas
                        </button>
                    </div>

                </div>
                <div class="card-body">
                    <div id="myGridEtiquetas" style="height: 600px; width: 100%;" class="ag-theme-alpine"></div>
                </div>
            </div>
        </div>
    </section>

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

    <!-- MODAL 1: SELECCIONAR ETIQUETA -->
    <div class="modal fade" id="modalSeleccionarEtiqueta">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Seleccionar Etiqueta</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Buscar por código:</strong></label>
                        <input type="text" id="buscadorEtiquetas" class="form-control mb-3" placeholder="Escribe el código de la etiqueta o nombre del plato">
                    </div>
                    <div id="listaEtiquetas" style="max-height: 400px; overflow-y: scroll; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: SELECCIONAR MESA Y POSICIÓN -->
    <div class="modal fade" id="modalAsignarPosicion">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Asignar Posición a <strong id="etiquetaSeleccionada"></strong></h4>
                    <button type="button" class="btn btn-warning" id="btnVolverEtiquetas">
                        <i class="fas fa-arrow-left"></i> Volver
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Mesa:</strong></label>
                        <select id="selectMesa" class="form-control">
                            <option value="">Selecciona una mesa</option>
                            <option value="1">Platos Calientes</option>
                            <option value="2">Platos Fríos</option>
                            <option value="3">Ensaladas</option>
                            <option value="4">Postres</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Posición:</strong></label>
                        <select id="selectPosicion" class="form-control">
                            <option value="">Selecciona primero una mesa</option>
                        </select>
                    </div>
                    <input type="hidden" id="etiquetaBarcodeAsignar" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnGuardarPosicion">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ROL_USUARIO = <?php echo $_SESSION['role_id']; ?>;
    </script>
</div>

<?php $page_scripts = ['../js/etiquetas.js']; ?>
<?php include "../inc/layout/footer.php"; ?>
