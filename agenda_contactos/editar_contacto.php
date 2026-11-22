<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'conexion.php';

$user_id = $_SESSION['user_id'];
$contacto_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = "";

// Obtener categorías disponibles
try {
    $stmtCat = $pdo->query("SELECT id, nombre FROM categorias ORDER BY id ASC");
    $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categorias = [];
}

// Validar que el contacto pertenezca al usuario actual
try {
    $stmt = $pdo->prepare("SELECT * FROM contactos WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$contacto_id, $user_id]);
    $contacto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contacto) {
        header("Location: dashboard.php");
        exit();
    }
} catch (PDOException $e) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $id_categoria = isset($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : 5;
    $favorito = (isset($_POST['favorito']) && $_POST['favorito'] === 'Sí') ? 'Sí' : 'No';

    if (empty($nombre) || empty($telefono) || empty($correo) || empty($ciudad)) {
        $mensaje = "Por favor, complete todos los campos.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Por favor, ingrese un correo electrónico válido.";
    } else {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE contactos SET id_categoria = ?, nombre = ?, telefono = ?, correo = ?, ciudad = ?, favorito = ? WHERE id = ? AND id_usuario = ?");
            if ($stmtUpdate->execute([$id_categoria, $nombre, $telefono, $correo, $ciudad, $favorito, $contacto_id, $user_id])) {
                header("Location: dashboard.php");
                exit();
            }
        } catch (PDOException $e) {
            $mensaje = "Error al actualizar el contacto.";
        }
    }

    $contacto['nombre'] = $nombre;
    $contacto['telefono'] = $telefono;
    $contacto['correo'] = $correo;
    $contacto['ciudad'] = $ciudad;
    $contacto['id_categoria'] = $id_categoria;
    $contacto['favorito'] = $favorito;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contacto - Agenda Personal</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>Agenda de Contactos</h1>
        <nav>
            <a href="dashboard.php">Volver al Panel</a>
        </nav>
    </header>

    <main>
        <h2>Editar Contacto</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="editar_contacto.php?id=<?php echo $contacto_id; ?>" method="POST" novalidate>
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($contacto['nombre'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($contacto['telefono'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div>
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($contacto['correo'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div>
                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($contacto['ciudad'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div>
                <label for="id_categoria">Categoría:</label>
                <select id="id_categoria" name="id_categoria" style="width: 100%; padding: 0.75rem; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 1rem;">
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo ((int)$contacto['id_categoria'] === (int)$cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="favorito">Contacto Favorito:</label>
                <select id="favorito" name="favorito" style="width: 100%; padding: 0.75rem; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 1rem;">
                    <option value="No" <?php echo (($contacto['favorito'] ?? 'No') === 'No') ? 'selected' : ''; ?>>No</option>
                    <option value="Sí" <?php echo (($contacto['favorito'] ?? 'No') === 'Sí') ? 'selected' : ''; ?>>Sí</option>
                </select>
            </div>
            <button type="submit">Actualizar Contacto</button>
        </form>
    </main>

    <footer>
        <p>© 2026 - Agenda Personal de Contactos</p>
    </footer>
</body>
</html>
