<?php
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);

$turnos = $_POST['turnos'] ?? [];

if (empty($turnos)) {
    echo 'Error: no hay datos';
    exit();
}

foreach ($turnos as $turno_id => $horario) {
    $stmt = $pdo->prepare('UPDATE turnos SET hora_inicio = ?, hora_fin = ? WHERE id = ?');
    $stmt->execute([
        $horario['hora_inicio'],
        $horario['hora_fin'],
        $turno_id
    ]);
}

// Actualizar el CRON con los nuevos horarios
require 'actualizar_cron.php';


echo 'ok';
