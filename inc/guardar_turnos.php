<?php
require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

if ($_POST) {
    $plato_id = $_POST['plato_id'];

    try {
        $pdo->beginTransaction();

        // Obtener el nombre del plato
        $stmtNombrePlato = $pdo->prepare("SELECT nombre_es FROM platos WHERE id = ?");
        $stmtNombrePlato->execute([$plato_id]);
        $plato = $stmtNombrePlato->fetch(PDO::FETCH_ASSOC);
        $nombre_plato = $plato ? $plato['nombre_es'] : 'Desconocido';

        $stmt = $pdo->prepare('DELETE FROM plato_turnos WHERE plato_id = ?');
        $stmt->execute([$plato_id]);

        if (isset($_POST['turnos']) && is_array($_POST['turnos'])) {
            $stmti = $pdo->prepare('INSERT into plato_turnos (plato_id, turno_id) VALUES (?,?)');
            $turnos_nombres = [];

            foreach ($_POST['turnos'] as $turno_id) {
                $stmti->execute([$plato_id, $turno_id]);
                $stmt_turno = $pdo->prepare('SELECT nombre FROM turnos WHERE id = ?');
                $stmt_turno->execute([$turno_id]);
                $turno = $stmt_turno->fetch(PDO::FETCH_ASSOC);
                $turnos_nombres[] = $turno['nombre'];
            }

            $turnos_str = implode(', ', $turnos_nombres);
            $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
            $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Asignó turnos: ' . $turnos_str . ' al plato: ' . $nombre_plato]);
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
