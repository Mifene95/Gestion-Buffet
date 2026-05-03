<?php
require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

header('Content-Type: application/json');

$stmt = $pdo->prepare("
    SELECT 
        pp.id as posicion_id,
        m.id as mesa_id,
        m.nombre as mesa,
        pp.posicion,
        pt.turno_id,
        t.nombre as turno_nombre,
        pt.plato_id,
        pl.nombre_es
    FROM plato_posiciones pp
    INNER JOIN mesas m ON pp.mesa_id = m.id
    LEFT JOIN posicion_platos pt ON pp.id = pt.posicion_id
    LEFT JOIN turnos t ON pt.turno_id = t.id
    LEFT JOIN platos pl ON pt.plato_id = pl.id
    ORDER BY m.id, pp.posicion, t.id
");


$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$buffet = [];
foreach ($datos as $row) {
    $key = $row['posicion_id'];

    if (!isset($buffet[$key])) {
        $buffet[$key] = [
            'posicion_id' => $row['posicion_id'],
            'mesa_id' => $row['mesa_id'],
            'mesa' => $row['mesa'],
            'posicion' => $row['posicion'],
            'seccion' => $row['mesa'] . ' / ' . $row['posicion'],
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
