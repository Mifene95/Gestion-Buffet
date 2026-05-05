<?php
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);
require 'zkong_auth.php';

header('Content-Type: application/json');

$login = zkong_login();

if (!$login['success']) {
    echo json_encode(['error' => 'No se pudo conectar con ZKONG']);
    exit();
}

$token = $login['data']['token'];

$url = ZKONG_URL . '/zk/erp/esl/list?page=0&size=100';

$body = json_encode([
    'storeId'      => ZKONG_STORE_ID,
    'itemBarCode'  => '',
    'itemTitle'    => '',
    'priceTagCode' => '',
    'oemModel'     => '',
    'shelfNo'      => ''
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: ' . $token
]);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$etiquetas = $data['data']['list'] ?? [];

// Traer posiciones asignadas de la BD
$stmt = $pdo->query('
    SELECT ep.etiqueta_barcode, ep.nombre_posicion, m.nombre as mesa_nombre, pp.posicion
    FROM etiqueta_posiciones ep
    JOIN plato_posiciones pp ON ep.posicion_id = pp.id
    JOIN mesas m ON pp.mesa_id = m.id
');
$posiciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear mapa barcode → posicion
$mapa_posiciones = [];
foreach ($posiciones as $pos) {
    $mapa_posiciones[$pos['etiqueta_barcode']] = $pos['mesa_nombre'] . ' / ' . $pos['posicion'];
}

// Añadir posicion a cada etiqueta
foreach ($etiquetas as &$etiqueta) {
    $barcode = $etiqueta['priceTagCode'];
    $etiqueta['ubicacion'] = $mapa_posiciones[$barcode] ?? 'Sin asignar';
}

echo json_encode($etiquetas);
