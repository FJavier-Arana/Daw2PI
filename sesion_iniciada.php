<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesión iniciada</title>
</head>
<body>
    <h1>Has iniciado sesión</h1>
    <p>Usuario: <?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?></p>
</body>
</html>
