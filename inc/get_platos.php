<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require('db.php');
require('auth_check.php');
validar_acceso([1, 2]);

header('Content-Type: application/json');

$stmt = $pdo->prepare("
    SELECT 
        platos.id,
        platos.nombre_es, 
        platos.nombre_en, 
        platos.nombre_fr,  
        platos.posicion,
        platos.mesa_id, -- Añadimos esto para tener el ID de la mesa
        mesas.nombre AS mesa,
        GROUP_CONCAT(DISTINCT alergenos.nombre SEPARATOR ', ') AS alergenos,
        GROUP_CONCAT(DISTINCT turnos.nombre SEPARATOR ', ') AS turnos,
        GROUP_CONCAT(DISTINCT turnos.id SEPARATOR ',') AS turnos_ids -- IMPORTANTE PARA TU FORMULARIO
    FROM platos
    LEFT JOIN plato_alergenos ON platos.id = plato_alergenos.plato_id
    LEFT JOIN alergenos ON plato_alergenos.alergeno_id = alergenos.id
    LEFT JOIN plato_turnos ON platos.id = plato_turnos.plato_id
    LEFT JOIN turnos ON plato_turnos.turno_id = turnos.id
    LEFT JOIN mesas on platos.mesa_id = mesas.id
    GROUP BY platos.id
");

$stmt->execute();
$platos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($platos);
