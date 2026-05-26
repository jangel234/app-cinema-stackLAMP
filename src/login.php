<?php
// 1. TODA LA LÓGICA DE VALIDACIÓN VA PRIMERO
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si el usuario ya tiene sesión, lo mandamos directo al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, nombre, password_hash, rol FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        // Redirigimos a la página correcta según el rol
        if (in_array($usuario['rol'], ['superadmin', 'admin'], true)) {
            header('Location: admin.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $error = "El correo o la contraseña son incorrectos.";
    }
}

// 2. AHORA SÍ, INCLUIMOS EL DISEÑO FRONTEND
include 'includes/header.php';
?>

<div class="container my-5 py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card card-auth p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="color: var(--onyx);">Iniciar Sesión</h3>
                    <p class="text-muted small">Accede para pedir tus boletos</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small" role="alert"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@cine.com" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-baltic w-100 py-2">Ingresar</button>
                </form>

                <div class="text-center mt-4">
                    <span class="small text-muted">¿Eres nuevo?</span>
                    <a href="registro.php" class="small fw-bold ms-1" style="color: var(--cherry-rose); text-decoration: none;">Crea una cuenta aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>