<?php
require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

header('Content-Type: application/json');

$plato_ids = explode(',', $_GET['plato_ids']);
$plato_ids = array_map('intval', $plato_ids);
$plato_ids = array_filter($plato_ids);

if (empty($plato_ids)) {
    echo json_encode([]);
    exit();
}

$placeholders = implode(',', array_fill(0, count($plato_ids), '?'));
$stmt = $pdo->prepare("SELECT id, nombre_es FROM platos WHERE id IN ($placeholders)");
$stmt->execute($plato_ids);
$platos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($platos);
