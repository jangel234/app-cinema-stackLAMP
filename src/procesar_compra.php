<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$funcion_id = $_POST['funcion_id'] ?? 0;
$asientos_seleccionados = $_POST['asientos'] ?? [];

// Si alguien entra directamente a esta URL sin enviar datos, lo regresamos al inicio
if (empty($funcion_id) || empty($asientos_seleccionados) || !is_array($asientos_seleccionados)) {
    header('Location: index.php');
    exit;
}

$cantidad = count($asientos_seleccionados);
$usuario_id = $_SESSION['usuario_id'];
$nombres_asientos = [];
$error_compra = '';

$pdo->beginTransaction();
try {
    // 1. Obtener precio y detalles de la función
    $stmt = $pdo->prepare("
        SELECT f.precio, f.fecha_hora, p.titulo, s.nombre AS sala 
        FROM funciones f
        JOIN peliculas p ON f.pelicula_id = p.id
        JOIN salas s ON f.sala_id = s.id
        WHERE f.id = ?
    ");
    $stmt->execute([$funcion_id]);
    $funcion = $stmt->fetch();

    if (!$funcion) throw new Exception("La función seleccionada ya no existe.");
    
    $precio_unitario = $funcion['precio'];
    $total = $cantidad * $precio_unitario;

    // 2. Registrar la compra principal
    $stmt = $pdo->prepare("INSERT INTO compras (usuario_id, total) VALUES (?, ?)");
    $stmt->execute([$usuario_id, $total]);
    $compra_id = $pdo->lastInsertId();

    // 3. Intentar reservar cada asiento y obtener sus etiquetas para el ticket
    $stmtBoletos = $pdo->prepare("INSERT INTO boletos (compra_id, funcion_id, asiento_id, precio_pagado) VALUES (?, ?, ?, ?)");
    $stmtDetalleAsiento = $pdo->prepare("SELECT fila, numero FROM asientos WHERE id = ?");

    foreach ($asientos_seleccionados as $asiento_id) {
        // Reservar en BD (falla si el asiento ya fue tomado por la llave única)
        $stmtBoletos->execute([$compra_id, $funcion_id, (int)$asiento_id, $precio_unitario]);
        
        // Obtener el nombre del asiento (Ej: A1, B4) para el ticket
        $stmtDetalleAsiento->execute([(int)$asiento_id]);
        $asiento_data = $stmtDetalleAsiento->fetch();
        $nombres_asientos[] = $asiento_data['fila'] . $asiento_data['numero'];
    }

    // 4. Confirmar la transacción
    $pdo->commit();

   // 5. Envío REAL de correo electrónico con PHPMailer
    $stmtEmail = $pdo->prepare("SELECT email, nombre FROM usuarios WHERE id = ?");
    $stmtEmail->execute([$usuario_id]);
    $usuario_data = $stmtEmail->fetch();
    $destinatario = $usuario_data['email'];

    // Cargar los archivos de PHPMailer
    require_once 'libs/PHPMailer/src/Exception.php';
    require_once 'libs/PHPMailer/src/PHPMailer.php';
    require_once 'libs/PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor (SMTP)
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'cinestack0@gmail.com';   // <-- PON TU GMAIL AQUÍ
        $mail->Password   = 'lbaoekbxaxitmwvj';    // <-- PON TU CONTRASEÑA DE APLICACIÓN AQUÍ
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            
        $mail->Port       = 587;                                    

        // Remitente y Destinatarios
        $mail->setFrom('TU_CORREO@gmail.com', 'CineStack Taquilla');
        $mail->addAddress($destinatario, $usuario_data['nombre']);     

        // Contenido (Armamos un pequeño HTML para que se vea profesional)
        $mail->isHTML(true);                                  
        $mail->Subject = 'Tu Ticket Digital - CineStack #' . $compra_id;
        
        $cuerpoHTML = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>
                <h2 style='color: #0C6291; text-align: center;'>¡Gracias por tu compra, {$usuario_data['nombre']}!</h2>
                <hr>
                <p><strong>Película:</strong> {$funcion['titulo']}</p>
                <p><strong>Fecha y Hora:</strong> " . date('d/m/Y h:i A', strtotime($funcion['fecha_hora'])) . "</p>
                <p><strong>Sala:</strong> {$funcion['sala']}</p>
                <p><strong>Asientos:</strong> " . implode(", ", $nombres_asientos) . "</p>
                <p><strong>Total:</strong> $" . number_format($total, 2) . "</p>
                <hr>
                <p style='text-align: center; color: #777;'>Muestra este correo en taquilla o confitería para ingresar.</p>
            </div>
        ";
        
        $mail->Body    = $cuerpoHTML;
        $mail->AltBody = "Película: {$funcion['titulo']} | Sala: {$funcion['sala']} | Asientos: " . implode(", ", $nombres_asientos);

        $mail->send();
    } catch (Exception $e) {
        // Si el correo falla (ej. por falta de internet), evitamos que la página colapse.
        // El usuario aún verá su ticket en la pantalla web, pero registramos el error silenciosamente.
        error_log("El correo no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}");
    }

} catch (Exception $e) {
    $pdo->rollBack();
    if ($e->getCode() == 23000) {
        $error_compra = "Uno o más de los asientos que seleccionaste ya fueron comprados por otro usuario. Por favor, intenta de nuevo.";
    } else {
        $error_compra = "Ocurrió un error al procesar tu pago. Detalle: " . $e->getMessage();
    }
}

// ==========================================
// RENDERIZADO DE LA INTERFAZ
// ==========================================
include 'includes/header.php';
?>

<div class="container my-5 py-4 text-center">
    
    <?php if ($error_compra): ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-lg p-5">
                    <h1 class="display-1 text-warning mb-3">⚠️</h1>
                    <h3 class="fw-bold" style="color: var(--onyx);">Transacción Declinada</h3>
                    <p class="text-muted mt-3 mb-4"><?= htmlspecialchars($error_compra) ?></p>
                    <a href="index.php" class="btn btn-baltic w-100 py-2">Volver a la Cartelera</a>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <h2 class="fw-bold text-success mb-2">¡Pago Exitoso! ✅</h2>
                <p class="text-muted mb-4">Se ha enviado un comprobante a <strong><?= htmlspecialchars($usuario_data['email']) ?></strong>.</p>
                
                <div class="card card-sky border-0 shadow-lg text-start mb-4 overflow-hidden">
                    <div class="bg-onyx text-center py-3">
                        <h4 class="mb-0 text-uppercase tracking-wide" style="color: var(--cherry-rose); letter-spacing: 2px;">Ticket Digital</h4>
                    </div>
                    
                    <div class="card-body p-4 position-relative">
                        <div class="text-center mb-4">
                            <img src="https://barcode.tec-it.com/barcode.ashx?data=CINE-<?= $compra_id ?>&code=Code128&translate-esc=on" alt="Código de barras" class="img-fluid" style="mix-blend-mode: multiply; opacity: 0.8; height: 60px;">
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 text-center border-bottom pb-3 mb-3">
                                <h3 class="fw-bold" style="color: var(--onyx);"><?= htmlspecialchars($funcion['titulo']) ?></h3>
                                <p class="mb-0 text-muted"><?= date('d/m/Y h:i A', strtotime($funcion['fecha_hora'])) ?></p>
                            </div>
                            
                            <div class="col-6 mb-3">
                                <small class="text-muted d-block">Folio de Compra</small>
                                <span class="fw-bold">#<?= str_pad($compra_id, 6, "0", STR_PAD_LEFT) ?></span>
                            </div>
                            <div class="col-6 mb-3 text-end">
                                <small class="text-muted d-block">Sala</small>
                                <span class="fw-bold"><?= htmlspecialchars($funcion['sala']) ?></span>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <small class="text-muted d-block">Asientos (<?= $cantidad ?>)</small>
                                <span class="fw-bold" style="color: var(--baltic-blue); font-size: 1.1rem;">
                                    <?= implode(', ', $nombres_asientos) ?>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mt-2 border border-light">
                            <span class="text-muted fw-bold">Total Pagado:</span>
                            <span class="fw-bold fs-4 text-success">$<?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                </div>

                <a href="index.php" class="btn btn-baltic w-100 py-3 fw-bold shadow">Aceptar y regresar al inicio</a>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>