<?php
require('db.php');
require('auth_check.php');
validar_acceso([1]);

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_POST) {
    $plato_id = $_POST['plato_id'];
    $nombre_es = $_POST['nombre_es'];
    $nombre_en = $_POST['nombre_en'];
    $nombre_fr = $_POST['nombre_fr'];
    $alergenos = $_POST['alergenos'] ?? [];

    try {
        $pdo->beginTransaction();

        // Obtener mesa y posición
        $stmt_plato = $pdo->prepare('SELECT p.nombre_es, p.posicion, m.nombre as mesa_nombre FROM platos p LEFT JOIN mesas m ON p.mesa_id = m.id WHERE p.id = ?');
        $stmt_plato->execute([$plato_id]);
        $plato_info = $stmt_plato->fetch(PDO::FETCH_ASSOC);
        $mesa_nombre = $plato_info['mesa_nombre'];
        $posicion = $plato_info['posicion'];

        // UPDATE plato
        $stmt = $pdo->prepare('UPDATE platos SET nombre_es = ?, nombre_en = ?, nombre_fr = ? WHERE id = ?');
        $stmt->execute([$nombre_es, $nombre_en, $nombre_fr, $plato_id]);

        // Registrar cambio de datos CON el nombre
        $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
        $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Editó datos del plato: ' . $nombre_es . ' en posición: ' . $posicion . ' (' . $mesa_nombre . ')']);

        // DELETE y INSERT alérgenos
        $stmt = $pdo->prepare('DELETE FROM plato_alergenos WHERE plato_id = ?');
        $stmt->execute([$plato_id]);

        if (!empty($alergenos)) {
            $stmt_alergenos = $pdo->prepare('INSERT INTO plato_alergenos (plato_id, alergeno_id) VALUES (?, ?)');
            $alergenos_nombres = [];

            foreach ($alergenos as $alergeno_id) {
                $stmt_alergenos->execute([$plato_id, $alergeno_id]);
                $stmt_alergeno = $pdo->prepare('SELECT nombre FROM alergenos WHERE id = ?');
                $stmt_alergeno->execute([$alergeno_id]);
                $alergeno = $stmt_alergeno->fetch(PDO::FETCH_ASSOC);
                $alergenos_nombres[] = $alergeno['nombre'];
            }

            $alergenos_str = implode(', ', $alergenos_nombres);
            $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
            $stmt_log->execute([$_SESSION['user_id'], $plato_id, 'Asignó alérgenos: ' . $alergenos_str . ' al plato: ' . $nombre_es]);
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
