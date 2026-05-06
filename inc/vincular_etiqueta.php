<?php
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);
require 'zkong_auth.php';
require 'zkong_api.php';

$plato_id = $_POST['plato_id'] ?? null;
$etiqueta_barcode = $_POST['etiqueta_barcode'] ?? null;

if (!$plato_id || !$etiqueta_barcode) {
    echo 'Error: faltan datos';
    exit();
}

// Obtener datos del plato
$stmt = $pdo->prepare('
    SELECT p.nombre_es, p.nombre_en, p.nombre_fr,
        GROUP_CONCAT(a.nombre SEPARATOR ", ") as alergenos
    FROM platos p
    LEFT JOIN plato_alergenos pa ON p.id = pa.plato_id
    LEFT JOIN alergenos a ON pa.alergeno_id = a.id
    WHERE p.id = ?
    GROUP BY p.id
');
$stmt->execute([$plato_id]);
$plato = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plato) {
    echo 'Error: plato no encontrado';
    exit();
}

// LOGIN
$login = zkong_login();
$token = $login['data']['token'];

// PASO 1: Enviar plato a ZKONG
$resultado = zkong_enviar_plato(
    $plato_id,
    $plato['nombre_es'],
    $plato['nombre_en'],
    $plato['nombre_fr'],
    $plato['alergenos'] ?? 'Sin alérgenos',
    $token
);

if (!$resultado['success']) {
    echo 'Error al importar plato: ' . $resultado['message'];
    exit();
}

// PASO 2: Vincular etiqueta con plato
$login = zkong_login();
$token = $login['data']['token'];

$url = ZKONG_URL . '/zk/bind/bindItemPriceTag/1';

$body = json_encode([
    'storeId'      => ZKONG_STORE_ID,
    'itemBarCode'  => 'PLATO_' . $plato_id,
    'priceTagCode' => $etiqueta_barcode
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

if ($data['success']) {
    echo 'ok';
} else {
    echo 'Error: ' . $data['message'];
}
