
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

function csv_seguro($valor): string
{
    $valor = (string) ($valor ?? '');

    // Evitar fórmulas al abrir el CSV en Excel.
    if (preg_match('/^[\s\x00-\x1F]*[=+\-@]/u', $valor)) {
        $valor = "'" . $valor;
    }

    return $valor;
}

$stmt = $pdo->query(
    "SELECT m.fecha_hora, m.tipo, r.nombres, r.cedula,
            r.torre, r.apartamento, r.placa, r.color,
            p.numero AS puesto, a.nombre AS administrador
     FROM movimientos m
     JOIN residentes r ON r.id = m.residente_id
     LEFT JOIN puestos p ON p.id = m.puesto_id
     LEFT JOIN administradores a ON a.id = m.administrador_id
     ORDER BY m.fecha_hora DESC
     LIMIT 10000"
);

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="movimientos.csv"');

$out = fopen('php://output', 'w');

// BOM para facilitar la lectura de caracteres españoles en Excel.
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, [
    'Fecha y hora', 'Tipo', 'Residente', 'Cedula',
    'Torre', 'Apartamento', 'Placa', 'Color',
    'Puesto', 'Administrador'
], ';');

while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($out, array_map('csv_seguro', array_values($r)), ';');
}

fclose($out);
exit;
?>
