<?php
require_once __DIR__ . '/zkong_auth.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// FUNCIÓN PRINCIPAL: Enviar plato a ZKONG
function zkong_enviar_plato($plato_id, $nombre_es, $nombre_en, $nombre_fr, $alergenos)
{

    // PASO 1: Login para obtener token
    $login = zkong_login();

    if (!$login['success']) {
        return ['success' => false, 'message' => 'Error al conectar con ZKONG'];
    }

    $token = $login['data']['token'];

    // PASO 2: Formatear alérgenos como texto
    // $alergenos viene como "Gluten, Lácteos, Huevos"
    $alergenos_texto = !empty($alergenos) ? $alergenos : 'Sin alérgenos';

    // PASO 3: Preparar datos del producto
    $body = json_encode([
        'agencyId'       => ZKONG_AGENCY_ID,
        'merchantId'     => ZKONG_MERCHANT_ID,
        'storeId'        => ZKONG_STORE_ID,
        'emptyNeedDelete' => 1,  // sobreescribe aunque esté vacío
        'itemList' => [
            [
                'barCode'      => 'PLATO_' . $plato_id,
                'attrCategory' => 'practicas',
                'attrName'     => 'Fernandez',
                'itemTitle'    => $nombre_es,
                'custFeature1' => $nombre_es,
                'custFeature2' => $nombre_en,
                'custFeature3' => $nombre_fr,
                'custFeature4' => $alergenos_texto
            ]
        ]
    ]);

    // PASO 4: Enviar a ZKONG
    $url = ZKONG_URL . '/zk/item/batchImportItem';

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

    return json_decode($response, true);
}
