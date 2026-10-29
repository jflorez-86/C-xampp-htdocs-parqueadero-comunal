
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $actual = $_POST['actual'] ?? '';
    $nueva = $_POST['nueva'] ?? '';
    $confirmacion = $_POST['confirmacion'] ?? '';

    if (strlen($nueva) < 12 || $nueva !== $confirmacion) {
        $mensaje = 'La nueva contraseña debe tener al menos 12 caracteres y coincidir.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT password FROM administradores WHERE id = ?'
        );
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($actual, $admin['password'])) {
            $mensaje = 'La contraseña actual no es correcta.';
        } else {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'UPDATE administradores SET password = ? WHERE id = ?'
            );
            $stmt->execute([$hash, $_SESSION['admin_id']]);

            session_regenerate_id(true);
            $mensaje = 'Contraseña actualizada correctamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar contraseña</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<main class="contenedor">
    <section class="panel">
        <h1>Cambiar contraseña</h1>

        <?php if ($mensaje !== ''): ?>
            <p><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>
            <label>Contraseña actual</label>
            <input type="password" name="actual"
                   autocomplete="current-password" required>

            <label>Nueva contraseña</label>
            <input type="password" name="nueva"
                   minlength="12" autocomplete="new-password" required>

            <label>Confirmar nueva contraseña</label>
            <input type="password" name="confirmacion"
                   minlength="12" autocomplete="new-password" required>

            <button class="btn btn-primary">Actualizar contraseña</button>
        </form>
    </section>
</main>
</body>
</html>
