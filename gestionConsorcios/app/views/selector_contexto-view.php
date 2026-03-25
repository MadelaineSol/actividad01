<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombre_admin = 'Administración';
if (isset($_SESSION['user_data'])) {
    if (is_object($_SESSION['user_data']) && isset($_SESSION['user_data']->nombre)) {
        $nombre_admin = $_SESSION['user_data']->nombre;
    } elseif (is_array($_SESSION['user_data']) && isset($_SESSION['user_data']['nombre'])) {
        $nombre_admin = $_SESSION['user_data']['nombre'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GesCon | Selector de contexto</title>

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
      --gescon-white:#ffffff;
      --gescon-text:#1f3f18;
      --gescon-muted:#688162;
      --gescon-border:#d5edd2;
      --gescon-shadow:0 16px 35px rgba(16,168,8,.10);
    }

    *{
      box-sizing:border-box;
    }

    html, body{
      margin:0;
      padding:0;
      overflow-x:hidden;
    }

    body{
      min-height:100vh;
      font-family:'Nunito', sans-serif;
      background:
        radial-gradient(circle at top left, rgba(244,197,28,.16), transparent 22%),
        radial-gradient(circle at bottom right, rgba(22,198,12,.10), transparent 24%),
        var(--gescon-bg);
      color:var(--gescon-text);
    }

    .page-wrap{
      width:100%;
      max-width:1440px;
      margin:0 auto;
      padding:1rem;
    }

    @media (min-width:768px){
      .page-wrap{
        padding:1.25rem;
      }
    }

    @media (min-width:1200px){
      .page-wrap{
        padding:1.5rem 1.75rem;
      }
    }

    .selector-contexto{
      width:100%;
    }

    .selector-contexto *{
      box-sizing:border-box;
    }

    .selector-contexto .soft-card,
    .selector-contexto .mini-card{
      background:rgba(255,255,255,.96);
      border:1px solid #d5edd2;
      box-shadow:0 16px 35px rgba(16,168,8,.10);
    }

    .selector-contexto .soft-card{
      border-radius:28px;
    }

    .selector-contexto .mini-card{
      border-radius:20px;
      padding:1rem;
      height:100%;
    }

    .selector-contexto .accent-line{
      width:92px;
      height:6px;
      border-radius:999px;
      background:linear-gradient(90deg,#f4c51c 0%,#16c60c 100%);
      margin-bottom:.9rem;
    }

    .selector-contexto .page-title{
      font-size:clamp(1.8rem,3vw,2.6rem);
      font-weight:900;
      color:#0b8707;
      line-height:1.05;
      margin-bottom:.2rem;
    }

    .selector-contexto .page-subtitle{
      color:#688162;
      font-size:.98rem;
      font-weight:700;
      line-height:1.5;
      margin:0;
    }

    .selector-contexto .section-title{
      font-size:1.12rem;
      color:#0b8707;
      font-weight:900;
      margin-bottom:.15rem;
    }

    .selector-contexto .section-subtitle{
      font-size:.87rem;
      color:#688162;
      font-weight:700;
      margin:0;
      line-height:1.5;
    }

    .selector-contexto .form-label{
      font-size:.9rem;
      color:#1f3f18;
      font-weight:900;
      margin-bottom:.45rem;
    }

    .selector-contexto .form-control,
    .selector-contexto .form-select{
      min-height:52px;
      border-radius:16px;
      border:2px solid #d5edd2;
      font-weight:800;
      color:#1f3f18;
      width:100%;
    }

    .selector-contexto .form-control:focus,
    .selector-contexto .form-select:focus{
      border-color:#16c60c;
      box-shadow:0 0 0 .25rem rgba(22,198,12,.12);
    }

    .selector-contexto .btn-gescon{
      background:linear-gradient(135deg,#f4c51c 0%,#d6a90c 100%);
      border:none;
      color:#433900;
      border-radius:16px;
      min-height:48px;
      padding:.75rem 1.15rem;
      font-weight:900;
      box-shadow:0 10px 18px rgba(244,197,28,.20);
    }

    .selector-contexto .btn-gescon:hover{
      color:#433900;
      opacity:.96;
    }

    .selector-contexto .btn-gescon-outline{
      background:#fff;
      color:#0b8707;
      border:2px solid #d5edd2;
      border-radius:16px;
      min-height:48px;
      padding:.75rem 1.15rem;
      font-weight:900;
    }

    .selector-contexto .btn-gescon-outline:hover{
      border-color:#16c60c;
      color:#0b8707;
    }

    .selector-contexto .tag-soft{
      display:inline-flex;
      align-items:center;
      padding:.42rem .78rem;
      border-radius:999px;
      background:#eaffea;
      color:#0b8707;
      font-size:.74rem;
      font-weight:900;
      white-space:nowrap;
    }

    .selector-contexto .info-box{
      background:#f8fff7;
      border:1px solid #d5edd2;
      border-radius:18px;
      padding:1rem;
    }

    .selector-contexto .info-box strong{
      color:#0b8707;
    }

    .top-actions{
      display:flex;
      justify-content:flex-end;
      margin-bottom:1rem;
    }

    .logout-link{
      background:#fff;
      color:#b42318;
      border:2px solid #f3c2c2;
      border-radius:16px;
      padding:.6rem 1rem;
      font-weight:900;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:8px;
      box-shadow:0 6px 14px rgba(0,0,0,.05);
    }

    .logout-link:hover{
      color:#b42318;
      opacity:.96;
    }

    @media (max-width:575.98px){
      .page-wrap{
        padding:.85rem;
      }

      .selector-contexto .soft-card{
        border-radius:22px;
      }

      .selector-contexto .mini-card{
        border-radius:18px;
      }
    }
  </style>
</head>
<body>

  <div class="page-wrap">
    <div class="top-actions">
      <a href="?action=login&logout=1" class="logout-link">
        <i class="bi bi-box-arrow-right"></i>
        Cerrar sesión
      </a>
    </div>

    <div class="selector-contexto">

      <div class="soft-card p-3 p-md-4 mb-4">
        <div class="row g-4 align-items-center">
          <div class="col-12 col-xxl-8">
            <div class="accent-line"></div>
            <h1 class="page-title">Seleccionar contexto de trabajo</h1>
            <p class="page-subtitle">Elegí si vas a operar sobre un <strong>edificio</strong> o un <strong>barrio</strong>, y luego seleccioná cuál administrar.</p>
          </div>

          <div class="col-12 col-xxl-4">
            <div class="mini-card">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                  <div class="section-title">Administrador actual</div>
                  <div class="section-subtitle"><?= htmlspecialchars($nombre_admin, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <span class="tag-soft">Sesión activa</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-xl-8">
          <div class="soft-card p-3 p-md-4 h-100">
            <div class="mb-3">
              <h2 class="section-title">Elegí dónde trabajar</h2>
              <p class="section-subtitle">Esta selección puede usarse para filtrar módulos, expensas, cobranzas, reportes y accesos internos.</p>
            </div>

            <form action="?action=guardar_contexto" method="post" id="formContextoTrabajo">
              <div class="row g-3">
                <div class="col-12 col-lg-6">
                  <label for="tipo_contexto" class="form-label">Tipo de gestión</label>
                  <select name="tipo_contexto" id="tipo_contexto" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <option value="edificio">Edificio</option>
                    <option value="barrio">Barrio</option>
                  </select>
                </div>

                <div class="col-12 col-lg-6">
                  <label for="contexto_id" class="form-label">Unidad de trabajo</label>
                  <select name="contexto_id" id="contexto_id" class="form-select" required disabled>
                    <option value="">Primero elegí el tipo</option>
                  </select>
                </div>

                <!-- <div class="col-12">
                  <label for="observacion_contexto" class="form-label">Observación interna</label>
                  <input
                    type="text"
                    name="observacion_contexto"
                    id="observacion_contexto"
                    class="form-control"
                    placeholder="Ej: operar cobranzas del turno mañana"
                  >
                </div> -->

                <!-- <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                  <button type="submit" class="btn btn-gescon">Ingresar al contexto</button>
                  <button type="button" class="btn btn-gescon-outline" id="btnLimpiarContexto">Limpiar</button>
                </div> -->
              </div>
            </form>
          </div>
        </div>

        <div class="col-12 col-xl-4">
          <div class="row g-4">
            <div class="col-12">
              <div class="soft-card p-3 p-md-4">
                <div class="mb-3">
                  <h2 class="section-title">Cómo usar esta vista</h2>
                  <p class="section-subtitle">Flujo sugerido para el sistema.</p>
                </div>

                <div class="d-grid gap-3">
                  <div class="mini-card">
                    <h6 class="mb-1" style="color:#1f3f18;font-weight:900;">1. Elegí tipo</h6>
                    <small style="color:#688162;font-weight:700;">Edificio o Barrio</small>
                    <p style="color:#688162;font-size:.85rem;font-weight:700;line-height:1.55;margin:.6rem 0 0;">Define qué conjunto de datos y módulos querés operar.</p>
                  </div>

                  <div class="mini-card">
                    <h6 class="mb-1" style="color:#1f3f18;font-weight:900;">2. Elegí el contexto</h6>
                    <small style="color:#688162;font-weight:700;">Entidad específica</small>
                    <p style="color:#688162;font-size:.85rem;font-weight:700;line-height:1.55;margin:.6rem 0 0;">Podés cargar edificios o barrios reales desde base de datos después.</p>
                  </div>

                  <div class="mini-card">
                    <h6 class="mb-1" style="color:#1f3f18;font-weight:900;">3. Entrá a operar</h6>
                    <small style="color:#688162;font-weight:700;">Sesión contextual</small>
                    <p style="color:#688162;font-size:.85rem;font-weight:700;line-height:1.55;margin:.6rem 0 0;">Con esto podés filtrar dashboard, cobranzas, expensas y reportes por contexto elegido.</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="soft-card p-3 p-md-4">
                <div class="mb-3">
                  <h2 class="section-title">Vista previa</h2>
                  <p class="section-subtitle">Resumen del contexto elegido.</p>
                </div>

                <div class="info-box">
                  <div class="mb-2">Tipo seleccionado: <strong id="previewTipo">—</strong></div>
                  <div class="mb-2">Contexto seleccionado: <strong id="previewNombre">—</strong></div>
                  <div class="mb-0">Estado: <strong id="previewEstado">Esperando selección</strong></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

 <script>
(function(){
  const tipoSelect = document.getElementById('tipo_contexto');
  const contextoSelect = document.getElementById('contexto_id');
  const btnLimpiar = document.getElementById('btnLimpiarContexto');
  const form = document.getElementById('formContextoTrabajo');

  const previewTipo = document.getElementById('previewTipo');
  const previewNombre = document.getElementById('previewNombre');
  const previewEstado = document.getElementById('previewEstado');

  function resetContextoSelect(texto = 'Primero elegí el tipo') {
    contextoSelect.innerHTML = '';
    const option = document.createElement('option');
    option.value = '';
    option.textContent = texto;
    contextoSelect.appendChild(option);
    contextoSelect.disabled = true;
  }

  function actualizarPreview() {
    const tipoTexto = tipoSelect.options[tipoSelect.selectedIndex]?.text || '—';
    const nombreTexto = contextoSelect.disabled
      ? '—'
      : (contextoSelect.options[contextoSelect.selectedIndex]?.text || '—');

    previewTipo.textContent = tipoSelect.value ? tipoTexto : '—';
    previewNombre.textContent = (contextoSelect.value && !contextoSelect.disabled) ? nombreTexto : '—';

    if (!tipoSelect.value) {
      previewEstado.textContent = 'Esperando selección';
    } else if (tipoSelect.value && !contextoSelect.value) {
      previewEstado.textContent = 'Falta elegir contexto';
    } else {
      previewEstado.textContent = 'Listo para ingresar';
    }
  }

  function guardarYRedirigir() {
    if (tipoSelect.value && contextoSelect.value) {
      form.submit();
    }
  }

  function cargarOpcionesDesdeBD(tipo) {
    resetContextoSelect('Cargando...');
    
    fetch('app/action/obtener_contextos.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'tipo=' + encodeURIComponent(tipo)
    })
    .then(response => response.json())
    .then(data => {
      contextoSelect.innerHTML = '';

      const primera = document.createElement('option');
      primera.value = '';
      primera.textContent = 'Seleccionar...';
      contextoSelect.appendChild(primera);

      if (Array.isArray(data) && data.length > 0) {
        data.forEach(item => {
          const opt = document.createElement('option');
          opt.value = item.id;
          opt.textContent = item.nombre;
          contextoSelect.appendChild(opt);
        });
        contextoSelect.disabled = false;
      } else {
        const vacia = document.createElement('option');
        vacia.value = '';
        vacia.textContent = 'Sin resultados';
        contextoSelect.appendChild(vacia);
        contextoSelect.disabled = true;
      }

      actualizarPreview();
    })
    .catch(error => {
      console.error(error);
      resetContextoSelect('Error al cargar');
      actualizarPreview();
    });
  }

  tipoSelect.addEventListener('change', function(){
    const tipo = this.value;

    if (!tipo) {
      resetContextoSelect();
      actualizarPreview();
      return;
    }

    cargarOpcionesDesdeBD(tipo);
  });

  contextoSelect.addEventListener('change', function(){
    actualizarPreview();
    guardarYRedirigir();
  });

  btnLimpiar.addEventListener('click', function(){
    tipoSelect.value = '';
    resetContextoSelect();
    document.getElementById('observacion_contexto').value = '';
    actualizarPreview();
  });

  resetContextoSelect();
  actualizarPreview();
})();
</script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>