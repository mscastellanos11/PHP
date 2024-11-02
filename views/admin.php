<?php
session_start();
require_once '../inc/funciones.php';
require_once '../inc/conexion.php';

if (!verificar_rol('admin')) {
    echo "Acceso denegado.";
    exit;
}

$tituloError = '';
$descripcionError = '';
$imagenError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    // Validaciones
    if (empty($titulo)) {
        $tituloError = 'Ingrese un título.';
    }

    if (empty($descripcion)) {
        $descripcionError = 'Ingrese una descripción.';
    }

    if (!empty($_FILES['imagen']['name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['imagen']['type'], $allowedTypes)) {
            $imagenNombre = basename($_FILES['imagen']['name']);
            $imagenTemp = $_FILES['imagen']['tmp_name'];
            $imagenDestino = "../uploads/" . $imagenNombre;
            if (!move_uploaded_file($imagenTemp, $imagenDestino)) {
                $imagenError = 'Error al mover el archivo.';
            }
        } else {
            $imagenError = 'Formato de imagen no permitido.';
        }
    } else {
        $imagenError = 'Seleccione una imagen.';
    }

    // Si no hay errores, insertar en la base de datos
    if (empty($tituloError) && empty($descripcionError) && empty($imagenError)) {
        $conexion = db_connect(); // Conectar a la base de datos

        $sql = "INSERT INTO posts (titulo, descripcion, imagen, usuario_id) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt === false) {
            die("Error en la preparación de la sentencia: " . $conexion->error);
        }

        $stmt->bind_param("sssi", $titulo, $descripcion, $imagenNombre, $user_id);

        if ($stmt->execute()) {
            // Redirigir a postprivado.php pasando el user_id
            header("Location: postprivado.php?user_id=" . urlencode($user_id));
            exit;
        } else {
            echo "Error al insertar en la base de datos: " . $stmt->error;
        }

        $stmt->close();
        $conexion->close();
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            margin: 0;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }
        
        header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 50px;
            padding: 10px 20px;
            background-color: white;
            border-bottom: 1px solid #ccc;
        }

        header a {
            text-decoration: none;
            color: black;
            font-size: 16px;
            margin-left: 20px;
        }

        .caja {
            display: flex;
            display: grid; 
            place-items: center; 
            min-height: 100vh; 
            background-color: #f0f0f0;
        }

        h2 {
            color: #333;
            text-align: center;
        }


        .file-label {
            
            
            background-color: #f0f0f0;
            cursor: pointer;
            text-align: center;
            font-size: 13px; 
            width: 90px; 
            position: absolute;
            top: 1px;
        }

        .formulario {
            display: flex; 
            flex-direction: column; 
            background-color: #f0f0f0;
            padding: 20px;
            max-width: 400px;
            width: 100%;
            margin-bottom: 100px;
        }

        .formulario h3 {
            margin-top: 0;
            font-size: 16px;
            color: #555;
            text-align: center;
        }

        .formulario label {
            font-size: 14px;
            color: #333;
        }

        .formulario input[type="text"]  {
            width: 100%;
            padding: 10px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .formulario input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 5px;
            font-size: 11.5px;
        }

        .error {
            color: red;
            font-size: 12px;
            margin: 0 0 10px 0;
        }

        .formulario button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .formulario button:hover {
            background-color: #0056b3;
        }
        input [type="file"]{
            width: 100px;
        }
    </style>
</head>
<body>
    <header>
        <a href="dashboard.php">Volver al Dashboard</a>
        <a href="posts.php">Post creados</a>
    </header>

    <div class="caja">
        <div class="formulario">
            <h2>Área de Administración</h2>
            <pre style=" color: #555; font-family: Arial, sans-serif;">
                <h3> Formulario para la creación de un post
  asociado al ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?>, con conexión activa.</h3></pre>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" id="titulo" value="<?php echo isset($titulo) ? htmlspecialchars($titulo) : ''; ?>">
                <?php if ($tituloError): ?><div class="error"><?php echo $tituloError; ?></div><?php endif; ?>
                
                <label for="descripcion">Descripción:</label>
                <input type="text" name="descripcion" id="descripcion" value="<?php echo isset($descripcion) ? htmlspecialchars($descripcion) : ''; ?>">
                <?php if ($descripcionError): ?><div class="error"><?php echo $descripcionError; ?></div><?php endif; ?>
                
                <label for="imagen">Imagen:</label>
                <input type="file" name="imagen" id="imagen" accept="image/jpeg, image/png, image/gif"Elegir archivo>
                
                
                <?php if ($imagenError): ?><div class="error"><?php echo $imagenError; ?></div><?php endif; ?>
                
                <button type="submit" style="width: 324px;">Crear</button>
            </form>
        </div>
    </div>  
</body>
</html