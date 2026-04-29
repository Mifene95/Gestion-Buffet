<?php
include("db.php");
include("auth_check.php");
validar_acceso([1]);

if ($_POST) {
    $id_plato = $_POST["plato_id"];

    try {
        $pdo->beginTransaction();

        // Obtener el nombre del plato ANTES de borrarlo
        $stmtNombrePlato = $pdo->prepare("SELECT nombre_es FROM platos WHERE id = ?");
        $stmtNombrePlato->execute([$id_plato]);
        $plato = $stmtNombrePlato->fetch(PDO::FETCH_ASSOC);
        $nombre_plato = $plato ? $plato['nombre_es'] : 'Desconocido';

        // Registrar la eliminación CON el nombre
        $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
        $stmt_log->execute([$_SESSION['user_id'], $id_plato, 'Eliminó plato: ' . $nombre_plato]);

        // Ahora borrar el plato
        $stmt = $pdo->prepare("DELETE FROM platos WHERE id = ?");
        $resultado = $stmt->execute([$id_plato]);

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
