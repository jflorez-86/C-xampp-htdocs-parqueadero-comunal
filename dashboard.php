
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

$total = (int) $pdo->query(
    'SELECT COUNT(*) FROM puestos'
)->fetchColumn();

$ocupados = (int) $pdo->query(
    "SELECT COUNT(*) FROM puestos WHERE estado = 'ocupado'"
)->fetchColumn();

$disponibles = $total - $ocupados;

$autorizados = (int) $pdo->query(
    "SELECT COUNT(*) FROM residentes
     WHERE autorizado = 'autorizado' AND estado = 'activo'"
)->fetchColumn();

$puestos = $pdo->query(
    'SELECT numero, estado FROM puestos ORDER BY numero'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de control</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/app.js" defer></script>
</head>
<body>
<header class="topbar">
    <div>
        <strong>PARQUEADERO COMUNAL</strong>
        <small>Administración y vigilancia</small>
    </div>
    <div>
        <?= htmlspecialchars($_SESSION['admin_nombre']) ?>
        <a class="btn btn-danger" href="logout.php">Salir</a>
    </div>
</header>

<main class="contenedor">
    <h1>Panel principal</h1>
    <p>Bienvenido al sistema de control vehicular.</p>

    <section class="resumen">
        <article class="stat-card">
            <span>Capacidad total</span>
            <strong><?= $total ?></strong>
            <small>Puestos registrados</small>
        </article>
        <article class="stat-card">
            <span>Disponibles</span>
            <strong class="verde"><?= $disponibles ?></strong>
            <small>Espacios libres</small>
        </article>
        <article class="stat-card">
            <span>Ocupados</span>
            <strong class="rojo"><?= $ocupados ?></strong>
            <small>Vehículos dentro</small>
        </article>
        <article class="stat-card">
            <span>Residentes autorizados</span>
            <strong><?= $autorizados ?></strong>
            <small>Con estado activo</small>
        </article>
    </section>

    <nav class="acciones">
        <a class="btn btn-primary" href="residentes.php">
            Registrar residentes
        </a>
    </nav>

    <section class="panel">
        <h2>Mapa de disponibilidad</h2>
        <div class="leyenda">
            <span class="verde">■ Disponible</span>
            <span class="rojo">■ Ocupado</span>
        </div>

        <div class="puestos">
            <?php foreach ($puestos as $puesto): ?>
                <div class="puesto <?= $puesto['estado'] === 'ocupado'
                    ? 'ocupado' : 'disponible' ?>">
                    <strong><?= (int) $puesto['numero'] ?></strong>
                    <small><?= $puesto['estado'] === 'ocupado'
                        ? 'Ocupado' : 'Libre' ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
</body>
</html>
