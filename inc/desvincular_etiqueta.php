<?php
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);
require 'zkong_auth.php';

$barcode = $_POST['barcode'] ?? null;

if (!$barcode) {
    echo 'Error: falta el barcode';
    exit();
}

$login = zkong_login();
$token = $login['data']['token'];

$url = ZKONG_URL . '/zk/bind/batchUnbind';

$body = json_encode([
    'storeId' => ZKONG_STORE_ID,
    'tagItemBinds' => [
        ['eslBarcode' => $barcode]
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
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if ($data['success']) {
    echo 'ok';
} else {
    echo 'Error: ' . $data['message'];
}
