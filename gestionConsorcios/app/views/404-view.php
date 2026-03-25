<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Error 404 - Página no encontrada</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root{
      --card: rgba(255,255,255,.06);
      --border: rgba(255,255,255,.10);
      --text: rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.65);
      --accent: #22c55e;
      --accent2:#38bdf8;
      --shadow: 0 20px 60px rgba(0,0,0,.35);
    }

    body{
      background:
        radial-gradient(1200px 700px at 20% 10%, rgba(56,189,248,.18), transparent 60%),
        radial-gradient(900px 600px at 90% 20%, rgba(34,197,94,.14), transparent 55%),
        radial-gradient(900px 600px at 40% 90%, rgba(251,113,133,.12), transparent 60%),
        linear-gradient(180deg, #070b14 0%, #090f1b 40%, #070b14 100%);
      color: var(--text);
      min-height: 100vh;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    .glass{
      background: var(--card);
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 18px;
    }

    .btn-accent{
      background: linear-gradient(135deg, rgba(34,197,94,1), rgba(56,189,248,1));
      border: 0;
      color: #041015;
      font-weight: 700;
      border-radius: 12px;
      padding: .6rem 1.2rem;
    }

    .btn-accent:hover{
      filter: brightness(1.05);
    }

    .muted{
      color: var(--muted);
    }

    .error-code{
      font-size: 6rem;
      font-weight: 900;
      letter-spacing: -.05em;
      background: linear-gradient(135deg, #22c55e, #38bdf8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
  </style>
</head>

<body>

  <div class="glass p-5 text-center" style="max-width: 520px; width:100%;">
    
    <div class="error-code">404</div>

    <h3 class="fw-bold mt-3 mb-2">Página no encontrada</h3>

    <p class="muted mb-4">
      El archivo o recurso que estás intentando acceder no existe
      o fue movido.
    </p>

    <div class="d-flex justify-content-center gap-3">
      <a href="<?= BASE_URL ?>index.php?m=home&a=index" class="btn btn-accent">
        <i class="bi bi-house-door me-1"></i> Ir al Dashboard
      </a>

      <a href="javascript:history.back()" class="btn btn-outline-light">
        <i class="bi bi-arrow-left me-1"></i> Volver
      </a>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>