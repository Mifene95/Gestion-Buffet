<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    if (empty($email) || empty($pass)) {
        header("Location: ../index.php?error=vacio");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {

        session_regenerate_id();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre']  = $user['username'];
        $_SESSION['role_id'] = $user['role_id'];

        //Obtener ip y navegador
        $ip = $_SERVER['REMOTE_ADDR'];
        $navegador = $_SERVER['HTTP_USER_AGENT'];

        $stmt_login = $pdo->prepare('INSERT into logs_login (usuario_id, ip_address, navegador, fecha) VALUES (?, ?, ?, NOW())');
        $stmt_login->execute([$_SESSION['user_id'], $ip, $navegador]);




        header("Location: ../pages/dashboard.php");
        exit();
    } else {
        header("Location: ../index.php?error=1");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
