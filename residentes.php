
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

$residentes = $pdo->query(
    'SELECT * FROM residentes ORDER BY id DESC'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Residentes</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="topbar">
    <strong>Gestión de residentes</strong>
    <a class="btn btn-secondary" href="dashboard.php">Volver al panel</a>
</header>

<main class="contenedor">
    <section class="panel">
        <h1>Registrar residente</h1>

        <form action="guardar_residente.php" method="POST"
              class="form-grid">
            <div>
                <label>Nombres y apellidos</label>
                <input name="nombres" maxlength="150" required>
            </div>
            <div>
                <label>Cédula</label>
                <input name="cedula" maxlength="30" required>
            </div>
            <div>
                <label>Torre</label>
                <input name="torre" maxlength="20" required>
            </div>
            <div>
                <label>Apartamento</label>
                <input name="apartamento" maxlength="20" required>
            </div>
            <div>
                <label>Placa</label>
                <input name="placa" maxlength="10"
                       style="text-transform:uppercase" required>
            </div>
            <div>
                <label>Color del vehículo</label>
                <input name="color" maxlength="40" required>
            </div>
            <div>
                <label>Estado del residente</label>
                <select name="estado">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
            <div>
                <label>Autorización</label>
                <select name="autorizado">
                    <option value="autorizado">Autorizado</option>
                    <option value="no autorizado">No autorizado</option>
                </select>
            </div>
            <div class="ancho-completo">
                <button class="btn btn-primary" type="submit">
                    Guardar residente
                </button>
            </div>
        </form>
    </section>

    <section class="panel">
        <h2>Residentes registrados</h2>
        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula</th>
                        <th>Torre</th>
                        <th>Apto.</th>
                        <th>Placa</th>
                        <th>Color</th>
                        <th>Estado</th>
                        <th>Autorización</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($residentes as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nombres']) ?></td>
                        <td><?= htmlspecialchars($r['cedula']) ?></td>
                        <td><?= htmlspecialchars($r['torre']) ?></td>
                        <td><?= htmlspecialchars($r['apartamento']) ?></td>
                        <td><?= htmlspecialchars($r['placa']) ?></td>
                        <td><?= htmlspecialchars($r['color']) ?></td>
                        <td><?= htmlspecialchars($r['estado']) ?></td>
                        <td>
                            <span class="<?= $r['autorizado'] === 'autorizado'
                                ? 'etiqueta-verde' : 'etiqueta-roja' ?>">
                                <?= htmlspecialchars($r['autorizado']) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
