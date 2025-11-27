<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                    <li><a href="inventario.php">Inventario</a></li>
                    <?php if (isset($_SESSION['usuario']) === 'Administrador'): ?>
                         <li><a href="logout.php">Cerrar Sesión (<?= htmlspecialchars($_SESSION['nombre']) ?>)</a></li>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['usuario']) === 'empleado'): ?>
                        <li><a href="logout.php">Cerrar Sesión (<?= htmlspecialchars($_SESSION['nombre']) ?>)</a></li>
                    <?php else: ?>
                        <li><a href="logout.php">Cerrar Sesión (<?= htmlspecialchars($_SESSION['nombre']) ?>)</a></li>
                    <?php endif; ?>
                </ul>

                
            </nav>
        </div>
    </header>

    <main>
        <!-- Puedes agregar contenido aquí si lo deseas -->
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y'); ?> FerreMax - Sistema desarrollado por Melani</p>
        </div>
    </footer>
</body>
</html>