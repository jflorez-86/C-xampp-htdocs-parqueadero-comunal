
<?php
require __DIR__ . '/includes/seguridad.php';
require __DIR__ . '/config/conexion.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador inválido.');
}

$stmt = $pdo->prepare('SELECT * FROM residentes WHERE id = ?');
$stmt->execute([$id]);
$r = $stmt->fetch();

if (!$r) {
    http_response_code(404);
    exit('Residente no encontrado.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $campos = [
        'nombres' => trim($_POST['nombres'] ?? ''),
        'cedula' => trim($_POST['cedula'] ?? ''),
        'torre' => trim($_POST['torre'] ?? ''),
        'apartamento' => trim($_POST['apartamento'] ?? ''),
        'placa' => strtoupper(trim($_POST['placa'] ?? '')),
        'color' => trim($_POST['color'] ?? ''),
        'estado' => $_POST['estado'] ?? '',
        'autorizado' => $_POST['autorizado'] ?? ''
    ];

    foreach (['nombres', 'cedula', 'torre', 'apartamento',
              'placa', 'color'] as $campo) {
        if ($campos[$campo] === '') {
            exit('Todos los campos son obligatorios.');
        }
    }

    if (!in_array($campos['estado'], ['activo', 'inactivo'], true) ||
        !in_array($campos['autorizado'],
                  ['autorizado', 'no autorizado'], true)) {
        exit('Estado o autorización inválidos.');
    }

    try {
        $stmt = $pdo->prepare(
            'UPDATE residentes SET
                nombres = ?, cedula = ?, torre = ?,
                apartamento = ?, placa = ?, color = ?,
                estado = ?, autorizado = ?
             WHERE id = ?'
        );

        $stmt->execute([
            $campos['nombres'], $campos['cedula'],
            $campos['torre'], $campos['apartamento'],
            $campos['placa'], $campos['color'],
            $campos['estado'], $campos['autorizado'], $id
        ]);

        header('Location: residentes.php?actualizado=1');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            exit('La cédula o placa pertenece a otro registro.');
        }

        error_log($e->getMessage());
        http_response_code(500);
        exit('No fue posible actualizar el residente.');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar residente</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<main class="contenedor">
    <section class="panel">
        <h1>Editar residente</h1>
        <form method="POST" class="form-grid">
            <?= csrf_field() ?>

            <?php foreach ([
                'nombres' => 'Nombres y apellidos',
                'cedula' => 'Cédula',
                'torre' => 'Torre',
                'apartamento' => 'Apartamento',
                'placa' => 'Placa',
                'color' => 'Color'
            ] as $campo => $etiqueta): ?>
                <div>
                    <label><?= htmlspecialchars($etiqueta) ?></label>
                    <input name="<?= $campo ?>"
                           value="<?= htmlspecialchars($r[$campo]) ?>"
                           required>
                </div>
            <?php endforeach; ?>

            <div>
                <label>Estado</label>
                <select name="estado">
                    <option value="activo"
                        <?= $r['estado'] === 'activo' ? 'selected' : '' ?>>
                        Activo
                    </option>
                    <option value="inactivo"
                        <?= $r['estado'] === 'inactivo' ? 'selected' : '' ?>>
                        Inactivo
                    </option>
                </select>
            </div>
            <div>
                <label>Autorización</label>
                <select name="autorizado">
                    <option value="autorizado"
                        <?= $r['autorizado'] === 'autorizado'
                            ? 'selected' : '' ?>>
                        Autorizado
                    </option>
                    <option value="no autorizado"
                        <?= $r['autorizado'] === 'no autorizado'
                            ? 'selected' : '' ?>>
                        No autorizado
                    </option>
                </select>
            </div>

            <div class="ancho-completo">
                <button class="btn btn-primary">Guardar cambios</button>
                <a class="btn btn-secondary" href="residentes.php">
                    Cancelar
                </a>
            </div>
        </form>
    </section>
</main>
</body>
</html>
