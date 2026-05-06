<?php
require 'db.php';
require 'auth_check.php';

header('Content-Type: application/json');

$hora_actual = date('H:i:s');

$stmt = $pdo->prepare('
    SELECT id, nombre 
    FROM turnos 
    WHERE hora_inicio <= ? AND hora_fin >= ?
');
$stmt->execute([$hora_actual, $hora_actual]);
$turno = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'turno' => $turno ? $turno['nombre'] : null,
    'hora'  => $hora_actual
]);
