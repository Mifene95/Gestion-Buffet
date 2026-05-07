<?php
require __DIR__ . '/db.php';
require __DIR__ . '/zkong_auth.php';
require __DIR__ . '/zkong_api.php';

$hora_actual = date('H:i:s');
$hora_minuto = date('H:i');

// Buscar turno activo ahora
$stmt = $pdo->prepare('SELECT id, nombre FROM turnos WHERE hora_inicio <= ? AND hora_fin >= ?');
$stmt->execute([$hora_actual, $hora_actual]);
$turno_actual = $stmt->fetch(PDO::FETCH_ASSOC);

// Leer el último turno guardado
$ultimo_turno_file = __DIR__ . '/ultimo_turno.txt';
$ultimo_turno = file_exists($ultimo_turno_file) ? trim(file_get_contents($ultimo_turno_file)) : '';

$turno_actual_nombre = $turno_actual ? $turno_actual['nombre'] : 'sin_turno';

// Si no ha cambiado, no hacer nada
if ($turno_actual_nombre === $ultimo_turno) {
    exit();
}

// Ha cambiado el turno → actualizar
file_put_contents($ultimo_turno_file, $turno_actual_nombre);

$login = zkong_login();
$token = $login['data']['token'];

// SIN TURNO → mostrar logo buffet
if (!$turno_actual) {
    $stmt = $pdo->query('SELECT etiqueta_barcode FROM etiqueta_posiciones');
    $etiquetas = $stmt->fetchAll(PDO::FETCH_COLUMN);

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

    $log = date('Y-m-d H:i:s') . " - Sin turno activo - mostrando logo buffet\n";
    file_put_contents(__DIR__ . '/cron_log.txt', $log, FILE_APPEND);
    exit();
}

// CON TURNO → actualizar platos
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
    $log = date('Y-m-d H:i:s') . " - Turno: " . $turno_actual['nombre'] . " - Sin platos asignados\n";
    file_put_contents(__DIR__ . '/cron_log.txt', $log, FILE_APPEND);
    exit();
}

$actualizadas = 0;

foreach ($platos as $plato) {
    $resultado = zkong_enviar_plato(
        $plato['plato_id'],
        $plato['nombre_es'],
        $plato['nombre_en'],
        $plato['nombre_fr'],
        $plato['alergenos'] ?? '',
        $token
    );

    if (!$resultado['success']) continue;

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
    curl_exec($ch2);
    curl_close($ch2);

    $actualizadas++;
}

$log = date('Y-m-d H:i:s') . " - Turno: " . $turno_actual['nombre'] . " - Actualizadas: {$actualizadas}\n";
file_put_contents(__DIR__ . '/cron_log.txt', $log, FILE_APPEND);
