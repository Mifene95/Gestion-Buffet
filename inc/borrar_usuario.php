<?php

require('db.php');
require('auth_check.php');
validar_acceso([1]);

if ($_POST) {
    $id_usuario = $_POST['usuario_id'];

    try {
        // Obtenemos nombre usuario antes de borrarlo
        $stmtNombreUsuario = $pdo->prepare('SELECT username FROM usuarios WHERE id = ?');
        $stmtNombreUsuario->execute([$id_usuario]);
        $usuario = $stmtNombreUsuario->fetch(PDO::FETCH_ASSOC);
        $username = $usuario ? $usuario['username'] : 'Desconocido';

        // Insertamos el log con la acción
        $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, accion, fecha) VALUES (?, ?, NOW())');
        $stmt_log->execute([$_SESSION['user_id'], 'Eliminó usuario: ' . $username]);

        // Ahora borramos el usuario
        $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
        $resultado = $stmt->execute([$id_usuario]);

        if ($resultado) {
            echo 'ok';
        } else {
            echo 'error_tecnico';
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
