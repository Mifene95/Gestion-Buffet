<?php
require('../inc/db.php');
require('../inc/auth_check.php');
validar_acceso([1]);

$page_title = 'Crear Usuario';

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
                        <li class="breadcrumb-item"><a href="gestion_usuarios.php">Gestión de Usuarios</a></li>
                        <li class="breadcrumb-item active">Crear Usuario</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <form id="formNuevoUsuario">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Datos del Usuario</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="username">Nombre de Usuario</label>
                                    <input type="text" name="username" id="username" class="form-control" placeholder="Ej: admin123" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Ej: usuario@hotel.com" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control password-input" placeholder="Contraseña" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" data-target="#password">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirm">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirm" id="password_confirm" class="form-control password-input" placeholder="Confirmar contraseña" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" data-target="#password_confirm">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="role_id">Tipo de Usuario</label>
                                    <select name="role_id" id="role_id" class="form-control" required>
                                        <option value="">Selecciona un rol</option>
                                        <option value="1">Administrador</option>
                                        <option value="2">Usuario</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row pb-5">
                            <div class="col-12 text-right">
                                <a href="dashboard.php" class="btn btn-default">Cancelar</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Usuario
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
include '../inc/layout/footer.php';
?>