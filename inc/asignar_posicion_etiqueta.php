<?php
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);

$barcode = $_POST['barcode'] ?? null;
$posicion_id = $_POST['posicion_id'] ?? null;
$nombre_posicion = $_POST['nombre_posicion'] ?? null;

if (!$barcode || !$posicion_id || !$nombre_posicion) {
    echo 'Error: faltan datos';
    exit();
}

// INSERT o UPDATE si ya existe
$stmt = $pdo->prepare('
    INSERT INTO etiqueta_posiciones (etiqueta_barcode, posicion_id, nombre_posicion)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE posicion_id = ?, nombre_posicion = ?
');
$stmt->execute([$barcode, $posicion_id, $nombre_posicion, $posicion_id, $nombre_posicion]);

echo 'ok';
