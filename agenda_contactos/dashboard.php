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

// Parámetros de búsqueda, filtro y ordenamiento (Opciones A, B, C y D)
$busqueda = trim($_GET['buscar'] ?? '');
$categoria_filtro = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$solo_favoritos = (isset($_GET['favoritos']) && $_GET['favoritos'] === '1');
$orden = $_GET['orden'] ?? 'fecha_desc';

// Lista blanca de ordenamientos permitidos (previene inyección SQL en ORDER BY)
$ordenes_permitidos = [
    'fecha_desc'  => 'c.fecha_creacion DESC, c.id DESC',
    'fecha_asc'   => 'c.fecha_creacion ASC, c.id ASC',
    'nombre_asc'  => 'c.nombre ASC',
    'nombre_desc' => 'c.nombre DESC',
    'ciudad_asc'  => 'c.ciudad ASC',
    'ciudad_desc' => 'c.ciudad DESC'
];
$order_by_sql = $ordenes_permitidos[$orden] ?? $ordenes_permitidos['fecha_desc'];

try {
    // Cargar categorías para el filtro
    $stmtCat = $pdo->query("SELECT id, nombre FROM categorias ORDER BY id ASC");
    $categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    // Construir consulta dinámica segura con PDO
    $sql = "SELECT c.*, cat.nombre AS categoria_nombre
            FROM contactos c
            LEFT JOIN categorias cat ON c.id_categoria = cat.id
            WHERE c.id_usuario = ?";
    $params = [$user_id];

    // Opción A: Búsqueda por nombre, correo o ciudad
    if ($busqueda !== '') {
        $sql .= " AND (c.nombre LIKE ? OR c.correo LIKE ? OR c.ciudad LIKE ?)";
        $termino = '%' . $busqueda . '%';
        $params[] = $termino;
        $params[] = $termino;
        $params[] = $termino;
    }

    // Opción C: Filtro por categoría
    if ($categoria_filtro > 0) {
        $sql .= " AND c.id_categoria = ?";
        $params[] = $categoria_filtro;
    }

    // Opción D: Filtro de solo favoritos
    if ($solo_favoritos) {
        $sql .= " AND c.favorito = 'Sí'";
    }

    // Opción B: Ordenamiento
    $sql .= " ORDER BY " . $order_by_sql;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Estadísticas rápidas
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) as total FROM contactos WHERE id_usuario = ?");
    $stmtTotal->execute([$user_id]);
    $total_contactos = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

    $stmtFav = $pdo->prepare("SELECT COUNT(*) as total_fav FROM contactos WHERE id_usuario = ? AND favorito = 'Sí'");
    $stmtFav->execute([$user_id]);
    $total_favoritos = $stmtFav->fetch(PDO::FETCH_ASSOC)['total_fav'];

    $stmtCiudades = $pdo->prepare("SELECT COUNT(DISTINCT ciudad) as total_ciudades FROM contactos WHERE id_usuario = ?");
    $stmtCiudades->execute([$user_id]);
    $total_ciudades = $stmtCiudades->fetch(PDO::FETCH_ASSOC)['total_ciudades'];

} catch (PDOException $e) {
    $categorias = [];
    $contactos = [];
    $total_contactos = 0;
    $total_favoritos = 0;
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

        <!-- Tarjetas de Estadísticas Rápidas -->
        <section style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div style="background: #f8fafc; border-left: 4px solid #2980b9; padding: 1.2rem; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h3 style="font-size: 0.9rem; color: #7f8c8d; margin-bottom: 0.3rem; border: none; padding: 0;">Total de Contactos</h3>
                <p style="font-size: 1.8rem; font-weight: bold; color: #2c3e50;"><?php echo $total_contactos; ?></p>
            </div>
            <div style="background: #f8fafc; border-left: 4px solid #f39c12; padding: 1.2rem; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <h3 style="font-size: 0.9rem; color: #7f8c8d; margin-bottom: 0.3rem; border: none; padding: 0;">Favoritos</h3>
                <p style="font-size: 1.8rem; font-weight: bold; color: #2c3e50;"><?php echo $total_favoritos; ?></p>
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

            <!-- Panel de Búsqueda, Categorías, Favoritos y Ordenamiento -->
            <form method="GET" action="dashboard.php" style="max-width: 100%; background: #f8fafc; padding: 1rem; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 0.8rem; align-items: end;">
                <div style="margin-bottom: 0;">
                    <label for="buscar" style="font-size: 0.85rem;">Buscar (nombre, correo o ciudad):</label>
                    <input type="text" id="buscar" name="buscar" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ej. Diego, Quito, @gmail...">
                </div>

                <div style="margin-bottom: 0;">
                    <label for="categoria" style="font-size: 0.85rem;">Categoría:</label>
                    <select id="categoria" name="categoria" style="width: 100%; padding: 0.75rem; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 0.95rem;">
                        <option value="0">Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($categoria_filtro === (int)$cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 0;">
                    <label for="orden" style="font-size: 0.85rem;">Ordenar por:</label>
                    <select id="orden" name="orden" style="width: 100%; padding: 0.75rem; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 0.95rem;">
                        <option value="fecha_desc" <?php echo ($orden === 'fecha_desc') ? 'selected' : ''; ?>>Fecha (Más recientes)</option>
                        <option value="fecha_asc" <?php echo ($orden === 'fecha_asc') ? 'selected' : ''; ?>>Fecha (Más antiguos)</option>
                        <option value="nombre_asc" <?php echo ($orden === 'nombre_asc') ? 'selected' : ''; ?>>Nombre (A - Z)</option>
                        <option value="nombre_desc" <?php echo ($orden === 'nombre_desc') ? 'selected' : ''; ?>>Nombre (Z - A)</option>
                        <option value="ciudad_asc" <?php echo ($orden === 'ciudad_asc') ? 'selected' : ''; ?>>Ciudad (A - Z)</option>
                        <option value="ciudad_desc" <?php echo ($orden === 'ciudad_desc') ? 'selected' : ''; ?>>Ciudad (Z - A)</option>
                    </select>
                </div>

                <div style="margin-bottom: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.9rem; cursor: pointer; margin-bottom: 0;">
                        <input type="checkbox" name="favoritos" value="1" <?php echo $solo_favoritos ? 'checked' : ''; ?> style="width: auto;">
                        Solo favoritos ★
                    </label>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" style="padding: 0.65rem; font-size: 0.9rem;">Aplicar</button>
                        <a href="dashboard.php" class="btn" style="background-color: #7f8c8d; padding: 0.65rem; font-size: 0.9rem; text-align: center; flex: 1;">Limpiar</a>
                    </div>
                </div>
            </form>

            <?php if (empty($contactos)): ?>
                <p style="color: #7f8c8d; text-align: center; padding: 2rem;">No se encontraron contactos con los criterios seleccionados.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table id="tabla-contactos">
                        <thead>
                            <tr>
                                <th>Fav.</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Ciudad</th>
                                <th>Categoría</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contactos as $c): ?>
                                <tr>
                                    <td style="text-align: center; font-size: 1.1rem; color: <?php echo ($c['favorito'] === 'Sí') ? '#f39c12' : '#bdc3c7'; ?>;" title="<?php echo htmlspecialchars($c['favorito'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo ($c['favorito'] === 'Sí') ? '★' : '☆'; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($c['telefono'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($c['correo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($c['ciudad'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <span style="background: #eaf2f8; color: #2471a3; padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                            <?php echo htmlspecialchars($c['categoria_nombre'] ?? 'Otro', ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem; color: #7f8c8d;">
                                        <?php echo date('d/m/Y H:i', strtotime($c['fecha_creacion'])); ?>
                                    </td>
                                    <td>
                                        <a href="editar_contacto.php?id=<?php echo $c['id']; ?>" style="color: #2980b9; text-decoration: none; font-weight: 600; margin-right: 10px;">Editar</a>
                                        <form action="eliminar_contacto.php" method="POST" style="display: inline; margin: 0; padding: 0; background: none; border: none; box-shadow: none;" onsubmit="return confirm('¿Está seguro de que desea eliminar este contacto?');">
                                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                            <button type="submit" style="background: none; border: none; color: #c0392b; font-weight: 600; cursor: pointer; padding: 0; font-size: inherit; font-family: inherit; width: auto; display: inline;">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>© 2026 - Agenda Personal de Contactos</p>
    </footer>
</body>
</html>
