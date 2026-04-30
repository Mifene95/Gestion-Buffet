<?php
require('db.php');
require('auth_check.php');
validar_acceso([1]);

if ($_POST) {
    $plato_ids = array_filter(explode(',', $_POST['plato_ids']));

    if (empty($plato_ids)) {
        echo "Error: No hay platos para borrar";
        exit();
    }

    try {
        $pdo->beginTransaction();

        foreach ($plato_ids as $plato_id) {
            $plato_id = (int)$plato_id;

            // Obtener nombre antes de borrar
            $stmt_nombre = $pdo->prepare("SELECT nombre_es FROM platos WHERE id = ?");
            $stmt_nombre->execute([$plato_id]);
            $plato = $stmt_nombre->fetch(PDO::FETCH_ASSOC);
            $nombre_plato = $plato ? $plato['nombre_es'] : 'Desconocido';

            // Borrar primero
            $stmt = $pdo->prepare("DELETE FROM platos WHERE id = ?");
            $stmt->execute([$plato_id]);

            // Log DESPUÉS de borrar (plato_id será NULL)
            $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, NULL, ?, NOW())');
            $stmt_log->execute([$_SESSION['user_id'], 'Eliminó plato: ' . $nombre_plato]);
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
