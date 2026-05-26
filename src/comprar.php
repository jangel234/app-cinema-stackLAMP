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
    $count = $pdo->query("SELECT COUNT(*) FROM promociones")->fetchColumn();
    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d', strtotime('+30 days'));
    $promociones = [
        [
            'titulo' => 'Combo Palomitas + Gaseosa',
            'descripcion' => 'Boleto + combo de palomitas y bebida a precio promocional.',
            'imagen_url' => 'img/promotions/combo1.jpg',
            'codigo_descuento' => 'COMBO2026',
            'tipo' => 'monto',
            'stock' => 25,
            'descuento' => 50.00,
        ],
        [
            'titulo' => 'Descuento 20%',
            'descripcion' => '20% de descuento en el precio total de tu compra.',
            'imagen_url' => 'img/promotions/discount20.jpg',
            'codigo_descuento' => 'DESCUENTO20',
            'tipo' => 'porcentaje',
            'stock' => 40,
            'descuento' => 20.00,
        ],
        [
            'titulo' => 'Pack Amigos',
            'descripcion' => '3 boletos por el precio de 2.',
            'imagen_url' => 'img/promotions/pack_amigos.jpg',
            'codigo_descuento' => 'AMIGOS3x2',
            'tipo' => '2x1',
            'stock' => 15,
            'descuento' => 30.00,
        ],
    ];

    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO promociones (titulo, descripcion, imagen_url, codigo_descuento, fecha_inicio, fecha_fin, tipo, stock, descuento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($promociones as $promo) {
            $stmt->execute([
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
        return;
    }

    $activeCount = $pdo->query("SELECT COUNT(*) FROM promociones WHERE stock > 0 AND fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE()")->fetchColumn();
    if ($activeCount == 0) {
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
        "SELECT id, titulo, descripcion, stock, descuento, tipo FROM promociones 
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

                    <div class="d-flex justify-content-center mt-5 text-muted small gap-4">
                        <div><span class="leyenda-asiento bg-secondary"></span> Ocupado</div>
                        <div><span class="leyenda-asiento asiento-disponible"></span> Disponible</div>
                        <div><span class="leyenda-asiento" style="background-color: var(--cherry-rose);"></span> Seleccionado</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 compra-resumen">
                    <div class="card-body">
                        <h5 class="card-title fw-bold border-bottom pb-3 mb-3">Resumen de Compra</h5>
                        <p class="mb-1 text-muted small">Película:</p>
                        <p class="fw-bold"><?= htmlspecialchars($detalle_funcion['titulo']) ?></p>
                        
                        <p class="mb-1 text-muted small">Función:</p>
                        <p class="fw-bold"><?= date('d/m/Y h:i A', strtotime($detalle_funcion['fecha_hora'])) ?></p>

                        <p class="mb-1 text-muted small">Sala:</p>
                        <p class="fw-bold"><?= htmlspecialchars($detalle_funcion['sala']) ?></p>

                        <div class="border-top pt-3 mt-3">
                            <p class="d-flex justify-content-between mb-1">
                                <span>Boletos (<span id="contador-boletos">0</span>):</span>
                                <span class="fw-bold text-success">$<span id="total-precio">0.00</span></span>
                            </p>
                            <p class="text-muted small text-truncate" id="lista-asientos-texto">Ningún asiento seleccionado</p>
                        </div>

                        <form id="form-compra" action="procesar_compra.php" method="POST">
                            <input type="hidden" name="funcion_id" value="<?= $funcion_id ?>">
                            <?php if (!empty($promociones)): ?>
                                <div class="mb-3">
                                    <label for="promocion" class="form-label small fw-bold">Promoción disponible</label>
                                    <select id="promocion" name="promocion_id" class="form-select" onchange="actualizarResumen()">
                                        <option value="" data-descuento="0" data-tipo="monto">Ninguna</option>
                                        <?php foreach ($promociones as $promo): ?>
                                            <?php
                                                $promoLabel = htmlspecialchars($promo['titulo']);
                                                if ($promo['tipo'] === 'porcentaje') {
                                                    $promoLabel .= ' - ' . number_format($promo['descuento'], 0) . '% de descuento';
                                                } elseif ($promo['tipo'] === '2x1') {
                                                    $promoLabel .= ' - 2x1 en boletos';
                                                } else {
                                                    $promoLabel .= ' - $' . number_format($promo['descuento'], 2) . ' de descuento';
                                                }
                                            ?>
                                            <option value="<?= $promo['id'] ?>" data-descuento="<?= $promo['descuento'] ?>" data-tipo="<?= htmlspecialchars($promo['tipo']) ?>">
                                                <?= $promoLabel ?> (<?= htmlspecialchars($promo['stock']) ?> disponibles)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Selecciona una promoción para aplicarla en tu compra. Si eliges 2x1, el precio se ajustará automáticamente.</div>
                                </div>
                            <?php endif; ?>
                            <div class="border-top pt-3 mt-3">
                                <p class="d-flex justify-content-between mb-1">
                                    <span>Boletos (<span id="contador-boletos">0</span>):</span>
                                    <span class="fw-bold text-success">$<span id="total-precio">0.00</span></span>
                                </p>
                                <p class="text-muted small text-truncate" id="lista-asientos-texto">Ningún asiento seleccionado</p>
                                <p class="text-muted small mb-0" id="descuento-texto">Descuento: $<span id="descuento-precio">0.00</span></p>
                            </div>
                            <div id="inputs-asientos"></div>
                            <button type="submit" id="btn-pagar" class="btn btn-cherry w-100 mt-3 py-2" disabled>
                                Confirmar y Pagar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const precioUnitario = <?= $detalle_funcion['precio'] ?>;
            let asientosSeleccionados = [];
            const selectPromocion = document.getElementById('promocion');

            function toggleAsiento(btn) {
                const idAsiento = btn.getAttribute('data-id');
                const etiqueta = btn.getAttribute('data-etiqueta');

                if (btn.classList.contains('asiento-seleccionado')) {
                    // Deseleccionar
                    btn.classList.remove('asiento-seleccionado');
                    asientosSeleccionados = asientosSeleccionados.filter(item => item.id !== idAsiento);
                } else {
                    // Seleccionar (Límite opcional: 10 boletos por transacción)
                    if (asientosSeleccionados.length >= 10) {
                        alert("Solo puedes comprar hasta 10 boletos por transacción.");
                        return;
                    }
                    btn.classList.add('asiento-seleccionado');
                    asientosSeleccionados.push({ id: idAsiento, etiqueta: etiqueta });
                }
                
                actualizarResumen();
            }

            function actualizarResumen() {
                const contador = document.getElementById('contador-boletos');
                const total = document.getElementById('total-precio');
                const listaTexto = document.getElementById('lista-asientos-texto');
                const containerInputs = document.getElementById('inputs-asientos');
                const btnPagar = document.getElementById('btn-pagar');

                const cantidad = asientosSeleccionados.length;
                const subtotal = cantidad * precioUnitario;
                let descuentoSeleccionado = 0;
                let tipoPromocion = 'monto';

                if (selectPromocion) {
                    tipoPromocion = selectPromocion.selectedOptions[0].dataset.tipo || 'monto';
                    const descuentoRaw = parseFloat(selectPromocion.selectedOptions[0].dataset.descuento || '0');
                    if (tipoPromocion === 'porcentaje') {
                        descuentoSeleccionado = subtotal * (descuentoRaw / 100);
                    } else if (tipoPromocion === '2x1') {
                        const pares = Math.ceil(cantidad / 2);
                        descuentoSeleccionado = subtotal - (pares * precioUnitario);
                    } else {
                        descuentoSeleccionado = descuentoRaw;
                    }
                }

                const totalFinal = Math.max(0, subtotal - descuentoSeleccionado);
                
                // Actualizar textos
                contador.innerText = cantidad;
                total.innerText = totalFinal.toFixed(2);
                document.getElementById('descuento-precio').innerText = descuentoSeleccionado.toFixed(2);
                
                if (cantidad > 0) {
                    const nombresAsientos = asientosSeleccionados.map(a => a.etiqueta).join(', ');
                    listaTexto.innerText = "Lugares: " + nombresAsientos;
                    btnPagar.disabled = false;
                } else {
                    listaTexto.innerText = "Ningún asiento seleccionado";
                    btnPagar.disabled = true;
                }

                // Generar los inputs ocultos (name="asientos[]") para el POST
                containerInputs.innerHTML = '';
                asientosSeleccionados.forEach(asiento => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'asientos[]';
                    input.value = asiento.id;
                    containerInputs.appendChild(input);
                });
            }
        </script>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>