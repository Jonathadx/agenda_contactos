<?php
session_start();

// Si ya hay una sesión activa, redirigir al panel principal
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    if (empty($correo) || empty($password)) {
        $mensaje = "Por favor, ingrese su correo y contraseña.";
    } else {
        try {
            // Buscar al usuario por correo
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar contraseña con password_verify
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Iniciar sesión y guardar datos clave
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_name'] = $usuario['nombre'];
                $_SESSION['user_email'] = $usuario['correo'];

                header("Location: dashboard.php");
                exit();
            } else {
                $mensaje = "Correo o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            $mensaje = "Error en el sistema. Intente nuevamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Agenda Personal</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>Agenda de Contactos</h1>
        <nav>
            <a href="registro.php">Registrarse</a>
        </nav>
    </header>

    <main>
        <h2>Iniciar Sesión</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <div>
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div>
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Ingresar</button>
        </form>

        <p style="margin-top: 1.5rem; text-align: center;">
            ¿No tienes una cuenta? <a href="registro.php" style="color: #2980b9; font-weight: 600;">Regístrate aquí</a>
        </p>
    </main>

    <footer>
        <p>© 2026 - Agenda Personal de Contactos</p>
    </footer>
</body>
</html>
