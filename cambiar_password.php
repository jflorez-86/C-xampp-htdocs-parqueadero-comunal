
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

$mensaje = '';
$tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/csrf.php';
    validar_csrf();

    $actual = $_POST['actual'] ?? '';
    $nueva = $_POST['nueva'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (strlen($nueva) < 12) {
        $mensaje = 'La contraseña debe tener al menos 12 caracteres.';
        $tipo = 'error';
    } elseif ($nueva !== $confirmar) {
        $mensaje = 'Las contraseñas nuevas no coinciden.';
        $tipo = 'error';
    } else {
        $stmt = $pdo->prepare(
            'SELECT password FROM administradores WHERE id = ?'
        );
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($actual, $admin['password'])) {
            $mensaje = 'La contraseña actual es incorrecta.';
            $tipo = 'error';
        } else {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'UPDATE administradores SET password = ? WHERE id = ?'
            );
            $stmt->execute([$hash, $_SESSION['admin_id']]);

            session_regenerate_id(true);
            $mensaje = 'Contraseña actualizada correctamente.';
            $tipo = 'exito';
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
        <p>Actualiza la contraseña de tu cuenta de administrador.</p>

        <?php if ($mensaje !== ''): ?>
            <p role="status">
                <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>

            <label for="actual">Contraseña actual</label>
            <input id="actual" type="password" name="actual"
                   autocomplete="current-password" required>

            <label for="nueva">Nueva contraseña</label>
            <input id="nueva" type="password" name="nueva"
                   minlength="12" autocomplete="new-password" required>

            <label for="confirmar">Confirmar contraseña nueva</label>
            <input id="confirmar" type="password" name="confirmar"
                   minlength="12" autocomplete="new-password" required>

            <button class="btn btn-primary" type="submit">
                Guardar nueva contraseña
            </button>
        </form>

        <p><a href="dashboard.php">Volver al panel principal</a></p>
    </section>
</main>
</body>
</html>
