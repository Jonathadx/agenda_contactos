<?php
session_start();

// Restricción de acceso: Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'conexion.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

try {
    // Obtener los contactos del usuario autenticado
    $stmt = $pdo->prepare("SELECT * FROM contactos WHERE id_usuario = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // PLUS 3: Estadísticas rápidas (Total de contactos y total de ciudades únicas)
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) as total FROM contactos WHERE id_usuario = ?");
    $stmtTotal->execute([$user_id]);
    $total_contactos = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

    $stmtCiudades = $pdo->prepare("SELECT COUNT(DISTINCT ciudad) as total_ciudades FROM contactos WHERE id_usuario = ?");
    $stmtCiudades->execute([$user_id]);
    $total_ciudades = $stmtCiudades->fetch(PDO::FETCH_ASSOC)['total_ciudades'];

} catch (PDOException $e) {
    $contactos = [];
    $total_contactos = 0;
    $total_ciudades = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Agenda Personal</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
        <h1>Mi Agenda de Contactos</h1>
        <nav>
            <span style="color: #ecf0f1; margin-right: 1rem;">Hola, <?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="logout.php" style="background-color: #c0392b; padding: 0.4rem 0.8rem; border-radius: 4px;">Cerrar Sesión</a>
        </nav>
    </header>

    <main>
        <section style="margin-bottom: 2rem;">
            <h2>Perfil de Usuario</h2>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($user_email, ENT_QUOTES, 'UTF-8'); ?></p>
        </section>

        <!-- PLUS 3: Tarjetas de Estadísticas Rápidas -->
        <section style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div style="background: #f8fafc; border-left: 4px solid #2980b9; padding: 1.2rem; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h3 style="font-size: 0.9rem; color: #7f8c8d; margin-bottom: 0.3rem; border: none; padding: 0;">Total de Contactos</h3>
                <p style="font-size: 1.8rem; font-weight: bold; color: #2c3e50;"><?php echo $total_contactos; ?></p>
            </div>
            <div style="background: #f8fafc; border-left: 4px solid #27ae60; padding: 1.2rem; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h3 style="font-size: 0.9rem; color: #7f8c8d; margin-bottom: 0.3rem; border: none; padding: 0;">Ciudades Diferentes</h3>
                <p style="font-size: 1.8rem; font-weight: bold; color: #2c3e50;"><?php echo $total_ciudades; ?></p>
            </div>
        </section>

        <section>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <h2>Mis Contactos</h2>
                <a href="agregar_contacto.php" class="btn">+ Nuevo Contacto</a>
            </div>

            <!-- PLUS 1: Barra de Buscador en Tiempo Real -->
            <?php if (!empty($contactos)): ?>
                <div style="margin-bottom: 1.5rem;">
                    <input type="text" id="buscador" placeholder="🔍 Escribe para buscar por nombre, teléfono, correo o ciudad..." style="width: 100%; padding: 0.8rem; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 1rem;">
                </div>
            <?php endif; ?>

            <?php if (empty($contactos)): ?>
                <p style="color: #7f8c8d; text-align: center; padding: 2rem;">No tienes contactos registrados todavía.</p>
            <?php else: ?>
                <table id="tabla-contactos">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Ciudad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contactos as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($c['telefono'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($c['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($c['ciudad'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="editar_contacto.php?id=<?php echo $c['id']; ?>" style="color: #2980b9; text-decoration: none; font-weight: 600; margin-right: 10px;">Editar</a>
                                    <a href="eliminar_contacto.php?id=<?php echo $c['id']; ?>" style="color: #c0392b; text-decoration: none; font-weight: 600;" onclick="return confirm('¿Está seguro de que desea eliminar este contacto?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>© 2026 - Agenda Personal de Contactos</p>
    </footer>

    <!-- Script de JavaScript para el buscador instantáneo -->
    <script>
        const buscador = document.getElementById('buscador');
        if (buscador) {
            buscador.addEventListener('keyup', function() {
                let filtro = this.value.toLowerCase();
                let filas = document.querySelectorAll('#tabla-contactos tbody tr');

                filas.forEach(fila => {
                    let textoFila = fila.textContent.toLowerCase();
                    if (textoFila.includes(filtro)) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            });
        }
    </script>
</body>
</html>