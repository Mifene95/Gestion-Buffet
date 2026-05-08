<?php
require '../inc/auth_check.php';
validar_acceso([1, 2]);
require "../inc/db.php";

$page_title = 'Gestionar Platos';

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
                        <li class="breadcrumb-item active">Gestionar Platos</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Listado de Platos Existentes</h3>
                    <?php if ($_SESSION['role_id'] === 1): ?>
                        <div class="d-flex ml-auto">
                            <button class="btn btn-info mr-2" id="btnBuscarPlato">
                                <i class="fas fa-search"></i> Buscar Plato
                            </button>
                            <a href="crear_plato.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> Crear Plato
                            </a>
                        </div>

                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div id="myGrid" style="height: 600px; width: 100%;" class="ag-theme-alpine"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL BUSCAR PLATO (DENTRO DEL CONTENT-WRAPPER) -->
    <div class="modal fade" id="modalBuscarPlato">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Buscar Plato</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Buscar por nombre:</strong></label>
                        <input type="text" id="buscadorPlatosEditar" class="form-control mb-3" placeholder="Escribe el nombre del plato...">
                    </div>

                    <div id="listaPlatosEditar" style="max-height: 400px; overflow-y: scroll; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                    </div>
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

<?php $page_scripts = ['../js/tabla_platos.js']; ?>
<?php include "../inc/layout/footer.php"; ?>
