<?php
session_start();
require_once 'config/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_nombre'])) {
    header("Location: login.php");
    exit();
}

global $enlace;

$query = "SELECT * FROM usuarios";
$result = $enlace->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ferretería</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .header {
            background-color: #343a40;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav {
            background-color: #495057;
            padding: 1rem 2rem;
        }
        .nav a {
            color: white;
            text-decoration: none;
            margin-right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }
        .nav a:hover {
            background-color: #6c757d;
        }
        .container {
            padding: 2rem;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .btn {
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ferretería - Sistema de Inventario</h1>
        <div>
            <span>Bienvenido, <?php echo $_SESSION['usuario_nombre']; ?> (<?php echo $_SESSION['usuario_usuario']; ?>)</span>
            <a href="logout.php" class="btn btn-danger" style="margin-left: 1rem;">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="catalogo.php">Catálogo</a>
        <a href="productos.php">Productos</a>
    </div>
    
    <div class="container">
        <div class="stats">
            <?php
            // Obtener estadísticas
            $query = "SELECT COUNT(*) as total FROM producto";
            $stmt = $enlace->prepare($query);
            if ($stmt === false) {
                die("Error al preparar la consulta: " . $enlace->error); // Captura el error de la consulta
            }
            $stmt->execute();
            $result = $stmt->get_result();
            // Verificar si la consulta devolvió un resultado
            if ($row = $result->fetch_assoc()) {
                // Obtener el total de productos
                $total_productos = $row['total'];
            } else {
                // Si no hay resultados
                echo "No se encontraron productos.";
            }
            $stmt->close();
            
            $query = "SELECT COUNT(producto_stock) as total_stock FROM producto";
            $stmt = $enlace->prepare($query);
             if ($stmt === false) {
                die("Error al preparar la consulta: " . $enlace->error); // Captura el error de la consulta
            }
            $stmt->execute();
            $result = $stmt->get_result();
            // Verificar si la consulta devolvió un resultado
            if ($row = $result->fetch_assoc()) {
                // Obtener el total de productos
                $total_stock = $row['total_stock'];
            } else {
                // Si no hay resultados
                echo "No se encontraron productos disponibles.";
            }
            $stmt->close();
            
            $query = "SELECT COUNT(*) as bajos FROM producto WHERE producto_stock < 10";
            $stmt = $enlace->prepare($query);
            if ($stmt === false) {
                die("Error al preparar la consulta: " . $enlace->error); // Captura el error de la consulta
            }
            $stmt->execute();

            $result = $stmt->get_result();
            // Verificar si la consulta devolvió un resultado
            if ($row = $result->fetch_assoc()) {
                // Obtener el total de productos
                $stock_bajos = $row['bajos'];
            } else {
                // Si no hay resultados
                echo "No se encontraron productos bajos.";
            }
            $stmt->close();
            ?>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_productos; ?></div>
                <div>Total Productos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_stock; ?></div>
                <div>Unidades en Stock</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stock_bajos; ?></div>
                <div>Productos con Stock Bajo</div>
            </div>
        </div>
        
        <div class="card">
            <h2>Productos</h2>
            <?php
                // --- Configuración de paginación ---
                $registros_por_pagina = 10; // cantidad de productos por página
                $pagina_actual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                $inicio = ($pagina_actual - 1) * $registros_por_pagina;

                // --- Contar total de registros ---
                $total_query = "SELECT COUNT(*) AS total FROM producto";
                $total_result = $enlace->query($total_query);
                $total_filas = $total_result->fetch_assoc()['total'];
                $total_paginas = ceil($total_filas / $registros_por_pagina);

                // --- Consulta con límite y desplazamiento (paginación) ---
                $query = "SELECT 
                            producto_id,
                            producto_codigo,
                            producto_nombre,
                            producto_precio,
                            producto_stock,
                            producto_foto,
                            categoria_id,
                            usuario_id
                        FROM producto
                        ORDER BY producto_id DESC
                        LIMIT ?, ?";

                $stmt = $enlace->prepare($query);
                if ($stmt === false) {
                    die("Error al preparar la consulta: " . $enlace->error);
                }

                // Enlazar parámetros (inicio y límite)
                $stmt->bind_param("ii", $inicio, $registros_por_pagina);
                $stmt->execute();
                $resultado = $stmt->get_result();

                // --- Mostrar tabla ---
                if ($resultado && $resultado->num_rows > 0) {
                    echo '<table border="1" cellpadding="8" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Foto</th>
                                    <th>ID Categoría</th>
                                    <th>ID Usuario</th>
                                </tr>
                            </thead>
                            <tbody>';

                    while ($producto = $resultado->fetch_assoc()) {
                        echo "<tr>
                                <td>{$producto['producto_id']}</td>
                                <td>{$producto['producto_codigo']}</td>
                                <td>{$producto['producto_nombre']}</td>
                                <td>{$producto['producto_precio']}</td>
                                <td>{$producto['producto_stock']}</td>
                                <td>{$producto['producto_foto']}</td>
                                <td>{$producto['categoria_id']}</td>
                                <td>{$producto['usuario_id']}</td>
                            </tr>";
                    }

                    echo '</tbody></table>';
                } else {
                    echo "<p>No se encontraron productos.</p>";
                }

                // --- Navegación de páginas ---
                if ($total_paginas > 1) {
                    echo '<div style="margin-top:15px; text-align:center;">';

                    // Botón "Anterior"
                    if ($pagina_actual > 1) {
                        echo '<a href="?pagina=' . ($pagina_actual - 1) . '" style="margin: 0 5px;">&laquo; Anterior</a>';
                    }

                    // Números de página
                    for ($i = 1; $i <= $total_paginas; $i++) {
                        if ($i == $pagina_actual) {
                            echo '<strong style="margin: 0 5px; color: blue;">' . $i . '</strong>';
                        } else {
                            echo '<a href="?pagina=' . $i . '" style="margin: 0 5px;">' . $i . '</a>';
                        }
                    }

                    // Botón "Siguiente"
                    if ($pagina_actual < $total_paginas) {
                        echo '<a href="?pagina=' . ($pagina_actual + 1) . '" style="margin: 0 5px;">Siguiente &raquo;</a>';
                    }

                    echo '</div>';
                }

                $stmt->close();
                $enlace->close();
            ?>
        </div>
    </div>
</body>
</html>