<?php
require 'db.php';

// Obtener horarios de la BD
$stmt = $pdo->query('SELECT nombre, hora_inicio FROM turnos');
$turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ruta al script PHP
$ruta_php = '/Applications/XAMPP/xamppfiles/bin/php';
$ruta_script = '/Applications/XAMPP/xamppfiles/htdocs/Gestion-Buffet/inc/cambiar_turno_automatico.php';

// Generar líneas del crontab
$cron_lines = [];
foreach ($turnos as $turno) {
    $hora = date('H', strtotime($turno['hora_inicio']));
    $minuto = date('i', strtotime($turno['hora_inicio']));
    $cron_lines[] = "{$minuto} {$hora} * * * {$ruta_php} {$ruta_script}";
}

// Escribir el crontab
$cron_content = implode("\n", $cron_lines) . "\n";
file_put_contents('/tmp/crontab_buffet', $cron_content);
exec('crontab /tmp/crontab_buffet');

echo "Crontab actualizado:\n" . $cron_content;
