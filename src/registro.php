<?php
// 1. TODA LA LÓGICA DEBE IR ANTES DE INCLUIR EL HEADER
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errores = [];
$registro_exitoso = false;
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($nombre) < 2) $errores[] = "El nombre es muy corto.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "Ingresa un correo electrónico válido.";
    if (strlen($password) < 6) $errores[] = "La contraseña debe contener al menos 6 caracteres.";

    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$nombre, $email, $hash]);
            
            // OBTENER EL ID DEL NUEVO USUARIO
            $nuevo_id = $pdo->lastInsertId();

            // INICIAR SESIÓN AUTOMÁTICAMENTE
            $_SESSION['usuario_id'] = $nuevo_id;
            $_SESSION['usuario_nombre'] = $nombre;

            // Marcar bandera de éxito para disparar el modal
            $registro_exitoso = true;
            $mensaje_exito = "¡Tu cuenta ha sido creada con éxito, $nombre!";

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errores[] = "El correo $email ya está registrado. Intenta iniciar sesión.";
            } else {
                $errores[] = "Ocurrió un error inesperado en el servidor.";
            }
        }
    }
}

// 2. AHORA SÍ, INCLUIMOS EL HTML
include 'includes/header.php';
?>

<div class="container my-5 py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card card-auth p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="color: var(--onyx);">Registrarse</h3>
                    <p class="text-muted small">Únete para obtener beneficios y promociones</p>
                </div>

                <form method="POST" action="registro.php">
                    <div class="mb-3">
                        <label for="nombre" class="form-label small fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre ?? '') ?>" placeholder="Juan Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" placeholder="juan@correo.com" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <button type="submit" class="btn btn-cherry w-100 py-2">Crear Cuenta</button>
                </form>

                <div class="text-center mt-4">
                    <span class="small text-muted">¿Ya tienes una cuenta?</span>
                    <a href="login.php" class="small fw-bold ms-1" style="color: var(--baltic-blue); text-decoration: none;">Inicia sesión aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($registro_exitoso || !empty($errores)): ?>
<div class="modal fade" id="resultadoModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-body p-4 text-center">
        
        <?php if ($registro_exitoso): ?>
            <h2 class="display-1 text-success mb-3">✅</h2>
            <h4 class="fw-bold" style="color: var(--onyx);">¡Bienvenido!</h4>
            <p class="text-muted"><?= htmlspecialchars($mensaje_exito) ?></p>
            <p class="small text-muted mb-4">Tu sesión ya está activa. Serás redirigido a la cartelera para pedir tus boletos.</p>
            <a href="index.php" class="btn btn-baltic w-100 py-2">Ir a la Cartelera</a>
        
        <?php else: ?>
            <h2 class="display-1 text-warning mb-3">⚠️</h2>
            <h4 class="fw-bold" style="color: var(--onyx);">Atención</h4>
            <div class="text-start mt-3 mb-4 text-muted">
                <ul class="mb-0">
                    <?php foreach($errores as $err): ?> <li><?= htmlspecialchars($err) ?></li> <?php endforeach; ?>
                </ul>
            </div>
            <button type="button" class="btn btn-cherry w-100 py-2" data-bs-dismiss="modal">Entendido</button>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('resultadoModal'));
        myModal.show();
    });
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>