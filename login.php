<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión</title>
</head>
<body>
    <form method="post" action="login2.php">
        <h2>Iniciar sesión</h2>
        <label for="usuario">Usuario</label>
        <input id="usuario" type="text" name="usuario" maxlength="50" required>

        <label for="clave">Contraseña</label>
        <input id="clave" type="password" name="clave" maxlength="50" required>

        <button type="submit">Iniciar sesión</button>
    </form>
</body>
</html>
