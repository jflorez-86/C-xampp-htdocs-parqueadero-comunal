
<?php
require __DIR__ . '/config/conexion.php';

$total = (int) $pdo->query(
    "SELECT COUNT(*) FROM administradores"
)->fetchColumn();

if ($total > 0) {
    exit('El administrador inicial ya fue creado.');
}

$nombre = 'Administrador Principal';
$usuario = 'admin';
$password = password_hash(
    'CambiarAdmin2026!',
    PASSWORD_DEFAULT
);

$stmt = $pdo->prepare(
    "INSERT INTO administradores (nombre, usuario, password)
     VALUES (?, ?, ?)"
);
$stmt->execute([$nombre, $usuario, $password]);

echo 'Administrador creado. Inicia sesión y cambia la contraseña.';
?>
