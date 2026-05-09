<?php
require 'db.php';
require 'auth_check.php';
validar_acceso([1]);

$dia_semana = isset($_POST['dia_semana']) ? (int)$_POST['dia_semana'] : null;
$turnos     = $_POST['turnos'] ?? [];

if ($dia_semana === null || !in_array($dia_semana, [0,1,2,3,4,5,6], true) || empty($turnos)) {
    echo 'Error: datos inválidos';
    exit();
}

$stmt = $pdo->prepare('
    UPDATE turnos_horarios
    SET hora_inicio = ?, hora_fin = ?
    WHERE turno_id = ? AND dia_semana = ?
');

foreach ($turnos as $turno_id => $horario) {
    $stmt->execute([
        $horario['hora_inicio'],
        $horario['hora_fin'],
        (int)$turno_id,
        $dia_semana,
    ]);
}

echo 'ok';
