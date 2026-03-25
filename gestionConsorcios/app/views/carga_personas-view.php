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

<style>
.personas-view *{
  box-sizing:border-box;
}

.personas-view .soft-card,
.personas-view .mini-card{
  background:rgba(255,255,255,.96);
  border:1px solid #d5edd2;
  box-shadow:0 16px 35px rgba(16,168,8,.10);
}

.personas-view .soft-card{
  border-radius:28px;
}

.personas-view .mini-card{
  border-radius:20px;
  padding:1rem;
  height:100%;
}

.personas-view .accent-line{
  width:92px;
  height:6px;
  border-radius:999px;
  background:linear-gradient(90deg,#f4c51c 0%,#16c60c 100%);
  margin-bottom:.9rem;
}

.personas-view .page-title{
  font-size:clamp(1.8rem,3vw,2.6rem);
  font-weight:900;
  color:#0b8707;
  line-height:1.05;
  margin-bottom:.2rem;
}

.personas-view .page-subtitle{
  color:#688162;
  font-size:.98rem;
  font-weight:700;
  line-height:1.5;
  margin:0;
}

.personas-view .section-title{
  font-size:1.12rem;
  color:#0b8707;
  font-weight:900;
  margin-bottom:.15rem;
}

.personas-view .section-subtitle{
  font-size:.87rem;
  color:#688162;
  font-weight:700;
  margin:0;
  line-height:1.5;
}

.personas-view .form-label{
  font-size:.9rem;
  color:#1f3f18;
  font-weight:900;
  margin-bottom:.45rem;
}

.personas-view .form-control,
.personas-view .form-select{
  min-height:52px;
  border-radius:16px;
  border:2px solid #d5edd2;
  font-weight:800;
  color:#1f3f18;
  width:100%;
}

.personas-view .form-control:focus,
.personas-view .form-select:focus{
  border-color:#16c60c;
  box-shadow:0 0 0 .25rem rgba(22,198,12,.12);
}

.personas-view textarea.form-control{
  min-height:auto;
}

.personas-view .btn-gescon{
  background:linear-gradient(135deg,#f4c51c 0%,#d6a90c 100%);
  border:none;
  color:#433900;
  border-radius:16px;
  min-height:48px;
  padding:.75rem 1.15rem;
  font-weight:900;
  box-shadow:0 10px 18px rgba(244,197,28,.20);
}

.personas-view .btn-gescon:hover{
  color:#433900;
  opacity:.96;
}

.personas-view .btn-gescon-outline{
  background:#fff;
  color:#0b8707;
  border:2px solid #d5edd2;
  border-radius:16px;
  min-height:48px;
  padding:.75rem 1.15rem;
  font-weight:900;
}

.personas-view .btn-gescon-outline:hover{
  border-color:#16c60c;
  color:#0b8707;
}

.personas-view .tag-soft{
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

.personas-view .info-box{
  background:#f8fff7;
  border:1px solid #d5edd2;
  border-radius:18px;
  padding:1rem;
}

.personas-view .info-box strong{
  color:#0b8707;
}

@media (max-width:575.98px){
  .personas-view .soft-card{
    border-radius:22px;
  }

  .personas-view .mini-card{
    border-radius:18px;
  }
}
</style>

<div class="personas-view">
  <div class="row g-4">

    <div class="col-12">
      <div class="soft-card p-3 p-md-4">
        <div class="row g-4 align-items-center">
          <div class="col-12 col-xxl-8">
            <div class="accent-line"></div>
            <h1 class="page-title">Alta de personas</h1>
            <p class="page-subtitle">Cargá propietarios, inquilinos o personas con ambos roles dentro del sistema GesCon.</p>
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
    </div>

    <div class="col-12 col-xl-8">
      <div class="soft-card p-3 p-md-4 h-100">
        <div class="mb-3">
          <h2 class="section-title">Formulario de carga</h2>
          <p class="section-subtitle">Completá los datos para registrar una nueva persona en la tabla <strong>personas</strong>.</p>
        </div>


     <div id="respuestaAjaxPersona" style="display:none;margin-bottom:1rem;border-radius:16px;padding:.9rem 1rem;font-weight:800;"></div>

<form id="formNuevaPersona" method="post" action="app/action/guardar_persona.php">
    <input type="hidden" name="id_empresa_administradora" value="<?= htmlspecialchars($_SESSION['contexto_id'], ENT_QUOTES, 'UTF-8'); ?>">
  <div class="row g-3">

    <div class="col-12 col-md-6">
      <label class="form-label fw-bold">Tipo de persona</label>
      <select name="tipo_persona" class="form-select" required>
        <option value="">Seleccionar...</option>
        <option value="propietario">Propietario</option>
        <option value="inquilino">Inquilino</option>
        <option value="ambos">Ambos</option>
      </select>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label fw-bold">Estado</label>
      <select name="estado" class="form-select" required>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label fw-bold">Nombre</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label fw-bold">Apellido</label>
      <input type="text" name="apellido" class="form-control" required>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label fw-bold">DNI</label>
      <input type="text" name="dni" class="form-control">
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label fw-bold">CUIT</label>
      <input type="text" name="cuit" class="form-control">
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label fw-bold">Teléfono</label>
      <input type="text" name="telefono" class="form-control">
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label fw-bold">Email</label>
      <input type="email" name="email" class="form-control">
    </div>

    <div class="col-12">
      <label class="form-label fw-bold">Dirección</label>
      <input type="text" name="direccion" class="form-control">
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label fw-bold">Fecha de nacimiento</label>
      <input type="date" name="fecha_nacimiento" class="form-control">
    </div>

    <div class="col-12">
      <label class="form-label fw-bold">Observaciones</label>
      <textarea name="observaciones" class="form-control" rows="4"></textarea>
    </div>

    <div class="col-12 d-flex flex-wrap gap-2 pt-2">
      <button type="submit" class="btn btn-gescon">Guardar persona</button>
      <button type="reset" class="btn btn-gescon-outline">Limpiar</button>
    </div>

  </div>
</form>
      </div>
    </div>

    <div class="col-12 col-xl-4">
      <div class="row g-4">

        <div class="col-12">
          <div class="soft-card p-3 p-md-4">
            <div class="mb-3">
              <h2 class="section-title">Qué podés registrar</h2>
              <p class="section-subtitle">Roles admitidos en la tabla de personas.</p>
            </div>

            <div class="d-grid gap-3">
              <div class="mini-card">
                <h6>Propietario</h6>
                <small>Rol de dominio</small>
                <p>Persona titular de una unidad funcional o de varias unidades dentro del sistema.</p>
              </div>

              <div class="mini-card">
                <h6>Inquilino</h6>
                <small>Rol de ocupación</small>
                <p>Persona que alquila o ocupa una unidad, con datos de contacto y seguimiento.</p>
              </div>

              <div class="mini-card">
                <h6>Ambos</h6>
                <small>Caso combinado</small>
                <p>Sirve para personas que son propietarias en una unidad e inquilinas en otra.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="soft-card p-3 p-md-4">
            <div class="mb-3">
              <h2 class="section-title">Checklist</h2>
              <p class="section-subtitle">Control rápido antes de guardar.</p>
            </div>

            <div class="d-grid gap-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked id="cp1">
                <label class="form-check-label fw-bold" for="cp1">Datos básicos completos</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" checked id="cp2">
                <label class="form-check-label fw-bold" for="cp2">Rol correctamente seleccionado</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="cp3">
                <label class="form-check-label fw-bold" for="cp3">Vinculación con unidad pendiente</label>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formNuevaPersona');
  const respuesta = document.getElementById('respuestaAjaxPersona');

  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (form.dataset.enviando === "1") return;
    form.dataset.enviando = "1";

    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      respuesta.style.display = 'block';

      if (data.status === 'ok') {
        respuesta.style.background = '#eaffea';
        respuesta.style.border = '1px solid #d5edd2';
        respuesta.style.color = '#0b8707';
        respuesta.innerHTML = data.message;
        form.reset();
      } else {
        respuesta.style.background = '#ffe8e8';
        respuesta.style.border = '1px solid #f3c2c2';
        respuesta.style.color = '#b42318';
        respuesta.innerHTML = data.message;
      }

      form.dataset.enviando = "0";
    })
    .catch(() => {
      form.dataset.enviando = "0";
      respuesta.style.display = 'block';
      respuesta.style.background = '#6e2020';
      respuesta.style.border = '1px solid #f3c2c2';
      respuesta.style.color = '#b42318';
      respuesta.innerHTML = 'Error al enviar.';
    });
  });
});
</script>