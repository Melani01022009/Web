<?php
session_start();
require_once 'config/database.php';

global $enlace;

// --- Consulta para los 10 productos principales ---
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
          ORDER BY p.producto_id DESC
          LIMIT 10";

$result = $enlace->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - FerreMax</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos específicos para el catálogo */
        .catalogo-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .catalogo-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .catalogo-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .top-productos-section {
            background-color: #f9f9f9;
            padding: 60px 20px;
            font-family: 'Poppins', sans-serif;
        }

        .top-productos-section .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .top-header h2 {
            font-size: 1.8rem;
            color: #2a4d69;
            font-weight: 700;
        }

        .btn-ver-mas {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-ver-mas:hover {
            background-color: #0056b3;
        }

      
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

     
        .producto-card {
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

       
        .producto-img img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .producto-info {
            padding: 20px;
        }

        .producto-info h3 {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 8px;
        }

        .producto-info .categoria {
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .producto-info p {
            margin: 4px 0;
            color: #444;
            font-size: 0.95rem;
        }

        /* BOTÓN DETALLE */
        .btn-detalle {
            display: inline-block;
            margin-top: 10px;
            background-color: #2a4d69;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .btn-detalle:hover {
            background-color: #1f364d;
        }

        
        .no-productos {
            text-align: center;
            font-size: 1rem;
            color: #888;
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
                    <li><a href="productos.php">Productos</a></li>
                          <li> <a href="editor_empleado.php">inventario</a> </li>
                    <?php if(isset($_SESSION['usuario_usuario'])): ?>
                    <li><a href="logout.php">Cerrar Sesión (<?php echo $_SESSION['usuario_nombre']; ?>)</a></li>
                    <?php else: ?>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <section class="catalogo-header">
        <div class="container">
            <h1>Catálogo de Productos</h1>
            <p>Encuentra todo lo que necesitas para tus proyectos</p>
        </div>
    </section>

    <main>
        <div class="container">
            <section class="top-productos-section">
                <div class="container">
                    <div class="top-header">
                        <h2> Productos Principales</h2>
                        <a href="productos.php" class="btn-ver-mas">Ver inventario completo →</a>
                    </div>

                    <?php if ($result && $result->num_rows > 0): ?>
                        <div class="productos-grid">
                            <?php while ($producto = $result->fetch_assoc()): ?>
                                <div class="producto-card">
                                    <div class="producto-img">
                                        <?php if (!empty($producto['producto_foto'])): ?>
                                            <img src="ruta/a/imagenes/<?php echo htmlspecialchars($producto['producto_foto']); ?>" 
                                                alt="<?php echo htmlspecialchars($producto['producto_nombre']); ?>">
                                        <?php else: ?>
                                            <img src="ruta/a/imagenes/default.jpg" alt="Sin imagen">
                                        <?php endif; ?>
                                    </div>
                                    <div class="producto-info">
                                        <h3><?php echo htmlspecialchars($producto['producto_nombre']); ?></h3>
                                        <p class="categoria"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></p>
                                        <p><strong>Precio:</strong> $<?php echo number_format($producto['producto_precio'], 2); ?></p>
                                        <p><strong>Stock:</strong> <?php echo htmlspecialchars($producto['producto_stock']); ?></p>
                                        <a href="#detalle_producto.php?id=<?php echo $producto['producto_id']; ?>" class="btn-detalle">Ver detalle</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-productos">No hay productos disponibles.</p>
                    <?php endif; ?>

                    <?php $enlace->close(); ?>
                </div>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> FerreMax - Sistema desarrollado por Melani</p>
        </div>
    </footer>

    <script>
        // Script para el filtro de precio
        const precioSlider = document.getElementById('precio');
        const precioValor = document.getElementById('precio-valor');
        
        precioSlider.addEventListener('input', function() {
            precioValor.textContent = '$' + this.value;
        });
    </script>
</body>
</html>