<?php
require 'db.php';
require 'auth_check.php';

header('Content-Type: application/json');

$hora_actual = date('H:i:s');
$dia_semana  = (int)date('w');

$stmt = $pdo->prepare('
    SELECT t.id, t.nombre
    FROM turnos_horarios th
    JOIN turnos t ON t.id = th.turno_id
    WHERE th.dia_semana = ? AND th.hora_inicio <= ? AND th.hora_fin >= ?
');
$stmt->execute([$dia_semana, $hora_actual, $hora_actual]);
$turno = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'turno' => $turno ? $turno['nombre'] : null,
    'hora'  => $hora_actual,
]);
