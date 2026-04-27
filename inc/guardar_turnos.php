<?php

require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

if ($_POST) {
    $plato_id = $_POST['plato_id'];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('DELETE from plato_turnos WHERE plato_id = ?');
        $stmt->execute([$plato_id]);

        if (isset($_POST['turnos']) && is_array($_POST['turnos'])) {
            $stmti = $pdo->prepare('INSERT into plato_turnos (plato_id, turno_id) VALUES (?,?)');
            foreach ($_POST['turnos'] as $turno_id) {
                $stmti->execute([$plato_id, $turno_id]);
            }
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
