<?php
require '../inc/db.php';
require '../inc/auth_check.php';
validar_acceso([1, 2]);

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Consultas para las tarjetas
$total_platos = $pdo->query("SELECT COUNT(*) FROM platos")->fetchColumn();
$total_admin = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE role_id = 1")->fetchColumn();

// Consulta posiciones por mesa
$stmt_mesas = $pdo->prepare("
    SELECT 
        m.id,
        m.nombre,
        m.posiciones_totales,
        COUNT(DISTINCT p.id) as posiciones_rellenas
    FROM mesas m
    LEFT JOIN platos p ON m.id = p.mesa_id
    GROUP BY m.id, m.nombre, m.posiciones_totales
");
$stmt_mesas->execute();
$mesas = $stmt_mesas->fetchAll(PDO::FETCH_ASSOC);

// Consulta platos sin alérgenos
$stmt_sin_alergenos = $pdo->prepare("
    SELECT p.id, p.nombre_es
    FROM platos p
    LEFT JOIN plato_alergenos pa ON p.id = pa.plato_id
    WHERE pa.plato_id IS NULL
");
$stmt_sin_alergenos->execute();
$platos_sin_alergenos = $stmt_sin_alergenos->fetchAll(PDO::FETCH_ASSOC);

// Consulta cambios por día (para el gráfico)
$stmt_cambios_dia = $pdo->prepare("
    SELECT DATE(fecha) as dia, COUNT(*) as total_cambios
    FROM logs_cambios
    WHERE fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(fecha)
    ORDER BY DATE(fecha) ASC
");
$stmt_cambios_dia->execute();
$cambios_por_dia = $stmt_cambios_dia->fetchAll(PDO::FETCH_ASSOC);

// Consulta cambios con detalles (últimos 7 días)
$stmt_cambios = $pdo->prepare("
    SELECT 
        lc.id,
        lc.fecha,
        lc.accion,
        u.username,
        p.nombre_es as plato_nombre,
        t.nombre as turno_nombre,
        a.nombre as alergeno_nombre
    FROM logs_cambios lc
    LEFT JOIN usuarios u ON lc.usuario_id = u.id
    LEFT JOIN platos p ON lc.plato_id = p.id
    LEFT JOIN turnos t ON lc.turno_id = t.id
    LEFT JOIN alergenos a ON lc.alergeno_id = a.id
    WHERE lc.fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY lc.fecha DESC
    LIMIT 100
");
$stmt_cambios->execute();
$cambios_detallados = $stmt_cambios->fetchAll(PDO::FETCH_ASSOC);


include '../inc/layout/header.php';
include '../inc/layout/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Panel de Control</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- FILA 1: Tarjetas principales -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $total_platos; ?></h3>
                            <p>Platos en Buffet</p>
                        </div>
                        <div class="icon"><i class="fas fa-hamburger"></i></div>
                    </div>
                </div>

                <?php if ($_SESSION['role_id'] == 1): ?>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $total_admin; ?></h3>
                                <p>Usuarios</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-shield"></i></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- FILA 2: Posiciones por mesa -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Posiciones Rellenadas por Mesa</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($mesas as $mesa): ?>
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <div class="small-box bg-success">
                                            <div class="inner">
                                                <h3><?php echo $mesa['posiciones_rellenas']; ?> / <?php echo $mesa['posiciones_totales']; ?></h3>
                                                <p><?php echo htmlspecialchars($mesa['nombre']); ?></p>
                                            </div>
                                            <div class="icon"><i class="fas fa-chair"></i></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILA 3: Gráfico circular global -->
            <div class="row mt-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-pie-chart mr-2"></i>Estado Global del Buffet</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartGlobal"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FILA 4: Platos sin alérgenos -->
            <div class="row mt-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Platos sin Alérgenos Asignados</h3>
                        </div>
                        <div class="card-body">
                            <?php if (count($platos_sin_alergenos) > 0): ?>
                                <ul class="list-group">
                                    <?php foreach ($platos_sin_alergenos as $plato): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-exclamation-circle text-warning mr-2"></i>
                                                <?php echo htmlspecialchars($plato['nombre_es']); ?>
                                            </span>
                                            <?php if ($_SESSION['role_id'] == 1): ?>
                                                <a href="editar_plato.php?id=<?php echo $plato['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-success"><i class="fas fa-check-circle mr-2"></i>Todos los platos tienen alérgenos asignados ✓</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FILA 5: Cambios por día -->
            <div class="row mt-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Cambios Realizados (Últimos 7 días)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartCambios"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- TABLA: Cambios detallados -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Historial de Cambios</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Acción</th>
                                        <th>Plato</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cambios_detallados as $cambio): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($cambio['fecha'])); ?></td>
                                            <td><strong><?php echo htmlspecialchars($cambio['username']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($cambio['accion']); ?></td>
                                            <td><?php echo $cambio['plato_nombre'] ? htmlspecialchars($cambio['plato_nombre']) : '-'; ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    const mesasData = <?php echo json_encode($mesas); ?>;
    const cambiosData = <?php echo json_encode($cambios_por_dia); ?>;
</script>

<?php include '../inc/layout/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/dashboard.js"></script>