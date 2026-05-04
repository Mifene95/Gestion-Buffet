<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require('db.php');
require('auth_check.php');
validar_acceso([1]);



header('Content-Type: application/json');

$stmt = $pdo->prepare('
    SELECT 
        usuarios.id,
        usuarios.username,
        usuarios.email,
        roles.nombre as rol,
        estado.estado as estado
    FROM usuarios
    LEFT JOIN roles on usuarios.role_id = roles.id
    LEFT JOIN estado on usuarios.estado_id = estado.id
');
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($usuarios);
