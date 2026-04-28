<?php
require('../inc/db.php');
require('../inc/auth_check.php');
validar_acceso([1]);

// Capturar el ID desde la URL
$plato_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$plato_id) {
    header("Location: tabla_platos.php");
    exit();
}

// Consultar todos los alérgenos
$stmt_alergenos = $pdo->query("SELECT * FROM alergenos");
$todos_alergenos = $stmt_alergenos->fetchAll(PDO::FETCH_ASSOC);

// Alérgenos asignados al plato actual
$stmt_plato_alergenos = $pdo->prepare("
    SELECT alergeno_id FROM plato_alergenos WHERE plato_id = ?
");
$stmt_plato_alergenos->execute([$plato_id]);
$alergenos_asignados = $stmt_plato_alergenos->fetchAll(PDO::FETCH_COLUMN);

//  Consultar el plato con sus turnos_ids concatenados
$stmt = $pdo->prepare('
    SELECT p.*, GROUP_CONCAT(pt.turno_id) as turnos_ids 
    FROM platos p 
    LEFT JOIN plato_turnos pt ON p.id = pt.plato_id 
    WHERE p.id = ? 
    GROUP BY p.id
');
$stmt->execute([$plato_id]);
$plato = $stmt->fetch(PDO::FETCH_ASSOC); // Usamos $plato, no $usuario

if (!$plato) {
    header("Location: tabla_platos.php");
    exit();
}

// Consultar las mesas para el select
$stmt_mesas = $pdo->query("SELECT * FROM mesas");
$mesas = $stmt_mesas->fetchAll(PDO::FETCH_ASSOC);

//  Consultar todos los turnos para los checkboxes
$stmt_turnos = $pdo->query("SELECT * FROM turnos");
$todos_los_turnos = $stmt_turnos->fetchAll(PDO::FETCH_ASSOC);

include '../inc/layout/header.php';
include '../inc/layout/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="text-center">Editar Plato</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <form id="formEditarPlato">
                        <input type="hidden" name="plato_id" id="plato_id" value="<?= $plato['id'] ?>">

                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-utensils mr-2"></i> Datos del Plato</h3>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nombre_es">Nombre (Español)</label>
                                            <input type="text" name="nombre_es" id="nombre_es" class="form-control" value="<?= htmlspecialchars($plato['nombre_es']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nombre_en">Nombre (Inglés)</label>
                                            <input type="text" name="nombre_en" id="nombre_en" class="form-control" value="<?= htmlspecialchars($plato['nombre_en']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nombre_fr">Nombre (Francés)</label>
                                            <input type="text" name="nombre_fr" id="nombre_fr" class="form-control" value="<?= htmlspecialchars($plato['nombre_fr']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mesa_id">Mesa / Sección</label>
                                            <select name="mesa_id" id="mesa_id" class="form-control" required>
                                                <option value="">Selecciona una mesa</option>
                                                <?php foreach ($mesas as $mesa): ?>
                                                    <option value="<?= $mesa['id'] ?>" <?= $plato['mesa_id'] == $mesa['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($mesa['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="posicion">Posición en Mesa</label>
                                            <input type="number" name="posicion" id="posicion" class="form-control" value="<?= $plato['posicion'] ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="d-block">Disponibilidad de Turnos</label>
                                            <div id="checkboxesTurnosEdit" class="p-3 border rounded bg-light">
                                                <div class="row">
                                                    <?php
                                                    $turnos_activos = explode(',', $plato['turnos_ids']);
                                                    ?>
                                                    <?php foreach ($todos_los_turnos as $turno): ?>
                                                        <div class="col-md-4">
                                                            <div class="custom-control custom-checkbox">
                                                                <input class="custom-control-input" type="checkbox" name="turnos[]"
                                                                    id="turno_<?= $turno['id'] ?>"
                                                                    value="<?= $turno['id'] ?>"
                                                                    <?= in_array($turno['id'], $turnos_activos) ? 'checked' : '' ?>>
                                                                <label class="custom-control-label" for="turno_<?= $turno['id'] ?>">
                                                                    <?= $turno['nombre'] ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="d-block">Alérgenos</label>
                                            <div class="p-3 border rounded bg-light">
                                                <div class="row">
                                                    <?php foreach ($todos_alergenos as $alergeno): ?>
                                                        <div class="col-md-4 ">
                                                            <div class="custom-control custom-checkbox ">
                                                                <input class="custom-control-input" type="checkbox" name="alergenos[]"
                                                                    id="alergeno_<?= $alergeno['id'] ?>"
                                                                    value="<?= $alergeno['id'] ?>"
                                                                    <?= in_array($alergeno['id'], $alergenos_asignados) ? 'checked' : '' ?>>
                                                                <label class="custom-control-label" for="alergeno_<?= $alergeno['id'] ?>">
                                                                    <?= htmlspecialchars($alergeno['nombre']) ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row pb-5">
                            <div class="col-12 text-right">
                                <a href="tabla_platos.php" class="btn btn-default mr-2">
                                    <i class="fas fa-arrow-left"></i> Volver a la lista
                                </a>
                                <button type="submit" class="btn btn-success" id="btnGuardarEditPlato">
                                    <i class="fas fa-save"></i> Guardar Cambios en el Plato
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
<script src="../js/editar_platos.js"></script>