  <!-- Cards & KPIs -->

  <?php
// session_start();

/* Si NO está logueado */
if(!isset($_SESSION['user_data'])){
//    require_once 'app/views/login-view.php';
  
}

/* Si está logueado entra al sistema */
//require_once 'index.php';


//var_dump($_SESSION['user_data']);
//echo("holaaaaa");
?>
        <section id="cards" class="section-anchor">
          <div class="glass p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div>
                <div class="pill fw-semibold mb-2"><i class="bi bi-speedometer2 me-1"></i> Cards & KPIs</div>
                <h4 class="fw-bold mb-1">Resumen rápido</h4>
                <div class="muted">Ejemplo para Home / Dashboard.</div>
                <?php //var_dump("holaaaaa");   ?>
              </div>
              <div class="d-flex gap-2">
                <a class="btn btn-soft" href="#"><i class="bi bi-arrow-clockwise me-1"></i>Actualizar</a>
                <a class="btn btn-accent" href="#"><i class="bi bi-download me-1"></i>Exportar</a>
              </div>
            </div>

            <div class="row g-3 mt-2">
              <div class="col-12 col-md-4">
                <div class="glass p-3" style="border-radius:16px;">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="muted small">Pendientes a proveedores</div>
                      <div class="fw-bold display-6 mb-0 mono">$ 1.245.000</div>
                      <span class="badge badge-soft badge-warn mt-2"><i class="bi bi-hourglass-split me-1"></i>8 vencen esta semana</span>
                    </div>
                    <div class="pill"><i class="bi bi-truck"></i></div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="glass p-3" style="border-radius:16px;">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="muted small">Cobros pendientes</div>
                      <div class="fw-bold display-6 mb-0 mono">$ 820.300</div>
                      <span class="badge badge-soft badge-ok mt-2"><i class="bi bi-check2-circle me-1"></i>+12% vs mes pasado</span>
                    </div>
                    <div class="pill"><i class="bi bi-cash-coin"></i></div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="glass p-3" style="border-radius:16px;">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="muted small">Facturas cargadas</div>
                      <div class="fw-bold display-6 mb-0 mono">143</div>
                      <span class="badge badge-soft mt-2"><i class="bi bi-receipt me-1"></i>Últimos 30 días</span>
                    </div>
                    <div class="pill"><i class="bi bi-receipt-cutoff"></i></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="divider my-4"></div>

            <div class="muted small mb-2">Snippet (copiable):</div>
            <code class="kit">&lt;div class="glass p-3" style="border-radius:16px;"&gt;
  &lt;div class="muted small"&gt;Título&lt;/div&gt;
  &lt;div class="fw-bold display-6 mono"&gt;$ 0&lt;/div&gt;
  &lt;span class="badge badge-soft mt-2"&gt;Estado&lt;/span&gt;
&lt;/div&gt;</code>
          </div>
        </section>

        <!-- Buttons -->
        <section id="buttons" class="section-anchor mt-4">
          <div class="glass p-4">
            <div class="pill fw-semibold mb-2"><i class="bi bi-hand-index-thumb me-1"></i> Botones</div>
            <h4 class="fw-bold mb-3">Acciones y estilos</h4>

            <div class="d-flex flex-wrap gap-2">
              <button class="btn btn-accent"><i class="bi bi-plus-lg me-1"></i>Primario</button>
              <button class="btn btn-soft"><i class="bi bi-pencil me-1"></i>Editar</button>
              <button class="btn btn-soft"><i class="bi bi-trash3 me-1"></i>Eliminar</button>
              <button class="btn btn-soft"><i class="bi bi-search me-1"></i>Buscar</button>
              <button class="btn btn-soft"><i class="bi bi-download me-1"></i>Exportar</button>
              <button class="btn btn-soft"><i class="bi bi-filter me-1"></i>Filtrar</button>
            </div>

            <div class="divider my-4"></div>

            <div class="row g-3">
              <div class="col-12 col-md-6">
                <div class="muted small mb-2">Botón primario:</div>
                <code class="kit">&lt;button class="btn btn-accent"&gt;Guardar&lt;/button&gt;</code>
              </div>
              <div class="col-12 col-md-6">
                <div class="muted small mb-2">Botón secundario:</div>
                <code class="kit">&lt;button class="btn btn-soft"&gt;Cancelar&lt;/button&gt;</code>
              </div>
            </div>
          </div>
        </section>

        <!-- Forms -->
        <section id="forms" class="section-anchor mt-4">
          <div class="glass p-4">
            <div class="pill fw-semibold mb-2"><i class="bi bi-ui-checks me-1"></i> Forms</div>
            <h4 class="fw-bold mb-3">Inputs / selects / validación</h4>

            <form class="row g-3">
              <div class="col-12 col-md-6">
                <label class="form-label">Proveedor</label>
                <input class="form-control soft-input" placeholder="Ej: Almacén Central" />
              </div>
              <div class="col-12 col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control soft-input" />
              </div>
              <div class="col-12 col-md-3">
                <label class="form-label">Condición</label>
                <select class="form-select soft-input">
                  <option>Contado</option>
                  <option>Cuenta corriente</option>
                </select>
              </div>

              <div class="col-12 col-md-4">
                <label class="form-label">Monto</label>
                <div class="input-group">
                  <span class="input-group-text soft-input">$</span>
                  <input class="form-control soft-input" placeholder="0,00" />
                </div>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">Estado</label>
                <select class="form-select soft-input">
                  <option>Pendiente</option>
                  <option>Pagado</option>
                  <option>Vencido</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label">Adjunto</label>
                <input type="file" class="form-control soft-input" />
              </div>

              <div class="col-12">
                <label class="form-label">Comentario</label>
                <textarea class="form-control soft-input" rows="3" placeholder="Notas..."></textarea>
              </div>

              <div class="col-12 d-flex gap-2">
                <button type="button" class="btn btn-accent"><i class="bi bi-check2 me-1"></i>Guardar</button>
                <button type="button" class="btn btn-soft"><i class="bi bi-x-lg me-1"></i>Cancelar</button>
              </div>
            </form>

            <div class="divider my-4"></div>

            <div class="muted small mb-2">Snippet input + icono:</div>
            <code class="kit">&lt;div class="input-group"&gt;
  &lt;span class="input-group-text soft-input"&gt;&lt;i class="bi bi-envelope"&gt;&lt;/i&gt;&lt;/span&gt;
  &lt;input class="form-control soft-input" name="email" /&gt;
&lt;/div&gt;</code>
          </div>
        </section>

        <!-- Tables -->
        <section id="tables" class="section-anchor mt-4">
          <div class="glass p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <div class="pill fw-semibold mb-2"><i class="bi bi-table me-1"></i> Tablas</div>
                <h4 class="fw-bold mb-0">Listado (Facturas / Pagos / Clientes)</h4>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-soft btn-sm"><i class="bi bi-printer me-1"></i>Imprimir</button>
                <button class="btn btn-soft btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</button>
              </div>
            </div>

            <div class="table-responsive mt-3 glass p-2" style="border-radius:16px;">
              <table class="table table-soft align-middle mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th class="text-end">Monto</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="mono">1021</td>
                    <td>ConstruMarket</td>
                    <td class="mono">2026-02-22</td>
                    <td class="text-end mono">$ 180.000</td>
                    <td><span class="badge badge-soft badge-warn">Pendiente</span></td>
                    <td class="text-end">
                      <button class="btn btn-soft btn-sm"><i class="bi bi-eye"></i></button>
                      <button class="btn btn-soft btn-sm"><i class="bi bi-pencil"></i></button>
                      <button class="btn btn-soft btn-sm"><i class="bi bi-trash3"></i></button>
                    </td>
                  </tr>
                  <tr>
                    <td class="mono">1022</td>
                    <td>Almacén Central</td>
                    <td class="mono">2026-02-20</td>
                    <td class="text-end mono">$ 65.300</td>
                    <td><span class="badge badge-soft badge-ok">Pagado</span></td>
                    <td class="text-end">
                      <button class="btn btn-soft btn-sm"><i class="bi bi-eye"></i></button>
                      <button class="btn btn-soft btn-sm"><i class="bi bi-pencil"></i></button>
                      <button class="btn btn-soft btn-sm"><i class="bi bi-trash3"></i></button>
                    </td>
                  </tr>
                  <tr>
                    <td class="mono">1023</td>
                    <td>Fiberco</td>
                    <td class="mono">2026-02-12</td>
                    <td class="text-end mono">$ 320.000</td>
                    <td><span class="badge badge-soft badge-bad">Vencido</span></td>
                    <td class="text-end">
                      <button class="btn btn-soft btn-sm"><i class="bi bi-eye"></i></button>
                      <button class="btn btn-soft btn-sm"><i class="bi bi-pencil"></i></button>
                      <button class="btn btn-soft btn-sm"><i class="bi bi-trash3"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="divider my-4"></div>

            <div class="muted small mb-2">Snippet tabla soft:</div>
            <code class="kit">&lt;div class="table-responsive glass p-2" style="border-radius:16px;"&gt;
  &lt;table class="table table-soft align-middle mb-0"&gt;...&lt;/table&gt;
&lt;/div&gt;</code>
          </div>
        </section>

        <!-- Filters -->
        <section id="filters" class="section-anchor mt-4">
          <div class="glass p-4">
            <div class="pill fw-semibold mb-2"><i class="bi bi-funnel me-1"></i> Filtros</div>
            <h4 class="fw-bold mb-3">Barra de búsqueda + chips</h4>

            <div class="glass p-3" style="border-radius:16px;">
              <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                  <label class="form-label muted small mb-1">Buscar</label>
                  <div class="input-group">
                    <span class="input-group-text soft-input"><i class="bi bi-search"></i></span>
                    <input class="form-control soft-input" placeholder="Proveedor, cliente, # factura..." />
                  </div>
                </div>
                <div class="col-6 col-md-2">
                  <label class="form-label muted small mb-1">Desde</label>
                  <input type="date" class="form-control soft-input" />
                </div>
                <div class="col-6 col-md-2">
                  <label class="form-label muted small mb-1">Hasta</label>
                  <input type="date" class="form-control soft-input" />
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                  <button class="btn btn-accent w-100"><i class="bi bi-funnel-fill me-1"></i>Aplicar</button>
                  <button class="btn btn-soft"><i class="bi bi-x-lg"></i></button>
                </div>
              </div>

              <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="badge badge-soft">Estado: Pendiente <a class="link ms-1" href="#" onclick="return false;">x</a></span>
                <span class="badge badge-soft">Condición: CC <a class="link ms-1" href="#" onclick="return false;">x</a></span>
                <span class="badge badge-soft">Sucursal: #1 <a class="link ms-1" href="#" onclick="return false;">x</a></span>
              </div>
            </div>
          </div>
        </section>

        <!-- Alerts -->
        <section id="alerts" class="section-anchor mt-4">
          <div class="glass p-4">
            <div class="pill fw-semibold mb-2"><i class="bi bi-exclamation-triangle me-1"></i> Alertas</div>
            <h4 class="fw-bold mb-3">Mensajes de estado</h4>

            <div class="alert border-0 mb-2" style="background: rgba(34,197,94,.14); color: var(--text);">
              <i class="bi bi-check2-circle me-1"></i> Guardado correctamente.
            </div>
            <div class="alert border-0 mb-2" style="background: rgba(251,191,36,.14); color: var(--text);">
              <i class="bi bi-exclamation-circle me-1"></i> Hay campos sin completar.
            </div>
            <div class="alert border-0 mb-0" style="background: rgba(251,113,133,.16); color: var(--text);">
              <i class="bi bi-x-octagon-fill me-1"></i> Error al procesar la operación.
            </div>
          </div>
        </section>

        <!-- Empty state -->
        <section id="empty" class="section-anchor mt-4">
          <div class="glass p-4 text-center">
            <div class="pill fw-semibold mb-2"><i class="bi bi-inbox me-1"></i> Estado vacío</div>
            <div class="display-6 mb-1"><i class="bi bi-receipt"></i></div>
            <h4 class="fw-bold mb-1">Todavía no hay facturas</h4>
            <div class="muted mb-3">Cuando cargues tu primera factura, aparecerá acá.</div>
            <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#modalNuevo">
              <i class="bi bi-plus-lg me-1"></i> Cargar factura
            </button>
          </div>
        </section>

        <!-- Layout base -->
        <section id="layout" class="section-anchor mt-4 mb-4">
          <div class="glass p-4">
            <div class="pill fw-semibold mb-2"><i class="bi bi-layout-text-window-reverse me-1"></i> Layout base</div>
            <h4 class="fw-bold mb-3">Plantilla rápida para views</h4>

            <code class="kit">&lt;div class="container py-4"&gt;
  &lt;div class="glass p-4"&gt;
    &lt;div class="d-flex justify-content-between align-items-center"&gt;
      &lt;h4 class="fw-bold mb-0"&gt;Título&lt;/h4&gt;
      &lt;div class="d-flex gap-2"&gt;
        &lt;a class="btn btn-soft" href="#"&gt;Volver&lt;/a&gt;
        &lt;button class="btn btn-accent"&gt;Nuevo&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;

    &lt;div class="divider my-3"&gt;&lt;/div&gt;

    &lt;!-- Contenido acá --&gt;
  &lt;/div&gt;
&lt;/div&gt;</code>
          </div>
        </section>