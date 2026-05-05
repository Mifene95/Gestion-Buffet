<?php
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);

header('Content-Type: application/json');

$mesa_id = $_GET['mesa_id'] ?? null;

$stmt = $pdo->prepare('SELECT id, posicion FROM plato_posiciones WHERE mesa_id = ? ORDER BY posicion');
$stmt->execute([$mesa_id]);
$posiciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($posiciones);
