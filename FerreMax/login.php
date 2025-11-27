<?php
session_start();
?>

<!-- FORMULARIO DE LOGIN -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Simple</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #dcdde1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            width: 300px;
            text-align: center;
        }

        h2 {
            color: #2f3640;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background-color: #273c75;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            background-color: #40739e;
        }

        .mensaje {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Iniciar Sesión</h2>

    <form action="valid_login.php" method="POST">
        <input type="text" name="usuario_usuario" placeholder="Usuario" required>
        <input type="password" name="usuario_clave" placeholder="Contraseña" required>
        <button type="submit" name="entrar">Entrar</button>
    </form>
    <?php  
        if(isset($_SESSION['error'])) :?>
            <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif ?>
</div>

</body>
</html>
