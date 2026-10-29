
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

exigir_rol('superadmin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

validar_csrf();

$nombre = trim($_POST['nombre'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';
$rol = $_POST['rol'] ?? 'admin';

if ($nombre === '' ||
    !preg_match('/^[a-zA-Z0-9_.-]{4,50}$/', $usuario) ||
    strlen($password) < 12 ||
    !in_array($rol, ['admin', 'superadmin'], true)) {
    exit('Verifica los datos. La contraseña debe tener al menos 12 caracteres.');
}

try {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'INSERT INTO administradores (nombre, usuario, password, rol)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$nombre, $usuario, $hash, $rol]);

    header('Location: administradores.php?creado=1');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        exit('El nombre de usuario ya existe.');
    }

    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudo crear el administrador.');
}
?>
