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

echo json_encode($data['data']['list'] ?? []);
