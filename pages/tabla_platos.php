<?php
require '../inc/auth_check.php';
validar_acceso([1, 2]);
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
                <div class="card-header d-flex  align-items-center">
                    <h3 class="card-title">Listado de Platos Existentes</h3>

                    <select id="selector_idioma" class="form-control ml-3" style="width: auto;">
                        <option value="nombre_es">Español</option>
                        <option value="nombre_en">Ingles</option>
                        <option value="nombre_fr">Frances</option>
                    </select>
                </div>
                <div class="card-body">
                    <div id="myGrid" style="height: 400px; width: 100%;" class="ag-theme-alpine"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalTurnos">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Editar Turno</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Datos del Plato</h3>
                        </div>

                        <div class="card-body">

                            <div class="form-group">
                                <label>Nombre del Plato:</label>
                                <p id="nombrePlatoModal" class="form-control-plaintext mb-0"></p>
                            </div>

                            <div class="form-group">
                                <label>Turnos</label>
                                <div id="checkboxesTurnos"></div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnGuardarTurnos">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
<?php include "../inc/layout/footer.php"; ?>
<script src="../js/tabla_platos.js"></script>