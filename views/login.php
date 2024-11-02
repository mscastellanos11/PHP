<?php
session_start();
require_once '../inc/conexion.php';
require_once '../inc/funciones.php';

$errores = [
    'error' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = limpiar_dato($_POST['email']);
    $password = $_POST['password'];

    // Consultamos si el email existe
    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_name'] = $usuario['nombre'];
        $_SESSION['user_role'] = $usuario['rol'];
        $_SESSION['user_email'] = $usuario['email'];
        // Reto imagen
        $_SESSION['user_imagen'] = $usuario['imagen'];
        
        header("Location: dashboard.php");
        exit;
    } else {
        // echo "Email o contraseña incorrectos.";
        $errores['error'] = 'Email o contraseña incorrectos.';

    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body{
            margin: 0; /* Elimina márgenes por defecto */

        }
        
        .caja{
            display: flex; /* Activa el modo de grid */
            place-items: center; /* Centra el contenido horizontal y verticalmente */
            min-height: 100vh; /* Asegura que el body tenga al menos la altura completa de la pantalla */
            background-image: url('../1.gif');
            background-size: cover; 
            background-position: center; 
            justify-items: stretch;
            justify-content: flex-start;
            flex-wrap: nowrap;
            flex-direction: column;
            align-items: center;
            padding-top: 60px;
        }

        .gif {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 2px solid black;
            background-image: url('../2.gif');
            background-size: cover;
            background-position: center;     
            margin-bottom: 60px;
        }

        header{
            display: flex; /* Activa el modo flexbox */
            justify-content: flex-end; /* Alinea horizontalmente el contenido a la derecha */
            align-items: center; /* Centra verticalmente el contenido dentro del header */
            height: 50px;
        }

        a{
            padding-right: 20px;
            text-decoration: none; /* Elimina el subrayado del enlace */
            color: black;
            font-size: 27px;
        }
        
        form{
            width: 100%;
            border: 2px solid purple; 
            background-color: rgba(255, 255, 255, 0.5);
            border-top-right-radius: 35px;   
            padding: 3px;      
            border-bottom-left-radius:10px ;
        }

        h2{
            text-align: center;
        }

        input{
            width: -webkit-fill-available;
        }

        .error {
            text-align: center;
            color: red;
            font-weight: bold;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

    <header>
        <a href="../index.php">Index</a>
        <a href="registro.php">Registrar</a>
    </header>

    <div class="caja">
        <div class="gif"></div>
        <form method="post">
            <h2>Inicio de Sesión</h2>
    
            <?php if (!empty($errores['error'])): ?>
                <p class="error"><?php echo $errores['error']; ?></p>
            <?php endif; ?>
    
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" >
    
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" >
    
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>