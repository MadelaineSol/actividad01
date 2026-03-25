<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($view) || $view === '') {
    $view = 'index';
}

if ($view !== 'login' && (!isset($_SESSION['user_data']) || empty($_SESSION['user_data']))) {
    header("Location: ?view=login");
    exit;
}

$view_file = __DIR__ . '/../views/' . $view . '-view.php';
$view_name = $view;

if (!isset($view) || $view === '') {
    $view = 'index';
}

$view_file = __DIR__ . '/../views/' . $view . '-view.php';
$view_name = $view;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GesCon</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
  --gescon-green:#16c60c;
  --gescon-green-dark:#10a808;
  --gescon-green-deep:#0b8707;
  --gescon-green-soft:#eaffea;
  --gescon-yellow:#f4c51c;
  --gescon-yellow-dark:#d6a90c;
  --gescon-bg:#f6fff5;
  --gescon-border:#d5edd2;
  --gescon-text:#1f3f18;
  --gescon-muted:#688162;
  --gescon-shadow:0 16px 35px rgba(16,168,8,.10);
  --sidebar-width:260px;
}

*{
  box-sizing:border-box;
}

html,body{
  margin:0;
  padding:0;
  overflow-x:hidden;
}

body{
  font-family:'Nunito',sans-serif;
  background:
    radial-gradient(circle at top left, rgba(244,197,28,.16), transparent 22%),
    radial-gradient(circle at bottom right, rgba(22,198,12,.10), transparent 24%),
    var(--gescon-bg);
  color:var(--gescon-text);
}

/* NAVBAR MOBILE */
.mobile-navbar{
  background:linear-gradient(135deg,var(--gescon-green) 0%,var(--gescon-green-dark) 100%);
  box-shadow:0 8px 18px rgba(16,168,8,.18);
}

.mobile-navbar .navbar-brand{
  display:flex;
  align-items:center;
  gap:.6rem;
  color:#fff;
  font-weight:900;
  font-size:1.35rem;
  text-decoration:none;
}

.brand-house{
  width:34px;
  height:34px;
  border-radius:12px;
  background:var(--gescon-yellow);
  color:#4f4300;
  display:flex;
  align-items:center;
  justify-content:center;
  flex-shrink:0;
  box-shadow:0 8px 16px rgba(244,197,28,.25);
}

.mobile-navbar .navbar-toggler{
  border:none;
  box-shadow:none !important;
}

.mobile-navbar .navbar-toggler i{
  color:#fff;
  font-size:1.5rem;
}

/* SIDEBARS */
.sidebar{
  background:linear-gradient(180deg,var(--gescon-green) 0%,var(--gescon-green-dark) 55%,var(--gescon-green-deep) 100%);
  color:#fff;
  padding:1.2rem 1rem;
  position:relative;
  overflow:hidden;
  min-height:100%;
}

.sidebar::before,
.desktop-sidebar::before{
  content:"";
  position:absolute;
  width:220px;
  height:220px;
  border-radius:50%;
  background:rgba(255,255,255,.08);
  right:-70px;
  bottom:-70px;
  pointer-events:none;
}

.brand-panel{
  position:relative;
  z-index:2;
  margin-bottom:1.4rem;
}

.brand-wrap{
  display:flex;
  align-items:center;
  gap:.9rem;
  background:rgba(255,255,255,.13);
  border:1px solid rgba(255,255,255,.16);
  padding:.95rem 1rem;
  border-radius:22px;
  box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.brand-icon{
  width:46px;
  height:46px;
  border-radius:16px;
  background:var(--gescon-yellow);
  color:#4f4300;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:1.3rem;
  flex-shrink:0;
  box-shadow:0 10px 20px rgba(244,197,28,.22);
}

.brand-title{
  margin:0;
  color:var(--gescon-yellow);
  font-size:2rem;
  line-height:1;
  font-weight:900;
}

.brand-subtitle{
  margin:0;
  color:rgba(255,255,255,.9);
  font-size:.9rem;
  line-height:1.45;
  font-weight:700;
}

.menu-label{
  font-size:.74rem;
  letter-spacing:.08em;
  text-transform:uppercase;
  color:rgba(255,255,255,.72);
  margin:1rem .55rem .65rem;
  position:relative;
  z-index:2;
}

.sidebar .nav-link,
.desktop-sidebar .nav-link{
  color:#fff;
  border-radius:18px;
  padding:.9rem 1rem;
  display:flex;
  align-items:center;
  gap:.8rem;
  font-weight:800;
  border:1px solid transparent;
  transition:.2s ease;
  position:relative;
  z-index:2;
  text-decoration:none;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active,
.desktop-sidebar .nav-link:hover,
.desktop-sidebar .nav-link.active{
  background:rgba(255,255,255,.14);
  border-color:rgba(255,255,255,.12);
  color:#fff;
}

.nav-dot{
  width:11px;
  height:11px;
  border-radius:50%;
  background:var(--gescon-yellow);
  box-shadow:0 0 0 4px rgba(244,197,28,.14);
  flex-shrink:0;
}

.sidebar-footer{
  margin-top:1.4rem;
  background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.14);
  border-radius:22px;
  padding:1rem;
  position:relative;
  z-index:2;
}

.sidebar-footer h6{
  color:var(--gescon-yellow);
  font-weight:900;
  margin-bottom:.35rem;
}

.sidebar-footer p{
  margin:0;
  color:rgba(255,255,255,.9);
  font-size:.84rem;
  line-height:1.55;
  font-weight:700;
}

.desktop-sidebar{
  display:none;
}

.offcanvas.offcanvas-start{
  width:min(86vw,340px);
}

/* MAIN */
.main{
  width:100%;
  min-height:100vh;
  margin-left:0;
}

.main-content{
  width:100%;
  max-width:1700px;
  margin:0 auto;
  padding:.9rem;
}

@media (min-width:768px){
  .main-content{
    padding:1.2rem;
  }
}

/* SOLO EN PANTALLAS GRANDES aparece la sidebar fija */
@media (min-width:1200px){
  .desktop-sidebar{
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:fixed;
    top:0;
    left:0;
    width:var(--sidebar-width);
    height:100vh;
    background:linear-gradient(180deg,var(--gescon-green) 0%,var(--gescon-green-dark) 55%,var(--gescon-green-deep) 100%);
    color:#fff;
    padding:1.2rem 1rem;
    overflow-y:auto;
    overflow-x:hidden;
    z-index:1030;
  }

  .main{
    margin-left:var(--sidebar-width);
  }

  .main-content{
    padding:1.5rem 1.75rem;
  }
}

/* EN PANTALLAS CHICAS Y MEDIANAS, NUNCA sidebar fija */
@media (max-width:1199.98px){
  .desktop-sidebar{
    display:none !important;
  }

  .main{
    margin-left:0 !important;
  }
}
</style>
</head>

<body>

<nav class="navbar mobile-navbar d-xl-none sticky-top">
  <div class="container-fluid px-3">
    <a class="navbar-brand" href="?view=index">
      <span class="brand-house"><i class="bi bi-house-fill"></i></span>
      <span>GesCon</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
      <i class="bi bi-list"></i>
    </button>
  </div>
</nav>

<div class="offcanvas offcanvas-start text-bg-dark d-xl-none" tabindex="-1" id="mobileSidebar">
  <div class="offcanvas-header border-bottom border-secondary-subtle">
    <h5 class="offcanvas-title fw-bold">GesCon</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body p-0">
    <aside class="sidebar h-100">
      <div>
        <div class="brand-panel">
          <div class="brand-wrap">
            <div class="brand-icon"><i class="bi bi-house-fill"></i></div>
            <div>
              <h1 class="brand-title">GesCon</h1>
              <p class="brand-subtitle">Sistema de gestión claro, moderno y operativo.</p>
            </div>
          </div>
        </div>

        <div class="menu-label">Principal</div>
        <nav class="nav flex-column">
          <a href="?view=index" class="nav-link <?= $view_name === 'index' ? 'active' : '' ?>"><span class="nav-dot"></span>Dashboard</a>
          <a href="?view=expensas_resumen" class="nav-link <?= $view_name === 'expensas_resumen' ? 'active' : '' ?>"><span class="nav-dot"></span>Resumen de expensas</a>
          <a href="?view=expensas_gastos" class="nav-link <?= $view_name === 'expensas_gastos' ? 'active' : '' ?>"><span class="nav-dot"></span>Gastos y rubros</a>
          <a href="?view=expensas_cobranzas" class="nav-link <?= $view_name === 'expensas_cobranzas' ? 'active' : '' ?>"><span class="nav-dot"></span>Cobranzas y deuda</a>
        </nav>

        <div class="menu-label">Administración</div>
        <nav class="nav flex-column">
          <a href="#" class="nav-link"><span class="nav-dot"></span>Banco / API</a>
          <a href="#" class="nav-link"><span class="nav-dot"></span>Configuración</a>
          <a href="#" class="nav-link"><span class="nav-dot"></span>Notificaciones</a>
          <a href="?action=login&logout=1" class="nav-link"><span class="nav-dot"></span>Cerrar sesión</a>
        </nav>
      </div>

      <div class="sidebar-footer">
        <h6>Panel operativo</h6>
        <p>Controlá alquileres, gastos, movimientos y vencimientos desde una sola plataforma.</p>
      </div>
    </aside>
  </div>
</div>

<aside class="desktop-sidebar">
  <div>
    <div class="brand-panel">
      <div class="brand-wrap">
        <div class="brand-icon"><i class="bi bi-house-fill"></i></div>
        <div>
          <h1 class="brand-title">GesCon</h1>
          <p class="brand-subtitle">Sistema de gestión claro, moderno y operativo.</p>
        </div>
      </div>
    </div>

    <div class="menu-label">Principal</div>
    <nav class="nav flex-column">
      <!-- <a href="?view=index" class="nav-link <?= $view_name === 'index' ? 'active' : '' ?>"><span class="nav-dot"></span>Dashboard</a> -->
      <a href="?view=index" class="nav-link <?= $view_name === 'expensas_resumen' ? 'active' : '' ?>"><span class="nav-dot"></span>Inicio</a>
      <!-- <a href="?view=expensas_gastos" class="nav-link <?= $view_name === 'expensas_gastos' ? 'active' : '' ?>"><span class="nav-dot"></span>Gastos y rubros</a> -->
      <a href="?view=expensas_pdf" class="nav-link <?= $view_name === 'expensas_cobranzas' ? 'active' : '' ?>"><span class="nav-dot"></span>Generar expensa PDF</a>
      <a href="?view=expensas_cobranzas" class="nav-link <?= $view_name === 'expensas_cobranzas' ? 'active' : '' ?>"><span class="nav-dot"></span>Cobranzas y deuda</a>
       <a href="?view=carga_personas" class="nav-link <?= $view_name === 'expensas_cobranzas' ? 'active' : '' ?>"><span class="nav-dot"></span>Cargar nuevas personas ( Propietarios o inquilinos)</a>
        <a href="?view=personas_listado" class="nav-link <?= $view_name === 'expensas_cobranzas' ? 'active' : '' ?>"><span class="nav-dot"></span>Listado de propietarios/inquilinos</a>
         <a href="?view=expensas_gastos" class="nav-link <?= $view_name === 'expensas_cobranzas' ? 'active' : '' ?>"><span class="nav-dot"></span>Gastos</a>
       <a href="?action=login&logout=1" class="nav-link"><span class="nav-dot"></span>Cerrar sesión</a>
    </nav>

    <div class="menu-label">Administración</div>
    <!-- <nav class="nav flex-column">
      <a href="#" class="nav-link"><span class="nav-dot"></span>Banco / API</a>
      <a href="#" class="nav-link"><span class="nav-dot"></span>Configuración</a>
      <a href="#" class="nav-link"><span class="nav-dot"></span>Notificaciones</a>
      <a href="?action=login&logout=1" class="nav-link"><span class="nav-dot"></span>Cerrar sesión</a>
    </nav> -->
  </div>

  <div class="sidebar-footer">
    <h6>Panel operativo</h6>
    <p>Controlá alquileres, gastos, movimientos y vencimientos desde una sola plataforma.</p>
  </div>
</aside>

<main class="main">
  <!-- <div class="main-content"> -->
    <div class="main-content" style="width:100%;max-width:none;">
    <div style="display:flex;justify-content:flex-end;margin-bottom:14px;">

  <a href="?action=login&logout=1"
     style="
     background:#fff;
     color:#b42318;
     border:2px solid #f3c2c2;
     border-radius:16px;
     padding:.55rem 1rem;
     font-weight:900;
     text-decoration:none;
     display:inline-flex;
     align-items:center;
     gap:8px;
     box-shadow:0 6px 14px rgba(0,0,0,.05);
     ">

     <i class="bi bi-box-arrow-right"></i>
     Cerrar sesión

  </a>

</div>
    <?php
    if (file_exists($view_file)) {
        require_once $view_file;
    } else {
        echo '<div class="alert alert-danger">No se encontró la vista: ' . htmlspecialchars($view_name) . '</div>';
    }
    ?>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>