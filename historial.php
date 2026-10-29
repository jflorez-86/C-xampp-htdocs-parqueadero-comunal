
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$placa = strtoupper(trim($_GET['placa'] ?? ''));
$tipo = $_GET['tipo'] ?? '';

if (($desde !== '' &&
     !preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) ||
    ($hasta !== '' &&
     !preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) ||
    ($tipo !== '' && !in_array($tipo, ['entrada', 'salida'], true))) {
    exit('Los filtros enviados no son válidos.');
}

$sql = "SELECT m.id, m.tipo, m.fecha_hora,
               r.nombres, r.cedula, r.placa,
               p.numero AS puesto,
               a.nombre AS administrador
        FROM movimientos m
        JOIN residentes r ON r.id = m.residente_id
        LEFT JOIN puestos p ON p.id = m.puesto_id
        LEFT JOIN administradores a ON a.id = m.administrador_id
        WHERE 1=1";

$params = [];

if ($desde !== '') {
    $sql .= ' AND m.fecha_hora >= ?';
    $params[] = $desde . ' 00:00:00';
}
if ($hasta !== '') {
    $fechaFinal = DateTimeImmutable::createFromFormat('!Y-m-d', $hasta);
    if (!$fechaFinal || $fechaFinal->format('Y-m-d') !== $hasta) {
        exit('Fecha final inválida.');
    }

    $sql .= ' AND m.fecha_hora < ?';
    $params[] = $fechaFinal->modify('+1 day')->format('Y-m-d');
}
if ($placa !== '') {
    $sql .= ' AND r.placa = ?';
    $params[] = $placa;
}
if ($tipo !== '') {
    $sql .= ' AND m.tipo = ?';
    $params[] = $tipo;
}

$sql .= ' ORDER BY m.fecha_hora DESC LIMIT 1000';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movimientos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial de movimientos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<main class="contenedor">
    <section class="panel">
        <h1>Historial de movimientos</h1>
        <form method="GET" class="form-grid">
            <div>
                <label>Desde</label>
                <input type="date" name="desde"
                       value="<?= htmlspecialchars($desde) ?>">
            </div>
            <div>
                <label>Hasta</label>
                <input type="date" name="hasta"
                       value="<?= htmlspecialchars($hasta) ?>">
            </div>
            <div>
                <label>Placa</label>
                <input name="placa" value="<?= htmlspecialchars($placa) ?>">
            </div>
            <div>
                <label>Tipo</label>
                <select name="tipo">
                    <option value="">Todos</option>
                    <option value="entrada"
                        <?= $tipo === 'entrada' ? 'selected' : '' ?>>
                        Entrada
                    </option>
                    <option value="salida"
                        <?= $tipo === 'salida' ? 'selected' : '' ?>>
                        Salida
                    </option>
                </select>
            </div>
            <div class="ancho-completo">
                <button class="btn btn-primary">Buscar</button>
                <a class="btn btn-secondary" href="exportar.php">
                    Exportar todo a CSV
                </a>
            </div>
        </form>

        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>Fecha y hora</th>
                        <th>Tipo</th>
                        <th>Residente</th>
                        <th>Placa</th>
                        <th>Puesto</th>
                        <th>Administrador</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($movimientos as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['fecha_hora']) ?></td>
                        <td><?= htmlspecialchars($m['tipo']) ?></td>
                        <td><?= htmlspecialchars($m['nombres']) ?></td>
                        <td><?= htmlspecialchars($m['placa']) ?></td>
                        <td><?= htmlspecialchars((string) $m['puesto']) ?></td>
                        <td><?= htmlspecialchars($m['administrador'] ?? 'N/A') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
