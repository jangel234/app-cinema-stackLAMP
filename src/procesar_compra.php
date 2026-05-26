<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function ensurePromocionesSchema(PDO $pdo) {
    $fields = [
        'stock' => "INT NOT NULL DEFAULT 0 AFTER codigo_descuento",
        'descuento' => "DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER codigo_descuento",
        'tipo' => "ENUM('monto','porcentaje','2x1') NOT NULL DEFAULT 'monto' AFTER descuento",
    ];

    foreach ($fields as $field => $definition) {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM promociones LIKE ?");
        $stmt->execute([$field]);
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE promociones ADD COLUMN {$field} {$definition}");
        }
    }
}

function ensureComprasSchema(PDO $pdo) {
    $stmt = $pdo->prepare("SHOW COLUMNS FROM compras LIKE 'promocion_id'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE compras ADD COLUMN promocion_id INT NULL AFTER total");
    }
}

ensurePromocionesSchema($pdo);
ensureComprasSchema($pdo);

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$funcion_id = $_POST['funcion_id'] ?? 0;
$codigo_promocion = trim($_POST['codigo_promocion'] ?? '');
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
$promoTitulo = '';
$compra_id = 0;

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
    $descuento = 0.00;
    $promoTitulo = '';
    $promocion_id = null;
    if (!empty($codigo_promocion)) {
        $stmtPromo = $pdo->prepare("SELECT id, titulo, descuento, tipo FROM promociones WHERE codigo_descuento = ? AND stock > 0 AND fecha_inicio <= NOW() AND fecha_fin >= NOW() LIMIT 1 FOR UPDATE");
        $stmtPromo->execute([$codigo_promocion]);
        $promoData = $stmtPromo->fetch();
        if (!$promoData) {
            throw new Exception("El código de promoción no es válido o ya no está disponible.");
        }
        $promoTitulo = $promoData['titulo'];
        $promocion_id = $promoData['id'];

        $subtotal = $cantidad * $precio_unitario;
        if ($promoData['tipo'] === 'porcentaje') {
            $descuento = $subtotal * (floatval($promoData['descuento']) / 100);
        } elseif ($promoData['tipo'] === '2x1') {
            $pares = ceil($cantidad / 2);
            $descuento = $subtotal - ($pares * $precio_unitario);
        } else {
            $descuento = floatval($promoData['descuento']);
        }
    }

    $total = max(0, $cantidad * $precio_unitario - $descuento);

    // 2. Registrar la compra principal
    $stmt = $pdo->prepare("INSERT INTO compras (usuario_id, total, promocion_id) VALUES (?, ?, ?)");
    $stmt->execute([$usuario_id, $total, $promocion_id]);
    $compra_id = $pdo->lastInsertId();

    // 3. Intentar reservar cada asiento y obtener sus etiquetas para el ticket
    $stmtBoletos = $pdo->prepare("INSERT INTO boletos (compra_id, funcion_id, asiento_id, precio_pagado) VALUES (?, ?, ?, ?)");
    $stmtDetalleAsiento = $pdo->prepare("SELECT fila, numero FROM asientos WHERE id = ?");

    $precioPorBoleto = round($total / $cantidad, 2);
    foreach ($asientos_seleccionados as $asiento_id) {
        // Reservar en BD (falla si el asiento ya fue tomado por la llave única)
        $stmtBoletos->execute([$compra_id, $funcion_id, (int)$asiento_id, $precioPorBoleto]);
        
        // Obtener el nombre del asiento (Ej: A1, B4) para el ticket
        $stmtDetalleAsiento->execute([(int)$asiento_id]);
        $asiento_data = $stmtDetalleAsiento->fetch();
        $nombres_asientos[] = $asiento_data['fila'] . $asiento_data['numero'];
    }

    if ($promocion_id) {
        $stmtUpdatePromo = $pdo->prepare("UPDATE promociones SET stock = stock - 1 WHERE id = ? AND stock > 0");
        $stmtUpdatePromo->execute([$promocion_id]);
        if ($stmtUpdatePromo->rowCount() === 0) {
            throw new Exception("La promoción ya no está disponible.");
        }
    }

    // 4. Confirmar la transacción
    $pdo->commit();

    // 4.5. EJECUCIÓN DE SCRIPT DE RESPALDO AUTOMÁTICO
    $script_respaldo = realpath(__DIR__ . '/../scripts/backup_cine.sh');
    if ($script_respaldo) {
        // REPARACIÓN CRÍTICA PARA WINDOWS: Elimina los saltos de línea CRLF (\r) del script
        shell_exec("sed -i 's/\\r$//' " . escapeshellarg($script_respaldo));
        shell_exec("bash " . escapeshellarg($script_respaldo) . " > /dev/null 2>&1 &");
    }

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
        
        $promocionInfoHTML = $promoTitulo ? "<p><strong>Promoción aplicada:</strong> " . htmlspecialchars($promoTitulo) . "</p>" : '';
        $promocionInfoAlt = $promoTitulo ? " | Promoción: {$promoTitulo}" : '';

        $cuerpoHTML = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>
                <h2 style='color: #0C6291; text-align: center;'>¡Gracias por tu compra, {$usuario_data['nombre']}!</h2>
                <hr>
                <p><strong>Película:</strong> {$funcion['titulo']}</p>
                <p><strong>Fecha y Hora:</strong> " . date('d/m/Y h:i A', strtotime($funcion['fecha_hora'])) . "</p>
                <p><strong>Sala:</strong> {$funcion['sala']}</p>
                <p><strong>Asientos:</strong> " . implode(", ", $nombres_asientos) . "</p>
                $promocionInfoHTML
                <p><strong>Total:</strong> $" . number_format($total, 2) . "</p>
                <hr>
                <p style='text-align: center; color: #777;'>Muestra este correo en taquilla o confitería para ingresar.</p>
            </div>
        ";
        
        $mail->Body    = $cuerpoHTML;
        $mail->AltBody = "Película: {$funcion['titulo']} | Sala: {$funcion['sala']} | Asientos: " . implode(", ", $nombres_asientos) . $promocionInfoAlt;

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
                    <a href="comprar.php?funcion_id=<?= htmlspecialchars($funcion_id) ?>" class="btn btn-baltic w-100 py-2">Volver a Selección de Asientos</a>
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

                        <?php if (!empty($promoTitulo)): ?>
                            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded shadow-sm mb-2 border border-light">
                                <span class="text-muted fw-bold">Promoción aplicada:</span>
                                <span class="fw-bold text-primary"><?= htmlspecialchars($promoTitulo) ?></span>
                            </div>
                        <?php endif; ?>
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