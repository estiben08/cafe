<?php
/*
 * includes/anuuncio.php
 * Modal de partido — se muestra UNA sola vez por sesión de pestaña.
 * El overlay se oculta por defecto (visibility:hidden) y el script
 * lo muestra solo si es la primera carga de la sesión.
 * La clave 'tn_modal_visto' es independiente de 'tn_session_active'.
 */
?>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg-modal:  #28040A;
    --gold:      #D4A843;
    --gold-lt:   #F0C96A;
    --cream:     #FCF6DB;
    --overlay-c: rgba(0, 0, 0, 0.82);
  }

  /* Oculto por defecto — el JS lo muestra si corresponde */
  #tantico-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 9999;
    background: var(--overlay-c);
    backdrop-filter: blur(16px) saturate(1.6);
    -webkit-backdrop-filter: blur(16px) saturate(1.6);
    align-items: center; justify-content: center;
    padding: 1.25rem;
    font-family: 'DM Sans', sans-serif;
  }
  #tantico-overlay.t-visible {
    display: flex;
    animation: tOverlayIn 0.4s ease forwards;
  }
  @keyframes tOverlayIn { from { opacity: 0; } to { opacity: 1; } }

  #tantico-modal {
    display: flex;
    width: 100%; max-width: 660px;
    border-radius: 22px;
    overflow: hidden;
    border: 1px solid rgba(212,168,67,0.18);
    box-shadow: 0 0 0 1px rgba(212,168,67,0.06), 0 40px 100px rgba(0,0,0,0.75);
    transform: scale(0.86) translateY(28px);
    opacity: 0;
    animation: tModalIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.12s forwards;
    position: relative;
  }
  @keyframes tModalIn {
    to { transform: scale(1) translateY(0); opacity: 1; }
  }

  .t-left {
    flex: 1;
    background: var(--bg-modal);
    padding: 30px 28px 28px;
    display: flex; flex-direction: column; gap: 0;
    position: relative; overflow: hidden;
  }

  .t-stripe {
    height: 4px;
    background: linear-gradient(90deg,
      #FCD116 0%, #FCD116 50%,
      #003893 50%, #003893 75%,
      #CE1126 75%, #CE1126 100%);
    border-radius: 2px;
    margin-bottom: 18px;
  }

  .t-eyebrow {
    font-size: 11px; font-weight: 600;
    letter-spacing: .12em; text-transform: uppercase;
    color: var(--gold);
    display: flex; align-items: center; gap: 7px;
    margin-bottom: 12px;
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
    font-family: 'Playfair Display', serif;
    font-size: clamp(17px, 3.2vw, 21px);
    font-weight: 900; color: var(--cream);
    line-height: 1.22; margin-bottom: 8px;
  }
  .t-title em { font-style: italic; color: var(--gold-lt); }

  .t-sub {
    font-size: 12.5px; font-weight: 400;
    color: white;
    line-height: 1.55; margin-bottom: 16px;
  }

  .t-feat-bar {
    display: flex;
    border: 1px solid rgba(212,168,67,0.22);
    border-radius: 12px; overflow: hidden;
    background: rgba(212,168,67,0.04);
    margin-bottom: 18px;
  }
  .t-feat-item {
    flex: 1;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 13px 8px 11px; gap: 7px;
    position: relative;
    transition: background .2s; cursor: default;
  }
  .t-feat-item:hover { background: rgba(212,168,67,0.08); }
  .t-feat-item + .t-feat-item::before {
    content: '';
    position: absolute; left: 0; top: 16%; height: 68%; width: 1px;
    background: rgba(212,168,67,0.2);
  }
  .t-feat-item i { font-size: 22px; color: var(--gold); line-height: 1; }
  .t-feat-label {
    font-size: 9px; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: rgba(212,168,67,0.85);
    text-align: center; line-height: 1.3;
  }

  .t-partido-block {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(212,168,67,0.18);
    border-radius: 14px;
    padding: 14px 16px 12px;
    display: flex; flex-direction: column; gap: 10px;
  }
  .t-partido-fecha {
    font-size: 10px; font-weight: 700;
    letter-spacing: .14em; text-transform: uppercase;
    color: var(--gold); text-align: center;
  }
  .t-partido-matchup {
    display: flex; align-items: center;
    justify-content: center; gap: 10px;
  }
  .t-team-flag {
    width: 36px; height: 26px; border-radius: 4px;
    overflow: hidden; display: flex; flex-direction: column;
    flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.4);
  }
  .t-flag-co { background: linear-gradient(to bottom, #FCD116 0%, #FCD116 50%, #003893 50%, #003893 75%, #CE1126 75%, #CE1126 100%); }
  .t-flag-cr { background: linear-gradient(to bottom, #002B7F 0%, #002B7F 20%, #fff 20%, #fff 40%, #CE1126 40%, #CE1126 60%, #fff 60%, #fff 80%, #002B7F 80%, #002B7F 100%); }
  .t-team-info { display: flex; flex-direction: column; align-items: center; gap: 3px; min-width: 60px; }
  .t-team-name {
    font-size: 10px; font-weight: 700;
    letter-spacing: .06em; text-transform: uppercase;
    color: var(--cream); text-align: center;
  }
  .t-vs-badge {
    font-family: 'Playfair Display', serif;
    font-size: 13px; font-weight: 900;
    color: rgba(212,168,67,0.5); flex-shrink: 0;
  }

  .t-countdown-label {
    font-size: 9px; font-weight: 700;
    letter-spacing: .14em; text-transform: uppercase;
    color: var(--gold); text-align: center; margin-top: 2px;
  }
  .t-countdown { display: flex; justify-content: center; gap: 6px; }
  .t-cd-unit { display: flex; flex-direction: column; align-items: center; gap: 2px; }
  .t-cd-num {
    font-family: 'Playfair Display', serif;
    font-size: 22px; font-weight: 900; color: var(--gold-lt);
    line-height: 1; background: rgba(212,168,67,0.08);
    border: 1px solid rgba(212,168,67,0.2);
    border-radius: 8px; padding: 4px 8px;
    min-width: 44px; text-align: center;
  }
  .t-cd-lbl {
    font-size: 8px; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: rgba(252,246,219,0.35);
  }
  .t-cd-sep {
    font-family: 'Playfair Display', serif;
    font-size: 20px; font-weight: 900;
    color: rgba(212,168,67,0.3);
    align-self: flex-start; margin-top: 5px;
  }

  .t-right {
    width: 300px; flex-shrink: 0;
    position: relative; overflow: hidden;
    background: #1a0305;
  }
  .t-right img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: center top;
    display: block; min-height: 460px;
  }
  .t-right-overlay {
    position: absolute; inset: 0;
    background:
      linear-gradient(to right, rgba(40,4,10,0.45) 0%, transparent 45%),
      linear-gradient(to top, rgba(40,4,10,0.65) 0%, transparent 45%);
    pointer-events: none;
  }

  .t-badge-inner {
    background: rgba(40,4,10,0.72);
    border: 1px solid rgba(212,168,67,0.32);
    border-radius: 20px; padding: 5px 14px;
    backdrop-filter: blur(6px);
  }
  .t-badge-text {
    font-size: 9px; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: rgba(212,168,67,0.92); text-align: center;
  }

  .t-right-badge {
    position: absolute; bottom: 18px; left: 0; right: 0;
    display: flex; justify-content: center; pointer-events: none;
    z-index: 5;
  }
  .t-left-badge {
    display: none;
    justify-content: center;
    margin-top: 16px;
  }

  #t-btn-close {
    position: absolute; top: 14px; right: 14px; z-index: 20;
    width: 30px; height: 30px; border-radius: 50%;
    border: 1px solid rgba(252,246,219,.2);
    background: rgba(28,4,10,.65);
    color: rgba(252,246,219,.65);
    font-size: 13px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
    transition: background .2s, color .2s;
  }
  #t-btn-close:hover { background: rgba(212,168,67,.2); color: var(--gold-lt); }

  @media (max-width: 540px) {
    #tantico-modal { flex-direction: column; min-height: 580px; }
    .t-feat-bar {
      background: rgba(58, 2, 8, 0.76);
      border-color: rgba(212, 168, 67, 0.30);
    }
    .t-partido-block {
      background: rgba(58, 2, 8, 0.65);
      border-color: rgba(212, 168, 67, 0.30);
    }
    .t-right {
      position: absolute; inset: 0;
      width: 100%; height: 100%; z-index: 0;
    }
    .t-right img { min-height: unset; height: 100%; object-position: center 20%; }
    .t-left {
      position: relative; z-index: 1;
      padding: 22px 18px 20px;
      background: rgba(28, 4, 10, 0.78);
      backdrop-filter: blur(2px);
      -webkit-backdrop-filter: blur(2px);
    }
    .t-right-badge { display: none; }
    .t-left-badge  { display: flex; }
  }
</style>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

<!-- El overlay empieza con display:none — el JS lo activa si corresponde -->
<div id="tantico-overlay">
  <div id="tantico-modal" role="dialog" aria-modal="true" aria-label="Modal Tantico — Partidos en vivo">

    <button id="t-btn-close" aria-label="Cerrar"><i class="fa-solid fa-x"></i></button>

    <div class="t-left">
      <div class="t-stripe"></div>
      <div class="t-eyebrow">
        <span class="t-live-dot"></span>
        Transmisión en vivo · Neiva, Huila
      </div>
      <div class="t-title">
        La Selección Colombia<br>
        <em>se vive diferente aquí</em>
      </div>
      <p class="t-sub">
        Cada gol y cada jugada en pantallas de alto nivel.
        Café, comida y un ambiente pensado para disfrutar el partido.
      </p>
      <div class="t-feat-bar">
        <div class="t-feat-item">
          <i class="fa-solid fa-tv"></i>
          <div class="t-feat-label">Pantallas<br>4K</div>
        </div>
        <div class="t-feat-item">
          <i class="fa-solid fa-users"></i>
          <div class="t-feat-label">Ambiente<br>futbolero</div>
        </div>
        <div class="t-feat-item">
          <i class="fa-brands fa-java"></i>
          <div class="t-feat-label">Bebidas<br>premium</div>
        </div>
      </div>
      <div class="t-partido-block">
        <div class="t-partido-fecha">Lunes 01 de Junio</div>
        <div class="t-partido-matchup">
          <div class="t-team-info">
            <div class="t-team-flag t-flag-co"></div>
            <div class="t-team-name">Colombia</div>
          </div>
          <div class="t-vs-badge">VS</div>
          <div class="t-team-info">
            <div class="t-team-flag t-flag-cr"></div>
            <div class="t-team-name">Costa Rica</div>
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

      <br>

      <div class="t-left-badge">
        <div class="t-badge-inner">
          <div class="t-badge-text"><i class="fa-solid fa-mug-hot"></i> Café Región · Neiva, Huila</div>
        </div>
      </div>
    </div>

    <div class="t-right">
      <img src="assets/imagenes/tantoooo.png" alt="Barista Tantico preparando café especial"/>
      <div class="t-right-overlay"></div>
      <div class="t-right-badge">
        <div class="t-badge-inner">
          <div class="t-badge-text"><i class="fa-solid fa-mug-hot"></i> Café Región · Neiva, Huila</div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
(function () {
  var KEY_SESSION = 'tn_session_active'; // guardada por intro.php
  var KEY_MODAL   = 'tn_modal_visto';    // propia del modal

  var ov = document.getElementById('tantico-overlay');

  // Guardia 1: si no pasó por la intro todavía, no mostrar nada.
  // (index.php redirigirá en su propio script — esto es solo seguridad extra)
  if (!sessionStorage.getItem(KEY_SESSION)) {
    return; // overlay ya está oculto por CSS, no hacer nada
  }

  // Guardia 2: si ya vio el modal en esta sesión, no mostrarlo
  if (sessionStorage.getItem(KEY_MODAL)) {
    return; // ídem
  }

  // Primera vez en esta sesión con intro completada → mostrar modal
  sessionStorage.setItem(KEY_MODAL, '1');
  ov.classList.add('t-visible');

  // ── Countdown ──────────────────────────────────────────────────────────
  var TARGET = new Date('2026-06-01T18:00:00-05:00').getTime();

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
  var iv = setInterval(tick, 1000);

  // ── Cerrar modal ───────────────────────────────────────────────────────
  function closeModal() {
    if (!ov) return;
    ov.style.transition = 'opacity 0.35s';
    ov.style.opacity = '0';
    setTimeout(function () { ov.remove(); clearInterval(iv); }, 380);
  }

  document.getElementById('t-btn-close').addEventListener('click', closeModal);
  ov.addEventListener('click', function (e) { if (e.target === ov) closeModal(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

})();
</script>