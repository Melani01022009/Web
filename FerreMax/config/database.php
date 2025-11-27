<?php
$servidor = "localhost:3307";
$usuario = "root";
$clave = "1234";
$baseDeDatos = "inventario";

// Crear conexión
$enlace = new mysqli($servidor, $usuario, $clave, $baseDeDatos);

if($enlace->connect_error){
    die("Error en la conexion: " . $enlace->connect_error);
}

?>