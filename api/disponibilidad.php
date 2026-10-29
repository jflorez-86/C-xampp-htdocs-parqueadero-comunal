
<?php
require __DIR__ . '/../includes/seguridad.php';
require __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$puestos = $pdo->query(
    'SELECT numero, estado FROM puestos ORDER BY numero'
)->fetchAll();

$total = count($puestos);
$ocupados = count(array_filter(
    $puestos,
    fn($p) => $p['estado'] === 'ocupado'
));

echo json_encode([
    'capacidad' => 50,
    'total' => $total,
    'ocupados' => $ocupados,
    'disponibles' => $total - $ocupados,
    'puestos' => $puestos
], JSON_UNESCAPED_UNICODE);
?>
