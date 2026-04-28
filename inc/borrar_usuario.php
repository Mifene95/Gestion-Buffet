<?php

require('db.php');
require('auth_check.php');
validar_acceso([1]);

if ($_POST) {
    $id_usuario = $_POST['usuario_id'];

    try {
        $stmt = $pdo->prepare('DELETE from usuarios WHERE id = ?');
        $resultado = $stmt->execute([$id_usuario]);

        if ($resultado) {
            echo 'ok';
        } else {
            echo 'error_tecnico';
        }
    } catch (Exception $e) {
        echo "Error" . $e->getMessage();
    }
}
