<?php
/*
 * includes/anuuncio.php
 * Modal Dinámico de Anuncios y Popups Tantico
 * Gestionable al 100% desde el Panel de Administración (admin/dashboard.php).
 * Se muestra UNA sola vez por sesión de pestaña si está activo en la base de datos.
 */

$anuncio = null;
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=coffeecol;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $stmt = $pdo->query("SELECT * FROM anuncios WHERE activo = 1 ORDER BY id DESC LIMIT 1");
    $anuncio = $stmt->fetch();
} catch (Exception $e) {
    $anuncio = null;
}

// Si no hay anuncio activo en el sistema, no renderizar nada
if (!$anuncio) {
    return;
}

$isRoot = !str_contains($_SERVER['PHP_SELF'] ?? '', '/includes/');
$basePath = $isRoot ? '' : '../';

$titulo             = $anuncio['titulo'];
$subtitulo          = $anuncio['subtitulo'];
$eyebrow            = $anuncio['eyebrow'] ?: 'Transmisión en vivo · Neiva, Huila';
$badge_texto        = $anuncio['badge_texto'] ?: 'Café Región · Neiva, Huila';
$imagen_rel         = $anuncio['imagen'] ?: 'assets/imagenes/tantoooo.png';
$imagen_src         = (str_starts_with($imagen_rel, 'http') || str_starts_with($imagen_rel, 'data:')) ? $imagen_rel : $basePath . ltrim($imagen_rel, '/');
$mostrar_partido    = (int)$anuncio['mostrar_partido'] === 1;
$fecha_evento_texto = $anuncio['fecha_evento_texto'] ?: 'Próximo Encuentro';
$fecha_objetivo     = $anuncio['fecha_objetivo'] ?: '2026-06-01 18:00:00';
$equipo1_nombre     = $anuncio['equipo1_nombre'] ?: 'Colombia';
$equipo1_bandera    = $anuncio['equipo1_bandera'] ?: 'co';
$equipo2_nombre     = $anuncio['equipo2_nombre'] ?: 'Costa Rica';
$equipo2_bandera    = $anuncio['equipo2_bandera'] ?: 'cr';

$feat1_icono = $anuncio['feat1_icono'] ?: 'fa-solid fa-tv';
$feat1_texto = $anuncio['feat1_texto'] ?: 'Pantallas<br>4K';
$feat2_icono = $anuncio['feat2_icono'] ?: 'fa-solid fa-users';
$feat2_texto = $anuncio['feat2_texto'] ?: 'Ambiente<br>futbolero';
$feat3_icono = $anuncio['feat3_icono'] ?: 'fa-brands fa-java';
$feat3_texto = $anuncio['feat3_texto'] ?: 'Bebidas<br>premium';
$boton_texto = $anuncio['boton_texto'] ?? '';
$boton_enlace = $anuncio['boton_enlace'] ?? '';

$btn_href = $boton_enlace ?: '#';
if ($btn_href !== '#' && !str_starts_with($btn_href, 'http') && !str_starts_with($btn_href, '/') && !str_starts_with($btn_href, '#')) {
    $btn_href = $basePath . $btn_href;
}

$banderas_gradientes = [
    'co' => 'linear-gradient(to bottom, #FCD116 0%, #FCD116 50%, #003893 50%, #003893 75%, #CE1126 75%, #CE1126 100%)',
    'cr' => 'linear-gradient(to bottom, #002B7F 0%, #002B7F 20%, #fff 20%, #fff 40%, #CE1126 40%, #CE1126 60%, #fff 60%, #fff 80%, #002B7F 80%, #002B7F 100%)',
    'ar' => 'linear-gradient(to bottom, #74ACDF 0%, #74ACDF 33%, #fff 33%, #fff 66%, #74ACDF 66%, #74ACDF 100%)',
    'br' => 'linear-gradient(to bottom, #009C3B 0%, #009C3B 100%)',
    'es' => 'linear-gradient(to bottom, #AA151B 0%, #AA151B 25%, #F1BF00 25%, #F1BF00 75%, #AA151B 75%, #AA151B 100%)',
    'us' => 'linear-gradient(to bottom, #B22234 0%, #B22234 50%, #3C3B6E 50%, #3C3B6E 100%)',
    'mx' => 'linear-gradient(to right, #006847 0%, #006847 33%, #fff 33%, #fff 66%, #CE1126 66%, #CE1126 100%)',
    'de' => 'linear-gradient(to bottom, #000 0%, #000 33%, #D00 33%, #D00 66%, #FFCE00 66%, #FFCE00 100%)',
    'pe' => 'linear-gradient(to right, #D91023 0%, #D91023 33%, #fff 33%, #fff 66%, #D91023 66%, #D91023 100%)',
    'uy' => 'linear-gradient(to bottom, #fff 0%, #fff 20%, #0038A8 20%, #0038A8 40%, #fff 40%, #fff 60%, #0038A8 60%, #0038A8 80%, #fff 80%, #fff 100%)',
    'cl' => 'linear-gradient(to bottom, #0039A6 0%, #0039A6 50%, #D52B1E 50%, #D52B1E 100%)',
    'ec' => 'linear-gradient(to bottom, #FFDD00 0%, #FFDD00 50%, #034EA2 50%, #034EA2 75%, #ED1C24 75%, #ED1C24 100%)'
];

$flag1_style = $banderas_gradientes[$equipo1_bandera] ?? $banderas_gradientes['co'];
$flag2_style = $banderas_gradientes[$equipo2_bandera] ?? $banderas_gradientes['cr'];
?>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg-modal:  #28040A;
    --gold:      #D4A843;
    --gold-lt:   #F0C96A;
    --cream:     #FCF6DB;
    --overlay-c: rgba(0, 0, 0, 0.84);
  }

  /* Oculto por defecto — el JS lo muestra si corresponde */
  #tantico-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 9999;
    background: var(--overlay-c);
    backdrop-filter: blur(16px) saturate(1.6);
    -webkit-backdrop-filter: blur(16px) saturate(1.6);
    align-items: center; justify-content: center;
    padding: 1rem;
    font-family: 'Inter', sans-serif;
  }
  #tantico-overlay.t-visible {
    display: flex;
    animation: tOverlayIn 0.4s ease forwards;
  }
  @keyframes tOverlayIn { from { opacity: 0; } to { opacity: 1; } }

  #tantico-modal {
    display: flex;
    width: 100%; max-width: 660px;
    max-height: 94vh;
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    border-radius: 22px;
    border: 1px solid rgba(212,168,67,0.22);
    box-shadow: 0 0 0 1px rgba(212,168,67,0.06), 0 30px 80px rgba(0,0,0,0.85);
    transform: scale(0.9) translateY(20px);
    opacity: 0;
    animation: tModalIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) 0.08s forwards;
    position: relative;
    background: var(--bg-modal);
  }
  @keyframes tModalIn {
    to { transform: scale(1) translateY(0); opacity: 1; }
  }

  .t-left {
    flex: 1;
    background: var(--bg-modal);
    padding: 28px 24px 24px;
    display: flex; flex-direction: column; gap: 0;
    position: relative; overflow: hidden;
    min-width: 0;
  }

  .t-stripe {
    height: 4px;
    background: linear-gradient(90deg,
      #FCD116 0%, #FCD116 50%,
      #003893 50%, #003893 75%,
      #CE1126 75%, #CE1126 100%);
    border-radius: 2px;
    margin-bottom: 16px;
  }

  .t-eyebrow {
    font-size: 10.5px; font-weight: 700;
    letter-spacing: .12em; text-transform: uppercase;
    color: var(--gold);
    display: flex; align-items: center; gap: 7px;
    margin-bottom: 10px;
  }
  .t-live-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #2ECC71; box-shadow: 0 0 6px #2ECC71;
    animation: tBlink 1.4s ease-in-out infinite; flex-shrink: 0;
  }
  @keyframes tBlink {
    0%,100% { opacity: 1; transform: scale(1); }
    50%      { opacity: .4; transform: scale(.7); }
  }

  .t-title {
    font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif;
    font-size: clamp(19px, 3.2vw, 24px);
    font-weight: 400; color: var(--cream);
    line-height: 1.25; margin-bottom: 8px;
    word-break: break-word;
  }
  .t-title em { font-style: italic; color: var(--gold-lt); }

  .t-sub {
    font-size: 12px; font-weight: 400;
    color: rgba(255, 255, 255, 0.88);
    line-height: 1.5; margin-bottom: 14px;
  }

  .t-feat-bar {
    display: flex;
    border: 1px solid rgba(212,168,67,0.22);
    border-radius: 12px; overflow: hidden;
    background: rgba(212,168,67,0.05);
    margin-bottom: 14px;
  }
  .t-feat-item {
    flex: 1;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 10px 4px 8px; gap: 5px;
    position: relative;
    transition: background .2s; cursor: default;
    min-width: 0;
  }
  .t-feat-item:hover { background: rgba(212,168,67,0.09); }
  .t-feat-item + .t-feat-item::before {
    content: '';
    position: absolute; left: 0; top: 16%; height: 68%; width: 1px;
    background: rgba(212,168,67,0.2);
  }
  .t-feat-item i { font-size: 18px; color: var(--gold); line-height: 1; }
  .t-feat-label {
    font-size: 8.5px; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: rgba(212,168,67,0.9);
    text-align: center; line-height: 1.25;
    word-break: break-word;
  }

  .t-partido-block {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(212,168,67,0.18);
    border-radius: 12px;
    padding: 12px 14px 10px;
    display: flex; flex-direction: column; gap: 8px;
  }
  .t-partido-fecha {
    font-size: 9.5px; font-weight: 700;
    letter-spacing: .12em; text-transform: uppercase;
    color: var(--gold); text-align: center;
  }
  .t-partido-matchup {
    display: flex; align-items: center;
    justify-content: center; gap: 8px;
  }
  .t-team-flag {
    width: 34px; height: 24px; border-radius: 4px;
    overflow: hidden; display: flex; flex-direction: column;
    flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.5);
  }
  .t-team-info { display: flex; flex-direction: column; align-items: center; gap: 3px; min-width: 55px; }
  .t-team-name {
    font-size: 9.5px; font-weight: 700;
    letter-spacing: .05em; text-transform: uppercase;
    color: var(--cream); text-align: center;
  }
  .t-vs-badge {
    font-family: 'Playfair Display', serif;
    font-size: 12px; font-weight: 900;
    color: rgba(212,168,67,0.5); flex-shrink: 0;
  }

  .t-countdown-label {
    font-size: 8.5px; font-weight: 700;
    letter-spacing: .12em; text-transform: uppercase;
    color: var(--gold); text-align: center; margin-top: 1px;
  }
  .t-countdown { display: flex; justify-content: center; gap: 5px; }
  .t-cd-unit { display: flex; flex-direction: column; align-items: center; gap: 2px; }
  .t-cd-num {
    font-family: 'Playfair Display', serif;
    font-size: 18px; font-weight: 900; color: var(--gold-lt);
    line-height: 1; background: rgba(212,168,67,0.08);
    border: 1px solid rgba(212,168,67,0.2);
    border-radius: 6px; padding: 3px 6px;
    min-width: 38px; text-align: center;
  }
  .t-cd-lbl {
    font-size: 7.5px; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: rgba(252,246,219,0.4);
  }
  .t-cd-sep {
    font-family: 'Playfair Display', serif;
    font-size: 16px; font-weight: 900;
    color: rgba(212,168,67,0.3);
    align-self: flex-start; margin-top: 3px;
  }

  .t-right {
    width: 270px; flex-shrink: 0;
    position: relative; overflow: hidden;
    background: #1a0305;
  }
  .t-right img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: center top;
    display: block; min-height: 440px;
  }
  .t-right-overlay {
    position: absolute; inset: 0;
    background:
      linear-gradient(to right, rgba(40,4,10,0.45) 0%, transparent 45%),
      linear-gradient(to top, rgba(40,4,10,0.65) 0%, transparent 45%);
    pointer-events: none;
  }

  .t-badge-inner {
    background: rgba(40,4,10,0.78);
    border: 1px solid rgba(212,168,67,0.32);
    border-radius: 20px; padding: 4px 12px;
    backdrop-filter: blur(6px);
  }
  .t-badge-text {
    font-size: 8.5px; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: rgba(212,168,67,0.92); text-align: center;
  }

  .t-right-badge {
    position: absolute; bottom: 16px; left: 0; right: 0;
    display: flex; justify-content: center; pointer-events: none;
    z-index: 5;
  }
  .t-left-badge {
    display: none;
    justify-content: center;
    margin-top: 12px;
  }

  #t-btn-close {
    position: absolute; top: 12px; right: 12px; z-index: 30;
    width: 32px; height: 32px; border-radius: 50%;
    border: 1px solid rgba(252,246,219,.25);
    background: rgba(28,4,10,.75);
    color: rgba(252,246,219,.85);
    font-size: 13px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
    transition: background .2s, color .2s, transform .2s;
  }
  #t-btn-close:hover {
    background: rgba(212,168,67,.25);
    color: var(--gold-lt);
    transform: scale(1.08);
  }

  /* ── ADAPTACIÓN MÓVIL (< 580px) ── */
  @media (max-width: 580px) {
    #tantico-overlay {
      padding: 10px 8px;
    }
    #tantico-modal {
      flex-direction: column;
      max-width: 440px;
      border-radius: 18px;
      max-height: 92vh;
    }
    .t-feat-bar {
      background: rgba(58, 2, 8, 0.76);
      border-color: rgba(212, 168, 67, 0.30);
      margin-bottom: 10px;
    }
    .t-partido-block {
      background: rgba(58, 2, 8, 0.65);
      border-color: rgba(212, 168, 67, 0.30);
    }
    .t-right {
      position: absolute; inset: 0;
      width: 100%; height: 100%; z-index: 0;
      opacity: 0.35; pointer-events: none;
    }
    .t-right img { min-height: unset; height: 100%; object-position: center 20%; }
    .t-left {
      position: relative; z-index: 2;
      padding: 20px 15px 16px;
      background: rgba(28, 4, 10, 0.94);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }
    .t-title {
      font-size: clamp(17px, 4.8vw, 21px);
      margin-bottom: 6px;
    }
    .t-sub {
      font-size: 11.5px;
      margin-bottom: 10px;
    }
    .t-feat-item {
      padding: 7px 2px 6px;
    }
    .t-feat-item i {
      font-size: 16px;
    }
    .t-feat-label {
      font-size: 7.5px;
    }
    .t-cd-num {
      font-size: 16px;
      min-width: 34px;
      padding: 3px 4px;
    }
    .t-cd-lbl {
      font-size: 7px;
    }
    .t-right-badge { display: none; }
    .t-left-badge  { display: flex; }
    #t-btn-close {
      width: 34px; height: 34px;
      top: 10px; right: 10px;
      font-size: 13px;
    }
  }

  @media (max-width: 380px) {
    .t-left {
      padding: 16px 12px 14px;
    }
    .t-title {
      font-size: 16px;
    }
    .t-sub {
      font-size: 11px;
      margin-bottom: 8px;
    }
    .t-feat-item {
      padding: 6px 1px 5px;
    }
    .t-feat-item i {
      font-size: 14px;
    }
    .t-feat-label {
      font-size: 7px;
    }
    .t-cd-num {
      font-size: 14px;
      min-width: 28px;
      padding: 2px 2px;
    }
    .t-cd-lbl {
      font-size: 6.5px;
    }
    .t-cd-sep {
      font-size: 13px;
    }
  }
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Goudy+Bookletter+1911&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

<!-- El overlay empieza con display:none — el JS lo activa si corresponde -->
<div id="tantico-overlay">
  <div id="tantico-modal" role="dialog" aria-modal="true" aria-label="Modal Tantico — Anuncio">

    <button id="t-btn-close" aria-label="Cerrar"><i class="fa-solid fa-x"></i></button>

    <div class="t-left">
      <div class="t-stripe"></div>
      <div class="t-eyebrow">
        <span class="t-live-dot"></span>
        <?= htmlspecialchars($eyebrow) ?>
      </div>
      <div class="t-title">
        <?= $titulo ?>
      </div>
      <p class="t-sub">
        <?= nl2br(htmlspecialchars($subtitulo)) ?>
      </p>
      <div class="t-feat-bar">
        <div class="t-feat-item">
          <i class="<?= htmlspecialchars($feat1_icono) ?>"></i>
          <div class="t-feat-label"><?= $feat1_texto ?></div>
        </div>
        <div class="t-feat-item">
          <i class="<?= htmlspecialchars($feat2_icono) ?>"></i>
          <div class="t-feat-label"><?= $feat2_texto ?></div>
        </div>
        <div class="t-feat-item">
          <i class="<?= htmlspecialchars($feat3_icono) ?>"></i>
          <div class="t-feat-label"><?= $feat3_texto ?></div>
        </div>
      </div>

      <?php if ($mostrar_partido): ?>
      <div class="t-partido-block">
        <div class="t-partido-fecha"><?= htmlspecialchars($fecha_evento_texto) ?></div>
        <div class="t-partido-matchup">
          <div class="t-team-info">
            <div class="t-team-flag" style="background: <?= $flag1_style ?>;"></div>
            <div class="t-team-name"><?= htmlspecialchars($equipo1_nombre) ?></div>
          </div>
          <div class="t-vs-badge">VS</div>
          <div class="t-team-info">
            <div class="t-team-flag" style="background: <?= $flag2_style ?>;"></div>
            <div class="t-team-name"><?= htmlspecialchars($equipo2_nombre) ?></div>
          </div>
        </div>
        <div class="t-countdown-label">Faltan</div>
        <div class="t-countdown">
          <div class="t-cd-unit">
            <div class="t-cd-num" id="cd-dias">00</div>
            <div class="t-cd-lbl">Días</div>
          </div>
          <div class="t-cd-sep">:</div>
          <div class="t-cd-unit">
            <div class="t-cd-num" id="cd-horas">00</div>
            <div class="t-cd-lbl">Horas</div>
          </div>
          <div class="t-cd-sep">:</div>
          <div class="t-cd-unit">
            <div class="t-cd-num" id="cd-mins">00</div>
            <div class="t-cd-lbl">Mins</div>
          </div>
          <div class="t-cd-sep">:</div>
          <div class="t-cd-unit">
            <div class="t-cd-num" id="cd-segs">00</div>
            <div class="t-cd-lbl">Segs</div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if (!$mostrar_partido && !empty($boton_texto)): ?>
      <div style="margin-top: 10px; text-align: center;">
        <a href="<?= htmlspecialchars($btn_href) ?>" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #D4A843 0%, #F0C96A 100%); color: #28040A; font-weight: 700; font-size: 11.5px; padding: 10px 22px; border-radius: 25px; text-decoration: none; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(212,168,67,0.35); transition: transform 0.2s, box-shadow 0.2s;">
          <span><?= htmlspecialchars($boton_texto) ?></span>
          <i class="fa-solid fa-arrow-right" style="font-size: 10px;"></i>
        </a>
      </div>
      <?php endif; ?>

      <div class="t-left-badge" style="margin-top: 14px;">
        <div class="t-badge-inner">
          <div class="t-badge-text"><i class="fa-solid fa-mug-hot"></i> <?= htmlspecialchars($badge_texto) ?></div>
        </div>
      </div>
    </div>

    <div class="t-right">
      <img src="<?= htmlspecialchars($imagen_src) ?>" alt="Anuncio Tantico" onerror="this.src='<?= $basePath ?>assets/imagenes/tantoooo.png'"/>
      <div class="t-right-overlay"></div>
      <div class="t-right-badge">
        <div class="t-badge-inner">
          <div class="t-badge-text"><i class="fa-solid fa-mug-hot"></i> <?= htmlspecialchars($badge_texto) ?></div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
(function () {
  var ov = document.getElementById('tantico-overlay');
  if (!ov) return;

  var mostrado = false;
  var iv = null;

  function mostrarModalAnuncio() {
    if (mostrado) return;
    mostrado = true;
    ov.classList.add('t-visible');

    <?php if ($mostrar_partido): ?>
    // ── Countdown dinámico según fecha_objetivo de la base de datos ──────────
    var TARGET = new Date('<?= date('c', strtotime($fecha_objetivo)) ?>').getTime();

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
      var diff = Math.max(0, TARGET - Date.now());
      var d = document.getElementById('cd-dias');
      var h = document.getElementById('cd-horas');
      var m = document.getElementById('cd-mins');
      var s = document.getElementById('cd-segs');
      if (d) d.textContent = pad(Math.floor(diff / 86400000));
      if (h) h.textContent = pad(Math.floor((diff % 86400000) / 3600000));
      if (m) m.textContent = pad(Math.floor((diff % 3600000) / 60000));
      if (s) s.textContent = pad(Math.floor((diff % 60000) / 1000));
    }
    tick();
    iv = setInterval(tick, 1000);
    <?php endif; ?>
  }

  // Si existe la intro en pantalla, esperar a que termine
  if (document.getElementById('tantico-intro-overlay')) {
    window.addEventListener('tantico-intro-complete', mostrarModalAnuncio);
  } else {
    // Si no hay intro (o ya fue removida), mostrar el modal directamente
    setTimeout(mostrarModalAnuncio, 350);
  }

  // ── Cerrar modal ───────────────────────────────────────────────────────
  function closeModal() {
    if (!ov) return;
    ov.style.transition = 'opacity 0.35s';
    ov.style.opacity = '0';
    setTimeout(function () {
      ov.remove();
      if (iv) clearInterval(iv);
    }, 380);
  }

  var btnClose = document.getElementById('t-btn-close');
  if (btnClose) btnClose.addEventListener('click', closeModal);
  ov.addEventListener('click', function (e) { if (e.target === ov) closeModal(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

})();
</script>