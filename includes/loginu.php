<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingresar - Tantico</title>

  <!-- Content Security Policy -->
  <meta http-equiv="Content-Security-Policy" content="
    default-src 'self';
    script-src  'self' 'unsafe-inline' https://www.gstatic.com https://apis.google.com;
    style-src   'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com;
    font-src    'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com;
    img-src     'self' https://www.gstatic.com data:;
    connect-src 'self' https://*.googleapis.com https://*.firebaseio.com https://identitytoolkit.googleapis.com;
    frame-src   https://cafetantico.firebaseapp.com https://accounts.google.com;
  ">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Goudy+Bookletter+1911&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../csss/login.css">
  <link rel="icon" href="../assets/imagenes/banner1.png" type="image/x-icon">
</head>
<body>

<a href="/cafe/index.php" class="btn-volver">
  <i class="fa-solid fa-arrow-left"></i> Volver
</a>

<div class="login-wrapper">
  <div class="login-box">

    <div class="login-logo-wrap">
      <img src="/cafe/assets/imagenes/banner20.png" alt="Tantico" class="login-logo">
    </div>

    <h2>Bienvenidos a Tantico</h2>
    <p class="login-sub">Ingresa o crea tu cuenta</p>

    <!-- Mensaje de error desde URL (ej: magic link fallido) -->
    <?php
      $error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : '';
    ?>
    <?php if ($error): ?>
      <div class="alert alert-warning" role="alert">
        ⚠️ <?= $error ?>
      </div>
    <?php endif; ?>

    <div id="paso-email">
      <label class="field-label" for="email">Correo electrónico</label>
      <input
        type="email"
        id="email"
        name="email"
        placeholder="correo@ejemplo.com"
        class="form-control mb-3"
        autocomplete="email"
        required
        maxlength="254"
      >

      <button id="btn-magic" class="btn-login w-100" type="button">
        <i class="fa-solid fa-paper-plane"></i> Enviar enlace de acceso
      </button>
      <p id="msg-magic" class="msg-feedback" role="alert" aria-live="polite"></p>

      <div class="divider"><span>o</span></div>

      <button id="btn-google" class="btn-google w-100" type="button">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="18" alt="">
        Continuar con Google
      </button>

      <p class="login-footer">
        Al continuar aceptas nuestros <a href="/cafe/terminos.php">Términos de uso</a>
        y <a href="/cafe/privacidad.php">Política de privacidad</a>
      </p>
    </div>

  </div>
</div>

<script type="module">
  import { loginGoogle, enviarMagicLink, completarMagicLink } from '/cafe/js/auth.js?v=2';

  // Completar magic link si viene de correo
  try {
    await completarMagicLink();
  } catch (e) {
    // Error ya manejado dentro de completarMagicLink()
  }

  // Google
  document.getElementById('btn-google').addEventListener('click', async () => {
    const btn = document.getElementById('btn-google');
    const msg = document.getElementById('msg-magic');
    btn.disabled = true;
    btn.textContent = 'Conectando...';

    const res = await loginGoogle();

    if (res?.error) {
      msg.textContent = '❌ ' + res.error;
      btn.disabled = false;
      btn.innerHTML = '<img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="18" alt=""> Continuar con Google';
    }
  });

  // Magic Link
  document.getElementById('btn-magic').addEventListener('click', async () => {
    const email = document.getElementById('email').value.trim();
    const msg   = document.getElementById('msg-magic');
    const btn   = document.getElementById('btn-magic');

    msg.textContent = '';

    if (!email) {
      msg.textContent = '⚠️ Escribe tu correo primero.';
      document.getElementById('email').focus();
      return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enviando...';

    const res = await enviarMagicLink(email);

    if (res.ok) {
      msg.textContent = '✅ Enlace enviado, revisa tu correo (también el spam).';
    } else {
      msg.textContent = '❌ ' + res.error;
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Enviar enlace de acceso';
    }
  });
</script>

</body>
</html>