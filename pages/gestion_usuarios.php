<?php
require '../inc/auth_check.php';
validar_acceso([1]);
require "../inc/db.php";

$page_title = 'Gestión de Usuarios';

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
                        <li class="breadcrumb-item active">Gestión de Usuarios</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Gestión de Usuarios</h3>

                    <a href="crear_usuario.php" class="btn btn-success" style="margin-left: auto;">
                        <i class="fas fa-plus"></i> Crear Usuario
                    </a>
                </div>
                <div class="card-body">
                    <div id="myGrid" style="height: 600px; width: 100%;" class="ag-theme-alpine"></div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $page_scripts = ['../js/tabla_usuarios.js']; ?>
<?php include "../inc/layout/footer.php"; ?>
