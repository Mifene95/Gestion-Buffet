<?php
require '../inc/auth_check.php';
validar_acceso([1]);
require "../inc/db.php";

$stmt = $pdo->query('SELECT id, nombre FROM turnos ORDER BY id');
$turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->query('SELECT turno_id, dia_semana, hora_inicio, hora_fin FROM turnos_horarios');
$horarios_raw = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$horarios = [];
foreach ($horarios_raw as $row) {
    $horarios[$row['dia_semana']][$row['turno_id']] = [
        'hora_inicio' => substr($row['hora_inicio'], 0, 5),
        'hora_fin'    => substr($row['hora_fin'], 0, 5),
    ];
}

$dias = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    0 => 'Domingo',
];

$iconos_turno = [1 => 'fa-sun text-warning', 2 => 'fa-utensils text-success', 3 => 'fa-moon text-primary'];

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
                    <h3 class="card-title">Horarios de Turnos por Día</h3>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="tabsDias" role="tablist">
                        <?php $primero = true; foreach ($dias as $dia_num => $dia_nombre): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $primero ? 'active' : '' ?>"
                               id="tab-dia-<?= $dia_num ?>-tab"
                               data-toggle="tab"
                               href="#tab-dia-<?= $dia_num ?>"
                               role="tab">
                                <?= $dia_nombre ?>
                            </a>
                        </li>
                        <?php $primero = false; endforeach; ?>
                    </ul>

                    <div class="tab-content" id="tabsDiasContent">
                        <?php $primero = true; foreach ($dias as $dia_num => $dia_nombre): ?>
                        <div class="tab-pane fade <?= $primero ? 'show active' : '' ?>"
                             id="tab-dia-<?= $dia_num ?>"
                             role="tabpanel">

                            <?php foreach ($turnos as $turno): ?>
                            <?php
                                $hi = $horarios[$dia_num][$turno['id']]['hora_inicio'] ?? '00:00';
                                $hf = $horarios[$dia_num][$turno['id']]['hora_fin']    ?? '00:00';
                                $icono = $iconos_turno[$turno['id']] ?? 'fa-clock text-secondary';
                            ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas <?= $icono ?> mr-2"></i>
                                        <?= htmlspecialchars($turno['nombre']) ?>
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
                                                       data-dia="<?= $dia_num ?>"
                                                       value="<?= $hi ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Hora fin</label>
                                                <input type="time"
                                                       class="form-control hora-fin"
                                                       data-turno-id="<?= $turno['id'] ?>"
                                                       data-dia="<?= $dia_num ?>"
                                                       value="<?= $hf ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <div class="text-right">
                                <button type="button"
                                        class="btn btn-success btn-guardar-dia"
                                        data-dia="<?= $dia_num ?>">
                                    <i class="fas fa-save"></i> Guardar <?= $dia_nombre ?>
                                </button>
                            </div>
                        </div>
                        <?php $primero = false; endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $page_scripts = ['../js/configuracion.js']; ?>
<?php include "../inc/layout/footer.php"; ?>
