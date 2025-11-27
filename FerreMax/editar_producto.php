<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];
$query = "SELECT * FROM productos WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: productos.php");
    exit();
}

if ($_POST) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];
    
    $query = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $id]);
    
    header("Location: productos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Ferretería</title>
    <style>
        /* Estilos del dashboard... (copiar los mismos estilos) */
        .form-container {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 2rem auto;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .btn-back:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Editar Producto - Ferretería</h1>
        <div>
            <span>Bienvenido, <?php echo $_SESSION['nombre']; ?></span>
            <a href="logout.php" class="btn btn-danger" style="margin-left: 1rem;">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="catalogo.php">Catálogo</a>
        <a href="productos.php">Productos</a>
        <?php if ($_SESSION['rol'] == 'admin'): ?>
            <a href="usuarios.php">Usuarios</a>
        <?php endif; ?>
    </div>
    
    <div class="container">
        <div class="form-container">
            <h2>Editar Producto: <?php echo htmlspecialchars($producto['nombre']); ?></h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Categoría:</label>
                        <select name="categoria" required>
                            <option value="Herramientas" <?php echo $producto['categoria'] == 'Herramientas' ? 'selected' : ''; ?>>Herramientas</option>
                            <option value="Fontanería" <?php echo $producto['categoria'] == 'Fontanería' ? 'selected' : ''; ?>>Fontanería</option>
                            <option value="Electricidad" <?php echo $producto['categoria'] == 'Electricidad' ? 'selected' : ''; ?>>Electricidad</option>
                            <option value="Pinturas" <?php echo $producto['categoria'] == 'Pinturas' ? 'selected' : ''; ?>>Pinturas</option>
                            <option value="Materiales" <?php echo $producto['categoria'] == 'Materiales' ? 'selected' : ''; ?>>Materiales</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Precio:</label>
                        <input type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stock:</label>
                        <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn">Actualizar Producto</button>
                    <a href="productos.php" class="btn btn-back">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>