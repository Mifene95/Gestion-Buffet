<?php

include("db.php");
include("auth_check.php");

validar_acceso([1]);

if ($_POST) {
    $id_plato = $_POST["plato_id"];

    try {
        $stmt = $pdo->prepare("DELETE from platos WHERE id = ?");
        $resultado = $stmt->execute([$id_plato]);

        if ($resultado) {
            echo 'ok';
        } else {
            echo 'error_tecnico';
        }
    } catch (Exception $e) {
        echo "Error" . $e->getMessage();
    }
}
