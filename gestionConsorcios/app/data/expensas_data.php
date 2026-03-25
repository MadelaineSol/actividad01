<?php
/**
 * expensas_data.php
 * Carga datos reales desde la BD para la vista PDF de expensas
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database.php';

$con = Database::getCon();

$id_empresa = '';
if (isset($_SESSION['contexto_id']) && $_SESSION['contexto_id'] !== '') {
    $id_empresa = trim($_SESSION['contexto_id']);
}

$esc = function($v) use ($con) { return mysqli_real_escape_string($con, $v); };

// Helper: formatear pesos
function pesos($n) {
    return '$ ' . number_format(floatval($n), 2, ',', '.');
}

// Periodo actual o el ultimo con datos
$periodo = date('Y-m');

// Intentar obtener el ultimo periodo con cobranzas generadas
if ($id_empresa !== '') {
    $id_e = $esc($id_empresa);
    $qPer = mysqli_query($con, "SELECT DISTINCT `periodo` FROM `cobranzas`
        WHERE `id_empresa_administradora`='$id_e'
        ORDER BY `periodo` DESC LIMIT 1");
    if ($qPer && $rPer = mysqli_fetch_assoc($qPer)) {
        $periodo = $rPer['periodo'];
    }
}

$partes = explode('-', $periodo);
$anio = intval($partes[0]);
$mes = intval($partes[1]);
$meses_arr = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$periodo_nombre = (isset($meses_arr[$mes]) ? $meses_arr[$mes] : $mes) . ' ' . $anio;

// Vencimientos
$venc1 = sprintf('%02d/%02d/%04d', 10, $mes, $anio);
$venc2 = sprintf('%02d/%02d/%04d', 20, $mes, $anio);
$rec1 = 5;
$rec2 = 10;

// Obtener datos de UFs con cobranzas
$expensas_uf = [];

if ($id_empresa !== '') {
    $id_e = $esc($id_empresa);
    $periodo_e = $esc($periodo);

    // Obtener UFs activas
    $qUFs = mysqli_query($con, "SELECT uf.*, p.nombre as prop_nombre, p.apellido as prop_apellido
        FROM `unidades_funcionales` uf
        LEFT JOIN `personas` p ON uf.`propietario_id` = p.`id`
        WHERE uf.`id_empresa_administradora`='$id_e'
        AND uf.`estado_administrativo`='activa'
        ORDER BY uf.`id`");

    if ($qUFs) {
        while ($uf = mysqli_fetch_assoc($qUFs)) {
            $uf_id = $uf['id'];
            $prop_name = trim(($uf['prop_nombre'] ?? '') . ' ' . ($uf['prop_apellido'] ?? ''));
            if ($prop_name === '') $prop_name = 'Sin asignar';

            // Obtener cobranza de expensas para este periodo
            $qCob = mysqli_query($con, "SELECT * FROM `cobranzas`
                WHERE `unidad_funcional_id`='" . $esc($uf_id) . "'
                AND `id_empresa_administradora`='$id_e'
                AND `periodo`='$periodo_e'
                ORDER BY `id`");

            $total_expensa = 0;
            $deuda_anterior = 0;
            $conceptos = [];

            if ($qCob) {
                while ($c = mysqli_fetch_assoc($qCob)) {
                    if ($c['concepto'] === 'Deuda anterior') {
                        $deuda_anterior += floatval($c['importe']);
                    } else {
                        $total_expensa += floatval($c['importe']);
                    }
                    $conceptos[] = $c;
                }
            }

            // Pagos acreditados para periodos anteriores
            $qPagos = mysqli_query($con, "SELECT COALESCE(SUM(`importe_pagado`), 0) as total_pagado
                FROM `cobranzas`
                WHERE `unidad_funcional_id`='" . $esc($uf_id) . "'
                AND `id_empresa_administradora`='$id_e'
                AND `periodo` < '$periodo_e'");
            $pagos_ant = 0;
            if ($qPagos && $rPag = mysqli_fetch_assoc($qPagos)) {
                $pagos_ant = floatval($rPag['total_pagado']);
            }

            $expensas_uf[] = [
                'uf' => intval($uf_id),
                'uf_codigo' => $uf['codigo'] ?? $uf['nombre'],
                'prop' => $prop_name,
                'coef' => floatval($uf['coeficiente_expensas']),
                'expOrd' => $total_expensa,
                'cip' => 0,
                'arba' => 0,
                'tasa' => 0,
                'trami' => 0,
                'ambu' => 0,
                'canon' => 0,
                'multa' => 0,
                'total' => $total_expensa,
                'deuda' => $deuda_anterior,
                'saldo' => $total_expensa + $deuda_anterior,
                'pagos' => $pagos_ant,
                'aPagar' => $total_expensa + $deuda_anterior,
                'conceptos' => $conceptos
            ];
        }
    }

    // Gastos por rubro del periodo
    $rubros = [];
    $qRubros = mysqli_query($con, "SELECT `categoria` as rubro, SUM(`monto`) as importe, COUNT(*) as cantidad,
        ROUND(SUM(`monto`) / (SELECT SUM(`monto`) FROM `gastos` WHERE `id_empresa_administradora`='$id_e' AND `periodo`='$periodo_e' AND `estado`!='anulado') * 100, 2) as inc
        FROM `gastos`
        WHERE `id_empresa_administradora`='$id_e' AND `periodo`='$periodo_e' AND `estado`!='anulado'
        GROUP BY `categoria` ORDER BY importe DESC");
    if ($qRubros) {
        while ($r = mysqli_fetch_assoc($qRubros)) {
            $rubros[] = [
                'rubro' => $r['rubro'],
                'importe' => floatval($r['importe']),
                'inc' => floatval($r['inc']),
                'cantidad' => intval($r['cantidad'])
            ];
        }
    }

    // Proveedores
    $proveedores = [];
    $qProv = mysqli_query($con, "SELECT * FROM `proveedores` WHERE `id_empresa_administradora`='$id_e' ORDER BY `nombre`");
    if ($qProv) {
        while ($p = mysqli_fetch_assoc($qProv)) {
            $proveedores[] = [
                'prov' => $p['nombre'] ?: $p['razon_social'],
                'rubro' => $p['categoria'] ?? ''
            ];
        }
    }
} else {
    $rubros = [];
    $proveedores = [];
}
