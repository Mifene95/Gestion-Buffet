<?php
require 'db.php';
require 'auth_check.php';
require 'zkong_auth.php';
require 'zkong_api.php';

header('Content-Type: application/json');

// PASO 1: Obtener hora actual
$hora_actual = date('H:i:s');

// PASO 2: Buscar qué turno corresponde ahora
$stmt = $pdo->prepare('
    SELECT id, nombre 
    FROM turnos 
    WHERE hora_inicio <= ? AND hora_fin >= ?
');
$stmt->execute([$hora_actual, $hora_actual]);
$turno_actual = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turno_actual) {
    echo json_encode(['mensaje' => 'No hay turno activo ahora mismo']);
    exit();
}

// PASO 3: Buscar todos los platos asignados a ese turno
$stmt = $pdo->prepare('
    SELECT 
        pp.posicion_id,
        pp.plato_id,
        p.nombre_es,
        p.nombre_en,
        p.nombre_fr,
        ep.etiqueta_barcode,
        GROUP_CONCAT(a.nombre SEPARATOR ", ") as alergenos
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
    echo json_encode(['mensaje' => 'No hay platos asignados para este turno']);
    exit();
}

// PASO 4: Login ZKONG
$login = zkong_login();
$token = $login['data']['token'];

// PASO 5: Actualizar cada etiqueta
$actualizadas = 0;
$errores = 0;

foreach ($platos as $plato) {
    // Enviar plato a ZKONG
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

    // Desvincular etiqueta
    $url = ZKONG_URL . '/zk/bind/batchUnbind';
    $body = json_encode([
        'storeId' => ZKONG_STORE_ID,
        'tagItemBinds' => [['eslBarcode' => $plato['etiqueta_barcode']]]
    ]);

    $ch1 = curl_init($url);
    curl_setopt($ch1, CURLOPT_POST, true);
    curl_setopt($ch1, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch1, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . $token
    ]);
    curl_exec($ch1);
    curl_close($ch1);

    // Vincular etiqueta con nuevo plato
    $url = ZKONG_URL . '/zk/bind/bindItemPriceTag/1';
    $body = json_encode([
        'storeId'      => ZKONG_STORE_ID,
        'itemBarCode'  => 'PLATO_' . $plato['plato_id'],
        'priceTagCode' => $plato['etiqueta_barcode']
    ]);

    $ch2 = curl_init($url);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . $token
    ]);
    $response = curl_exec($ch2);
    curl_close($ch2);

    $data = json_decode($response, true);
    if ($data['success']) {
        $actualizadas++;
    } else {
        $errores++;
        echo json_encode([
            'error_zkong' => $data,
            'plato'       => $plato,
            'body_enviado' => json_decode($body, true)
        ]);
        exit();
    }
}

echo json_encode([
    'turno'        => $turno_actual['nombre'],
    'hora'         => $hora_actual,
    'actualizadas' => $actualizadas,
    'errores'      => $errores,
    'platos'       => $platos
]);
