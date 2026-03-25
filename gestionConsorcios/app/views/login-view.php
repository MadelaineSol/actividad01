<?php
// Login View - GesCon style
$err = isset($_GET['err']) ? trim($_GET['err']) : '';
$email_value = isset($_GET['email']) ? trim($_GET['email']) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | GesCon</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root{
      --gescon-green:#16c60c;
      --gescon-green-dark:#10a808;
      --gescon-green-deep:#0b8707;
      --gescon-green-soft:#eaffea;
      --gescon-yellow:#f4c51c;
      --gescon-yellow-dark:#d6a90c;
      --gescon-white:#ffffff;
      --gescon-text:#1f3f18;
      --gescon-muted:#688162;
      --gescon-border:#d5edd2;
      --gescon-bg:#f6fff5;
      --gescon-shadow:0 20px 45px rgba(16,168,8,.12);
      --gescon-radius:24px;
      --danger-bg:#fff1f1;
      --danger-border:#f3c2c2;
      --danger-text:#b42318;
    }

    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
    }

    body{
      font-family:'Nunito', sans-serif;
      background:
        radial-gradient(circle at top left, rgba(244,197,28,.16), transparent 22%),
        radial-gradient(circle at bottom right, rgba(22,198,12,.10), transparent 24%),
        var(--gescon-bg);
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:24px;
      color:var(--gescon-text);
      overflow-x:hidden;
    }

    .login-wrapper{
      width:100%;
      max-width:1160px;
      display:grid;
      grid-template-columns:1.05fr .95fr;
      background:var(--gescon-white);
      border-radius:32px;
      overflow:hidden;
      box-shadow:var(--gescon-shadow);
      min-height:680px;
      border:1px solid var(--gescon-border);
    }

    .login-brand{
      position:relative;
      background:linear-gradient(160deg, var(--gescon-green) 0%, var(--gescon-green-dark) 55%, var(--gescon-green-deep) 100%);
      padding:56px 48px;
      display:flex;
      flex-direction:column;
      justify-content:space-between;
      color:#fff;
      overflow:hidden;
    }

    .login-brand::before{
      content:"";
      position:absolute;
      width:240px;
      height:240px;
      border-radius:50%;
      background:rgba(255,255,255,.08);
      right:-70px;
      bottom:-70px;
    }

    .login-brand::after{
      content:"";
      position:absolute;
      width:180px;
      height:180px;
      border-radius:50%;
      background:rgba(255,255,255,.06);
      left:-50px;
      top:-50px;
    }

    .brand-content-wrap,
    .brand-card{
      position:relative;
      z-index:2;
    }

    .brand-top{
      margin-bottom:28px;
    }

    .brand-pill{
      display:inline-flex;
      align-items:center;
      gap:14px;
      background:rgba(255,255,255,.14);
      border:1px solid rgba(255,255,255,.18);
      padding:14px 18px;
      border-radius:22px;
      box-shadow:0 10px 25px rgba(0,0,0,.08);
      backdrop-filter:blur(8px);
    }

    .brand-icon{
      width:48px;
      height:48px;
      border-radius:16px;
      background:var(--gescon-yellow);
      color:#4f4300;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:24px;
      font-weight:900;
      box-shadow:0 10px 20px rgba(244,197,28,.24);
      flex-shrink:0;
    }

    .brand-name{
      font-size:34px;
      line-height:1;
      font-weight:900;
      color:var(--gescon-yellow);
      text-shadow:0 8px 18px rgba(0,0,0,.14);
      margin-bottom:4px;
    }

    .brand-mini{
      font-size:14px;
      color:rgba(255,255,255,.90);
      font-weight:700;
      line-height:1.4;
    }

    .brand-badge{
      display:inline-flex;
      align-items:center;
      gap:10px;
      background:rgba(255,255,255,.12);
      border:1px solid rgba(255,255,255,.18);
      color:#fff;
      padding:10px 16px;
      border-radius:999px;
      font-size:14px;
      font-weight:700;
      margin-bottom:24px;
      width:fit-content;
      backdrop-filter:blur(6px);
    }

    .brand-content h1{
      font-size:46px;
      line-height:1.08;
      font-weight:900;
      margin-bottom:18px;
      max-width:520px;
      color:#fff;
    }

    .brand-content p{
      font-size:16px;
      line-height:1.8;
      color:rgba(255,255,255,.88);
      max-width:510px;
      font-weight:700;
    }

    .brand-card{
      margin-top:36px;
      background:rgba(255,255,255,.12);
      border:1px solid rgba(255,255,255,.16);
      border-radius:24px;
      padding:22px;
      backdrop-filter:blur(10px);
      max-width:450px;
    }

    .brand-card-title{
      font-size:15px;
      font-weight:900;
      color:var(--gescon-yellow);
      margin-bottom:8px;
    }

    .brand-card-text{
      font-size:14px;
      line-height:1.7;
      color:rgba(255,255,255,.88);
      font-weight:700;
    }

    .login-form-side{
      display:flex;
      align-items:center;
      justify-content:center;
      background:rgba(255,255,255,.88);
      padding:50px 34px;
    }

    .login-box{
      width:100%;
      max-width:430px;
    }

    .mobile-brand{
      display:none;
      margin-bottom:26px;
    }

    .mobile-brand .brand-pill{
      background:#fff;
      border:1px solid var(--gescon-border);
      box-shadow:0 10px 24px rgba(16,168,8,.08);
    }

    .mobile-brand .brand-mini{
      color:var(--gescon-muted);
    }

    .accent-line{
      width:82px;
      height:6px;
      border-radius:999px;
      background:linear-gradient(90deg, var(--gescon-yellow) 0%, var(--gescon-green) 100%);
      margin-bottom:20px;
    }

    .login-title{
      font-size:38px;
      font-weight:900;
      color:var(--gescon-green-deep);
      margin-bottom:10px;
      line-height:1.05;
    }

    .login-subtitle{
      font-size:15px;
      color:var(--gescon-muted);
      margin-bottom:28px;
      line-height:1.7;
      font-weight:700;
    }

    .alert-error{
      margin-bottom:18px;
      background:var(--danger-bg);
      border:1px solid var(--danger-border);
      color:var(--danger-text);
      border-radius:16px;
      padding:14px 16px;
      font-size:14px;
      font-weight:800;
      line-height:1.5;
    }

    .form-group{
      margin-bottom:18px;
    }

    .form-label{
      display:block;
      font-size:14px;
      font-weight:800;
      color:var(--gescon-text);
      margin-bottom:8px;
    }

    .form-control{
      width:100%;
      height:56px;
      border:2px solid var(--gescon-border);
      border-radius:18px;
      padding:0 18px;
      font-size:15px;
      font-family:'Nunito', sans-serif;
      font-weight:800;
      outline:none;
      color:var(--gescon-text);
      background:#fff;
      transition:.25s ease;
    }

    .form-control::placeholder{
      color:#93a28f;
      font-weight:700;
    }

    .form-control:focus{
      border-color:var(--gescon-green);
      background:#fff;
      box-shadow:0 0 0 4px rgba(22,198,12,.10);
    }

    .form-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      margin:8px 0 24px;
      flex-wrap:wrap;
    }

    .remember{
      display:flex;
      align-items:center;
      gap:10px;
      font-size:14px;
      color:var(--gescon-muted);
      font-weight:800;
    }

    .remember input{
      accent-color:var(--gescon-green);
      width:16px;
      height:16px;
    }

    .forgot{
      font-size:14px;
      color:var(--gescon-green-deep);
      text-decoration:none;
      font-weight:800;
    }

    .forgot:hover{
      color:var(--gescon-green-dark);
    }

    .btn-login{
      width:100%;
      height:58px;
      border:none;
      border-radius:18px;
      background:linear-gradient(135deg, var(--gescon-yellow) 0%, var(--gescon-yellow-dark) 100%);
      color:#433900;
      font-size:17px;
      font-weight:900;
      font-family:'Nunito', sans-serif;
      cursor:pointer;
      transition:.25s ease;
      box-shadow:0 12px 24px rgba(244,197,28,.24);
    }

    .btn-login:hover{
      transform:translateY(-1px);
      box-shadow:0 16px 30px rgba(244,197,28,.28);
    }

    .login-footer{
      margin-top:24px;
      text-align:center;
      font-size:14px;
      color:var(--gescon-muted);
      font-weight:800;
    }

    .login-footer a{
      color:var(--gescon-green-deep);
      text-decoration:none;
      font-weight:900;
    }

    .login-footer a:hover{
      color:var(--gescon-green-dark);
    }

    @media (max-width: 980px){
      .login-wrapper{
        grid-template-columns:1fr;
        max-width:560px;
        min-height:auto;
      }

      .login-brand{
        display:none;
      }

      .mobile-brand{
        display:block;
      }

      .login-form-side{
        padding:40px 24px;
      }

      .login-title{
        font-size:32px;
      }
    }

    @media (max-width: 480px){
      body{
        padding:16px;
      }

      .login-form-side{
        padding:28px 18px;
      }

      .form-control{
        height:52px;
        border-radius:16px;
      }

      .btn-login{
        height:54px;
        border-radius:16px;
      }

      .login-title{
        font-size:28px;
      }

      .brand-name{
        font-size:28px;
      }
    }
  </style>
</head>
<body>

  <div class="login-wrapper">

    <div class="login-brand">
      <div class="brand-content-wrap">
        <div class="brand-top">
          <div class="brand-pill">
            <div class="brand-icon">⌂</div>
            <div>
              <div class="brand-name">GesCon</div>
              <div class="brand-mini">Sistema de gestión claro, moderno y operativo.</div>
            </div>
          </div>
        </div>

        <div class="brand-badge">Gestión de consorcios, alquileres y pagos</div>

        <div class="brand-content">
          <h1>Entrá a una gestión más simple, visual y ordenada.</h1>
          <p>
            Accedé a tu plataforma para administrar propiedades, inquilinos, pagos,
            vencimientos y reportes desde un solo lugar, con una experiencia moderna
            y alineada con la identidad visual de GesCon.
          </p>
        </div>
      </div>

      <div class="brand-card">
        <div class="brand-card-title">Acceso seguro</div>
        <div class="brand-card-text">
          Ingresá con tus credenciales para operar el sistema, consultar movimientos,
          revisar alertas y gestionar toda la información diaria de forma centralizada.
        </div>
      </div>
    </div>

    <div class="login-form-side">
      <div class="login-box">

        <div class="mobile-brand">
          <div class="brand-pill">
            <div class="brand-icon">⌂</div>
            <div>
              <div class="brand-name">GesCon</div>
              <div class="brand-mini">Sistema de gestión claro, moderno y operativo.</div>
            </div>
          </div>
        </div>

        <div class="accent-line"></div>
        <h2 class="login-title">Iniciar sesión</h2>
        <p class="login-subtitle">
          Ingresá tus datos para acceder al sistema.
        </p>

        <?php if($err !== ''): ?>
          <div class="alert-error">
            <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <form action="?action=login" method="POST" autocomplete="on">
          <div class="form-group">
            <label class="form-label">Usuario o email</label>
            <input
              type="text"
              name="usuario"
              class="form-control"
              placeholder="usuario@empresa.com"
              value="<?= htmlspecialchars($email_value, ENT_QUOTES, 'UTF-8'); ?>"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Contraseña</label>
            <input
              type="password"
              name="password"
              class="form-control"
              placeholder="••••••••"
              required
            >
          </div>

          <div class="form-row">
            <label class="remember">
              <input type="checkbox" name="recordarme">
              Recordarme
            </label>

            <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>
          </div>

          <button type="submit" class="btn-login">Ingresar</button>
        </form>

        <div class="login-footer">
          © GesCon · Plataforma de gestión
        </div>
      </div>
    </div>

  </div>

</body>
</html>