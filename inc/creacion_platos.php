<?php

session_start();

require 'db.php';

if ($_POST) {
    $nombre_es = $_POST['nombre_es'];
    $nombre_en = $_POST['nombre_en'];
    $nombre_fr = $_POST['nombre_fr'];
    $mesa_id = $_POST['mesa_id'];
    $posicion = $_POST['posicion'];

    try {
        //Transaccion all in o nada
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('INSERT into platos (nombre_es, nombre_en, nombre_fr, mesa, posicion) VALUES(?,?,?,?,?)');
        $resultado = $stmt->execute([$nombre_es, $nombre_en, $nombre_fr, $mesa_id, $posicion]);

        $plato_id = $pdo->lastInsertId();

        //Insertamos los alergenos y el id del plato
        if (isset($_POST['alergenos']) && is_array($_POST['alergenos'])) {
            $stmtA = $pdo->prepare('INSERT into plato_alergenos (plato_id, alergeno_id) VALUES (?,?)');

            foreach ($_POST['alergenos'] as $alergeno_id) {
                $stmtA->execute([$plato_id, $alergeno_id]);
            }
        }
        $pdo->commit();

        if ($resultado) {
            echo 'ok';
        } else {
            echo 'error_tecnico';
        }
    } catch (Exception $e) {
        //ROLLBACK si se queda abierta
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "Error" . $e->getMessage();
    };
};
