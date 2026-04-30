<?php
require('db.php');
require('auth_check.php');
validar_acceso([1]);

if ($_POST) {
    $plato_id = $_POST['plato_ids'];
    $mesa_id = $_POST['mesa_id'];
    $posicion = $_POST['posicion'];
    $turnos = $_POST['turnos'] ?? [];

    try {
        $pdo->beginTransaction();

        // UPDATE mesa y posición
        $stmt = $pdo->prepare('UPDATE platos SET mesa_id = ?, posicion = ? WHERE id = ?');
        $stmt->execute([$mesa_id, $posicion, $plato_id]);

        // QUITAR este plato de TODOS los turnos
        $stmt = $pdo->prepare('DELETE FROM plato_turnos WHERE plato_id = ?');
        $stmt->execute([$plato_id]);

        // INSERTAR en los turnos seleccionados
        if (!empty($turnos)) {
            $stmt_turnos = $pdo->prepare('INSERT INTO plato_turnos (plato_id, turno_id) VALUES (?, ?)');
            $turnos_nombres = [];

            foreach ($turnos as $turno_id) {
                // QUITAR cualquier otro plato que estuviera en este turno y posición
                $stmt_quitar = $pdo->prepare('
                    DELETE FROM plato_turnos 
                    WHERE turno_id = ? 
                    AND plato_id IN (
                        SELECT id FROM platos 
                        WHERE mesa_id = ? AND posicion = ? AND id != ?
                    )
                ');
                $stmt_quitar->execute([$turno_id, $mesa_id, $posicion, $plato_id]);

                // INSERTAR el nuevo plato
                $stmt_turnos->execute([$plato_id, $turno_id]);

                $stmt_turno = $pdo->prepare('SELECT nombre FROM turnos WHERE id = ?');
                $stmt_turno->execute([$turno_id]);
                $turno = $stmt_turno->fetch(PDO::FETCH_ASSOC);
                $turnos_nombres[] = $turno['nombre'];
            }

            $turnos_str = implode(', ', $turnos_nombres);
            $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
            $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Asignó turnos: ' . $turnos_str]);
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
