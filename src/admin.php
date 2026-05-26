<?php
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_rol'] ?? '', ['superadmin', 'admin'], true)) {
    header('Location: login.php');
    exit;
}

function ensurePromocionesSchema(PDO $pdo): void {
    $stmt = $pdo->query("SHOW TABLES LIKE 'promociones'");
    if (!$stmt->fetch()) {
        return;
    }

    $existingColumns = [];
    $stmt = $pdo->query("SHOW COLUMNS FROM promociones");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[$row['Field']] = true;
    }

    if (!isset($existingColumns['stock'])) {
        $pdo->exec("ALTER TABLE promociones ADD COLUMN stock INT NOT NULL DEFAULT 0 AFTER codigo_descuento");
    }
    if (!isset($existingColumns['descuento'])) {
        $pdo->exec("ALTER TABLE promociones ADD COLUMN descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER codigo_descuento");
    }
}

ensurePromocionesSchema($pdo);

$esSuperAdmin = ($_SESSION['usuario_rol'] ?? '') === 'superadmin';
$mensaje = '';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $esSuperAdmin) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] === 'admin' ? 'admin' : 'cliente';

    if (strlen($nombre) < 2) {
        $errores[] = 'El nombre es muy corto.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingresa un correo electrónico válido.';
    }
    if (strlen($password) < 6) {
        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    }

    if (empty($errores)) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errores[] = 'Ya existe un usuario con ese correo.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nombre, $email, $hash, $rol])) {
                $mensaje = 'Usuario creado correctamente. El usuario podrá iniciar sesión con sus credenciales.';
            } else {
                $errores[] = 'Ocurrió un error al crear el usuario.';
            }
        }
    }
}

// Datos básicos para el panel de administración
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalPeliculas = $pdo->query("SELECT COUNT(*) FROM peliculas")->fetchColumn();
$totalCompras = $pdo->query("SELECT COUNT(*) FROM compras")->fetchColumn();
$totalFunciones = $pdo->query("SELECT COUNT(*) FROM funciones")->fetchColumn();

$totalPromociones = 0;
$ultimasPromociones = [];
try {
    $totalPromociones = $pdo->query("SELECT COUNT(*) FROM promociones")->fetchColumn();
    $ultimasPromociones = $pdo->query("SELECT titulo, descuento, stock, fecha_inicio, fecha_fin FROM promociones ORDER BY stock ASC, fecha_fin ASC LIMIT 6")->fetchAll();
} catch (PDOException $e) {
    // Si la tabla o columnas no existen aún, evitamos el error y continuamos.
    $totalPromociones = 0;
    $ultimasPromociones = [];
}

$ultimosUsuarios = $pdo->query("SELECT nombre, email, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC LIMIT 10")->fetchAll();

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-6 fw-bold">Panel de Administración</h1>
            <p class="text-muted">Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>. Aquí puedes ver el estado general del sistema.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Usuarios</h6>
                    <h2 class="fw-bold"><?= htmlspecialchars($totalUsuarios) ?></h2>
                    <p class="text-muted small mb-0">Registros totales de clientes y administradores.</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Películas</h6>
                    <h2 class="fw-bold"><?= htmlspecialchars($totalPeliculas) ?></h2>
                    <p class="text-muted small mb-0">Total de películas cargadas en el catálogo.</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Compras</h6>
                    <h2 class="fw-bold"><?= htmlspecialchars($totalCompras) ?></h2>
                    <p class="text-muted small mb-0">Tickets vendidos hasta ahora.</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Funciones</h6>
                    <h2 class="fw-bold"><?= htmlspecialchars($totalFunciones) ?></h2>
                    <p class="text-muted small mb-0">Horarios registrados en el sistema.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-12">
            <?php if ($esSuperAdmin): ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold">Crear nuevo usuario</h5>
                        <p class="text-muted small">El horario de registro se asigna automáticamente. Solo ingresa los datos básicos y elige el rol.</p>

                        <?php if (!empty($errores)): ?>
                            <div class="alert alert-warning" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php elseif (!empty($mensaje)): ?>
                            <div class="alert alert-success" role="alert"><?= htmlspecialchars($mensaje) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="admin.php">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="nombre" class="form-label small fw-bold">Nombre completo</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="email" class="form-label small fw-bold">Correo electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="password" class="form-label small fw-bold">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="mb-4 mt-3">
                                <label for="rol" class="form-label small fw-bold">Rol</label>
                                <select class="form-select" id="rol" name="rol">
                                    <option value="cliente" <?= (($_POST['rol'] ?? '') === 'cliente' ? 'selected' : '') ?>>Cliente</option>
                                    <option value="admin" <?= (($_POST['rol'] ?? '') === 'admin' ? 'selected' : '') ?>>Admin</option>
                                </select>
                                <div class="form-text">El admin creado podrá iniciar sesión como admin, pero solo el admin principal tiene acceso total al panel.</div>
                            </div>
                            <button type="submit" class="btn btn-baltic">Crear usuario</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold">Vista del panel</h5>
                        <p class="text-muted small mb-0">Tienes acceso de solo lectura en este panel. Puedes ver estadísticas, usuarios y promociones, pero no puedes crear ni modificar usuarios.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">Promociones activas</h4>
            <p class="text-muted small">Controla las promociones disponibles y revisa su stock.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Promoción</th>
                            <th>Descuento</th>
                            <th>Stock</th>
                            <th>Válida hasta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ultimasPromociones)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay promociones activas en este momento.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ultimasPromociones as $promo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($promo['titulo']) ?></td>
                                    <td>$<?= number_format($promo['descuento'], 2) ?></td>
                                    <td><?= htmlspecialchars($promo['stock']) ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($promo['fecha_fin']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">Últimos usuarios registrados</h4>
            <p class="text-muted small">Revisa los usuarios que se añadieron recientemente.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Registrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimosUsuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($usuario['rol'])) ?></td>
                                <td><?= htmlspecialchars($usuario['fecha_registro']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
