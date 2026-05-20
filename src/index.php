<?php
require_once 'db.php';
include 'includes/header.php';

// 1. Obtener películas para el Carrusel destacado (Limitado a 3)
$stmtHero = $pdo->query("SELECT * FROM peliculas WHERE estado = 'cartelera' LIMIT 3");
$moviesHero = $stmtHero->fetchAll();

// 2. Obtener todas las películas para la grilla general
$stmtGrid = $pdo->query("SELECT * FROM peliculas ORDER BY id DESC");
$moviesGrid = $stmtGrid->fetchAll();
?>

<div id="heroCine" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="4000">
    <div class="container">
        <div class="carousel-inner">
            <?php foreach ($moviesHero as $index => $movie): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <div class="row align-items-center g-5">
                        <div class="col-md-5 text-center text-md-start">
                            <span
                                class="badge bg-danger mb-3 px-3 py-2 text-uppercase"><?= htmlspecialchars($movie['genero']) ?></span>
                            <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($movie['titulo']) ?></h1>
                            <p class="lead text-muted-light mb-4 text-secondary">
                                <?= htmlspecialchars(substr($movie['sinopsis'], 0, 180)) ?>...
                            </p>
                            <div class="d-flex gap-3 justify-content-center justify-content-md-start">
                                <span class="btn btn-outline-light disabled">Clasif:
                                    <?= htmlspecialchars($movie['clasificacion']) ?></span>
                                <span class="btn btn-outline-light disabled">⏱ <?= $movie['duracion'] ?> min</span>
                            </div>
                            <a href="comprar.php?pelicula_id=<?= $movie['id'] ?>"
                                class="btn btn-baltic btn-lg mt-4 px-5 shadow">Obtener tickets</a>
                        </div>
                        <div class="col-md-7 text-center">
                            <img src="<?= htmlspecialchars($movie['banner_url']) ?>" class="img-fluid hero-banner-img"
                                alt="<?= htmlspecialchars($movie['titulo']) ?>"
                                onerror="this.onerror=null; this.src='img/banners/placeholder_horizontal.jpg';">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCine" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCine" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<div class="container mt-5">
    <ul class="nav nav-tabs nav-tabs-cine mb-4 border-bottom border-2" id="cineTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#en-cartelera" type="button">En
                Cartelera</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#proximamente"
                type="button">Próximamente</button>
        </li>
    </ul>

    <div class="tab-content" id="cineTabsContent">
        <div class="tab-pane fade show active" id="en-cartelera">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                <?php
                $count = 0;
                foreach ($moviesGrid as $movie):
                    if ($movie['estado'] == 'cartelera'):
                        $count++;
                        ?>
                        <div class="col">
                            <div class="card h-100 card-pelicula">
                                <div class="poster-wrap">
                                    <img src="<?= htmlspecialchars($movie['poster_url']) ?>"
                                        alt="<?= htmlspecialchars($movie['titulo']) ?>" class="img-fluid"
                                        onerror="this.onerror=null; this.src='img/posters/placeholder_vertical.jpg';">
                                </div>
                                <div class="card-body d-flex flex-column p-3">
                                    <h6 class="card-title fw-bold text-truncate mb-1"
                                        title="<?= htmlspecialchars($movie['titulo']) ?>">
                                        <?= htmlspecialchars($movie['titulo']) ?>
                                    </h6>
                                    <p class="text-muted small mb-3"><?= htmlspecialchars($movie['genero']) ?> |
                                        <?= htmlspecialchars($movie['clasificacion']) ?>
                                    </p>
                                    <a href="comprar.php?pelicula_id=<?= $movie['id'] ?>"
                                        class="btn btn-baltic btn-sm w-100 mt-auto">Ver Funciones</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    endif;
                endforeach;
                if ($count === 0)
                    echo "<p class='text-muted p-3'>No hay películas en cartelera actualmente.</p>";
                ?>
            </div>
        </div>

        <div class="tab-pane fade" id="proximamente">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                <?php
                $countProx = 0;
                foreach ($moviesGrid as $movie):
                    if ($movie['estado'] == 'proximamente'):
                        $countProx++;
                        ?>
                        <div class="col">
                            <div class="card h-100 card-pelicula" style="opacity: 0.85;">
                                <div class="poster-wrap">
                                    <img src="<?= htmlspecialchars($movie['poster_url']) ?>"
                                        alt="<?= htmlspecialchars($movie['titulo']) ?>">
                                </div>
                                <div class="card-body d-flex flex-column p-3">
                                    <h6 class="card-title fw-bold text-truncate mb-1"><?= htmlspecialchars($movie['titulo']) ?>
                                    </h6>
                                    <p class="text-muted small mb-2"><?= htmlspecialchars($movie['genero']) ?></p>
                                    <button class="btn btn-secondary btn-sm w-100 mt-auto" disabled>Próximamente</button>
                                </div>
                            </div>
                        </div>
                        <?php
                    endif;
                endforeach;
                if ($countProx === 0)
                    echo "<p class='text-muted p-3'>No hay estrenos programados por ahora.</p>";
                ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>