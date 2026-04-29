<?php

session_start();

require 'db.php';

if (isset($_SESSION['user_id'])) {
    $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, accion, fecha) VALUES (?, ?, NOW())');
    $stmt_log->execute([$_SESSION['user_id'], 'Logout']);
}

session_destroy();
header("Location: ../index.php");
exit();
