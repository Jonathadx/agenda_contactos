<?php
require_once 'conexion.php';

$mensaje = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    if (empty($nombre) || empty($correo) || empty($password)) {
        $mensaje = "Por favor, complete todos los campos.";
        $tipo_alerta = "alert-error";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El formato del correo electrónico no es válido.";
        $tipo_alerta = "alert-error";
    } else {
        try {
            // Verificar si el correo ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            
            if ($stmt->rowCount() > 0) {
                $mensaje = "El correo electrónico ya está registrado.";
                $tipo_alerta = "alert-error";
            } else {
                // Encriptar la contraseña de forma segura
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Insertar el nuevo usuario
                $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nombre, correo, password) VALUES (?, ?, ?)");
                if ($stmtInsert->execute([$nombre, $correo, $password_hash])) {
                    $mensaje = "¡Registro exitoso! Ya puede iniciar sesión.";
                    $tipo_alerta = "alert-success";
                }
            }
        } catch (PDOException $e) {
            $mensaje = "Error en el sistema. Intente nuevamente.";
            $tipo_alerta = "alert-error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Agenda Personal</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>Agenda de Contactos</h1>
        <nav>
            <a href="index.php">Iniciar Sesión</a>
        </nav>
    </header>

    <main>
        <h2>Crear una Cuenta Nueva</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="<?php echo $tipo_alerta; ?>">
                <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <div>
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div>
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Registrarse</button>
        </form>
        
        <p style="margin-top: 1.5rem; text-align: center;">
            ¿Ya tienes una cuenta? <a href="index.php" style="color: #2980b9; font-weight: 600;">Inicia sesión aquí</a>
        </p>
    </main>

    <footer>
        <p>© 2026 - Agenda Personal de Contactos</p>
    </footer>
</body>
</html>
