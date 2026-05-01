<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingresar - CoffeeCol</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>

  <a href="../index.php" class="btn-volver">
    <i class="fa-solid fa-arrow-left"></i> Volver
  </a>

  <div class="login-wrapper">
    <div class="login-box">

      <img src="../assets/imagenes/banner20.png" alt="CoffeeCol" class="login-logo">
      <h2>Bienvenido</h2>
      <p class="login-sub">Ingresa o crea tu cuenta</p>

      <!-- Paso 1: Email -->
      <div id="paso-email">
        <input type="email" id="email" placeholder="correo@ejemplo.com" class="form-control mb-3">
        <button id="btn-magic" class="btn-login w-100">
          <i class="fa-solid fa-paper-plane me-2"></i>Enviar enlace de acceso
        </button>
        <p id="msg-magic" class="msg-feedback"></p>

        <div class="divider"><span>o</span></div>

        <button id="btn-google" class="btn-google w-100">
          <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="20" alt="Google">
          Continuar con Google
        </button>
      </div>

    </div>
  </div>

  <script type="module">
    import { loginGoogle, enviarMagicLink, completarMagicLink } from '../js/auth.js';

    // Completar magic link si viene de correo
    completarMagicLink();

    // Google
    document.getElementById('btn-google').addEventListener('click', async () => {
      const res = await loginGoogle();
      if (res?.error) {
        document.getElementById('msg-magic').textContent = '❌ ' + res.error;
      }
    });

    // Magic Link
    document.getElementById('btn-magic').addEventListener('click', async () => {
      const email = document.getElementById('email').value.trim();
      const msg   = document.getElementById('msg-magic');

      if (!email) {
        msg.textContent = '⚠️ Escribe tu correo primero.';
        return;
      }

      msg.textContent = 'Enviando...';
      const res = await enviarMagicLink(email);

      if (res.ok) {
        msg.textContent = '✅ Enlace enviado, revisa tu correo.';
        document.getElementById('btn-magic').disabled = true;
      } else {
        msg.textContent = '❌ ' + res.error;
      }
    });
  </script>

</body>
</html>