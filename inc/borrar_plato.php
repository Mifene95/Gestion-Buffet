<?php
include("db.php");
include("auth_check.php");
validar_acceso([1]);

if ($_POST) {
    $id_plato = $_POST["plato_id"];

    try {
        $pdo->beginTransaction();

        $stmt_log = $pdo->prepare('INSERT INTO logs_cambios (usuario_id, plato_id, accion, fecha) VALUES (?, ?, ?, NOW())');
        $stmt_log->execute([$_SESSION['user_id'], $id_plato, 'Eliminó plato']);

        $stmt = $pdo->prepare("DELETE from platos WHERE id = ?");
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
