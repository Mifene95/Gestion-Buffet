<?php
require('../inc/db.php');
require('../inc/auth_check.php');
validar_acceso([1, 2]);

// Capturar los IDs desde la URL (separados por coma)
$plato_ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];

if (empty($plato_ids)) {
    header("Location: gestion_buffet.php");
    exit();
}

// Si viene un plato_id específico, usa ese. Si no, muestra selector
$plato_id_editar = isset($_GET['plato_id']) ? (int)$_GET['plato_id'] : null;

if (!$plato_id_editar) {
    // Mostrar selector de platos
    $stmt = $pdo->prepare("SELECT id, nombre_es FROM platos WHERE id IN (" . implode(',', array_map('intval', $plato_ids)) . ")");
    $stmt->execute();
    $platos_posicion = $stmt->fetchAll(PDO::FETCH_ASSOC);

    include '../inc/layout/header.php';
    include '../inc/layout/sidebar.php';
?>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <h1 class="text-center">Selecciona Plato a Editar</h1>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-utensils mr-2"></i> Platos en esta Posición</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php foreach ($platos_posicion as $plato): ?>
                                        <a href="editar_buffet.php?ids=<?= implode(',', $plato_ids) ?>&plato_id=<?= $plato['id'] ?>" class="list-group-item list-group-item-action">
                                            <i class="fas fa-edit mr-2"></i><?= htmlspecialchars($plato['nombre_es']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                    <a href="gestionar_buffet.php" class="btn btn-default mr-2">
                                        <i class="fas fa-arrow-left"></i> Volver a la lista
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php
    include '../inc/layout/footer.php';
    exit();
}

// Si llegamos aquí, $plato_id_editar está definido
$stmt = $pdo->prepare('
    SELECT p.mesa_id, p.posicion, GROUP_CONCAT(pt.turno_id) as turnos_ids, p.nombre_es
    FROM platos p 
    LEFT JOIN plato_turnos pt ON p.id = pt.plato_id 
    WHERE p.id = ? 
    GROUP BY p.id
');
$stmt->execute([$plato_id_editar]);
$plato = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plato) {
    header("Location: gestion_buffet.php");
    exit();
}

// Consultar las mesas para el select
$stmt_mesas = $pdo->query("SELECT * FROM mesas");
$mesas = $stmt_mesas->fetchAll(PDO::FETCH_ASSOC);

// Consultar todos los turnos para los checkboxes
$stmt_turnos = $pdo->query("SELECT * FROM turnos");
$todos_los_turnos = $stmt_turnos->fetchAll(PDO::FETCH_ASSOC);

include '../inc/layout/header.php';
include '../inc/layout/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="text-center">Editar Posición del Buffet</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <form id="formEditarBuffet">
                        <input type="hidden" name="plato_ids" id="plato_ids" value="<?= $plato_id_editar ?>">

                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h3 class="card-title"><i class="fas fa-utensils mr-2"></i> Posición del Buffet</h3>
                                        <h5 class="text-primary mt-3">
                                            Plato: <?= htmlspecialchars($plato['nombre_es']) ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
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
                                            <div class="p-3 border rounded bg-light">
                                                <div class="row">
                                                    <?php
                                                    $turnos_activos = $plato['turnos_ids'] ? explode(',', $plato['turnos_ids']) : [];
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
                                </div>
                            </div>
                        </div>

                        <div class="row pb-5">
                            <div class="col-12 text-right">
                                <a href="gestionar_buffet.php" class="btn btn-default mr-2">
                                    <i class="fas fa-arrow-left"></i> Volver a la lista
                                </a>
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

<?php
include '../inc/layout/footer.php';
?>
<script src="../js/gestion_buffet.js"></script>