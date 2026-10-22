<?php
$pdo = new PDO('mysql:host=localhost;dbname=chema;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $apellido1 = trim($_POST['apellido1'] ?? '');
    $apellido2 = trim($_POST['apellido2'] ?? '');
    $pais      = trim($_POST['pais'] ?? '');
    $usuario   = trim($_POST['usuario'] ?? '');
    $clave     = $_POST['clave'] ?? '';

    if ($nombre === '' || $apellido1 === '' || $apellido2 === '' || $pais === '' || $usuario === '' || $clave === '') {
        $error = 'Rellena todos los campos.';
    } else {
        
        $existe = $pdo->prepare('SELECT 1 FROM `user` WHERE usuario = ?');
        $existe->execute([$usuario]);

        if ($existe->fetchColumn()) {
            $error = 'Ese nombre de usuario ya existe.';
        } else {
            
            $stmt = $pdo->prepare('INSERT INTO `user` (nombre, apellido1, apellido2, pais, usuario, `contraseña`)
                                   VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nombre, $apellido1, $apellido2, $pais, $usuario, $clave]);
            $ok = '¡Registro completado correctamente!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro</title>

</head>
<link rel="stylesheet" href="estilos.css">
<body>
    <form method="post">
        <h2>Registro</h2>


        <label>Nombre</label>
        <input type="text" name="nombre" maxlength="50" required>
        <label>Primer apellido</label>
        <input type="text" name="apellido1" maxlength="50" required>
        <label>Segundo apellido</label>
        <input type="text" name="apellido2" maxlength="50" required>
        <label>País</label>
        <input type="text" name="pais" maxlength="50" required>
        <label>Usuario</label>
        <input type="text" name="usuario" maxlength="50" required>
        <label>Contraseña</label>
        <input type="password" name="clave" maxlength="50" required>

        <button type="submit">Crear cuenta</button>
    </form>
    <hr>
    <a href="login.html">Iniciar sesion</a>
</body>
</html>