<?php
// Pintar con azul menu navegacion actual
$pagina_actual = basename($_SERVER['PHP_SELF']);

if (!function_exists('nav_active')) {
    function nav_active(string $page, string $current): string {
        return $page === $current ? 'active' : '';
    }
}
?>


<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="dashboard.php" class="brand-link">
        <span class="brand-icon-circle elevation-2">
            <i class="fas fa-utensils text-white"></i>
        </span>
        <span class="brand-text">Hotel Buffet</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="user-avatar-circle elevation-2">
                <i class="fas fa-user"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['nombre']); ?></a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?= nav_active('dashboard.php', $pagina_actual) ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Inicio</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="tabla_platos.php" class="nav-link <?= nav_active('tabla_platos.php', $pagina_actual) ?>">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>Gestionar Platos</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestionar_buffet.PHP" class="nav-link <?= nav_active('gestionar_buffet.PHP', $pagina_actual) ?>">
                        <i class="nav-icon fas fa-th"></i>
                        <p>Gestionar Buffet</p>
                    </a>
                </li>
                <?php if ($_SESSION['role_id'] == 1): ?>
                    <li class="nav-item">
                        <a href="gestion_usuarios.php" class="nav-link <?= nav_active('gestion_usuarios.php', $pagina_actual) ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Gestionar usuarios</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['role_id'] == 1): ?>
                    <li class="nav-item">
                        <a href="log_cambios.php" class="nav-link <?= nav_active('log_cambios.php', $pagina_actual) ?>">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Registro logs</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['role_id'] == 1): ?>
                    <li class="nav-item">
                        <a href="etiquetas.php" class="nav-link <?= nav_active('etiquetas.php', $pagina_actual) ?>">
                            <i class="nav-icon fas fa-tag"></i>
                            <p>Etiquetas</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['role_id'] == 1): ?>
                    <li class="nav-item">
                        <a href="configuracion.php" class="nav-link <?= nav_active('configuracion.php', $pagina_actual) ?>">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Configuración</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
