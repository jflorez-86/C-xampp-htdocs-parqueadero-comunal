
<?php
session_start();
require __DIR__ . '/config/conexion.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT id, nombre, password
         FROM administradores WHERE usuario = ?'
    );
    $stmt->execute([$usuario]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nombre'] = $admin['nombre'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso | Parqueadero Comunal</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="login-page">
    <main class="login-card">
        <div class="logo">P</div>
        <h1>Parqueadero Comunal</h1>
        <p>Panel de control de acceso vehicular</p>

        <?php if ($error): ?>
            <div class="alerta">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="usuario">Usuario administrador</label>
            <input id="usuario" name="usuario"
                   autocomplete="username" required>

            <label for="password">Contraseña</label>
            <input id="password" type="password"
                   name="password"
                   autocomplete="current-password" required>

            <button class="btn btn-primary" type="submit">
                Iniciar sesión
            </button>
        </form>

        <small>Sistema de gestión residencial</small>
    </main>
</body>
</html>
