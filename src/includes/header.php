<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cine | Taquilla Digital</title>
    <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/custom.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-onyx py-3 sticky-top shadow">
    <div class="container">
        <a class="navbar-brand text-uppercase" href="index.php" style="color: var(--azure-mist);">
            <span style="color: var(--cherry-rose);">Cine</span>Stack
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="index.php">Cartelera</a></li>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php if (isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], ['superadmin', 'admin'], true)): ?>
                        <li class="nav-item"><a class="nav-link px-3" href="admin.php">Panel Admin</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><span class="nav-link text-light px-3">🍿 Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span></li>
                    <li class="nav-item"><a class="btn btn-cherry btn-sm ms-2 px-3" href="logout.php">Salir</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link px-3" href="login.php">Iniciar Sesión</a></li>
                    <li class="nav-item"><a class="btn btn-baltic btn-sm ms-2 px-3" href="registro.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>