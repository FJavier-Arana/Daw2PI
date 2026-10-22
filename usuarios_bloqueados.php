<?php
session_start();
if (empty($_SESSION["usuario"])) {
    header("Location login.html");
    exit;
}
$dsn = "mysql:host=localhost;dbname=chema;charset=utf8mb4";
$dbUsuario = 'root';
$dbClave = '';

$pdo = new PDO($dsn, $dbUsuario, $dbClave ,[
    PDO::ATTR_ERRMODE => PDO:: ERRMODE_EXCEPTION,
]);

$todo = "SELECT * FROM `user` WHERE `bloqueado_hasta` IS NOT NULL";
$stmt = $pdo->prepare($todo);
$stmt->execute();
$usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<link rel="stylesheet" href="tabla.css">
<body>
    <table>
        <tr>
            <th>adnim</th>
            <th>nombre</th>
            <th colspan="2">apellidos</th>
            <th>pais</th>
            <th>contraseña</th>
            <th>intentos</th>
            <th>bloqueados hasta</th>
        
        
        
        </tr>
        <?php
            foreach ($usuario as $usuario) {
                echo("<tr>");  
                          
                    echo("<td>".$usuario["admin"]."</td>");
                    echo("<td>".$usuario["nombre"]."</td>");
                    echo("<td>".$usuario["apellido1"]."</td>");
                    echo("<td>".$usuario["apellido2"]."</td>");
                    echo("<td>".$usuario["pais"]."</td>");
                    echo("<td>".$usuario["contraseña"]."</td>");
                    echo("<td>".$usuario["intentos_fallidos"]."</td>");
                    echo("<td>".$usuario["bloqueado_hasta"]."</td>");
                    

                echo("</tr>");

            }
        ?>
    </table>
    <a href="sesion_iniciada.php">Volver</a>
</body>
</html>