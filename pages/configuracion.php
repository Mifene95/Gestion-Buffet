<?php
require '../inc/auth_check.php';
validar_acceso([1]); // Solo admin
require "../inc/db.php";

// Traer turnos con horarios
$stmt = $pdo->query('SELECT id, nombre, hora_inicio, hora_fin FROM turnos ORDER BY id');
$turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Configuración';

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
                        <li class="breadcrumb-item active">Configuración</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Horarios de Turnos</h3>
                </div>
                <div class="card-body">
                    <form id="formConfiguracion">
                        <?php foreach ($turnos as $turno): ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <?php if ($turno['id'] == 1): ?>
                                            <i class="fas fa-sun mr-2 text-warning"></i>
                                        <?php elseif ($turno['id'] == 2): ?>
                                            <i class="fas fa-utensils mr-2 text-success"></i>
                                        <?php else: ?>
                                            <i class="fas fa-moon mr-2 text-primary"></i>
                                        <?php endif; ?>
                                        <?= $turno['nombre'] ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Hora inicio</label>
                                                <input type="time"
                                                    class="form-control hora-inicio"
                                                    data-turno-id="<?= $turno['id'] ?>"
                                                    value="<?= substr($turno['hora_inicio'], 0, 5) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Hora fin</label>
                                                <input type="time"
                                                    class="form-control hora-fin"
                                                    data-turno-id="<?= $turno['id'] ?>"
                                                    value="<?= substr($turno['hora_fin'], 0, 5) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="text-right">
                            <button type="button" class="btn btn-success" id="btnGuardarConfig">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $page_scripts = ['../js/configuracion.js']; ?>
<?php include "../inc/layout/footer.php"; ?>
