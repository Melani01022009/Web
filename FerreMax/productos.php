<?php
session_start();
require_once 'config/database.php';

// --- Variables de búsqueda ---
$termino_busqueda = isset($_POST['termino_busqueda']) ? trim($_POST['termino_busqueda']) : '';
$categoria_filtro = isset($_POST['categoria_filtro']) ? trim($_POST['categoria_filtro']) : '';

// --- Obtener categorías dinámicamente ---
$sql_categorias = "SELECT categoria_id, categoria_nombre 
                   FROM categoria 
                   WHERE categoria_estado = 1 
                   ORDER BY categoria_nombre ASC";
$result_categorias = $enlace->query($sql_categorias);
$categorias = [];
if ($result_categorias && $result_categorias->num_rows > 0) {
    while ($row = $result_categorias->fetch_assoc()) {
        $categorias[] = $row;
    }
}

// --- Construir la consulta base ---
$query = "SELECT 
            p.producto_id, 
            p.producto_codigo, 
            p.producto_nombre, 
            p.producto_precio, 
            p.producto_stock, 
            p.producto_foto, 
            c.categoria_nombre
          FROM producto p
          INNER JOIN categoria c ON p.categoria_id = c.categoria_id
          WHERE 1=1";

$parametros = [];
$tipos = "";

// --- Filtro por término de búsqueda (sin producto_descripcion) ---
if (!empty($termino_busqueda)) {
    $query .= " AND (p.producto_nombre LIKE ? 
                OR p.producto_codigo LIKE ?)";
    $like = "%$termino_busqueda%";
    $parametros[] = &$like;
    $parametros[] = &$like;
    $tipos .= "ss";
}

// --- Filtro por categoría (por ID) ---
if (!empty($categoria_filtro)) {
    $query .= " AND p.categoria_id = ?";
    $parametros[] = &$categoria_filtro;
    $tipos .= "i"; // tipo entero
}

$query .= " ORDER BY p.producto_nombre ASC";

$stmt = $enlace->prepare($query);
if ($stmt === false) {
    die("Error al preparar la consulta: " . $enlace->error);
}

if (!empty($parametros)) {
    array_unshift($parametros, $tipos);
    call_user_func_array([$stmt, 'bind_param'], $parametros);
}

$stmt->execute();
$result = $stmt->get_result();
$resultados = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
// Procesar registro de nuevo producto
if ($_POST && isset($_POST['registrar_producto'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $marca = $_POST['marca'];
    $precio_actual = $_POST['precio_actual'];
    $precio_anterior = $_POST['precio_anterior'];
    $stock = $_POST['stock'];
    $codigo = $_POST['codigo'];
    
    $query = "INSERT INTO productos (nombre, descripcion, categoria, marca, precio_actual, precio_anterior, stock, codigo) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $success = $stmt->execute([$nombre, $descripcion, $categoria, $marca, $precio_actual, $precio_anterior, $stock, $codigo]);
    
    if ($success) {
        $mensaje_exito = "Producto registrado exitosamente!";
    } else {
        $mensaje_error = "Error al registrar el producto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda y Registro - FerreMax</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .busqueda-section {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #2980b9 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        
        .busqueda-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .busqueda-form {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 15px;
            align-items: end;
        }
        
        .resultados-section {
            padding: 30px 0;
        }
        
        .resultados-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .contador-resultados {
            color: var(--gray-color);
            font-size: 0.9rem;
        }
        
        .resultados-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .resultado-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--secondary-color);
        }
        
        .resultado-codigo {
            font-size: 0.8rem;
            color: var(--gray-color);
            margin-bottom: 5px;
        }
        
        .resultado-nombre {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .resultado-descripcion {
            color: var(--gray-color);
            font-size: 0.9rem;
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .resultado-detalles {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }
        
        .detalle-item {
            display: flex;
            flex-direction: column;
        }
        
        .detalle-label {
            color: var(--gray-color);
            font-size: 0.75rem;
        }
        
        .detalle-valor {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .precio-actual {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .precio-anterior {
            font-size: 0.9rem;
            color: var(--gray-color);
            text-decoration: line-through;
            margin-left: 8px;
        }
        
        .stock-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        
        .stock-cantidad {
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        
        .stock-alto {
            background: #d4edda;
            color: #155724;
        }
        
        .stock-medio {
            background: #fff3cd;
            color: #856404;
        }
        
        .stock-bajo {
            background: #f8d7da;
            color: #721c24;
        }
        
        .registro-section {
            background: #f8f9fa;
            padding: 40px 0;
            border-top: 1px solid #dee2e6;
        }
        
        .registro-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .registro-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-full-width {
            grid-column: 1 / -1;
        }
        
        .alertas {
            margin-bottom: 20px;
        }
        
        .sin-resultados {
            text-align: center;
            padding: 40px;
            color: var(--gray-color);
        }
        
        .sin-resultados i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .resultados-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Seccion de productos */

        .productos-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Ajusta las cards en columnas responsivas */
        gap: 20px;
        padding: 20px;
    }
    .card {
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        background-color: white;
        transition: transform 0.3s;
    }
    .card:hover {
        transform: translateY(-10px); /* Efecto hover */
    }
    .card-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .card-body {
        padding: 15px;
    }
    .card-title {
        font-size: 1.2em;
        margin-bottom: 10px;
    }
    .card-text {
        margin: 5px 0;
        font-size: 1em;
    }
    .card-text strong {
        font-weight: bold;
    }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>FerreMax</h1>
                <p>Ferretería Integral</p>
            </div>

            <nav>
                <ul>
                    <li><a href="catalogo.php">Catálogo</a></li>
                    <?php if(isset($_SESSION['usuario_usuario'])): ?>
                    <li><a href="logout.php">Cerrar Sesión (<?php echo $_SESSION['usuario_nombre']; ?>)</a></li>
                    <?php else: ?>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

   <section class="busqueda-section">
        <div class="container busqueda-container">
            <h2 style="text-align: center; margin-bottom: 20px;">Buscar Productos</h2>
            <form method="POST" class="busqueda-form">
                <div class="form-row">
                    <!-- Campo de texto -->
                    <div>
                        <label for="termino_busqueda" style="color: white; display: block; margin-bottom: 8px;">Término de búsqueda</label>
                        <input type="text" id="termino_busqueda" name="termino_busqueda"
                            class="form-control" placeholder="Nombre o código del producto..."
                            value="<?php echo htmlspecialchars($termino_busqueda); ?>">
                    </div>

                    <!-- Filtro de categoría -->
                    <div>
                        <label for="categoria_filtro" style="color: white; display: block; margin-bottom: 8px;">Categoría</label>
                        <select id="categoria_filtro" name="categoria_filtro" class="form-control">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['categoria_id']; ?>" 
                                    <?php echo ($categoria_filtro == $cat['categoria_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['categoria_nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Botón de búsqueda -->
                    <div>
                        <button type="submit" name="buscar" class="btn btn-primary" style="height: 42px;">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <main>
        <div class="container">
            <!-- Resultados de Búsqueda -->
          <section class="resultados-section">
            <div class="resultados-header">
                <h3>Resultados de Búsqueda</h3>
                <div class="contador-resultados">
                    <?php echo count($resultados); ?> producto(s) encontrado(s)
                </div>
            </div>

            <?php if (!empty($resultados)): ?>
                <div class="productos-container">
                    <?php foreach ($resultados as $producto): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($producto['producto_nombre']); ?></h5>
                                <p class="card-text"><strong>Código:</strong> <?php echo htmlspecialchars($producto['producto_codigo']); ?></p>
                                <p class="card-text"><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                                <p class="card-text"><strong>Precio:</strong> $<?php echo number_format($producto['producto_precio'], 2); ?></p>
                                <p class="card-text"><strong>Stock:</strong> <?php echo htmlspecialchars($producto['producto_stock']); ?></p>
                                <button type="submit" name="registrar_producto" class="btn btn-primary btn-lg"><a href="carrito.php">Agregar al carrito</a></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No se encontraron productos.</p>
            <?php endif; ?>

            <?php
            $stmt->close();
            $enlace->close();
            ?>
        </section>

            <!-- Formulario de Registro (Solo para administradores) -->
            <?php if(isset($_SESSION['usuario']) && $_SESSION['rol'] == 'admin'): ?>
            <section class="registro-section">
                <div class="container registro-container">
                    <h2 style="text-align: center; margin-bottom: 30px;">Registrar Nuevo Producto</h2>
                    
                    <div class="alertas">
                        <?php if(isset($mensaje_exito)): ?>
                            <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
                        <?php endif; ?>
                        <?php if(isset($mensaje_error)): ?>
                            <div class="alert alert-error"><?php echo $mensaje_error; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" class="registro-form">
                        <div class="form-grid">
                            <div class="form-group form-full-width">
                                <label for="nombre">Nombre del Producto *</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required>
                            </div>
                            
                            <div class="form-group form-full-width">
                                <label for="descripcion">Descripción</label>
                                <textarea id="descripcion" name="descripcion" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="categoria">Categoría *</label>
                                <select id="categoria" name="categoria" class="form-control" required>
                                    <option value="">Seleccionar categoría</option>
                                    <option value="Herramientas Manuales">Herramientas Manuales</option>
                                    <option value="Herramientas Eléctricas">Herramientas Eléctricas</option>
                                    <option value="Materiales de Construcción">Materiales de Construcción</option>
                                    <option value="Pinturas">Pinturas</option>
                                    <option value="Fijaciones">Fijaciones</option>
                                    <option value="Fontanería">Fontanería</option>
                                    <option value="Eléctricos">Eléctricos</option>
                                    <option value="Accesorios">Accesorios</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="marca">Marca *</label>
                                <input type="text" id="marca" name="marca" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="precio_actual">Precio Actual *</label>
                                <input type="number" id="precio_actual" name="precio_actual" class="form-control" step="0.01" min="0" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="precio_anterior">Precio Anterior (Oferta)</label>
                                <input type="number" id="precio_anterior" name="precio_anterior" class="form-control" step="0.01" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label for="stock">Stock *</label>
                                <input type="number" id="stock" name="stock" class="form-control" min="0" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="codigo">Código del Producto *</label>
                                <input type="text" id="codigo" name="codigo" class="form-control" required>
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin-top: 25px;">
                            <button type="submit" name="registrar_producto" class="btn btn-primary btn-lg">Registrar Producto</button>
                        </div>
                    </form>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> FerreMax - Sistema desarrollado por Melani</p>
        </div>
    </footer>
</body>
</html>


