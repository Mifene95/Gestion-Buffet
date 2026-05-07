<?php
require_once __DIR__ . '/db.php';

$stmt = $pdo->query('SELECT nombre, hora_inicio FROM turnos');
$turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ruta_php = '/Applications/XAMPP/xamppfiles/bin/php';
$ruta_script = '/Applications/XAMPP/xamppfiles/htdocs/Gestion-Buffet/inc/cambiar_turno_cron.php';

$cron_lines = [];
foreach ($turnos as $turno) {
    $hora   = date('H', strtotime($turno['hora_inicio']));
    $minuto = date('i', strtotime($turno['hora_inicio']));
    $cron_lines[] = "{$minuto} {$hora} * * * {$ruta_php} {$ruta_script}";
}

$cron_content = implode("\n", $cron_lines) . "\n";
file_put_contents('/tmp/crontab_buffet', $cron_content);
exec('crontab /tmp/crontab_buffet');
