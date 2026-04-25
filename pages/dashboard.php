<?php
session_start();
require '../inc/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Consultas para las tarjetas
$total_platos = $pdo->query("SELECT COUNT(*) FROM platos")->fetchColumn();
$total_admin  = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE role_id = 1")->fetchColumn();

// CARGAMOS LAS PIEZAS
include '../inc/layout/header.php';
include '../inc/layout/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Panel de Control</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $total_platos; ?></h3>
                            <p>Platos en Sistema</p>
                        </div>
                        <div class="icon"><i class="fas fa-hamburger"></i></div>
                    </div>
                </div>
                <?php // COMPROBAMOS SI ES ADMIN 
                ?>
                <?php if ($_SESSION['role_id'] == 1): ?>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $total_admin; ?></h3>
                                <p>Panel de Administrador</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-shield"></i></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php
include '../inc/layout/footer.php';
?>