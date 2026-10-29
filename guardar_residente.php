
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: residentes.php');
    exit;
}

$nombres = trim($_POST['nombres'] ?? '');
$cedula = trim($_POST['cedula'] ?? '');
$torre = trim($_POST['torre'] ?? '');
$apartamento = trim($_POST['apartamento'] ?? '');
$placa = strtoupper(trim($_POST['placa'] ?? ''));
$color = trim($_POST['color'] ?? '');
$estado = $_POST['estado'] ?? '';
$autorizado = $_POST['autorizado'] ?? '';

if (
    $nombres === '' || $cedula === '' || $torre === '' ||
    $apartamento === '' || $placa === '' || $color === '' ||
    !in_array($estado, ['activo', 'inactivo'], true) ||
    !in_array($autorizado, ['autorizado', 'no autorizado'], true)
) {
    exit('Verifica los campos del formulario.');
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO residentes
        (nombres, cedula, torre, apartamento, placa, color,
         estado, autorizado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $stmt->execute([
        $nombres, $cedula, $torre, $apartamento,
        $placa, $color, $estado, $autorizado
    ]);

    header('Location: residentes.php?guardado=1');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        exit('La cédula o la placa ya están registradas.');
    }

    error_log($e->getMessage());
    http_response_code(500);
    exit('No se pudo guardar el residente.');
}
?>
