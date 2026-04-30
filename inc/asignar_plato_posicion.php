<?php
require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

if ($_POST) {
    $posicion_id = (int)$_POST['posicion_id'];
    $plato_id = (int)$_POST['plato_id'];
    $turno_id = (int)$_POST['turno_id'];

    try {
        $pdo->beginTransaction();

        // Verificar si ya existe esta asignación
        $stmt_check = $pdo->prepare('SELECT id FROM posicion_platos WHERE posicion_id = ? AND turno_id = ?');
        $stmt_check->execute([$posicion_id, $turno_id]);
        $existe = $stmt_check->fetch();

        if ($existe) {
            // Actualizar
            $stmt = $pdo->prepare('UPDATE posicion_platos SET plato_id = ? WHERE posicion_id = ? AND turno_id = ?');
            $stmt->execute([$plato_id, $posicion_id, $turno_id]);
        } else {
            // Insertar
            $stmt = $pdo->prepare('INSERT INTO posicion_platos (posicion_id, plato_id, turno_id) VALUES (?, ?, ?)');
            $stmt->execute([$posicion_id, $plato_id, $turno_id]);
        }

        // Log
        $stmt_plato = $pdo->prepare('SELECT nombre_es FROM platos WHERE id = ?');
        $stmt_plato->execute([$plato_id]);
        $plato = $stmt_plato->fetch(PDO::FETCH_ASSOC);

        $stmt_turno = $pdo->prepare('SELECT nombre FROM turnos WHERE id = ?');
        $stmt_turno->execute([$turno_id]);
        $turno = $stmt_turno->fetch(PDO::FETCH_ASSOC);

        $stmt_mesa = $pdo->prepare('SELECT m.nombre, pp.posicion FROM plato_posiciones pp JOIN mesas m ON pp.mesa_id = m.id WHERE pp.id = ?');
        $stmt_mesa->execute([$posicion_id]);
        $mesa = $stmt_mesa->fetch(PDO::FETCH_ASSOC);

        $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
        $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Asignó ' . $plato['nombre_es'] . ' a ' . $mesa['nombre'] . ' posición ' . $mesa['posicion'] . ' turno ' . $turno['nombre']]);

        $pdo->commit();
        echo 'ok';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "Error: " . $e->getMessage();
    }
}
