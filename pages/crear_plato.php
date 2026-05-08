<?php
require '../inc/db.php';
require '../inc/auth_check.php';
validar_acceso([1, 2]);

$page_title = 'Crear Plato';

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
                        <li class="breadcrumb-item"><a href="tabla_platos.php">Gestionar Platos</a></li>
                        <li class="breadcrumb-item active">Crear Plato</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">

                <div class="col-md-8 col-lg-6">

                    <form id="formNuevoPlato">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Nombres del Plato</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nombre_es">Nombre (Español)</label>
                                    <input type="text" name="nombre_es" id="nombre_es" class="form-control" placeholder="Ej: Paella mixta" required>
                                </div>
                                <div class="form-group">
                                    <label for="nombre_en">Nombre (Inglés)</label>
                                    <input type="text" name="nombre_en" id="nombre_en" class="form-control" placeholder="Ej: Mixed Paella">
                                </div>
                                <div class="form-group">
                                    <label for="nombre_fr">Nombre (Francés)</label>
                                    <input type="text" name="nombre_fr" id="nombre_fr" class="form-control" placeholder="Ej: Paella mixte">
                                </div>
                            </div>
                        </div>

                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Ubicación en Buffet</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mesa</label>
                                            <?php
                                            $mesas = $pdo->query("SELECT id, nombre FROM mesas")->fetchAll(PDO::FETCH_ASSOC);
                                            ?>
                                            <select name="mesa_id" class="form-control">
                                                <?php
                                                foreach ($mesas as $mesa) {
                                                    echo '<option value="' . $mesa['id'] . '">' . $mesa['nombre'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Posición</label>
                                            <input type="text" name="posicion" class="form-control" placeholder="Ej: 03" required>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="card card-success card-outline ">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas"></i> Turno</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $lista_turno = $pdo->query('SELECT id, nombre from turnos')->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($lista_turno as $turno) : ?>
                                        <div class="col-md-3 col-sm-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" name="turno[]" id="turno_<?= $turno['id'] ?>" value="<?= $turno['id'] ?>">
                                                <label for="turno_<?= $turno['id'] ?>" class="custom-control-label"><?= $turno['nombre'] ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="card card-info card-outline ">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Alérgenos</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $lista_alergenos = $pdo->query('SELECT id, nombre FROM alergenos')->fetchAll(PDO::FETCH_ASSOC);

                                    $alergenos_iconos = [
                                        'Gluten' => '🌾',
                                        'Lácteos' => '🧀',
                                        'Huevos' => '🥚',
                                        'Pescado' => '🐟',
                                        'Crustáceos' => '🦐',
                                        'Frutos Secos' => '🌰'
                                    ];

                                    foreach ($lista_alergenos as $alergenos) :
                                        $icono = $alergenos_iconos[$alergenos['nombre']] ?? '⚠️';
                                    ?>
                                        <div class="col-md-4 col-sm-6 mb-2">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" name="alergenos[]" id="alergeno_<?= $alergenos['id'] ?>" value="<?= $alergenos['id'] ?>">
                                                <label for="alergeno_<?= $alergenos['id'] ?>" class="custom-control-label">
                                                    <?= $icono ?> <?= $alergenos['nombre'] ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>


                        <div class="row pb-5">
                            <div class="col-12 text-right">
                                <a href="tabla_platos.php" class="btn btn-default">Cancelar</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Plato
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
