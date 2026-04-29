<?php

session_start();

require 'db.php';
require '../inc/auth_check.php';
validar_acceso([1]);

if ($_POST) {
    $nombre_usuario = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $rol = $_POST['role_id'];

    if ($password !== $password_confirm) {
        echo 'pass_mismatch';
        exit;
    }

    $password_plana = $_POST['password'];
    $password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare('INSERT into usuarios (username, email, password, role_id) VALUES (?,?,?,?)');
        $resultado = $stmt->execute([$nombre_usuario, $email, $password_hash, $rol]);

        $stmt_log = $pdo->prepare('INSERT into logs_cambios (usuario_id, accion, fecha) VALUES (?, ?, NOW())');
        $stmt_log->execute([$_SESSION['user_id'], 'Creó un nuevo usuario: ' . $nombre_usuario]);

        if ($resultado) {
            echo 'ok';
        } else {
            echo 'error_tecnico';
        }
    } catch (Exception $e) {
        //ROLLBACK si se queda abierta
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "Error" . $e->getMessage();
    }
}
