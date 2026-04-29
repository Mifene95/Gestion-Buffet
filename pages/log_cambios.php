<?php

require '../inc/db.php';
require '../inc/auth_check.php';
validar_acceso([1]);

include '../inc/layout/header.php';
include '../inc/layout/sidebar.php';


// Consulta cambios con detalles - con paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

// Contar total
$stmt_total = $pdo->query("SELECT COUNT(*) FROM logs_cambios");
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $por_pagina);

$sql = "
    SELECT 
        lc.id,
        lc.fecha,
        lc.accion,
        u.username,
        p.nombre_es as plato_nombre
    FROM logs_cambios lc
    LEFT JOIN usuarios u ON lc.usuario_id = u.id
    LEFT JOIN platos p ON lc.plato_id = p.id
    ORDER BY lc.id DESC
    LIMIT $por_pagina OFFSET $offset
";
$stmt_cambios = $pdo->query($sql);
$cambios_detallados = $stmt_cambios->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-12">
                    <h1 class="text-center">Nuevo Plato</h1>
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

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cambios_detallados as $cambio): ?>
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
                                                    <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>">Anterior</a>
                                                </li>
                                            <?php endif; ?>

                                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                                <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($pagina < $total_paginas): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>">Siguiente</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


</div>

<?php include '../inc/layout/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/dashboard.js"></script>