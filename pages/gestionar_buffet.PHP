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
                    <div class="d-flex flex-column">
                        <h3 class="card-title mb-2">Listado de platos por turno y Sección</h3>
                        <select id="filtroMesa" class="form-control mb-2" style="width: 250px;">
                            <option value="">Todas las mesas</option>
                            <option value="1">Platos Calientes</option>
                            <option value="2">Platos Fríos</option>
                            <option value="3">Ensaladas</option>
                            <option value="4">Postres</option>
                        </select>
                        <div>
                            <span class="badge badge-primary p-2 mr-1">Total huecos: <strong id="totalPosiciones">0</strong></span>
                            <span class="badge badge-success p-2 mr-1">Turnos asignados: <strong id="posicionesAsignadas">0</strong></span>
                            <span class="badge badge-danger p-2">Turnos vacíos: <strong id="posicionesVacias">0</strong></span>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <a href="../inc/exportar_buffet_csv.php?filtro=asignados" class="btn btn-info btn-sm p-3">
                            <i class="fas fa-download"></i> Descargar Asignados
                        </a>
                        <a href="../inc/exportar_buffet_csv.php?filtro=todos" class="btn btn-secondary btn-sm p-3">
                            <i class="fas fa-download"></i> Descargar Todo
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="myGridBuffet" style="height: 600px; width: 100%;" class="ag-theme-alpine"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ROL_USUARIO = <?php echo $_SESSION['role_id']; ?>;
    </script>
    <!-- Modal para seleccionar plato -->
    <div class="modal fade" id="modalSeleccionarPlato">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header flex">
                    <h4 class="modal-title" id="modalTituloTurno">Seleccionar Plato</h4>
                    <button type="button" class="btn btn-warning" id="btnCambiarTurno" style="margin-left: auto;">
                        Cambiar Turno
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Buscar plato:</strong></label>
                        <input type="text" id="buscadorPlatos" class="form-control mb-3" placeholder="Escribe el nombre del plato...">
                    </div>

                    <div class="form-group">
                        <label><strong>Platos disponibles:</strong></label>
                        <div id="listaPlatos" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">

                        </div>
                    </div>

                    <input type="hidden" id="posicion_id" value="">
                    <input type="hidden" id="turno_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnAsignarPlato">
                        <i class="fas fa-save"></i> Asignar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../inc/layout/footer.php"; ?>
<script src="../js/gestion_buffet.js?v=4"></script>