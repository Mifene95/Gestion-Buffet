<?php
require('../inc/db.php');
require('../inc/auth_check.php');
validar_acceso([1]);

$usuario_id = $_GET['id'] ?? null;

if (!$usuario_id) {
    header("Location: gestion_usuarios.php");
    exit();
}


$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: gestion_usuarios.php");
    exit();
}

$page_title = 'Editar Usuario';

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
                        <li class="breadcrumb-item active">Editar Usuario</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <form id="formEditarUsuario">
                        <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>">

                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Datos del Usuario</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="username">Nombre de Usuario</label>
                                    <input type="text" name="username" id="username" class="form-control" value="<?= $usuario['username'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="<?= $usuario['email'] ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Contraseña (dejar en blanco para no cambiar)</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password" class="form-control password-input" placeholder="Nueva contraseña (opcional)">
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
                                        <input type="password" name="password_confirm" id="password_confirm" class="form-control password-input" placeholder="Confirmar contraseña (opcional)">
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
                                        <option value="1" <?= $usuario['role_id'] == 1 ? 'selected' : '' ?>>Administrador</option>
                                        <option value="2" <?= $usuario['role_id'] == 2 ? 'selected' : '' ?>>Usuario</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="estado_id">Estado</label>
                                    <select name="estado_id" id="estado_id" class="form-control" required>
                                        <option value="1" <?= $usuario['estado_id'] == 1 ? 'selected' : '' ?>>Activo</option>
                                        <option value="2" <?= $usuario['estado_id'] == 2 ? 'selected' : '' ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row pb-5">
                            <div class="col-12 text-right">
                                <a href="gestion_usuarios.php" class="btn btn-default">Cancelar</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $page_scripts = ['../js/editar_usuario.js']; ?>
<?php include '../inc/layout/footer.php'; ?>