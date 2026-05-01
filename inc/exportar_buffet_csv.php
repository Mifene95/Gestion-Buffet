<?php

session_start();
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);

$filtro = $_GET['filtro'] ?? 'asignados';

// Obtener datos del buffet
$stmt = $pdo->prepare('
    SELECT pp.id as posicion_id, m.nombre as mesa, pp.posicion,
        pt.turno_id, t.nombre as turno_nombre, pl.nombre_es
    FROM plato_posiciones pp
    INNER JOIN mesas m ON pp.mesa_id = m.id
    LEFT JOIN posicion_platos pt ON pp.id = pt.posicion_id
    LEFT JOIN turnos t ON pt.turno_id = t.id
    LEFT JOIN platos pl ON pt.plato_id = pl.id
    ORDER BY m.id, pp.posicion, t.id
');
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar datos
$buffet = [];
foreach ($datos as $row) {
    $key = $row['mesa'] . ' - Pos ' . $row['posicion'];
    if (!isset($buffet[$key])) {
        $buffet[$key] = ['turnos' => []];
    }
    if ($row['turno_id']) {
        $buffet[$key]['turnos'][$row['turno_id']] = $row['nombre_es'];
    }
}

// FILTRAR SOLO ASIGNADOS si se solicita
if ($filtro === 'asignados') {
    $buffet = array_filter($buffet, function ($item) {
        return !empty($item['turnos']);
    });
}

// Generar CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=buffet_' . $filtro . '_' . date('Ymd_His') . '.csv');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

// Fecha en formato España
$fecha = new DateTime();
$fecha_formateada = $fecha->format('d/m/Y');

// Escribir encabezados
$titulo = ($filtro === 'asignados') ? 'BUFFET DEL HOTEL' : 'BUFFET DEL HOTEL (Completo)';
fwrite($output, $titulo . " - " . $fecha_formateada . "\n");

// Encabezados de tabla
fputcsv($output, ['Sección', 'Desayuno', 'Comida', 'Cena'], ';');

// Datos
foreach ($buffet as $seccion => $turnos) {
    $desayuno = $turnos['turnos'][1] ?? '-';
    $comida = $turnos['turnos'][2] ?? '-';
    $cena = $turnos['turnos'][3] ?? '-';

    fputcsv($output, [$seccion, $desayuno, $comida, $cena], ';');
}

fclose($output);
exit;
