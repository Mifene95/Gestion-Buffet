<?php
require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

if ($_POST) {
    $plato_ids = explode(',', $_POST['plato_ids']);
    $mesa_id = $_POST['mesa_id'];
    $posicion = $_POST['posicion'];
    $turno_id = $_POST['turno_id'];

    try {
        $pdo->beginTransaction();

        // Para cada plato seleccionado
        foreach ($plato_ids as $plato_id) {
            $plato_id = (int)$plato_id;

            // 1. Actualiza mesa y posición
            $stmt = $pdo->prepare('UPDATE platos SET mesa_id = ?, posicion = ? WHERE id = ?');
            $stmt->execute([$mesa_id, $posicion, $plato_id]);

            // 2. Inserta el turno (sin eliminar otros)
            $stmt_check = $pdo->prepare('SELECT plato_id FROM plato_turnos WHERE plato_id = ? AND turno_id = ?');
            $stmt_check->execute([$plato_id, $turno_id]);

            if (!$stmt_check->fetch()) {  // Si no existe ya
                $stmt_insert = $pdo->prepare('INSERT INTO plato_turnos (plato_id, turno_id) VALUES (?, ?)');
                $stmt_insert->execute([$plato_id, $turno_id]);
            }

            // 3. Log
            $stmt_nombre = $pdo->prepare('SELECT nombre_es FROM platos WHERE id = ?');
            $stmt_nombre->execute([$plato_id]);
            $plato = $stmt_nombre->fetch(PDO::FETCH_ASSOC);

            $stmt_turno = $pdo->prepare('SELECT nombre FROM turnos WHERE id = ?');
            $stmt_turno->execute([$turno_id]);
            $turno = $stmt_turno->fetch(PDO::FETCH_ASSOC);

            $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
            $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Asignó ' . $plato['nombre_es'] . ' a turno ' . $turno['nombre']]);
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
