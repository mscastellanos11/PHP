<?php
session_start();
require_once '../inc/conexion.php';
require_once '../inc/funciones.php';

$errores = [
    'nombre' => '',
    'email' => '',
    'password' => '',
    'imagen' => '',
    'exito' => ''
];

$nombre = '';
$email = '';
$password = '';
$rol = 'viewer'; 
$imagenPefil = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiar_dato($_POST['nombre']);
    $email = limpiar_dato($_POST['email']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];  

    // Validaciones
    if (empty($nombre)) {
        $errores['nombre'] = 'El nombre es obligatorio.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El email no es válido.';
    }
    if (strlen($password) < 6) {
        $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
    }
    if ($rol !== 'admin' && $rol !== 'viewer') {
        $errores['rol'] = 'El rol seleccionado no es válido.';
    }
    
    // Validar la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $imagen = $_FILES['imagen'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($imagen['type'], $allowed_types)) {
            $errores['imagen'] = 'El formato de la imagen no es válido. Solo se permiten JPEG, PNG y GIF.';
        } else {
            // Guardar la imagen en el servidor
            $ruta_imagen = '../uploads/' . basename($imagen['name']);
            move_uploaded_file($imagen['tmp_name'], $ruta_imagen);
        }
    } else {
        $errores['imagen'] = 'La imagen es obligatoria.';
    }

    // Verificar si el email ya existe en la base de datos
    $sqlVerificacion = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
    $stmtVerificacion = $conexion->prepare($sqlVerificacion);
    $stmtVerificacion->bindParam(':email', $email);
    $stmtVerificacion->execute();
    $emailExiste = $stmtVerificacion->fetchColumn();

    if ($emailExiste) {
        $errores['email'] = 'El correo electrónico ya está registrado.';
    }

    // Si no hay errores, proceder con el registro
    if (empty(array_filter($errores))) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, email, password, rol, imagen) VALUES (:nombre, :email, :password, :rol, :imagen)";
        $stmt = $conexion->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':imagen', $ruta_imagen);

        if ($stmt->execute()) {
            $errores['exito'] = 'Usuario registrado exitosamente.';
        } else {
            echo "Error al registrar el usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body {
            margin: 0; 
        }
        .caja {
            display: flex;
            display: grid; 
            place-items: center; 
            min-height: 100vh; 
            background-color: #f0f0f0; 
        }

        header {
            display: flex; 
            justify-content: flex-end; 
            align-items: center; 
            height: 50px;
        }

        a {
            padding-right: 20px;
            text-decoration: none; 
            color: black;
            font-size: 27px;
        }

        form {
            width: 100%;
        }

        h2 {
            text-align: center;
        }

        .exito {
            text-align: center;
            color: green;
            font-weight: bold;
        }

        input {
            width: -webkit-fill-available;    
        }

        input[type="file"] {
            display: none; /* Ocultar el input de tipo file */
        }

        .file-label {
            
            border: 0.2px solid black;
            border-radius: 2px;
            background-color: #f0f0f0;
            cursor: pointer;
            text-align: center;
            font-size: 13px; /* Tamaño de fuente consistente */
            width: 90px; /* Asegura que el label tenga el mismo ancho que el select */
        }

        .error {
            color: red;
            font-size: 0.9em;
        }
        
        .caja2 {
            display: flex;
            justify-content: center; 
            align-items: center; 
            gap: 10px;
            background-color: #f0f0f0;
            padding-bottom: 10px;
            padding-top: 2px;
            text-align: center;
        }

        .caja3 {
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            text-align: center;
            border: 1px solid #d0d0d0;
            background-color: #f0f0f0;
            width: 100vh;
            height: 20px; 
            padding: 5px;   
        }

        button[type="submit"] {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <a href="../index.php">Index</a>
        <a href="login.php">Login</a>
    </header>

    <div class="caja">
        <form method="post" enctype="multipart/form-data" style="width: 500px;">
            <h2>Registro de Usuario</h2>
            <?php if (!empty($errores['exito'])): ?>
                <p class="exito"><?php echo $errores['exito']; ?></p>
            <?php endif; ?>
    
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($nombre); ?>" >
            <?php if (!empty($errores['nombre'])): ?>
                <p class="error"><?php echo $errores['nombre']; ?></p>
            <?php endif; ?>
        
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" >
            <?php if (!empty($errores['email'])): ?>
                <p class="error"><?php echo $errores['email']; ?></p>
            <?php endif; ?>
        
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" >
            <?php if (!empty($errores['password'])): ?>
                <p class="error"><?php echo $errores['password']; ?></p>
            <?php endif; ?>
            
            <div class="caja2">  
                <div class="caja3"> 
                    <label for="rol" style= "padding-top:10px">Rol:</label>
                </div>
                <div class="caja3">
                    <select id="rol" name="rol">
                        <option value="viewer" <?php echo $rol === 'viewer' ? 'selected' : ''; ?>>Invitado</option>
                        <option value="admin" <?php echo $rol === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
            </div>

            <div class="caja2">
                <div class="caja3">   
                    <label for="imagen" style= "padding-top:10px">Imagen de perfil:</label>
                </div>
                <div class="caja3">
                    <input type="file" name="imagen" id="imagen" accept="image/jpeg, image/png, image/gif">
                    <label for="imagen" class="file-label" style="margin-bottom:0px">Elegir archivo</label>
                </div>
            </div>   

            <?php if (!empty($errores['imagen'])): ?>
                <p class="error"><?php echo $errores['imagen']; ?></p>
            <?php endif; ?>    
            
            <button type="submit">Registrar</button>
        </form>
    </div>
</body>
</html>
