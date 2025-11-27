<?php
session_start();

require './config/database.php';

// Usuario y contraseña válidos
$usuario = $_POST['usuario_usuario'];
$password = $_POST['usuario_clave'];

$sql = "SELECT * FROM usuario WHERE usuario_usuario='$usuario' AND usuario_clave='$password'";
$result = $enlace->query($sql);

// Si se envía el formulario
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['usuario_nombre'] = $row['usuario_nombre'];
    $_SESSION['usuario_usuario'] = $row['usuario_usuario'];

    if($row['usuario_usuario'] == 'Administrador'){
        header("Location: dashboard.php");
    } else {
        header("Location: catalogo.php");
    }
    exit();
} else {
    $_SESSION['error'] = "Usuario o contrasena incorrectos.";
    header("Location: login.php");
}
