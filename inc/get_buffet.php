<?php
require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

header('Content-Type: application/json');

$stmt = $pdo->prepare("
    SELECT 
        mesas.id as mesa_id,
        mesas.nombre AS mesa,
        platos.posicion,
        turnos.id as turno_id,
        turnos.nombre as turno_nombre,
        platos.nombre_es,
        platos.id as plato_id
    FROM mesas
    LEFT JOIN platos ON mesas.id = platos.mesa_id
    LEFT JOIN plato_turnos ON platos.id = plato_turnos.plato_id
    LEFT JOIN turnos ON plato_turnos.turno_id = turnos.id
    ORDER BY mesas.id, platos.posicion, turnos.id
");

$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$buffet = [];
foreach ($datos as $row) {
    $key = $row['mesa_id'] . '_' . $row['posicion'];

    if (!isset($buffet[$key])) {
        $buffet[$key] = [
            'mesa_id' => $row['mesa_id'],
            'mesa' => $row['mesa'],
            'posicion' => $row['posicion'],
            'turnos' => []
        ];
    }

    if ($row['turno_id']) {
        $buffet[$key]['turnos'][$row['turno_id']] = [
            'turno_nombre' => $row['turno_nombre'],
            'plato_id' => $row['plato_id'],
            'plato_nombre' => $row['nombre_es']
        ];
    }
}

echo json_encode(array_values($buffet));
