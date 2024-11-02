<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// // Ruta absoluta
$ruta_absoluta = $_SESSION['user_imagen'];
// echo ($ruta_absoluta);
// echo '<br>';
// // Convertir a ruta relativa
$ruta_relativa = str_replace('C:\xampp\htdocs\curso_php\mi-proyecto\views/', '', $ruta_absoluta);
// echo ($ruta_relativa);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body{
            margin: 0; /* Elimina márgenes por defecto */
        }

        img{
            border-radius: 50%;
            border: 2px solid black;
            background-size: cover;
            background-position: center;     
            margin-bottom: 60px;
            width: 300px;
            height: 300px;
        }
        header{
            display: flex; /* Activa el modo flexbox */
            justify-content: flex-end; /* Alinea horizontalmente el contenido a la derecha */
            align-items: center; /* Centra verticalmente el contenido dentro del header */
            height: 50px;
        }
        .caja{
            display: flex;
            place-items: center; 
            background-color: #f0f0f0; 
            flex-direction: column;
            height: 95vh;
        }
        a{
            padding-right: 20px;
            text-decoration: none; /* Elimina el subrayado del enlace */
            color: black;
            font-size: 27px;
        }
        p{
            font-size: 15px;
            font-weight: bold;

        }
        h2{
            font-size: 16px;
            font-weight: bold;
        }




    </style>
</head>
<body>
<header>
<a href="admin.php">Administración</a>
<a href="../logout.php">Cerrar Sesión</a>
    </header>
    
    <div class="caja">
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>

    <p>Rol: <?php echo htmlspecialchars($_SESSION['user_role']); ?></p>
    <p>Imagen de perfil:</p>
    <img src="<?php echo htmlspecialchars($ruta_relativa); ?>" alt="Imagen de usuario no funciona">
    
    </div>


</body>
</html>