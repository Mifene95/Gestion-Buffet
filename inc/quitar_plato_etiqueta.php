<?php
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);
require 'zkong_auth.php';
require 'zkong_config.php';

$etiqueta_barcode = $_POST['etiqueta_barcode'] ?? null;

if (!$etiqueta_barcode) {
    echo 'Error: falta el barcode';
    exit();
}

$login = zkong_login();
$token = $login['data']['token'];

// PASO 1: Enviar producto "Sin Plato" a ZKONG
$url = ZKONG_URL . '/zk/item/batchImportItem';

$body = json_encode([
    'agencyId'        => ZKONG_AGENCY_ID,
    'merchantId'      => ZKONG_MERCHANT_ID,
    'storeId'         => ZKONG_STORE_ID,
    'emptyNeedDelete' => 1,
    'itemList' => [
        [
            'barCode'      => 'SIN_PLATO',
            'attrCategory' => 'practicas',
            'attrName'     => 'Fernandez2',
            'itemTitle'    => 'Bienvenido al HotelBuffet',
            'custFeature1' => '',
            'custFeature2' => '',
            'custFeature3' => '',
            'custFeature4' => ''
        ]
    ]
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: ' . $token
]);
curl_exec($ch);
curl_close($ch);

// PASO 2: Desvincular etiqueta primero
$url = ZKONG_URL . '/zk/bind/batchUnbind';

$body = json_encode([
    'storeId' => ZKONG_STORE_ID,
    'tagItemBinds' => [
        ['eslBarcode' => $etiqueta_barcode]
    ]
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: ' . $token
]);
curl_exec($ch);
curl_close($ch);

// PASO 3: Vincular etiqueta con "Sin Plato"
$url = ZKONG_URL . '/zk/bind/bindItemPriceTag/1';

$body = json_encode([
    'storeId'      => ZKONG_STORE_ID,
    'itemBarCode'  => 'SIN_PLATO',
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
