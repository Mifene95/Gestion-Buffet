<?php
require('db.php');
require('auth_check.php');
validar_acceso([1]);

if ($_POST) {
    $plato_id = $_POST['plato_id'];
    $nombre_es = $_POST['nombre_es'];
    $nombre_en = $_POST['nombre_en'];
    $nombre_fr = $_POST['nombre_fr'];
    $mesa_id = $_POST['mesa_id'];
    $posicion = $_POST['posicion'];
    $turnos = $_POST['turnos'] ?? [];

    try {
        $pdo->beginTransaction();


        $stmt = $pdo->prepare('UPDATE platos SET nombre_es = ?, nombre_en = ?, nombre_fr = ?, mesa_id = ?, posicion = ? WHERE id = ?');
        $stmt->execute([$nombre_es, $nombre_en, $nombre_fr, $mesa_id, $posicion, $plato_id]);

        //borrar turnos
        $stmt = $pdo->prepare('DELETE FROM plato_turnos WHERE plato_id = ?');
        $stmt->execute([$plato_id]);


        if (!empty($turnos)) {
            $stmti = $pdo->prepare('INSERT INTO plato_turnos (plato_id, turno_id) VALUES (?, ?)');
            foreach ($turnos as $turno_id) {
                $stmti->execute([$plato_id, $turno_id]);
            }
        }

        $pdo->commit();
        echo 'ok';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "Error: " . $e->getMessage();
    }
}
