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
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('INSERT into platos (nombre_es, nombre_en, nombre_fr, mesa_id, posicion) VALUES(?,?,?,?,?)');
        $resultado = $stmt->execute([$nombre_es, $nombre_en, $nombre_fr, $mesa_id, $posicion]);

        $plato_id = $pdo->lastInsertId();

        // Registrar creación del plato
        $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
        $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Creó plato nuevo plato: ' . $nombre_es]);

        // Insertar alérgenos
        if (isset($_POST['alergenos']) && is_array($_POST['alergenos'])) {
            $stmtA = $pdo->prepare('INSERT into plato_alergenos (plato_id, alergeno_id) VALUES (?,?)');
            foreach ($_POST['alergenos'] as $alergeno_id) {
                $stmtA->execute([$plato_id, $alergeno_id]);
            }
        }

        // CREAR O VERIFICAR POSICIÓN EN plato_posiciones
        $stmt_check = $pdo->prepare('SELECT id FROM plato_posiciones WHERE mesa_id = ? AND posicion = ?');
        $stmt_check->execute([$mesa_id, $posicion]);
        $posicion_existente = $stmt_check->fetch();

        if ($posicion_existente) {
            $posicion_id = $posicion_existente['id'];
        } else {
            $stmt_posicion = $pdo->prepare('INSERT INTO plato_posiciones (mesa_id, posicion) VALUES (?, ?)');
            $stmt_posicion->execute([$mesa_id, $posicion]);
            $posicion_id = $pdo->lastInsertId();
        }

        // GUARDAR TURNOS EN posicion_platos (tabla nueva)
        if (isset($_POST['turno']) && is_array($_POST['turno'])) {
            $stmtT = $pdo->prepare('INSERT INTO posicion_platos (posicion_id, plato_id, turno_id) 
                                    VALUES (?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE plato_id = ?');
            foreach ($_POST['turno'] as $turno_id) {
                $stmtT->execute([$posicion_id, $plato_id, $turno_id, $plato_id]);
            }
        }

        $pdo->commit();

        if ($resultado) {
            echo 'ok';
        } else {
            echo 'error_tecnico';
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "Error: " . $e->getMessage();
    }
}
