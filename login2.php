<?php
session_start();


$maxIntentos = 3;
$minutosBloqueo = 15;
$dsn = 'mysql:host=localhost;dbname=chema;charset=utf8mb4';
$dbUsuario = 'root';
$dbClave = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$pdo = new PDO($dsn, $dbUsuario, $dbClave, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$usuario = trim($_POST['usuario'] ?? '');
$clave = $_POST['clave'] ?? '';
$mensaje = '';
$resultadoIntento = 'fallido';

if ($usuario === '' || $clave === '') {
    $mensaje = 'Introduce el usuario y la contraseña.';
    $pdo->prepare('INSERT INTO `intentos_login` (usuario, resultado) VALUES (?, ?)')
        ->execute([$usuario, 'datos_incompletos']);
} else {
    // Bloquea la fila durante la comprobación para no perder incrementos simultáneos.
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT usuario, `contraseña`, intentos_fallidos, bloqueado_hasta
                               FROM `user` WHERE usuario = ? FOR UPDATE');
        $stmt->execute([$usuario]);
        $usuarioBD = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuarioBD) {
            $mensaje = 'Usuario o contraseña incorrectos.';
        } else {
            $ahora = new DateTimeImmutable('now');
            $bloqueadoHasta = !empty($usuarioBD['bloqueado_hasta'])
                ? new DateTimeImmutable($usuarioBD['bloqueado_hasta'])
                : null;

            if ($bloqueadoHasta && $bloqueadoHasta > $ahora) {
                $minutosRestantes = (int) ceil(($bloqueadoHasta->getTimestamp() - $ahora->getTimestamp()) / 60);
                $mensaje = "La cuenta está bloqueada. Inténtalo de nuevo en {$minutosRestantes} minuto(s).";
                $resultadoIntento = 'bloqueado';
            } else {
                // Al expirar el bloqueo, comienza de nuevo el contador.
                if ($bloqueadoHasta) {
                    $pdo->prepare('UPDATE `user` SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE usuario = ?')
                        ->execute([$usuario]);
                    $usuarioBD['intentos_fallidos'] = 0;
                }

                // Compatibilidad con las contraseñas en texto plano del registro actual y con hashes.
                $claveGuardada = (string) $usuarioBD['contraseña'];
                $esHash = password_get_info($claveGuardada)['algo'] !== null;
                $claveCorrecta = $esHash
                    ? password_verify($clave, $claveGuardada)
                    : hash_equals($claveGuardada, $clave);

                if ($claveCorrecta) {
                    $pdo->prepare('UPDATE `user` SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE usuario = ?')
                        ->execute([$usuario]);
                    $resultadoIntento = 'correcto';
                    $_SESSION['usuario'] = $usuarioBD['usuario'];
                } else {
                    $intentos = (int) $usuarioBD['intentos_fallidos'] + 1;
                    if ($intentos >= $maxIntentos) {
                        $pdo->prepare('UPDATE `user` SET intentos_fallidos = ?,
                                       bloqueado_hasta = DATE_ADD(NOW(), INTERVAL ' . (int) $minutosBloqueo . ' MINUTE)
                                       WHERE usuario = ?')->execute([$maxIntentos, $usuario]);
                        $mensaje = "Contraseña incorrecta. La cuenta queda bloqueada durante {$minutosBloqueo} minutos.";
                    } else {
                        $pdo->prepare('UPDATE `user` SET intentos_fallidos = ? WHERE usuario = ?')
                            ->execute([$intentos, $usuario]);
                        $restantes = $maxIntentos - $intentos;
                        $mensaje = "Usuario o contraseña incorrectos. Te quedan {$restantes} intento(s).";
                    }
                }
            }
        }

        $pdo->prepare('INSERT INTO `intentos_login` (usuario, resultado) VALUES (?, ?)')
            ->execute([$usuario, $resultadoIntento]);
        $pdo->commit();

        if ($resultadoIntento === 'correcto') {
            header('Location: sesion_iniciada.php');
            exit;
        }
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <form method="post" action="login2.php">
        <h2>Iniciar sesión</h2>
        <p role="alert"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
        <label for="usuario">Usuario</label>
        <input id="usuario" type="text" name="usuario" maxlength="50" required
               value="<?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?>">

        <label for="clave">Contraseña</label>
        <input id="clave" type="password" name="clave" maxlength="50" required>

        <button type="submit">Volver a intentarlo</button>
    </form>
</body>
</html>
