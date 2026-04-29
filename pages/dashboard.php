<?php
require '../inc/db.php';
require '../inc/auth_check.php';
validar_acceso([1, 2]);

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Consultas para las tarjetas
$total_platos = $pdo->query("SELECT COUNT(*) FROM platos")->fetchColumn();
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

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

// Consulta cambios por dia
$stmt_cambios_dia = $pdo->prepare("
    SELECT DATE(fecha) as dia, COUNT(*) as total_cambios
    FROM logs_cambios
    WHERE fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(fecha)
    ORDER BY DATE(fecha) ASC
");
$stmt_cambios_dia->execute();
$cambios_por_dia = $stmt_cambios_dia->fetchAll(PDO::FETCH_ASSOC);

// Consulta alérgenos más asignados
$stmt_alergenos_top = $pdo->prepare("
    SELECT a.nombre, COUNT(*) as total
    FROM plato_alergenos pa
    LEFT JOIN alergenos a ON pa.alergeno_id = a.id
    GROUP BY pa.alergeno_id
    ORDER BY total DESC
    LIMIT 4
");
$stmt_alergenos_top->execute();
$alergenos_top = $stmt_alergenos_top->fetchAll(PDO::FETCH_ASSOC);

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
                        <div class="icon" id="icono-platos" style="cursor: pointer;"><i class=" fas fa-hamburger"></i></div>
                    </div>
                </div>

                <?php if ($_SESSION['role_id'] == 1): ?>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $total_usuarios; ?></h3>
                                <p>Usuarios</p>
                            </div>
                            <div class="icon"><i id="icono-usuarios" style="cursor: pointer;" class="fas fa-user-shield"></i></div>
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

            <!-- FILA 3-4: Gráfico global + Platos sin alérgenos (LADO A LADO) -->
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

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Platos sin Alérgenos</h3>
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

            <!-- FILA 5-6: Cambios por día + Alérgenos  -->
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

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-bar-chart mr-2"></i>Alérgenos Más Comunes</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartAlergenos"></canvas>
                        </div>
                    </div>
                </div>
            </div>
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
    const alergenos_data = <?php echo json_encode($alergenos_top); ?>;
</script>

<?php include '../inc/layout/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/dashboard.js"></script>