<?php
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);

if ($_POST) {
    $id_usuario = $_POST['usuario_id'];
    $nombre_usuario = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['role_id'];
    $estado = $_POST['estado_id'];

    try {
        if (!empty($password)) {
            $password_confirm = $_POST['password_confirm'];
            if ($password !== $password_confirm) {
                echo 'pass_mismatch';
                exit;
            }

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('UPDATE usuarios SET username = ?, email = ?, password = ?, role_id = ?, estado_id = ? WHERE id = ?');
            $resultado = $stmt->execute([$nombre_usuario, $email, $password_hash, $rol, $estado, $id_usuario]);
        } else {
            $stmt = $pdo->prepare('UPDATE usuarios SET username = ?, email = ?, role_id = ?, estado_id = ? WHERE id = ?');
            $resultado = $stmt->execute([$nombre_usuario, $email, $rol, $estado, $id_usuario]);
        }

        if ($resultado) {
            echo 'ok';
        } else {
            echo 'error_tecnico';
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
