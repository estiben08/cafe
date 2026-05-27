<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tantico</title>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --amber:   #C8832A;
      --amber-lt:#E8A84C;
      --cream:   #F5ECD7;
      --dark:    #1A0A02;
      --mid:     #2E1506;
    }

    html, body {
      width: 100%; height: 100%;
      background: var(--dark);
      overflow: hidden;
    }

    /* ── Grain overlay ───────────────────────────────── */
    #grain {
      position: fixed; inset: 0; z-index: 10;
      pointer-events: none;
      opacity: .55;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='1'/%3E%3C/svg%3E");
      background-size: 180px 180px;
      mix-blend-mode: overlay;
    }

    /* ── Fondo con gradiente cálido radial ───────────── */
    #bg {
      position: fixed; inset: 0; z-index: 0;
      background: radial-gradient(ellipse 70% 60% at 50% 60%,
        #3D1A06 0%,
        #1A0A02 100%);
      opacity: 0;
      animation: bgFade 0.6s ease 0.1s forwards;
    }
    @keyframes bgFade { to { opacity: 1; } }

    /* ── Vapor / niebla ──────────────────────────────── */
    #steam-wrap {
      position: fixed; inset: 0; z-index: 1;
      pointer-events: none;
      overflow: hidden;
    }

    .steam {
      position: absolute;
      bottom: 30%;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(200,131,42,0.18) 0%, transparent 70%);
      filter: blur(28px);
      animation: steamRise linear infinite;
      opacity: 0;
    }

    .steam:nth-child(1) { width: 280px; height: 340px; left: 35%; animation-duration: 4.2s; animation-delay: 0.3s; }
    .steam:nth-child(2) { width: 200px; height: 260px; left: 48%; animation-duration: 3.8s; animation-delay: 0.9s; }
    .steam:nth-child(3) { width: 240px; height: 300px; left: 27%; animation-duration: 5s;   animation-delay: 0.0s; }
    .steam:nth-child(4) { width: 180px; height: 220px; left: 55%; animation-duration: 4.5s; animation-delay: 1.4s; }

    @keyframes steamRise {
      0%   { transform: translateY(0)   scaleX(1);   opacity: 0; }
      10%  { opacity: 1; }
      80%  { opacity: .6; }
      100% { transform: translateY(-85vh) scaleX(1.4); opacity: 0; }
    }

    /* ── Línea horizontal decorativa ────────────────── */
    #line-top, #line-bot {
      position: fixed; left: 50%; z-index: 3;
      height: 1px;
      background: linear-gradient(90deg, transparent, var(--amber), transparent);
      transform: translateX(-50%) scaleX(0);
      transform-origin: center;
      opacity: 0;
    }
    #line-top { top: 22%; width: 320px; animation: lineIn 0.7s cubic-bezier(0.22,1,0.36,1) 1.0s forwards; }
    #line-bot { bottom: 22%; width: 200px; animation: lineIn 0.7s cubic-bezier(0.22,1,0.36,1) 1.15s forwards; }
    @keyframes lineIn {
      to { transform: translateX(-50%) scaleX(1); opacity: 0.5; }
    }

    /* ── Wordmark pequeño sobre el logo ─────────────── */
    #wordmark {
      position: fixed; top: calc(50% - 110px); left: 50%;
      transform: translateX(-50%);
      z-index: 4;
      font-family: 'Cormorant Garamond', serif;
      font-size: 11px; font-weight: 400;
      letter-spacing: 0.42em; text-transform: uppercase;
      color: var(--amber-lt);
      opacity: 0;
      animation: fadeUp 0.8s ease 1.25s forwards;
    }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateX(-50%) translateY(8px); }
      to   { opacity: 0.75; transform: translateX(-50%) translateY(0); }
    }

    /* ── Logo central ────────────────────────────────── */
    #logo-wrap {
      position: fixed; top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      z-index: 4;
    }

#logo-img {
  width: 200px;
  display: block;
  opacity: 0;
  filter: brightness(0) invert(1);
  transform: scale(0.92);
  animation: logoReveal 1.1s cubic-bezier(0.16, 1, 0.3, 1) 0.55s forwards;
}
@keyframes logoReveal {
  0%   { opacity: 0; transform: scale(0.88); filter: brightness(0) invert(1) blur(6px); }
  60%  { opacity: 1; filter: brightness(0) invert(1) blur(0px); }
  100% { opacity: 1; transform: scale(1);    filter: brightness(0) invert(1) blur(0px); }
}

    /* ── Tagline debajo del logo ─────────────────────── */
    #tagline {
      position: fixed; top: calc(50% + 85px); left: 50%;
      transform: translateX(-50%);
      z-index: 4;
      font-family: 'Cormorant Garamond', serif;
      font-size: 13px; font-weight: 300;
      font-style: italic;
      letter-spacing: 0.18em;
      color: var(--cream);
      white-space: nowrap;
      opacity: 0;
      animation: fadeUp 0.9s ease 1.4s forwards;
    }

    /* ── Puntos decorativos ──────────────────────────── */
    .dot-dec {
      position: fixed; z-index: 3;
      width: 3px; height: 3px; border-radius: 50%;
      background: var(--amber);
      opacity: 0;
      animation: dotPop 0.4s ease forwards;
    }
    #dot1 { top: 22%; left: calc(50% - 168px); animation-delay: 1.0s; }
    #dot2 { top: 22%; left: calc(50% + 165px); animation-delay: 1.05s; }
    #dot3 { bottom: 22%; left: calc(50% - 108px); animation-delay: 1.15s; }
    #dot4 { bottom: 22%; left: calc(50% + 105px); animation-delay: 1.2s; }
    @keyframes dotPop {
      0%   { opacity: 0; transform: scale(0); }
      70%  { opacity: 0.9; transform: scale(1.4); }
      100% { opacity: 0.5; transform: scale(1); }
    }

    /* ── Fade out final ──────────────────────────────── */
    #curtain {
      position: fixed; inset: 0; z-index: 20;
      background: var(--dark);
      opacity: 0;
      pointer-events: none;
      animation: curtainDrop 0.55s ease 2.45s forwards;
    }
    @keyframes curtainDrop {
      to { opacity: 1; }
    }
  </style>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300&display=swap" rel="stylesheet">
</head>
<body>

  <div id="grain"></div>
  <div id="bg"></div>

  <div id="steam-wrap">
    <div class="steam"></div>
    <div class="steam"></div>
    <div class="steam"></div>
    <div class="steam"></div>
  </div>

  <div id="line-top"></div>
  <div id="line-bot"></div>

  <div class="dot-dec" id="dot1"></div>
  <div class="dot-dec" id="dot2"></div>
  <div class="dot-dec" id="dot3"></div>
  <div class="dot-dec" id="dot4"></div>

  <div id="wordmark">Café &amp; Espacio</div>

  <div id="logo-wrap">
    <img id="logo-img" src="../assets/imagenes/tantico.png" alt="Tantico Logo">
  </div>

  <div id="tagline">El sabor que te espera</div>

  <div id="curtain"></div>

  <script>
  (function () {
    var KEY = 'tn_session_active';
    sessionStorage.setItem(KEY, '1');

    // Redirigir al index cuando termina la animación (2.45s fade + 0.55s = 3s total)
    setTimeout(function () {
      window.location.replace('../index.php');
    }, 3000);
  })();
  </script>

</body>
</html>