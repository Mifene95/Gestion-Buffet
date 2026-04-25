<?php

session_start();

require 'db.php';

if ($_POST) {
    $nombre_es = $_POST['nombre_es'];
    $nombre_en = $_POST['nombre_en'];
    $nombre_fr = $_POST['nombre_fr'];
    $mesa_id = $_POST['mesa_id'];
    $posicion = $_POST['posicio'];

    try {
        $stmt = $pdo->prepare('INSERT into platos (nombre_es, nombre_en, nombre_fr, mesa, posicion) VALUES(?,?,?,?,?)');
        $resultado = $stmt->execute([$nombre_es, $nombre_en, $nombre_fr, $mesa_id, $posicion]);

        if ($resultado) {
            echo 'ok';
        } else {
            echo 'error_tecnico';
        }
    } catch (Exception $e) {
        echo "Error" . $e->getMessage();
    };
};
