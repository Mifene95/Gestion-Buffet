<?php
require('db.php');
require('auth_check.php');
validar_acceso([1]);

if ($_POST) {
    $plato_ids = explode(',', $_POST['plato_ids']);
    $mesa_id = $_POST['mesa_id'];
    $posicion = $_POST['posicion'];
    $turnos = $_POST['turnos'] ?? [];

    try {
        $pdo->beginTransaction();

        // UPDATE SOLO el primer plato (el que se está editando)
        $first_id = $plato_ids[0];

        // Obtener nombre del plato y mesa
        $stmt_plato = $pdo->prepare('SELECT p.nombre_es, m.nombre as mesa_nombre FROM platos p LEFT JOIN mesas m ON p.mesa_id = m.id WHERE p.id = ?');
        $stmt_plato->execute([$first_id]);
        $plato_info = $stmt_plato->fetch(PDO::FETCH_ASSOC);
        $plato_nombre = $plato_info['nombre_es'];
        $mesa_nombre = $plato_info['mesa_nombre'];

        $stmt = $pdo->prepare('UPDATE platos SET mesa_id = ?, posicion = ? WHERE id = ?');
        $stmt->execute([$mesa_id, $posicion, $first_id]);

        // DELETE y INSERT turnos (solo para este plato)
        $stmt = $pdo->prepare('DELETE FROM plato_turnos WHERE plato_id = ?');
        $stmt->execute([$first_id]);

        if (!empty($turnos)) {
            $stmt_turnos = $pdo->prepare('INSERT INTO plato_turnos (plato_id, turno_id) VALUES (?, ?)');
            $turnos_nombres = [];

            foreach ($turnos as $turno_id) {
                $stmt_turnos->execute([$first_id, $turno_id]);
                $stmt_turno = $pdo->prepare('SELECT nombre FROM turnos WHERE id = ?');
                $stmt_turno->execute([$turno_id]);
                $turno = $stmt_turno->fetch(PDO::FETCH_ASSOC);
                $turnos_nombres[] = $turno['nombre'];
            }

            $turnos_str = implode(', ', $turnos_nombres);
            $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
            $stmt_log->execute([$_SESSION['user_id'], $first_id, 'Asignó turnos: ' . $turnos_str . ' al plato: ' . $plato_nombre . ' en posición: ' . $posicion . ' (' . $mesa_nombre . ')']);
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
