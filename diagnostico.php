
<?php
header('Content-Type: text/plain; charset=utf-8');

echo "PHP funciona\n";
echo "Ruta actual: " . __DIR__ . "\n";
echo "Archivo solicitado: " . ($_SERVER['REQUEST_URI'] ?? '') . "\n";
echo "Existe index.php: " .
     (file_exists(__DIR__ . '/index.php') ? 'SI' : 'NO') . "\n";
echo "Existe prueba.php: " .
     (file_exists(__DIR__ . '/prueba.php') ? 'SI' : 'NO') . "\n";
?>
