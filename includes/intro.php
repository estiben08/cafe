<?php
/*
 * includes/intro.php
 * Overlay de Introducción Animada Tantico
 * Se reproduce de forma fluida y cinematográfica cada vez que se entra a la página,
 * y al terminar da paso automático al Anuncio / Modal Popup.
 */
$isRoot = !str_contains($_SERVER['PHP_SELF'] ?? '', '/includes/');
$introBasePath = $isRoot ? '' : '../';
?>
<style>
  #tantico-intro-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: #1A0A02;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', sans-serif;
    user-select: none;
    transition: opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1);
  }

  #tantico-intro-overlay.fade-out {
    opacity: 0;
    pointer-events: none;
  }

  /* ── Grain overlay ───────────────────────────────── */
  #intro-grain {
    position: absolute;
    inset: 0;
    z-index: 10;
    pointer-events: none;
    opacity: .55;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='1'/%3E%3C/svg%3E");
    background-size: 180px 180px;
    mix-blend-mode: overlay;
  }

  /* ── Fondo con gradiente cálido radial ───────────── */
  #intro-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    background: radial-gradient(ellipse 70% 60% at 50% 60%, #3D1A06 0%, #1A0A02 100%);
    opacity: 0;
    animation: introBgFade 0.6s ease 0.1s forwards;
  }
  @keyframes introBgFade { to { opacity: 1; } }

  /* ── Vapor / niebla ──────────────────────────────── */
  #intro-steam-wrap {
    position: absolute;
    inset: 0;
    z-index: 1;
    pointer-events: none;
    overflow: hidden;
  }

  .intro-steam {
    position: absolute;
    bottom: 25%;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(200,131,42,0.2) 0%, transparent 70%);
    filter: blur(30px);
    animation: introSteamRise linear infinite;
    opacity: 0;
  }

  .intro-steam:nth-child(1) { width: 280px; height: 340px; left: 35%; animation-duration: 4.2s; animation-delay: 0.2s; }
  .intro-steam:nth-child(2) { width: 200px; height: 260px; left: 48%; animation-duration: 3.8s; animation-delay: 0.6s; }
  .intro-steam:nth-child(3) { width: 240px; height: 300px; left: 27%; animation-duration: 5s;   animation-delay: 0.0s; }
  .intro-steam:nth-child(4) { width: 180px; height: 220px; left: 55%; animation-duration: 4.5s; animation-delay: 1.0s; }

  @keyframes introSteamRise {
    0%   { transform: translateY(0) scaleX(1); opacity: 0; }
    15%  { opacity: 1; }
    80%  { opacity: .6; }
    100% { transform: translateY(-85vh) scaleX(1.4); opacity: 0; }
  }

  /* ── Líneas horizontales decorativas ────────────── */
  #intro-line-top, #intro-line-bot {
    position: absolute;
    left: 50%;
    z-index: 3;
    height: 1px;
    background: linear-gradient(90deg, transparent, #C8832A, transparent);
    transform: translateX(-50%) scaleX(0);
    transform-origin: center;
    opacity: 0;
  }
  #intro-line-top { top: 20%; width: 320px; animation: introLineIn 0.7s cubic-bezier(0.22,1,0.36,1) 0.8s forwards; }
  #intro-line-bot { bottom: 20%; width: 200px; animation: introLineIn 0.7s cubic-bezier(0.22,1,0.36,1) 0.95s forwards; }
  @keyframes introLineIn {
    to { transform: translateX(-50%) scaleX(1); opacity: 0.5; }
  }

  /* ── Wordmark superior ───────────────────────────── */
  #intro-wordmark {
    position: absolute;
    top: calc(50% - 115px);
    left: 50%;
    transform: translateX(-50%);
    z-index: 4;
    font-family: 'Inter', sans-serif;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: #E8A84C;
    opacity: 0;
    animation: introFadeUp 0.8s ease 1.0s forwards;
  }
  @keyframes introFadeUp {
    from { opacity: 0; transform: translateX(-50%) translateY(8px); }
    to   { opacity: 0.85; transform: translateX(-50%) translateY(0); }
  }

  /* ── Logo central ────────────────────────────────── */
  #intro-logo-wrap {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 4;
  }

  #intro-logo-img {
    width: 210px;
    max-width: 75vw;
    display: block;
    opacity: 0;
    filter: brightness(0) invert(1);
    transform: scale(0.92);
    animation: introLogoReveal 1.1s cubic-bezier(0.16, 1, 0.3, 1) 0.4s forwards;
  }
  @keyframes introLogoReveal {
    0%   { opacity: 0; transform: scale(0.88); filter: brightness(0) invert(1) blur(8px); }
    60%  { opacity: 1; filter: brightness(0) invert(1) blur(0px); }
    100% { opacity: 1; transform: scale(1);    filter: brightness(0) invert(1) blur(0px); }
  }

  /* ── Tagline inferior ────────────────────────────── */
  #intro-tagline {
    position: absolute;
    top: calc(50% + 90px);
    left: 50%;
    transform: translateX(-50%);
    z-index: 4;
    font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif;
    font-size: 17px;
    font-weight: 400;
    font-style: italic;
    letter-spacing: 0.12em;
    color: #F5ECD7;
    white-space: nowrap;
    opacity: 0;
    animation: introFadeUp 0.9s ease 1.15s forwards;
  }

  /* ── Puntos decorativos ──────────────────────────── */
  .intro-dot-dec {
    position: absolute;
    z-index: 3;
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: #C8832A;
    opacity: 0;
    animation: introDotPop 0.4s ease forwards;
  }
  #intro-dot1 { top: 20%; left: calc(50% - 168px); animation-delay: 0.8s; }
  #intro-dot2 { top: 20%; left: calc(50% + 165px); animation-delay: 0.85s; }
  #intro-dot3 { bottom: 20%; left: calc(50% - 108px); animation-delay: 0.95s; }
  #intro-dot4 { bottom: 20%; left: calc(50% + 105px); animation-delay: 1.0s; }
  @keyframes introDotPop {
    0%   { opacity: 0; transform: scale(0); }
    70%  { opacity: 0.9; transform: scale(1.4); }
    100% { opacity: 0.5; transform: scale(1); }
  }

  @media (max-width: 480px) {
    #intro-logo-img {
      width: 160px;
      max-width: 65vw;
    }
    #intro-wordmark {
      font-size: 9.5px;
      letter-spacing: 0.26em;
      top: calc(50% - 88px);
    }
    #intro-tagline {
      font-size: 14px;
      top: calc(50% + 72px);
      letter-spacing: 0.08em;
    }
    #intro-line-top { width: 220px; }
    #intro-line-bot { width: 140px; }
    #intro-dot1 { left: calc(50% - 115px); }
    #intro-dot2 { left: calc(50% + 112px); }
    #intro-dot3 { left: calc(50% - 75px); }
    #intro-dot4 { left: calc(50% + 72px); }
  }
</style>

<!-- Overlay Intro Contenedor -->
<div id="tantico-intro-overlay">
  <div id="intro-grain"></div>
  <div id="intro-bg"></div>

  <div id="intro-steam-wrap">
    <div class="intro-steam"></div>
    <div class="intro-steam"></div>
    <div class="intro-steam"></div>
    <div class="intro-steam"></div>
  </div>

  <div id="intro-line-top"></div>
  <div id="intro-line-bot"></div>

  <div class="intro-dot-dec" id="intro-dot1"></div>
  <div class="intro-dot-dec" id="intro-dot2"></div>
  <div class="intro-dot-dec" id="intro-dot3"></div>
  <div class="intro-dot-dec" id="intro-dot4"></div>

  <div id="intro-wordmark">Café &amp; Espacio</div>

  <div id="intro-logo-wrap">
    <img id="intro-logo-img" src="<?= $introBasePath ?>assets/imagenes/tantico.png" alt="Tantico Logo">
  </div>

  <div id="intro-tagline">El sabor que te espera</div>
</div>

<script>
(function () {
  var intro = document.getElementById('tantico-intro-overlay');
  if (!intro) return;

  var terminado = false;

  function finalizarIntro() {
    if (terminado) return;
    terminado = true;
    intro.classList.add('fade-out');

    setTimeout(function () {
      intro.remove();
      // Notificar al sistema para abrir el modal del anuncio sin demoras
      window.dispatchEvent(new CustomEvent('tantico-intro-complete'));
    }, 560);
  }

  // Termina automáticamente a los 2.3 segundos
  var timerIntro = setTimeout(finalizarIntro, 2300);

  // Permitir saltar con click o tecla si el usuario lo desea
  intro.addEventListener('click', function () {
    clearTimeout(timerIntro);
    finalizarIntro();
  });
})();
</script>