
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

validar_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('Residente inválido.');
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'SELECT id FROM residentes WHERE id = ? FOR UPDATE'
    );
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        throw new RuntimeException('Residente no encontrado.');
    }

    $stmt = $pdo->prepare(
        "SELECT id FROM puestos
         WHERE residente_id = ? AND estado = 'ocupado'
         LIMIT 1 FOR UPDATE"
    );
    $stmt->execute([$id]);

    if ($stmt->fetch()) {
        throw new RuntimeException(
            'No se puede desactivar mientras el vehículo esté dentro.'
        );
    }

    $stmt = $pdo->prepare(
        "UPDATE residentes SET estado = 'inactivo' WHERE id = ?"
    );
    $stmt->execute([$id]);

    $pdo->commit();
    header('Location: residentes.php?desactivado=1');
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(409);
    exit(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
