<?php
require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

header('Content-Type: application/json');

$stmt = $pdo->prepare("
    SELECT id, nombre_es 
    FROM platos 
    ORDER BY nombre_es
");
$stmt->execute();
$platos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($platos);
