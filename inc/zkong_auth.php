<?php
require_once __DIR__ . '/zkong_config.php';

// PASO 1: Obtener clave pública RSA de ZKONG
function zkong_get_public_key()
{
    $url = ZKONG_URL . '/zk/user/getErpPublicKey';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($data['success']) {
        return $data['data'];
    }

    return null;
}

// PASO 2: Encriptar password con RSA usando openssl nativo de PHP
function zkong_encrypt_password($password, $publicKeyString)
{
    $keyString = '-----BEGIN PUBLIC KEY-----' . "\n" .
        chunk_split($publicKeyString, 64, "\n") .
        '-----END PUBLIC KEY-----';

    $publicKey = openssl_pkey_get_public($keyString);

    openssl_public_encrypt($password, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);

    return base64_encode($encrypted);
}

// PASO 3: Login y obtener token
function zkong_login()
{
    $publicKey = zkong_get_public_key();

    if (!$publicKey) {
        return ['success' => false, 'message' => 'No se pudo obtener la clave pública'];
    }

    $passwordEncriptada = zkong_encrypt_password(ZKONG_PASSWORD, $publicKey);

    $url = ZKONG_URL . '/zk/user/login';
    $body = json_encode([
        'account' => ZKONG_ACCOUNT,
        'password' => $passwordEncriptada,
        'loginType' => 3
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Language: es'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}
