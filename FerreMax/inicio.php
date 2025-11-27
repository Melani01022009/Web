<?php
// Inicia la sesión solo si no está ya activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FerreMax - Sistema de Gestión</title>
    <link rel="stylesheet" href="css/style.css">
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

                    <?php if (isset($_SESSION['usuario'])): ?>
                        <li><a href="logout.php">Cerrar Sesión (<?= htmlspecialchars($_SESSION['nombre']) ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Iniciar Sesión</a></li>
                    <?php endif; ?>
                </ul>

                <a href="catalogo.php" class="btn-catalogo">Ver Catálogo Completo</a>
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
