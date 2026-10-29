
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $placa = strtoupper(trim($_POST['placa'] ?? ''));
    $tipo = $_POST['tipo'] ?? '';

    if ($placa === '' ||
        !in_array($tipo, ['entrada', 'salida'], true)) {
        exit('Datos de movimiento inválidos.');
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            'SELECT * FROM residentes WHERE placa = ? FOR UPDATE'
        );
        $stmt->execute([$placa]);
        $residente = $stmt->fetch();

        if (!$residente) {
            throw new RuntimeException('Vehículo no registrado.');
        }

        if ($tipo === 'entrada') {
            if ($residente['estado'] !== 'activo' ||
                $residente['autorizado'] !== 'autorizado') {
                throw new RuntimeException(
                    'Ingreso denegado: residente inactivo o no autorizado.'
                );
            }

            $stmt = $pdo->prepare(
                "SELECT id FROM puestos
                 WHERE estado = 'disponible'
                 ORDER BY numero LIMIT 1 FOR UPDATE"
            );
            $stmt->execute();
            $puesto = $stmt->fetch();

            if (!$puesto) {
                throw new RuntimeException(
                    'Parqueadero lleno: no hay puestos disponibles.'
                );
            }

            $puestoId = (int) $puesto['id'];

            $stmt = $pdo->prepare(
                "UPDATE puestos
                 SET estado = 'ocupado', residente_id = ?
                 WHERE id = ? AND estado = 'disponible'"
            );
            $stmt->execute([$residente['id'], $puestoId]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'No fue posible reservar el puesto.'
                );
            }
        } else {
            $stmt = $pdo->prepare(
                "SELECT id FROM puestos
                 WHERE residente_id = ? AND estado = 'ocupado'
                 LIMIT 1 FOR UPDATE"
            );
            $stmt->execute([$residente['id']]);
            $puesto = $stmt->fetch();

            if (!$puesto) {
                throw new RuntimeException(
                    'El vehículo no tiene un puesto ocupado asignado.'
                );
            }

            $puestoId = (int) $puesto['id'];

            $stmt = $pdo->prepare(
                "UPDATE puestos
                 SET estado = 'disponible', residente_id = NULL
                 WHERE id = ? AND estado = 'ocupado'"
            );
            $stmt->execute([$puestoId]);
        }

        $stmt = $pdo->prepare(
            'INSERT INTO movimientos
             (residente_id, puesto_id, tipo, administrador_id)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $residente['id'],
            $puestoId,
            $tipo,
            $_SESSION['admin_id']
        ]);

        $pdo->commit();
        $mensaje = 'Movimiento de ' . $tipo .
            ' registrado correctamente. Puesto: ' .
            $puestoId . '.';
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $mensaje = $e instanceof RuntimeException
            ? $e->getMessage()
            : 'No fue posible registrar el movimiento.';
        if (!($e instanceof RuntimeException)) {
            error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Control de acceso</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="topbar">
    <strong>Control de acceso vehicular</strong>
    <a class="btn btn-secondary" href="dashboard.php">Panel principal</a>
</header>
<main class="contenedor">
    <section class="panel">
        <h1>Registrar entrada o salida</h1>

        <?php if ($mensaje !== ''): ?>
            <p class="alerta">
                <?= htmlspecialchars($mensaje) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label for="placa">Placa del vehículo</label>
            <input id="placa" name="placa"
                   maxlength="10" required>

            <label for="tipo">Tipo de movimiento</label>
            <select id="tipo" name="tipo" required>
                <option value="entrada">Entrada</option>
                <option value="salida">Salida</option>
            </select>

            <button class="btn btn-primary" type="submit">
                Registrar movimiento
            </button>
        </form>
    </section>
</main>
</body>
</html>
