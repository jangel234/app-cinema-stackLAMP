<?php
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

function seedSamplePromociones(PDO $pdo) {
    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d', strtotime('+30 days'));
    $promociones = [
        [
            'titulo' => 'Combo Palomitas + Gaseosa',
            'descripcion' => 'Boleto + combo de palomitas y bebida a precio promocional.',
            'imagen_url' => 'img/promotions/combo1.jpg',
            'codigo_descuento' => 'cineDESCstack25promo',
            'tipo' => 'monto',
            'stock' => 25,
            'descuento' => 50.00,
        ],
        [
            'titulo' => 'Descuento 20%',
            'descripcion' => '20% de descuento en el precio total de tu compra.',
            'imagen_url' => 'img/promotions/discount20.jpg',
            'codigo_descuento' => 'cine20stack20promo',
            'tipo' => 'porcentaje',
            'stock' => 40,
            'descuento' => 20.00,
        ],
        [
            'titulo' => 'Pack Amigos',
            'descripcion' => '3 boletos por el precio de 2.',
            'imagen_url' => 'img/promotions/pack_amigos.jpg',
            'codigo_descuento' => 'cine2x1stack2x1promo',
            'tipo' => '2x1',
            'stock' => 15,
            'descuento' => 30.00,
        ],
    ];

    $stmtSelect = $pdo->prepare("SELECT id FROM promociones WHERE codigo_descuento = ? LIMIT 1");
    $stmtInsert = $pdo->prepare("INSERT INTO promociones (titulo, descripcion, imagen_url, codigo_descuento, fecha_inicio, fecha_fin, tipo, stock, descuento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtUpdate = $pdo->prepare("UPDATE promociones SET titulo = ?, descripcion = ?, imagen_url = ?, fecha_inicio = ?, fecha_fin = ?, tipo = ?, stock = ?, descuento = ? WHERE id = ?");
    
    foreach ($promociones as $promo) {
        $stmtSelect->execute([$promo['codigo_descuento']]);
        $existing = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmtUpdate->execute([
                $promo['titulo'],
                $promo['descripcion'],
                $promo['imagen_url'],
                $fechaInicio,
                $fechaFin,
                $promo['tipo'],
                $promo['stock'],
                $promo['descuento'],
                $existing['id'],
            ]);
        } else {
            $stmtInsert->execute([
                $promo['titulo'],
                $promo['descripcion'],
                $promo['imagen_url'],
                $promo['codigo_descuento'],
                $fechaInicio,
                $fechaFin,
                $promo['tipo'],
                $promo['stock'],
                $promo['descuento'],
            ]);
        }
    }
}

ensurePromocionesSchema($pdo);
seedSamplePromociones($pdo);

// Validar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$pelicula_id = $_GET['pelicula_id'] ?? null;
$funcion_id = $_GET['funcion_id'] ?? null;
$asientos = [];
$asientos_vendidos = [];
$promociones = [];

// Si no hay película ni función, regresar al index
if (!$pelicula_id && !$funcion_id) {
    header('Location: index.php');
    exit;
}

$vista_actual = $funcion_id ? 'asientos' : 'horarios';

// ==========================================
// LÓGICA PARA VISTA DE HORARIOS
// ==========================================
if ($vista_actual === 'horarios') {
    // Obtener detalles de la película
    $stmtPeli = $pdo->prepare("SELECT titulo, poster_url, clasificacion, duracion FROM peliculas WHERE id = ?");
    $stmtPeli->execute([$pelicula_id]);
    $pelicula = $stmtPeli->fetch();

    if (!$pelicula) {
        die("Película no encontrada.");
    }

    // Obtener funciones disponibles
    $stmtFunciones = $pdo->prepare("
        SELECT f.id, f.fecha_hora, f.precio, s.nombre AS sala, s.tipo AS tipo_sala
        FROM funciones f
        JOIN salas s ON f.sala_id = s.id
        WHERE f.pelicula_id = ? AND f.fecha_hora > NOW()
        ORDER BY f.fecha_hora ASC
    ");
    $stmtFunciones->execute([$pelicula_id]);
    $funciones = $stmtFunciones->fetchAll();
}

// ==========================================
// LÓGICA PARA VISTA DE ASIENTOS
// ==========================================
if ($vista_actual === 'asientos') {
    // Obtener detalles de la función y película
    $stmtDetalle = $pdo->prepare("
        SELECT f.id, f.fecha_hora, f.precio, f.pelicula_id, s.id AS sala_id, s.nombre AS sala, p.titulo
        FROM funciones f
        JOIN salas s ON f.sala_id = s.id
        JOIN peliculas p ON f.pelicula_id = p.id
        WHERE f.id = ?
    ");
    $stmtDetalle->execute([$funcion_id]);
    $detalle_funcion = $stmtDetalle->fetch();

    if (!$detalle_funcion) {
        die("Función no disponible.");
    }

    // Obtener todos los asientos de esa sala
    $stmtAsientos = $pdo->prepare("SELECT id, fila, numero, tipo FROM asientos WHERE sala_id = ? ORDER BY fila, numero");
    $stmtAsientos->execute([$detalle_funcion['sala_id']]);
    $asientos = $stmtAsientos->fetchAll();

    // Obtener los asientos ya vendidos para esta función
    $stmtVendidos = $pdo->prepare("SELECT asiento_id FROM boletos WHERE funcion_id = ?");
    $stmtVendidos->execute([$funcion_id]);
    $asientos_vendidos = $stmtVendidos->fetchAll(PDO::FETCH_COLUMN); // Devuelve un array simple de IDs

    // Consultar promociones activas disponibles para esta compra
    $stmtPromos = $pdo->prepare(
        "SELECT id, titulo, descripcion, stock, descuento, tipo, codigo_descuento FROM promociones 
         WHERE stock > 0 AND fecha_inicio <= NOW() AND fecha_fin >= NOW() 
         ORDER BY stock ASC, descuento DESC"
    );
    $stmtPromos->execute();
    $promociones = $stmtPromos->fetchAll();
}

include 'includes/header.php';
?>

<style>
    /* Estilos específicos para el mapa de asientos */
    .pantalla-cine {
        background: linear-gradient(to bottom, #ccc, transparent);
        height: 60px;
        border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        margin-bottom: 40px;
        position: relative;
        text-align: center;
        color: #555;
        font-weight: bold;
        padding-top: 10px;
        border-top: 4px solid var(--baltic-blue);
        box-shadow: 0 -10px 20px rgba(12, 98, 145, 0.2);
    }
    
    .mapa-asientos {
        display: grid;
        grid-template-columns: repeat(9, 1fr); /* Asumiendo max 10 asientos por fila */
        gap: 10px;
        justify-content: center;
        max-width: 600px;
        margin: 0 auto;
    }

    .asiento-btn {
        width: 100%;
        aspect-ratio: 1;
        border: none;
        border-radius: 8px 8px 4px 4px; /* Forma de butaca */
        font-size: 0.8rem;
        font-weight: bold;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .asiento-disponible {
        background-color: var(--baltic-blue);
        color: white;
    }
    .asiento-disponible:hover {
        transform: scale(1.1);
        background-color: #094e75;
    }

    .asiento-seleccionado {
        background-color: var(--cherry-rose) !important;
        color: white;
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(166, 52, 70, 0.4);
    }

    .asiento-ocupado {
        background-color: #444;
        color: #888;
        cursor: not-allowed;
    }

    .leyenda-asiento {
        display: inline-block;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 8px;
        vertical-align: middle;
    }

    /* Resumen de compra: sticky y con scroll interno (seguro en pantallas grandes) */
    .compra-resumen {
        position: sticky;
        top: 100px;
        max-height: calc(100vh - 140px);
        overflow-y: auto;
    }

    /* Desactivar sticky en pantallas pequeñas para evitar solapamiento */
    @media (max-width: 991.98px) {
        .compra-resumen {
            position: static;
            max-height: none;
            overflow: visible;
        }
    }
</style>

<div class="container my-5">
    
    <?php if ($vista_actual === 'horarios'): ?>
        <div class="row">
            <div class="col-md-4 mb-4">
                <img src="<?= htmlspecialchars($pelicula['poster_url']) ?>" class="img-fluid rounded shadow" alt="Poster" onerror="this.src='img/posters/placeholder_vertical.jpg'">
            </div>
            <div class="col-md-8">
                <h2 class="fw-bold" style="color: var(--onyx);"><?= htmlspecialchars($pelicula['titulo']) ?></h2>
                <p class="text-muted mb-4">
                    Clasificación: <span class="badge bg-secondary"><?= htmlspecialchars($pelicula['clasificacion']) ?></span> | 
                    Duración: <?= $pelicula['duracion'] ?> min
                </p>

                <h4 class="mb-3">Selecciona una función:</h4>
                <div class="list-group">
                    <?php if (empty($funciones)): ?>
                        <div class="alert alert-warning">No hay funciones programadas próximamente para esta película.</div>
                    <?php else: ?>
                        <?php foreach ($funciones as $f): 
                            $fecha_formateada = date('d/m/Y h:i A', strtotime($f['fecha_hora']));
                        ?>
                            <a href="comprar.php?funcion_id=<?= $f['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark"><?= $fecha_formateada ?></h5>
                                    <small class="text-muted"><?= htmlspecialchars($f['sala']) ?> - Formato: <?= htmlspecialchars($f['tipo_sala']) ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="d-block fw-bold text-success fs-5">$<?= number_format($f['precio'], 2) ?></span>
                                    <button class="btn btn-sm btn-baltic mt-1">Elegir Asientos</button>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <?php elseif ($vista_actual === 'asientos'): ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold" style="color: var(--onyx);">Elige tus lugares</h3>
                    <a href="comprar.php?pelicula_id=<?= $detalle_funcion['pelicula_id'] ?>" class="btn btn-outline-secondary btn-sm">Cambiar función</a>
                </div>

                <div class="card card-sky p-4 mb-4">
                    <div class="pantalla-cine">PANTALLA</div>

                    <div class="mapa-asientos">
                        <?php foreach ($asientos as $asiento): 
                            $es_ocupado = in_array($asiento['id'], $asientos_vendidos);
                            $clase_estado = $es_ocupado ? 'asiento-ocupado' : 'asiento-disponible';
                            $etiqueta = $asiento['fila'] . $asiento['numero'];
                        ?>
                            <button type="button" 
                                    class="asiento-btn <?= $clase_estado ?>" 
                                    data-id="<?= $asiento['id'] ?>" 
                                    data-etiqueta="<?= $etiqueta ?>"
                                    <?= $es_ocupado ? 'disabled' : '' ?>
                                    onclick="toggleAsiento(this)">
                                <?= $etiqueta ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-center mt-4 gap-4">
                        <div><span class="leyenda-asiento asiento-disponible"></span>Disponible</div>
                        <div><span class="leyenda-asiento asiento-seleccionado"></span>Seleccionado</div>
                        <div><span class="leyenda-asiento asiento-ocupado"></span>Ocupado</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 p-4 compra-resumen bg-light">
                    <h4 class="fw-bold mb-3 border-bottom pb-2">Resumen de Compra</h4>
                    
                    <p class="mb-1 text-muted">Película</p>
                    <h5 class="fw-bold"><?= htmlspecialchars($detalle_funcion['titulo']) ?></h5>
                    
                    <p class="mb-1 text-muted mt-3">Función</p>
                    <p class="fw-bold mb-0"><?= date('d/m/Y h:i A', strtotime($detalle_funcion['fecha_hora'])) ?></p>
                    <p class="small text-muted mb-0"><?= htmlspecialchars($detalle_funcion['sala']) ?></p>

                    <hr>

                    <p class="mb-1 text-muted">Asientos Seleccionados (<span id="contador-asientos">0</span>)</p>
                    <p class="fw-bold text-primary" id="lista-asientos">Ninguno</p>

                    <div class="d-flex justify-content-between mt-3">
                        <span class="text-muted">Precio Unitario:</span>
                        <span class="fw-bold">$<span id="precio-unitario"><?= number_format($detalle_funcion['precio'], 2) ?></span></span>
                    </div>

                    <div class="d-flex justify-content-between mt-2 fs-5">
                        <span class="fw-bold">Total estimado:</span>
                        <span class="fw-bold text-success">$<span id="total-precio">0.00</span></span>
                    </div>

                    <form action="procesar_compra.php" method="POST" id="form-compra">
                        <input type="hidden" name="funcion_id" value="<?= $funcion_id ?>">
                        <div id="inputs-asientos"></div>
                        
                        <label for="codigo_promocion" class="form-label fw-bold mt-3">Código de Promoción (Opcional)</label>
                        <input type="text" class="form-control mb-3" name="codigo_promocion" id="codigo_promocion" placeholder="ingresa tu código aquí...">

                        <button type="submit" class="btn btn-cherry w-100 py-2 mt-2 shadow" id="btn-comprar" disabled>Confirmar Compra</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    let asientosSeleccionados = [];
    const precioUnitario = <?= $detalle_funcion['precio'] ?? 0 ?>;

    function toggleAsiento(btn) {
        const id = btn.getAttribute('data-id');
        const etiqueta = btn.getAttribute('data-etiqueta');

        if (asientosSeleccionados.some(a => a.id === id)) {
            asientosSeleccionados = asientosSeleccionados.filter(a => a.id !== id);
            btn.classList.remove('asiento-seleccionado');
        } else {
            asientosSeleccionados.push({ id, etiqueta });
            btn.classList.add('asiento-seleccionado');
        }
        actualizarResumen();
    }

    function actualizarResumen() {
        const contador = document.getElementById('contador-asientos');
        const lista = document.getElementById('lista-asientos');
        const total = document.getElementById('total-precio');
        const btnComprar = document.getElementById('btn-comprar');
        const inputsContainer = document.getElementById('inputs-asientos');

        contador.textContent = asientosSeleccionados.length;
        
        if (asientosSeleccionados.length > 0) {
            lista.textContent = asientosSeleccionados.map(a => a.etiqueta).join(', ');
            total.textContent = (asientosSeleccionados.length * precioUnitario).toFixed(2);
            btnComprar.disabled = false;
        } else {
            lista.textContent = 'Ninguno';
            total.textContent = '0.00';
            btnComprar.disabled = true;
        }

        // Generar inputs ocultos para el formulario
        inputsContainer.innerHTML = '';
        asientosSeleccionados.forEach(a => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'asientos[]';
            input.value = a.id;
            inputsContainer.appendChild(input);
        });
    }
</script>
<?php include 'includes/footer.php'; ?>