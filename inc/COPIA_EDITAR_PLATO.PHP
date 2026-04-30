<?php
require('db.php');
require('auth_check.php');
validar_acceso([1]);

if ($_POST) {
    $plato_id = $_POST['plato_id'];
    $nombre_es = $_POST['nombre_es'];
    $nombre_en = $_POST['nombre_en'];
    $nombre_fr = $_POST['nombre_fr'];
    $mesa_id = $_POST['mesa_id'];
    $posicion = $_POST['posicion'];
    $turnos = $_POST['turnos'] ?? [];
    $alergenos = $_POST['alergenos'] ?? [];

    try {
        $pdo->beginTransaction();

        // UPDATE plato
        $stmt = $pdo->prepare('UPDATE platos SET nombre_es = ?, nombre_en = ?, nombre_fr = ?, mesa_id = ?, posicion = ? WHERE id = ?');
        $stmt->execute([$nombre_es, $nombre_en, $nombre_fr, $mesa_id, $posicion, $plato_id]);

        // Registrar cambio de datos CON el nombre
        $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
        $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Editó datos del plato: ' . $nombre_es]);

        // DELETE y INSERT turnos
        $stmt = $pdo->prepare('DELETE FROM plato_turnos WHERE plato_id = ?');
        $stmt->execute([$plato_id]);

        if (!empty($turnos)) {
            $stmti = $pdo->prepare('INSERT INTO plato_turnos (plato_id, turno_id) VALUES (?, ?)');
            $turnos_nombres = [];

            foreach ($turnos as $turno_id) {
                $stmti->execute([$plato_id, $turno_id]);
                $stmt_turno = $pdo->prepare('SELECT nombre FROM turnos WHERE id = ?');
                $stmt_turno->execute([$turno_id]);
                $turno = $stmt_turno->fetch(PDO::FETCH_ASSOC);
                $turnos_nombres[] = $turno['nombre'];
            }

            $turnos_str = implode(', ', $turnos_nombres);
            $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
            $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Asignó turnos: ' . $turnos_str . ':' . ' al plato: ' . $nombre_es]);
        }

        // DELETE y INSERT alérgenos
        $stmt = $pdo->prepare('DELETE FROM plato_alergenos WHERE plato_id = ?');
        $stmt->execute([$plato_id]);

        if (!empty($alergenos)) {
            $stmt_alergenos = $pdo->prepare('INSERT INTO plato_alergenos (plato_id, alergeno_id) VALUES (?, ?)');
            $alergenos_nombres = [];

            foreach ($alergenos as $alergeno_id) {
                $stmt_alergenos->execute([$plato_id, $alergeno_id]);
                $stmt_alergeno = $pdo->prepare('SELECT nombre FROM alergenos WHERE id = ?');
                $stmt_alergeno->execute([$alergeno_id]);
                $alergeno = $stmt_alergeno->fetch(PDO::FETCH_ASSOC);
                $alergenos_nombres[] = $alergeno['nombre'];
            }

            $alergenos_str = implode(', ', $alergenos_nombres);
            $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
            $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Asignó alérgenos: ' . $alergenos_str . ':' . ' al plato ' . $nombre_es]);
        }

        $pdo->commit();
        echo 'ok';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "Error: " . $e->getMessage();
    }
}
