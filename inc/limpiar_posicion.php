<?php
require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

if ($_POST) {
    $posicion_id = (int)$_POST['posicion_id'];

    try {
        // Obtener info de la posición
        $stmt_info = $pdo->prepare('SELECT m.nombre, pp.posicion FROM plato_posiciones pp JOIN mesas m ON pp.mesa_id = m.id WHERE pp.id = ?');
        $stmt_info->execute([$posicion_id]);
        $info = $stmt_info->fetch(PDO::FETCH_ASSOC);

        // Borrar asignaciones
        $stmt = $pdo->prepare('DELETE FROM posicion_platos WHERE posicion_id = ?');
        $stmt->execute([$posicion_id]);

        // Log
        $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, NULL, ?, NOW())');
        $stmt_log->execute([$_SESSION['user_id'], 'Limpió posición: ' . $info['nombre'] . ' posición ' . $info['posicion']]);

        echo 'ok';
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
