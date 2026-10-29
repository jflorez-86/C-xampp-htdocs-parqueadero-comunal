
<?php
$host = 'localhost';
$db   = 'parqueadero_comunal';
$user = 'root';
$pass = ''; // Configura la contraseña de tu MySQL.

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('No fue posible conectar con la base de datos.');
}
?>
