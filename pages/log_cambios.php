<?php

require '../inc/db.php';
require '../inc/auth_check.php';
validar_acceso([1]);

// Consulta cambios con detalles - con paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 15;
$offset = ($pagina - 1) * $por_pagina;

// Contar total
$stmt_total = $pdo->query("SELECT COUNT(*) FROM logs_cambios");
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $por_pagina);

//Unir los selects con union

$sql = "
    SELECT
        lc.fecha,
        u.username,
        lc.accion
    FROM logs_cambios lc
    LEFT JOIN usuarios u ON lc.usuario_id = u.id

    UNION ALL

    SELECT
        ll.fecha,
        u.username,
        CONCAT('Login desde IP: ', ll.ip_address, ' Navegador: ', SUBSTRING_INDEX(ll.navegador, '(', 1)) as accion
    FROM logs_login ll
    LEFT JOIN usuarios u ON ll.usuario_id = u.id

    ORDER BY fecha DESC
    LIMIT $por_pagina OFFSET $offset
";
$stmt_union = $pdo->query($sql);
$resultado = $stmt_union->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Registro de Logs';

include '../inc/layout/header.php';
include '../inc/layout/sidebar.php';

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
                        <li class="breadcrumb-item active">Registro de Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Registro de Actividad del Buffet</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultado as $cambio): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y H:i:s', strtotime($cambio['fecha'])); ?></td>
                                            <td><strong><?php echo htmlspecialchars($cambio['username']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($cambio['accion']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Paginación -->
                            <nav aria-label="Page navigation" class="mt-3">
                                <ul class="pagination justify-content-center">
                                    <?php if ($pagina > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?pagina=1">« Primera</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>">‹ Anterior</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php
                                    // Mostrar solo 5 páginas alrededor de la actual
                                    $inicio = max(1, $pagina - 2);
                                    $fin = min($total_paginas, $pagina + 2);

                                    if ($inicio > 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>

                                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                                        <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($fin < $total_paginas): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>

                                    <?php if ($pagina < $total_paginas): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>">Siguiente ›</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="?pagina=<?php echo $total_paginas; ?>">Última »</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../inc/layout/footer.php'; ?>
