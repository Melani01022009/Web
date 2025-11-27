<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carrito de compras</title>
<link rel="stylesheet" href="estilos.css">
</head>
<body>
<h2>🛒 Carrito de Compras</h2>

<style>
 body {
    font-family: 'Poppins', sans-serif;
    background-color: #f9f9f9;
    padding: 30px;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}

.productos-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.producto-card {
    background-color: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.2s;
}

.producto-card:hover {
    transform: scale(1.02);
}

.producto-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 8px;
}

.btn-agregar, .btn-ver-carrito, .btn-seguir, .btn-eliminar {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    display: inline-block;
    transition: background-color 0.3s;
}

.btn-agregar:hover, .btn-ver-carrito:hover, .btn-seguir:hover, .btn-eliminar:hover {
    background-color: #0056b3;
}

/* Tabla del carrito */
.tabla-carrito {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background-color: #fff;
}

.tabla-carrito th, .tabla-carrito td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center;
}
</style>

</body>
</html>

