<?php
require 'db.php';
require 'auth_check.php';
require 'zkong_auth.php';
require 'zkong_api.php';

header('Content-Type: application/json');

$hora_actual = date('H:i:s');

$stmt = $pdo->prepare('SELECT id, nombre FROM turnos WHERE hora_inicio <= ? AND hora_fin >= ?');
$stmt->execute([$hora_actual, $hora_actual]);
$turno_actual = $stmt->fetch(PDO::FETCH_ASSOC);

// SIN TURNO 
if (!$turno_actual) {
    $stmt = $pdo->query('SELECT etiqueta_barcode FROM etiqueta_posiciones');
    $etiquetas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $login = zkong_login();
    $token = $login['data']['token'];

    foreach ($etiquetas as $barcode) {
        $url = ZKONG_URL . '/zk/bind/batchUnbind';
        $body = json_encode(['storeId' => ZKONG_STORE_ID, 'tagItemBinds' => [['eslBarcode' => $barcode]]]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: ' . $token]);
        curl_exec($ch);
        curl_close($ch);

        $url = ZKONG_URL . '/zk/bind/bindItemPriceTag/1';
        $body = json_encode(['storeId' => ZKONG_STORE_ID, 'itemBarCode' => 'SIN_PLATO', 'priceTagCode' => $barcode]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: ' . $token]);
        curl_exec($ch);
        curl_close($ch);
    }

    echo json_encode(['turno' => null, 'mensaje' => 'Sin turno activo - mostrando logo buffet']);
    exit();
}

// CON TURNO
$stmt = $pdo->prepare('
    SELECT pp.posicion_id, pp.plato_id, p.nombre_es, p.nombre_en, p.nombre_fr,
        ep.etiqueta_barcode, GROUP_CONCAT(a.nombre SEPARATOR ", ") as alergenos
    FROM posicion_platos pp
    JOIN platos p ON pp.plato_id = p.id
    JOIN etiqueta_posiciones ep ON ep.posicion_id = pp.posicion_id
    LEFT JOIN plato_alergenos pa ON p.id = pa.plato_id
    LEFT JOIN alergenos a ON pa.alergeno_id = a.id
    WHERE pp.turno_id = ?
    GROUP BY pp.posicion_id, pp.plato_id
');
$stmt->execute([$turno_actual['id']]);
$platos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($platos)) {
    echo json_encode(['turno' => $turno_actual['nombre'], 'mensaje' => 'No hay platos asignados']);
    exit();
}

$login = zkong_login();
$token = $login['data']['token'];
$actualizadas = 0;
$errores = 0;

foreach ($platos as $plato) {
    $resultado = zkong_enviar_plato(
        $plato['plato_id'],
        $plato['nombre_es'],
        $plato['nombre_en'],
        $plato['nombre_fr'],
        $plato['alergenos'] ?? '',
        $token
    );

    if (!$resultado['success']) {
        $errores++;
        continue;
    }

    $url = ZKONG_URL . '/zk/bind/batchUnbind';
    $body = json_encode(['storeId' => ZKONG_STORE_ID, 'tagItemBinds' => [['eslBarcode' => $plato['etiqueta_barcode']]]]);
    $ch1 = curl_init($url);
    curl_setopt($ch1, CURLOPT_POST, true);
    curl_setopt($ch1, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch1, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: ' . $token]);
    curl_exec($ch1);
    curl_close($ch1);

    $url = ZKONG_URL . '/zk/bind/bindItemPriceTag/1';
    $body = json_encode(['storeId' => ZKONG_STORE_ID, 'itemBarCode' => 'PLATO_' . $plato['plato_id'], 'priceTagCode' => $plato['etiqueta_barcode']]);
    $ch2 = curl_init($url);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: ' . $token]);
    $response = curl_exec($ch2);
    curl_close($ch2);

    $data = json_decode($response, true);
    $data['success'] ? $actualizadas++ : $errores++;
}

echo json_encode([
    'turno'        => $turno_actual['nombre'],
    'hora'         => $hora_actual,
    'actualizadas' => $actualizadas,
    'errores'      => $errores
]);
