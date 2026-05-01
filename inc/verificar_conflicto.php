<?php

session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_POST) {
    $mesa_id = $_POST['mesa_id'];
    $posicion = $_POST['posicion'];
    $turnos = $_POST['turnos'] ?? [];

    try {
        // Verificar si existe la posición con alguno de esos turnos
        $placeholders = implode(',', array_fill(0, count($turnos), '?'));

        $stmt = $pdo->prepare("
            SELECT pt.turno_id, pl.nombre_es, t.nombre
            FROM posicion_platos pt
            JOIN plato_posiciones pp ON pt.posicion_id = pp.id
            JOIN platos pl ON pt.plato_id = pl.id
            JOIN turnos t ON pt.turno_id = t.id
            WHERE pp.mesa_id = ? AND pp.posicion = ? AND pt.turno_id IN ($placeholders)
            LIMIT 1
        ");

        $params = array_merge([$mesa_id, $posicion], $turnos);
        $stmt->execute($params);
        $conflicto = $stmt->fetch();

        if ($conflicto) {
            echo json_encode([
                'conflicto' => true,
                'plato_nombre' => $conflicto['nombre_es'],
                'turno_nombre' => $conflicto['nombre']
            ]);
        } else {
            echo json_encode(['conflicto' => false]);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
