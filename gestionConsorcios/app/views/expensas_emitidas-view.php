<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$contexto_id = '';
if (isset($_SESSION['contexto_id']) && $_SESSION['contexto_id'] !== '') {
    $contexto_id = $_SESSION['contexto_id'];
} else {
    require_once __DIR__ . '/../database.php';
    $tmpCon = Database::getCon();
    $tmpQ = mysqli_query($tmpCon, "SELECT DISTINCT `id_empresa_administradora` FROM `gastos` LIMIT 1");
    if ($tmpQ && $tmpR = mysqli_fetch_assoc($tmpQ)) $contexto_id = $tmpR['id_empresa_administradora'];
}

$periodo_actual = date('Y-m');
?>

<style>
.ee *{box-sizing:border-box}
.ee .soft-card{background:rgba(255,255,255,.96);border:1px solid #d5edd2;box-shadow:0 16px 35px rgba(16,168,8,.10);border-radius:28px}
.ee .accent-line{width:92px;height:6px;border-radius:999px;background:linear-gradient(90deg,#f4c51c,#16c60c);margin-bottom:.9rem}
.ee .page-title{font-size:clamp(1.8rem,3vw,2.6rem);font-weight:900;color:#0b8707;line-height:1.05;margin-bottom:.2rem}
.ee .page-subtitle{color:#688162;font-size:.98rem;font-weight:700;line-height:1.5;margin:0}
.ee .form-label{font-size:.85rem;color:#1f3f18;font-weight:900;margin-bottom:.35rem}
.ee .form-control{min-height:46px;border-radius:14px;border:2px solid #d5edd2;font-weight:800;color:#1f3f18;font-size:.9rem}
.ee .form-control:focus{border-color:#16c60c;box-shadow:0 0 0 .25rem rgba(22,198,12,.12)}
.ee .btn-gescon-outline{background:#fff;color:#0b8707;border:2px solid #d5edd2;border-radius:14px;min-height:46px;padding:.65rem 1.1rem;font-weight:900;font-size:.9rem}
.ee .btn-gescon-outline:hover{border-color:#16c60c;color:#0b8707}
.ee .btn-email{background:linear-gradient(135deg,#0b8707,#16c60c);border:none;color:#fff;border-radius:14px;min-height:46px;padding:.65rem 1.1rem;font-weight:900;box-shadow:0 10px 18px rgba(16,168,8,.20);font-size:.9rem}
.ee .btn-email:hover{opacity:.92;color:#fff}
.ee .tag-soft{display:inline-flex;align-items:center;padding:.32rem .65rem;border-radius:999px;background:#eaffea;color:#0b8707;font-size:.72rem;font-weight:900;white-space:nowrap}
.ee .alert-box{display:none;margin-bottom:1rem;border-radius:14px;padding:.8rem 1rem;font-weight:800;font-size:.9rem}
.ee .kpi-strip{display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem}
.ee .kpi-item{flex:1;min-width:140px;background:#fff;border:1px solid #d5edd2;border-radius:20px;padding:1rem;box-shadow:0 10px 24px rgba(16,168,8,.06)}
.ee .kpi-item .kl{font-size:.78rem;color:#688162;font-weight:800;margin-bottom:.3rem}
.ee .kpi-item .kv{font-size:1.3rem;color:#0b8707;font-weight:900;line-height:1}
.ee .kpi-item .kv.danger{color:#b42318}
.ee .unit-card{background:#fff;border:1px solid #d5edd2;border-radius:22px;overflow:hidden;margin-bottom:1rem;box-shadow:0 10px 24px rgba(16,168,8,.06)}
.ee .unit-header{padding:1rem 1.2rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;border-bottom:1px solid #f0f7ee}
.ee .unit-name{font-weight:900;color:#1f3f18;font-size:1rem}
.ee .unit-persona{font-weight:700;color:#688162;font-size:.85rem}
.ee .unit-badge{display:inline-flex;align-items:center;padding:.35rem .7rem;border-radius:999px;font-size:.75rem;font-weight:900}
.ee .badge-pagada{background:#e9ffe8;color:#0b8707}
.ee .badge-parcial{background:#fff6d8;color:#9a7700}
.ee .badge-pendiente{background:#ffe5e5;color:#b42318}
.ee .cargo-row{display:grid;grid-template-columns:1fr 100px 100px 100px 90px;align-items:center;padding:.6rem 1.2rem;gap:0 .5rem;border-bottom:1px solid #f6faf5;font-size:.88rem;font-weight:700;color:#1f3f18}
.ee .cargo-row:hover{background:#f8fff7}
.ee .cargo-row .monto{text-align:right;white-space:nowrap}
.ee .cargo-row .monto.green{color:#0b8707}
.ee .cargo-row .monto.red{color:#b42318}
.ee .cargo-row .estado-mini{text-align:center}
.ee .estado-dot{width:10px;height:10px;border-radius:50%;display:inline-block}
.ee .dot-pagada{background:#0b8707}.ee .dot-parcial{background:#d6a90c}.ee .dot-pendiente{background:#b42318}
.ee .unit-footer{background:#f8fff7;padding:.7rem 1.2rem;display:grid;grid-template-columns:1fr 100px 100px 100px 90px;gap:0 .5rem;font-size:.88rem;font-weight:900;color:#0b8707;border-top:2px solid #d5edd2}
.ee .unit-footer .monto{text-align:right}
.ee .unit-footer .monto.red{color:#b42318}
.ee .empty-state{text-align:center;padding:3rem 1rem;color:#688162;font-weight:700}
.ee .empty-state i{font-size:2.5rem;color:#d5edd2;display:block;margin-bottom:.6rem}
.ee .summary-item{padding:.65rem 0;border-bottom:1px solid #f0f7ee}.ee .summary-item:last-child{border-bottom:none}
.ee .summary-bar{height:6px;border-radius:99px;background:#eaffea;overflow:hidden;margin-top:.3rem}
.ee .summary-bar-fill{height:100%;border-radius:99px}
.ee .bar-green{background:linear-gradient(90deg,#16c60c,#0b8707)}
.ee .bar-red{background:linear-gradient(90deg,#e84c3d,#b42318)}
.ee .bar-yellow{background:linear-gradient(90deg,#f4c51c,#d6a90c)}
.ee .modal-content{border:none;border-radius:24px;overflow:hidden}
.ee .modal-header{background:#eaffea;border-bottom:1px solid #d5edd2}
.ee .modal-header .modal-title{color:#0b8707;font-weight:900}
.ee .email-row{display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid #f0f7ee;font-size:.85rem;font-weight:700}
.ee .email-status{padding:.25rem .6rem;border-radius:99px;font-size:.72rem;font-weight:900}
.ee .es-ok{background:#e9ffe8;color:#0b8707}.ee .es-err{background:#ffe5e5;color:#b42318}.ee .es-skip{background:#fff6d8;color:#9a7700}
@media(max-width:767.98px){.ee .cargo-row,.ee .unit-footer{grid-template-columns:1fr 80px 80px}.ee .cargo-row .col-pagado,.ee .cargo-row .col-estado,.ee .unit-footer .col-pagado,.ee .unit-footer .col-estado{display:none}.ee .unit-card{border-radius:18px}.ee .soft-card{border-radius:20px}}
@media(max-width:575.98px){.ee .kpi-item{min-width:100%}.ee .kpi-item .kv{font-size:1.1rem}}
</style>

<div class="ee">

<div class="soft-card p-3 p-md-4 mb-4">
  <div class="row g-3 align-items-center">
    <div class="col-12 col-lg-4">
      <div class="accent-line"></div>
      <h1 class="page-title">Expensas emitidas</h1>
      <p class="page-subtitle">Detalle por unidad con desglose de cargos, deuda arrastrada y estado de pago.</p>
    </div>
    <div class="col-12 col-lg-8">
      <div class="row g-2 align-items-end justify-content-lg-end">
        <div class="col-6 col-sm-auto">
          <label class="form-label">Periodo</label>
          <input type="month" id="eeFiltroPeriodo" class="form-control" value="<?php echo $periodo_actual; ?>" style="min-width:170px;">
        </div>
        <div class="col-6 col-sm-auto">
          <label class="form-label">Buscar</label>
          <input type="text" id="eeBuscar" class="form-control" placeholder="Unidad o persona..." style="min-width:150px;">
        </div>
        <div class="col-auto d-flex gap-2 align-items-end">
          <button type="button" class="btn btn-gescon-outline" id="eeBtnRefresh" title="Actualizar"><i class="bi bi-arrow-repeat"></i></button>
          <button type="button" class="btn btn-email" data-bs-toggle="modal" data-bs-target="#eeModalEmail">
            <i class="bi bi-envelope-fill"></i> Enviar por email
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="eeMsg" class="alert-box"></div>

<div class="kpi-strip">
  <div class="kpi-item"><div class="kl">Total emitido</div><div class="kv" id="eeKEmitido">$ 0</div></div>
  <div class="kpi-item"><div class="kl">Total cobrado</div><div class="kv" id="eeKCobrado">$ 0</div></div>
  <div class="kpi-item"><div class="kl">Total pendiente</div><div class="kv danger" id="eeKPendiente">$ 0</div></div>
  <div class="kpi-item"><div class="kl">Unidades</div><div class="kv" id="eeKUnidades">0</div></div>
</div>

<div class="row g-4">
  <div class="col-12 col-xl-8"><div id="eeCards"><div class="empty-state"><i class="bi bi-file-earmark-text"></i>Cargando...</div></div></div>
  <div class="col-12 col-xl-4">
    <div class="soft-card p-3 p-md-4 h-100">
      <div style="font-size:1.05rem;font-weight:900;color:#0b8707;margin-bottom:.3rem">Estado de cobranza</div>
      <div style="font-size:.84rem;color:#688162;font-weight:700;margin-bottom:1rem">Periodo: <strong id="eeResumenPer"><?php echo date('m/Y'); ?></strong></div>
      <div id="eeResumen"><div class="empty-state"><i class="bi bi-pie-chart"></i>Sin datos.</div></div>
      <div style="margin-top:1.2rem;padding-top:1rem;border-top:2px solid #eaffea">
        <div style="font-size:.9rem;font-weight:900;color:#1f3f18;margin-bottom:.6rem">Porcentaje de cobro</div>
        <div style="display:flex;align-items:center;gap:.6rem">
          <div style="flex:1;height:12px;border-radius:99px;background:#ffe5e5;overflow:hidden"><div id="eeBarCobro" style="height:100%;border-radius:99px;background:linear-gradient(90deg,#16c60c,#0b8707);width:0%;transition:width .5s"></div></div>
          <span style="font-weight:900;color:#0b8707;font-size:.95rem" id="eePctCobro">0%</span>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<!-- MODAL EMAIL -->
<div class="modal fade ee" id="eeModalEmail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-envelope-fill"></i> Enviar expensas por email</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div id="eeEmailMsg"></div>
        <div id="eeEmailPaso1">
          <p style="font-weight:700;color:#1f3f18;font-size:.95rem;margin-bottom:.5rem">Se va a enviar un email a cada propietario/inquilino con:</p>
          <p style="font-weight:700;color:#688162;font-size:.88rem;line-height:1.7;margin:0">
            <i class="bi bi-check-circle-fill" style="color:#0b8707"></i> Detalle de expensas del periodo<br>
            <i class="bi bi-check-circle-fill" style="color:#0b8707"></i> Multas y cargos adicionales<br>
            <i class="bi bi-check-circle-fill" style="color:#0b8707"></i> Deuda arrastrada de periodos anteriores<br>
            <i class="bi bi-check-circle-fill" style="color:#0b8707"></i> Saldo total pendiente de pago
          </p>
          <div style="background:#fff6d8;border:1px solid #f4c51c;border-radius:14px;padding:.8rem 1rem;margin-top:1rem;font-size:.85rem;font-weight:700;color:#9a7700">
            <i class="bi bi-info-circle"></i> Solo se envia a personas que tengan <strong>email cargado</strong> en el sistema.
          </div>
        </div>
        <div id="eeEmailPaso2" style="display:none"><div id="eeEmailRes"></div></div>
      </div>
      <div class="modal-footer border-0 px-4 pb-4" id="eeEmailFooter">
        <button type="button" class="btn btn-gescon-outline" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-email" id="eeBtnSendEmail"><i class="bi bi-send-fill"></i> Enviar ahora</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var B='<?php echo rtrim(URL, "/"); ?>/';
  var CID='<?php echo htmlspecialchars($contexto_id, ENT_QUOTES, "UTF-8"); ?>';
  var filtro=document.getElementById('eeFiltroPeriodo'),buscar=document.getElementById('eeBuscar'),cards=document.getElementById('eeCards'),msg=document.getElementById('eeMsg');

  filtro.addEventListener('change',function(){if(this.value){var p=this.value.split('-');document.getElementById('eeResumenPer').textContent=p[1]+'/'+p[0];}load();});
  document.getElementById('eeBtnRefresh').addEventListener('click',function(){load();});
  buscar.addEventListener('input',function(){var q=this.value.toLowerCase().trim();cards.querySelectorAll('.unit-card').forEach(function(c){c.style.display=c.innerText.toLowerCase().includes(q)?'':'none';});});
  load();

  function load(){
    var fd=new FormData();fd.append('id_empresa_administradora',CID);fd.append('periodo',filtro.value);
    fetch(B+'?action=listar_expensas_emitidas',{method:'POST',body:fd})
    .then(function(r){return r.json();}).then(function(data){
      if(data.status!=='ok'){cards.innerHTML='<div class="empty-state"><i class="bi bi-exclamation-triangle"></i>'+esc(data.message||'Error')+'</div>';return;}
      drawCards(data.unidades);drawKPIs(data.totales);drawResumen(data.unidades,data.totales);
    }).catch(function(){cards.innerHTML='<div class="empty-state"><i class="bi bi-wifi-off"></i>Error de conexion.</div>';});
  }

  function drawCards(unidades){
    if(!unidades||unidades.length===0){cards.innerHTML='<div class="empty-state"><i class="bi bi-file-earmark-text"></i>No hay expensas emitidas para este periodo.<br>Genera expensas desde el <strong>Libro de gastos</strong>.</div>';return;}
    var h='';
    unidades.forEach(function(u){
      var bc='badge-pendiente',bt='Pendiente';
      if(u.estado_general==='pagada'){bc='badge-pagada';bt='Pagada';}else if(u.estado_general==='parcial'){bc='badge-parcial';bt='Pago parcial';}
      h+='<div class="unit-card"><div class="unit-header"><div><div class="unit-name"><i class="bi bi-house-door-fill" style="color:#0b8707;margin-right:.3rem"></i>'+esc(u.unidad_nombre);
      if(u.unidad_codigo)h+=' <span style="color:#688162;font-size:.82rem">('+esc(u.unidad_codigo)+')</span>';
      h+='</div><div class="unit-persona">';
      if(u.persona_nombre)h+=esc(u.persona_nombre)+' <span class="tag-soft" style="margin-left:.3rem">'+esc(u.tipo_persona)+'</span>';
      else h+='<span style="color:#b42318">Sin persona asignada</span>';
      h+='</div></div><div class="unit-badge '+bc+'">'+bt+'</div></div>';
      h+='<div class="cargo-row" style="background:#f8fff7;font-size:.76rem;font-weight:900;color:#0b8707;border-bottom:2px solid #eaffea"><div>Concepto</div><div class="monto">Emitido</div><div class="monto col-pagado">Pagado</div><div class="monto">Saldo</div><div class="estado-mini col-estado">Estado</div></div>';
      u.cargos.forEach(function(c){
        var dc='dot-pendiente';if(c.estado==='pagada')dc='dot-pagada';else if(c.estado==='parcial')dc='dot-parcial';
        var isD=c.concepto==='Deuda anterior',isM=c.concepto==='Multa';
        h+='<div class="cargo-row"><div style="overflow:hidden;text-overflow:ellipsis">';
        if(isD)h+='<i class="bi bi-exclamation-triangle-fill" style="color:#b42318;margin-right:.3rem;font-size:.75rem"></i>';
        else if(isM)h+='<i class="bi bi-slash-circle" style="color:#b42318;margin-right:.3rem;font-size:.75rem"></i>';
        else h+='<i class="bi bi-receipt" style="color:#0b8707;margin-right:.3rem;font-size:.75rem"></i>';
        h+=esc(c.concepto);if(c.detalle)h+=' <span style="color:#688162;font-size:.78rem">- '+esc(c.detalle)+'</span>';
        h+='</div><div class="monto'+((isD||isM)?' red':'')+'">$ '+fmtM(c.importe)+'</div>';
        h+='<div class="monto green col-pagado">$ '+fmtM(c.pagado)+'</div>';
        h+='<div class="monto'+(c.saldo>0?' red':'')+'">$ '+fmtM(c.saldo)+'</div>';
        h+='<div class="estado-mini col-estado"><span class="estado-dot '+dc+'"></span></div></div>';
      });
      h+='<div class="unit-footer"><div>TOTAL</div><div class="monto">$ '+fmtM(u.total_emitido)+'</div><div class="monto col-pagado" style="color:#0b8707">$ '+fmtM(u.total_pagado)+'</div><div class="monto'+(u.total_saldo>0?' red':'')+'">$ '+fmtM(u.total_saldo)+'</div><div class="col-estado"></div></div></div>';
    });
    cards.innerHTML=h;
  }

  function drawKPIs(t){
    document.getElementById('eeKEmitido').textContent='$ '+fmtM(t.emitido);
    document.getElementById('eeKCobrado').textContent='$ '+fmtM(t.cobrado);
    document.getElementById('eeKPendiente').textContent='$ '+fmtM(t.pendiente);
    document.getElementById('eeKUnidades').textContent=t.cantidad_unidades;
    var pct=t.emitido>0?Math.round((t.cobrado/t.emitido)*100):0;
    document.getElementById('eeBarCobro').style.width=pct+'%';
    document.getElementById('eePctCobro').textContent=pct+'%';
  }

  function drawResumen(unidades,totales){
    var c=document.getElementById('eeResumen');if(!unidades||unidades.length===0){c.innerHTML='<div class="empty-state"><i class="bi bi-pie-chart"></i>Sin datos.</div>';return;}
    var pg=0,pa=0,pe=0;unidades.forEach(function(u){if(u.estado_general==='pagada')pg++;else if(u.estado_general==='parcial')pa++;else pe++;});
    var t=unidades.length,h='';
    h+='<div class="summary-item"><div class="d-flex justify-content-between"><span style="font-weight:900;color:#0b8707">Pagadas</span><span style="font-weight:900;color:#0b8707">'+pg+'/'+t+'</span></div><div class="summary-bar"><div class="summary-bar-fill bar-green" style="width:'+(t>0?Math.round(pg/t*100):0)+'%"></div></div></div>';
    h+='<div class="summary-item"><div class="d-flex justify-content-between"><span style="font-weight:900;color:#9a7700">Pago parcial</span><span style="font-weight:900;color:#9a7700">'+pa+'/'+t+'</span></div><div class="summary-bar"><div class="summary-bar-fill bar-yellow" style="width:'+(t>0?Math.round(pa/t*100):0)+'%"></div></div></div>';
    h+='<div class="summary-item"><div class="d-flex justify-content-between"><span style="font-weight:900;color:#b42318">Pendientes</span><span style="font-weight:900;color:#b42318">'+pe+'/'+t+'</span></div><div class="summary-bar"><div class="summary-bar-fill bar-red" style="width:'+(t>0?Math.round(pe/t*100):0)+'%"></div></div></div>';
    c.innerHTML=h;
  }

  // === EMAIL ===
  var mEl=document.getElementById('eeModalEmail');
  mEl.addEventListener('show.bs.modal',function(){
    document.getElementById('eeEmailPaso1').style.display='';
    document.getElementById('eeEmailPaso2').style.display='none';
    document.getElementById('eeEmailFooter').style.display='';
    document.getElementById('eeEmailMsg').innerHTML='';
  });

  document.getElementById('eeBtnSendEmail').addEventListener('click',function(){
    var btn=this;if(btn.dataset.busy==='1')return;
    btn.dataset.busy='1';btn.innerHTML='<i class="bi bi-hourglass-split"></i> Enviando...';
    var fd=new FormData();fd.append('id_empresa_administradora',CID);fd.append('periodo',filtro.value);

    fetch(B+'?action=enviar_expensas_email',{method:'POST',body:fd})
    .then(function(r){return r.json();}).then(function(data){
      btn.dataset.busy='0';btn.innerHTML='<i class="bi bi-send-fill"></i> Enviar ahora';
      document.getElementById('eeEmailPaso1').style.display='none';
      document.getElementById('eeEmailPaso2').style.display='';
      document.getElementById('eeEmailFooter').style.display='none';

      var res=data.resumen||{};
      var isOk=data.status==='ok';
      var h='<div style="text-align:center;margin-bottom:1rem">';
      h+='<i class="bi bi-'+(isOk?'check-circle-fill':'exclamation-circle-fill')+'" style="font-size:2.5rem;color:'+(isOk?'#0b8707':'#b42318')+'"></i>';
      h+='<div style="font-weight:900;color:'+(isOk?'#0b8707':'#b42318')+';font-size:1rem;margin-top:.4rem">'+esc(data.message)+'</div></div>';

      if(res.enviados!==undefined){
        h+='<div style="background:#f8fff7;border:1px solid #d5edd2;border-radius:14px;padding:1rem;margin-bottom:1rem">';
        h+='<div style="display:flex;justify-content:space-between;padding:.3rem 0;font-weight:700;font-size:.9rem"><span style="color:#688162">Emails enviados</span><span style="color:#0b8707;font-weight:900">'+res.enviados+'</span></div>';
        h+='<div style="display:flex;justify-content:space-between;padding:.3rem 0;font-weight:700;font-size:.9rem"><span style="color:#688162">Sin email cargado</span><span style="color:#9a7700;font-weight:900">'+res.sin_email+'</span></div>';
        h+='<div style="display:flex;justify-content:space-between;padding:.3rem 0;font-weight:700;font-size:.9rem"><span style="color:#688162">Errores de envio</span><span style="color:#b42318;font-weight:900">'+res.errores+'</span></div>';
        h+='</div>';
      }

      if(data.detalle&&data.detalle.length>0){
        h+='<div style="max-height:220px;overflow-y:auto">';
        data.detalle.forEach(function(d){
          var sc='es-ok',st='Enviado';
          if(d.status==='sin_email'){sc='es-skip';st='Sin email';}
          else if(d.status==='error'){sc='es-err';st='Error';}
          h+='<div class="email-row"><div><strong style="color:#1f3f18">'+esc(d.nombre)+'</strong><br><span style="font-size:.78rem;color:#688162">'+esc(d.unidad)+' · '+esc(d.email)+'</span></div>';
          h+='<span class="email-status '+sc+'">'+st+'</span></div>';
        });
        h+='</div>';
      }

      document.getElementById('eeEmailRes').innerHTML=h;
    }).catch(function(){
      btn.dataset.busy='0';btn.innerHTML='<i class="bi bi-send-fill"></i> Enviar ahora';
      document.getElementById('eeEmailMsg').innerHTML='<div style="background:#ffe8e8;border:1px solid #f3c2c2;color:#b42318;border-radius:14px;padding:.8rem 1rem;font-weight:800">Error de conexion.</div>';
    });
  });

  function fmtM(n){return parseFloat(n).toLocaleString('es-AR',{minimumFractionDigits:2,maximumFractionDigits:2});}
  function esc(s){if(!s)return '';var d=document.createElement('div');d.appendChild(document.createTextNode(s));return d.innerHTML;}
})();
</script>