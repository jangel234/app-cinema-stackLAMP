<?php include 'includes/header.php'; ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-onyx shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#" style="color: var(--cherry-rose); font-weight: bold;">CINE<span style="color: var(--azure-mist);">TICKET</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link text-light">Hola, Usuario</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-cherry" href="logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: var(--onyx); font-weight: bold;">Cartelera Disponible</h2>
        <input type="text" class="form-control w-25" placeholder="Buscar película...">
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-sky h-100">
                <img src="img/escape.jpg" class="card-img-top" alt="El Gran Escape" style="height: 350px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title" style="color: var(--onyx); font-weight: 600;">El Gran Escape</h5>
                    <p class="card-text mb-4">Disponibles: <span class="badge bg-onyx">60 asientos</span></p>
                    
                    <a href="comprar.php?pelicula_id=1" class="btn btn-baltic mt-auto w-100">Pedir Boletos</a>
                </div>
            </div>
        </div>
        </div>
</div>

<?php include 'includes/footer.php'; ?>