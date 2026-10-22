<?php
session_start();

if (empty($_SESSION['usuario'])) {
    header('Location: login.html');
    exit;
}

$dsn = 'mysql:host=localhost;dbname=chema;charset=utf8mb4';
$dbUsuario = 'root';
$dbClave = '';

$pdo = new PDO($dsn, $dbUsuario, $dbClave, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$usuario = $_SESSION['usuario'];
$stmt = $pdo->prepare('SELECT admin FROM `user` WHERE usuario = ?');
$stmt->execute([$usuario]);
$admin = (int) $stmt->fetchColumn();

if ($admin !== 1) {
    http_response_code(403);
    exit('No tienes permisos para ver esta página.');
}


/* ================================
   CONTAR USUARIOS
================================ */

$stmt = $pdo->prepare('SELECT COUNT(*) FROM `user`');
$stmt->execute();

$cant = $stmt->fetchColumn();


/* ================================
   CONTAR ADMINISTRADORES
================================ */

$stmt = $pdo->prepare('SELECT COUNT(*) FROM `user` WHERE admin = 1');
$stmt->execute();

$cantAdmins = $stmt->fetchColumn();


/* ================================
   CONTAR USUARIOS ACTIVOS
================================ */

$stmt = $pdo->prepare('
    SELECT COUNT(*)
    FROM `user`
    WHERE bloqueado_hasta IS NULL
       OR bloqueado_hasta <= NOW()
');

$stmt->execute();

$cantActivos = $stmt->fetchColumn();


/* ================================
   CONTAR INTENTOS FALLIDOS
================================ */

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM `intentos_login`
    WHERE resultado = 'fallido'
");

$stmt->execute();

$cantFallidos = $stmt->fetchColumn();

?>



<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Panel</title>

    <link rel="stylesheet" href="sesion.css">

</head>

<body class="admin-panel">

<?php if ($admin == 1): ?>

    
    <header class="topbar">

        <div class="logo">
            <span>⚡</span>
            Panel de Administración
        </div>

        <div class="user-info">

            <span>
                <?= htmlspecialchars($usuario) ?>
            </span>

            <a href="login.html" class="logout">
                Cerrar sesión
            </a>

        </div>

    </header>


    <main class="dashboard">

        <section class="welcome">

            <h1>
                Panel de administración
            </h1>

            <p>
                Gestiona usuarios, actividad y configuración del sistema.
            </p>

        </section>


        <!-- ESTADÍSTICAS -->

        <section class="stats">

            <div class="stat-card">

                <div class="stat-icon">
                    👥
                </div>

                <div>

                    <span>
                        Usuarios
                    </span>

                    <strong>
                        <?= $cant ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    🛡️
                </div>

                <div>

                    <span>
                        Administradores
                    </span>

                    <strong>
                        <?= $cantAdmins ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    🟢
                </div>

                <div>

                    <span>
                        Estado
                    </span>

                    <strong>
                        Activo
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    🔐
                </div>

                <div>

                    <span>
                        Tu nivel
                    </span>

                    <strong>
                        Admin
                    </strong>

                </div>

            </div>

        </section>


        <!-- GRÁFICAS -->

        <section class="charts">

            <div class="chart-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Actividad de usuarios
                        </h2>

                        <span>
                            Últimos 7 días
                        </span>

                    </div>

                </div>


                <div class="fake-chart">

                    <div style="height:35%"></div>
                    <div style="height:55%"></div>
                    <div style="height:45%"></div>
                    <div style="height:70%"></div>
                    <div style="height:60%"></div>
                    <div style="height:85%"></div>
                    <div style="height:75%"></div>

                </div>


                <div class="chart-labels">

                    <span>Lun</span>
                    <span>Mar</span>
                    <span>Mié</span>
                    <span>Jue</span>
                    <span>Vie</span>
                    <span>Sáb</span>
                    <span>Dom</span>

                </div>

            </div>


            <div class="chart-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Accesos
                        </h2>

                        <span>
                            Distribución
                        </span>

                    </div>

                </div>


                <div class="access-chart">

                    <div class="circle-chart">

                        <strong>
                            82%
                        </strong>

                        <small>
                            Correctos
                        </small>

                    </div>

                </div>


                <div class="legend">

                    <span>
                        🟢 Accesos correctos
                    </span>

                    <span>
                        🔴 Accesos rechazados
                    </span>

                </div>

            </div>

        </section>


        <!-- ADMINISTRACIÓN -->

        <section class="management">

            <h2>
                Administración
            </h2>


            <div class="admin-grid">


                <a href="usuarios.php" class="admin-card">

                    <span class="admin-icon">
                        👥
                    </span>

                    <h3>
                        Gestionar usuarios
                    </h3>

                    <p>
                        Crear, modificar y eliminar usuarios.
                    </p>

                </a>


                <a href="administradores.php" class="admin-card">

                    <span class="admin-icon">
                        🛡️
                    </span>

                    <h3>
                        Administradores
                    </h3>

                    <p>
                        Gestionar permisos administrativos.
                    </p>

                </a>


                <a href="intentos.php" class="admin-card">

                    <span class="admin-icon">
                        🔐
                    </span>

                    <h3>
                        Intentos de acceso
                    </h3>

                    <p>
                        Consultar los intentos de inicio de sesión.
                    </p>

                </a>


                <a href="usuarios_bloqueados.php" class="admin-card">

                    <span class="admin-icon">
                        🚫
                    </span>

                    <h3>
                        Usuarios bloqueados
                    </h3>

                    <p>
                        Consultar y gestionar bloqueos.
                    </p>

                </a>


                <a href="estadisticas.php" class="admin-card">

                    <span class="admin-icon">
                        📊
                    </span>

                    <h3>
                        Estadísticas
                    </h3>

                    <p>
                        Ver estadísticas completas.
                    </p>

                </a>


                <a href="configuracion.php" class="admin-card">

                    <span class="admin-icon">
                        ⚙️
                    </span>

                    <h3>
                        Configuración
                    </h3>

                    <p>
                        Configurar parámetros del sistema.
                    </p>

                </a>


            </div>

        </section>

    </main>


<?php else: ?>


    <!-- =========================
         PANEL USUARIO NORMAL
    ========================== -->

    <header class="topbar">

        <div class="logo">

            <span>⚡</span>

            Mi cuenta

        </div>


        <div class="user-info">

            <span>
                <?= htmlspecialchars($usuario) ?>
            </span>

            <a href="login.html" class="logout">
                Cerrar sesión
            </a>

        </div>

    </header>


    <main class="dashboard">

        <section class="welcome">

            <h1>
                Bienvenido, <?= htmlspecialchars($usuario) ?>
            </h1>

            <p>
                Gestiona tu cuenta y consulta tu actividad.
            </p>

        </section>


        <section class="stats">

            <div class="stat-card">

                <div class="stat-icon">
                    👤
                </div>

                <div>

                    <span>
                        Usuario
                    </span>

                    <strong>
                        <?= htmlspecialchars($usuario) ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon">
                    🟢
                </div>

                <div>

                    <span>
                        Estado
                    </span>

                    <strong>
                        Activo
                    </strong>

                </div>

            </div>

        </section>


        <section class="management">

            <h2>
                Mi cuenta
            </h2>


            <div class="admin-grid">


                <a href="perfil.php" class="admin-card">

                    <span class="admin-icon">
                        👤
                    </span>

                    <h3>
                        Mi perfil
                    </h3>

                    <p>
                        Consultar tus datos.
                    </p>

                </a>


                <a href="cambiar_clave.php" class="admin-card">

                    <span class="admin-icon">
                        🔑
                    </span>

                    <h3>
                        Cambiar contraseña
                    </h3>

                    <p>
                        Cambiar la contraseña de tu cuenta.
                    </p>

                </a>


                <a href="mi_actividad.php" class="admin-card">

                    <span class="admin-icon">
                        📊
                    </span>

                    <h3>
                        Mi actividad
                    </h3>

                    <p>
                        Consultar tus accesos.
                    </p>

                </a>


            </div>

        </section>

    </main>


<?php endif; ?>

</body>

    </html>
