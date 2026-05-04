<?php
require_once 'inc/zkong_auth.php';

$login = zkong_login();
$token = $login['data']['token'];

// Buscar tiendas
$url = ZKONG_URL . '/zk/store/storeList';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: ' . $token
]);
$response = curl_exec($ch);
curl_close($ch);

echo "<pre>";
print_r(json_decode($response, true));
echo "</pre>";
