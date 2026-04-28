<?php
require('db.php');
require('auth_check.php');
validar_acceso([1]);

header('Content-Type: application/json');

$stmt = $pdo->prepare('
    SELECT 
        usuarios.id,
        usuarios.username,
        usuarios.email,
        roles.nombre AS rol,
        usuarios.estado
    FROM usuarios
    LEFT JOIN roles ON usuarios.role_id = roles.id
');
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($usuarios);
