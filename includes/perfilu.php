<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

require 'C:/xamppp/htdocs/cafe/vendor/autoload.php';

// ── 1. Verificar token Firebase ───────────────────────────────
$token = $_COOKIE['fb_token'] ?? '';
if (!$token) {
    header('Location: /cafe/includes/loginu.php');
    exit;
}

try {
    $firebase = (new Kreait\Firebase\Factory)
        ->withServiceAccount('C:/xamppp/htdocs/cafetantico-firebase-adminsdk-fbsvc-a449960bbb.json');
    $fbAuth         = $firebase->createAuth();
    $verified       = $fbAuth->verifyIdToken($token);
    $claims         = $verified->claims();
    $firebase_uid   = $claims->get('sub');
    $fb_email       = $claims->get('email') ?? '';
    $fb_nombre      = $claims->get('name')  ?? explode('@', $fb_email)[0];
    $fb_foto        = $claims->get('picture') ?? '';
} catch (Exception $e) {
    header('Location: /cafe/includes/loginu.php?error=' . urlencode('Sesión expirada.'));
    exit;
}

// ── 2. Conexión MySQL ─────────────────────────────────────────
$pdo = new PDO('mysql:host=127.0.0.1;dbname=coffeecol;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// ── 3. Upsert usuario ─────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE firebase_uid = ?");
$stmt->execute([$firebase_uid]);
$usuario = $stmt->fetch();

if (!$usuario) {
    $ins = $pdo->prepare("INSERT INTO usuarios (firebase_uid, nombre, email, foto) VALUES (?, ?, ?, ?)");
    $ins->execute([$firebase_uid, $fb_nombre, $fb_email, $fb_foto]);
    $usuario_id = $pdo->lastInsertId();
    $stmt->execute([$firebase_uid]);
    $usuario = $stmt->fetch();
}
$usuario_id = $usuario['id'];

// ── 4. Puntos de cumpleaños (una vez por año) ─────────────────
if ($usuario['fecha_nacimiento']) {
    $hoy   = new DateTime();
    $cumple = new DateTime($usuario['fecha_nacimiento']);
    $esCumple = ($hoy->format('m-d') === $cumple->format('m-d'));
    $yaOtorgado = ((int)$usuario['puntos_cumple_otorgados'] === (int)$hoy->format('Y'));

    if ($esCumple && !$yaOtorgado) {
        $pdo->prepare("UPDATE usuarios SET puntos = puntos + 100, puntos_cumple_otorgados = ? WHERE id = ?")
            ->execute([$hoy->format('Y'), $usuario_id]);
        $pdo->prepare("INSERT INTO puntos_historial (usuario_id, descripcion, puntos) VALUES (?, ?, ?)")
            ->execute([$usuario_id, '🎂 Puntos de cumpleaños', 100]);
        $usuario['puntos'] += 100;
    }
}

// ── 5. Guardar fecha de nacimiento (POST) ─────────────────────
$msg_bday = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha_nacimiento'])) {
    $fn = $_POST['fecha_nacimiento'];
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fn)) {
        $pdo->prepare("UPDATE usuarios SET fecha_nacimiento = ? WHERE id = ?")
            ->execute([$fn, $usuario_id]);
        $usuario['fecha_nacimiento'] = $fn;
        $msg_bday = 'ok';
    }
}

// ── 6. Cargar datos ───────────────────────────────────────────
$pedidos = $pdo->prepare("
    SELECT p.id, p.numero_pedido, p.total, p.fecha, p.estado,
           GROUP_CONCAT(prod.nombre SEPARATOR ', ') AS productos
    FROM pedidos p
    LEFT JOIN pedido_items pi ON pi.pedido_id = p.id
    LEFT JOIN productos prod  ON prod.id = pi.producto_id
    WHERE p.firebase_uid = ?
    GROUP BY p.id
    ORDER BY p.fecha DESC
    LIMIT 5
");
$pedidos->execute([$firebase_uid]);
$pedidos_list = $pedidos->fetchAll();

$historial = $pdo->prepare("
    SELECT descripcion, puntos, fecha FROM puntos_historial
    WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 5
");
$historial->execute([$usuario_id]);
$historial_list = $historial->fetchAll();

$direcciones = $pdo->prepare("SELECT * FROM usuario_direcciones WHERE usuario_id = ? LIMIT 4");
$direcciones->execute([$usuario_id]);
$direcciones_list = $direcciones->fetchAll();

$total_favoritos  = $pdo->prepare("SELECT COUNT(*) FROM usuario_favoritos WHERE usuario_id = ?");
$total_favoritos->execute([$usuario_id]);
$num_favoritos = (int)$total_favoritos->fetchColumn();

$total_pedidos = $pdo->prepare("SELECT COUNT(*) FROM pedidos WHERE firebase_uid = ?");
$total_pedidos->execute([$firebase_uid]);
$num_pedidos = (int)$total_pedidos->fetchColumn();

$puntos       = (int)$usuario['puntos'];
$nivel_actual = $puntos >= 1000 ? 'Oro' : ($puntos >= 500 ? 'Plata' : 'Espresso');
$pts_siguiente = $puntos >= 1000 ? 1000 : ($puntos >= 500 ? 1000 : 500);
$progreso_pct  = min(100, round(($puntos / $pts_siguiente) * 100));
$pts_faltan    = max(0, $pts_siguiente - $puntos);

$fecha_registro = new DateTime($usuario['fecha_registro']);
$meses_es = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$desde = $meses_es[(int)$fecha_registro->format('n') - 1] . ' ' . $fecha_registro->format('Y');

$nombre_display = $usuario['nombre'] ?: $fb_nombre;
$foto_display   = $usuario['foto']   ?: $fb_foto;
$iniciales = strtoupper(substr($nombre_display, 0, 1));

$estados_label = [
    'pendiente'  => ['texto' => 'Pendiente',  'clase' => 'proceso'],
    'proceso'    => ['texto' => 'En proceso', 'clase' => 'proceso'],
    'enviado'    => ['texto' => 'En camino',  'clase' => 'camino'],
    'entregado'  => ['texto' => 'Entregado',  'clase' => 'entregado'],
    'cancelado'  => ['texto' => 'Cancelado',  'clase' => 'proceso'],
];

$tipos_icono = [
    'Casa'    => 'ti-home',
    'Trabajo' => 'ti-building',
    'Otra'    => 'ti-map-pin',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi perfil — CoffeeCol</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,600&display=swap" rel="stylesheet">
  <link rel="icon" href="/cafe/assets/imagenes/banner1.png" type="image/x-icon">
  <style>
    :root {
      --vino:   #28040A;
      --vino2:  #4a0b15;
      --crema:  #FCF6DB;
      --rosa:   #fdf0f0;
      --latte:  #c4956a;
      --espresso: #1a0408;
      --marron: #6b3a2a;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background: #f4ede4; min-height: 100vh; }

    /* ── Layout ── */
    .dashboard { display: grid; grid-template-columns: 220px 1fr; min-height: 100vh; }

    /* ── Sidebar ── */
    .sidebar { background: var(--espresso); display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
    .sidebar-logo { padding: 20px 18px 16px; border-bottom: 0.5px solid rgba(252,246,219,0.08); }
    .sidebar-logo-sub  { font-size: 11px; color: rgba(252,246,219,0.35); margin-top: 2px; }

    .btn-volver { display: flex; align-items: center; gap: 8px; padding: 10px 18px; color: rgba(252,246,219,0.4); font-size: 12px; text-decoration: none; border-bottom: 0.5px solid rgba(252,246,219,0.08); transition: all 0.15s; }
    .btn-volver:hover { color: var(--crema); background: rgba(252,246,219,0.05); }
    .btn-volver i { font-size: 14px; }

    .sidebar-user { padding: 14px 18px; border-bottom: 0.5px solid rgba(252,246,219,0.08); display: flex; align-items: center; gap: 10px; }
    .avatar-sm { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: var(--crema); flex-shrink: 0; border: 1.5px solid rgba(252,246,219,0.2); overflow: hidden; background: var(--vino2); }
    .avatar-sm img { width: 100%; height: 100%; object-fit: cover; }
    .user-name-sm  { font-size: 13px; font-weight: 500; color: var(--crema); }
    .user-email-sm { font-size: 11px; color: rgba(252,246,219,0.35); margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }

    nav.sidebar-nav { flex: 1; padding: 10px 0; overflow-y: auto; }
    .nav-section { font-size: 10px; color: rgba(252,246,219,0.22); padding: 12px 18px 4px; letter-spacing: 1.2px; text-transform: uppercase; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 18px; color: rgba(252,246,219,0.45); font-size: 13px; text-decoration: none; border-left: 2px solid transparent; transition: all 0.15s; cursor: pointer; }
    .nav-item:hover { color: var(--crema); background: rgba(252,246,219,0.05); border-left-color: rgba(252,246,219,0.2); }
    .nav-item.active { color: var(--crema); background: rgba(74,11,21,0.55); border-left-color: var(--latte); }
    .nav-item i { font-size: 16px; width: 18px; text-align: center; }
    .nav-logout { padding: 12px 18px; border-top: 0.5px solid rgba(252,246,219,0.08); }
    .nav-logout .nav-item { color: rgba(220,80,80,0.65); border-radius: 8px; border-left: none; padding: 8px 12px; }
    .nav-logout .nav-item:hover { color: #e57373; background: rgba(220,80,80,0.08); }

    /* ── Main ── */
    .main-content { overflow-y: auto; }

    /* ── Hero ── */
    .hero { background: var(--espresso); padding: 22px 28px; position: relative; overflow: hidden; }
    .hero::before { content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(74,11,21,0.35); }
    .hero::after  { content: ''; position: absolute; bottom: -70px; right: 80px; width: 140px; height: 140px; border-radius: 50%; background: rgba(107,58,42,0.18); }
    .hero-inner { display: flex; align-items: center; gap: 18px; position: relative; z-index: 1; }
    .avatar-lg { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 600; color: var(--crema); border: 2.5px solid rgba(252,246,219,0.2); flex-shrink: 0; overflow: hidden; background: var(--vino2); }
    .avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
    .hero-info { flex: 1; }
    .hero-name  { font-size: 22px; font-weight: 600; color: var(--crema); }
    .hero-email { font-size: 13px; color: rgba(252,246,219,0.45); margin-top: 3px; }
    .hero-since { font-size: 11px; color: rgba(252,246,219,0.3); margin-top: 5px; display: flex; align-items: center; gap: 5px; }
    .badge-nivel { background: rgba(196,149,106,0.18); border: 0.5px solid rgba(196,149,106,0.35); color: var(--latte); font-size: 11px; padding: 4px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }

    /* ── Contenido ── */
    .content { padding: 22px 28px; display: flex; flex-direction: column; gap: 22px; }

    /* ── Stats ── */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
    .stat-card { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 12px; padding: 16px; }
    .stat-icon { font-size: 20px; color: var(--latte); margin-bottom: 8px; }
    .stat-val  { font-size: 24px; font-weight: 600; color: #1a1a1a; }
    .stat-lbl  { font-size: 11px; color: #888; margin-top: 2px; }

    /* ── Sección título ── */
    .sec-title { font-size: 14px; font-weight: 600; color: #1a1a1a; margin-bottom: 12px; display: flex; align-items: center; gap: 7px; }
    .sec-title i { font-size: 16px; color: var(--latte); }

    /* ── Dos columnas ── */
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    /* ── Tarjeta genérica ── */
    .card-box { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 12px; padding: 18px; }

    /* ── Puntos ── */
    .pts-big  { font-size: 34px; font-weight: 600; color: #1a1a1a; }
    .pts-sub  { font-size: 12px; color: #888; margin-top: 2px; }
    .pts-top  { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .progress-wrap { margin-bottom: 10px; }
    .progress-lbl  { display: flex; justify-content: space-between; font-size: 11px; color: #888; margin-bottom: 6px; }
    .progress-bar  { height: 6px; background: #f0e8e0; border-radius: 3px; overflow: hidden; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, var(--vino2), var(--latte)); border-radius: 3px; }
    .pts-msg { font-size: 12px; color: #555; background: #fdf5ee; padding: 9px 12px; border-radius: 8px; border-left: 2.5px solid var(--latte); }

    .rewards-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-top: 14px; }
    .reward-card { background: #faf6f0; border: 0.5px solid #e8ddd5; border-radius: 8px; padding: 10px; text-align: center; cursor: pointer; transition: border-color 0.15s; }
    .reward-card:hover { border-color: var(--latte); }
    .reward-card.locked { opacity: 0.4; cursor: default; }
    .reward-icon { font-size: 20px; color: var(--latte); margin-bottom: 4px; }
    .reward-name { font-size: 11px; font-weight: 500; color: #333; }
    .reward-pts  { font-size: 10px; color: #888; margin-top: 2px; }

    .hist-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 0.5px solid #f0e8e0; }
    .hist-item:last-child { border-bottom: none; }
    .hist-desc { font-size: 12px; color: #333; }
    .hist-date { font-size: 11px; color: #aaa; margin-top: 1px; }
    .hist-pts  { font-size: 13px; font-weight: 600; }
    .pts-pos   { color: #2e7d32; }
    .pts-neg   { color: #c62828; }

    /* ── Cumpleaños ── */
    .bday-top   { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
    .bday-top i { font-size: 22px; color: var(--latte); margin-top: 2px; }
    .bday-title { font-size: 14px; font-weight: 600; color: #1a1a1a; }
    .bday-sub   { font-size: 11px; color: #888; margin-top: 2px; }
    .bday-form  { display: flex; gap: 8px; align-items: center; }
    .bday-form input  { flex: 1; height: 36px; border: 0.5px solid #d0c4bc; border-radius: 8px; padding: 0 10px; font-size: 13px; font-family: 'Inter', sans-serif; background: #faf6f0; color: #333; outline: none; }
    .bday-form input:focus { border-color: var(--latte); }
    .bday-form button { height: 36px; padding: 0 16px; border-radius: 8px; background: var(--espresso); border: none; color: var(--latte); font-size: 12px; font-weight: 500; cursor: pointer; white-space: nowrap; font-family: 'Inter', sans-serif; transition: background 0.15s; }
    .bday-form button:hover { background: var(--vino2); }
    .bday-info { margin-top: 10px; font-size: 11px; color: #666; display: flex; align-items: flex-start; gap: 6px; padding: 9px 11px; background: #faf6f0; border-radius: 7px; }
    .bday-info i { color: var(--latte); font-size: 14px; flex-shrink: 0; margin-top: 1px; }
    .bday-ok    { border-left: 2px solid #2e7d32; background: #f1f8f1; }
    .bday-ok i  { color: #2e7d32; }

    /* ── Acciones ── */
    .actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .action-btn { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.15s; text-decoration: none; }
    .action-btn:hover { border-color: #c4956a55; background: #fdf5ee; }
    .action-btn i { font-size: 18px; color: var(--latte); width: 20px; flex-shrink: 0; }
    .action-txt  { font-size: 12px; font-weight: 500; color: #333; }
    .action-sub  { font-size: 11px; color: #aaa; margin-top: 1px; }

    /* ── Pedidos ── */
    .orders-list { display: flex; flex-direction: column; gap: 8px; }
    .order-row { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .order-num  { font-size: 13px; font-weight: 600; color: #333; min-width: 60px; }
    .order-info { flex: 1; min-width: 120px; }
    .order-prod { font-size: 12px; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 260px; }
    .order-date { font-size: 11px; color: #aaa; margin-top: 2px; }
    .order-price { font-size: 13px; font-weight: 600; color: #333; min-width: 80px; text-align: right; }
    .order-status { font-size: 10px; padding: 3px 9px; border-radius: 20px; font-weight: 600; white-space: nowrap; }
    .s-entregado { background: #e8f5e9; color: #2e7d32; }
    .s-camino    { background: #e3f2fd; color: #1565c0; }
    .s-proceso   { background: #fff8e1; color: #f57f17; }
    .empty-state { text-align: center; padding: 30px; color: #aaa; font-size: 13px; }
    .empty-state i { font-size: 30px; display: block; margin-bottom: 8px; color: #ddd; }

    /* ── Direcciones ── */
    .addr-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .addr-card { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 10px; padding: 14px; cursor: pointer; transition: border-color 0.15s; }
    .addr-card:hover { border-color: #c4956a55; }
    .addr-card.add-new { border-style: dashed; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 5px; min-height: 74px; }
    .addr-card.add-new:hover { background: #fdf5ee; }
    .addr-type { font-size: 11px; font-weight: 600; color: var(--latte); display: flex; align-items: center; gap: 5px; margin-bottom: 6px; }
    .addr-line { font-size: 12px; color: #333; }
    .addr-city { font-size: 11px; color: #aaa; margin-top: 2px; }

    @media (max-width: 900px) {
      .dashboard { grid-template-columns: 1fr; }
      .sidebar { height: auto; position: static; flex-direction: row; flex-wrap: wrap; }
      .stats-grid { grid-template-columns: repeat(2,1fr); }
      .two-col { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<div class="dashboard">

  <!-- ══════════════════════════════════ SIDEBAR ══════════════════════════════════ -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <a href="/cafe/index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
        <img src="/cafe/assets/imagenes/banner20.png" alt="CoffeeCol" style="height:36px;width:auto;object-fit:contain;">
      </a>
      <div class="sidebar-logo-sub" style="margin-top:6px;">Mi cuenta</div>
    </div>
    <a href="/cafe/index.php" class="btn-volver">
      <i class="ti ti-arrow-left"></i> Volver al inicio
    </a>
    <div class="sidebar-user">
      <div class="avatar-sm">
        <?php if ($foto_display): ?>
          <img src="<?= htmlspecialchars($foto_display) ?>" alt="foto">
        <?php else: ?>
          <?= $iniciales ?>
        <?php endif; ?>
      </div>
      <div>
        <div class="user-name-sm"><?= htmlspecialchars($nombre_display) ?></div>
        <div class="user-email-sm"><?= htmlspecialchars($fb_email) ?></div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Principal</div>
      <a href="#resumen"    class="nav-item active"><i class="ti ti-layout-dashboard"></i> Resumen</a>
      <a href="#pedidos"    class="nav-item"><i class="ti ti-shopping-bag"></i> Mis pedidos</a>
      <a href="#puntos"     class="nav-item"><i class="ti ti-award"></i> Mis puntos</a>
      <div class="nav-section">Cuenta</div>
      <a href="#direcciones" class="nav-item"><i class="ti ti-map-pin"></i> Direcciones</a>
      <a href="#favoritos"   class="nav-item"><i class="ti ti-heart"></i> Favoritos</a>
      <a href="#"            class="nav-item"><i class="ti ti-credit-card"></i> Métodos de pago</a>
      <a href="#"            class="nav-item"><i class="ti ti-settings"></i> Configuración</a>
    </nav>
    <div class="nav-logout">
      <a href="/cafe/includes/cerrar_sesion.php" class="nav-item"><i class="ti ti-logout"></i> Cerrar sesión</a>
    </div>
  </aside>

  <!-- ══════════════════════════════════ MAIN ══════════════════════════════════ -->
  <main class="main-content">

    <!-- Hero banner -->
    <div class="hero">
      <div class="hero-inner">
        <div class="avatar-lg">
          <?php if ($foto_display): ?>
            <img src="<?= htmlspecialchars($foto_display) ?>" alt="foto de perfil">
          <?php else: ?>
            <?= $iniciales ?>
          <?php endif; ?>
        </div>
        <div class="hero-info">
          <div class="hero-name"><?= htmlspecialchars($nombre_display) ?></div>
          <div class="hero-email"><?= htmlspecialchars($fb_email) ?></div>
          <div class="hero-since"><i class="ti ti-calendar" style="font-size:12px"></i> Miembro desde <?= $desde ?></div>
        </div>
        <div class="badge-nivel"><i class="ti ti-star" style="font-size:12px"></i> <?= $nivel_actual ?></div>
      </div>
    </div>

    <div class="content">

      <!-- Stats -->
      <div id="resumen" class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon"><i class="ti ti-award"></i></div>
          <div class="stat-val"><?= $puntos ?></div>
          <div class="stat-lbl">Puntos disponibles</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><i class="ti ti-shopping-bag"></i></div>
          <div class="stat-val"><?= $num_pedidos ?></div>
          <div class="stat-lbl">Pedidos realizados</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><i class="ti ti-heart"></i></div>
          <div class="stat-val"><?= $num_favoritos ?></div>
          <div class="stat-lbl">Favoritos</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><i class="ti ti-map-pin"></i></div>
          <div class="stat-val"><?= count($direcciones_list) ?></div>
          <div class="stat-lbl">Direcciones guardadas</div>
        </div>
      </div>

      <!-- Dos columnas: Puntos | Cumpleaños + Acciones -->
      <div class="two-col">

        <!-- Puntos -->
        <div id="puntos">
          <div class="sec-title"><i class="ti ti-award"></i> Puntos y recompensas</div>
          <div class="card-box">
            <div class="pts-top">
              <div>
                <div class="pts-big"><?= $puntos ?></div>
                <div class="pts-sub">puntos acumulados</div>
              </div>
              <div class="badge-nivel"><i class="ti ti-flame" style="font-size:12px"></i> <?= $nivel_actual ?></div>
            </div>
            <div class="progress-wrap">
              <div class="progress-lbl"><span>Progreso al nivel <?= $nivel_actual === 'Espresso' ? 'Plata' : 'Oro' ?></span><span><?= $puntos ?> / <?= $pts_siguiente ?></span></div>
              <div class="progress-bar"><div class="progress-fill" style="width:<?= $progreso_pct ?>%"></div></div>
            </div>
            <?php if ($pts_faltan > 0): ?>
            <div class="pts-msg"><i class="ti ti-coffee" style="font-size:13px;vertical-align:-2px;margin-right:4px"></i> Te faltan <strong><?= $pts_faltan ?> puntos</strong> para el siguiente nivel</div>
            <?php else: ?>
            <div class="pts-msg"><i class="ti ti-trophy" style="font-size:13px;vertical-align:-2px;margin-right:4px"></i> ¡Alcanzaste el nivel máximo!</div>
            <?php endif; ?>

            <div class="rewards-grid">
              <div class="reward-card <?= $puntos < 300 ? 'locked' : '' ?>">
                <div class="reward-icon"><i class="ti ti-discount"></i></div>
                <div class="reward-name">10% descuento</div>
                <div class="reward-pts">300 pts</div>
              </div>
              <div class="reward-card <?= $puntos < 500 ? 'locked' : '' ?>">
                <div class="reward-icon"><i class="ti ti-coffee"></i></div>
                <div class="reward-name">Café gratis</div>
                <div class="reward-pts">500 pts</div>
              </div>
              <div class="reward-card <?= $puntos < 1000 ? 'locked' : '' ?>">
                <div class="reward-icon"><i class="ti ti-gift"></i></div>
                <div class="reward-name">Kit premium</div>
                <div class="reward-pts">1000 pts</div>
              </div>
            </div>

            <?php if ($historial_list): ?>
            <div style="margin-top:14px">
              <div style="font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px">Historial reciente</div>
              <?php foreach ($historial_list as $h): ?>
              <div class="hist-item">
                <div>
                  <div class="hist-desc"><?= htmlspecialchars($h['descripcion']) ?></div>
                  <div class="hist-date"><?= date('d M Y', strtotime($h['fecha'])) ?></div>
                </div>
                <div class="hist-pts <?= $h['puntos'] > 0 ? 'pts-pos' : 'pts-neg' ?>">
                  <?= $h['puntos'] > 0 ? '+' : '' ?><?= $h['puntos'] ?> pts
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Derecha: Cumpleaños + Acciones -->
        <div style="display:flex;flex-direction:column;gap:16px">

          <!-- Cumpleaños -->
          <div>
            <div class="sec-title"><i class="ti ti-cake"></i> Regalo de cumpleaños</div>
            <div class="card-box">
              <div class="bday-top">
                <i class="ti ti-gift"></i>
                <div>
                  <div class="bday-title">100 puntos en tu cumpleaños</div>
                  <div class="bday-sub">Los recibes automáticamente cada año</div>
                </div>
              </div>
              <form method="POST" action="">
                <div class="bday-form">
                  <input type="date" name="fecha_nacimiento"
                    value="<?= htmlspecialchars($usuario['fecha_nacimiento'] ?? '') ?>"
                    max="<?= date('Y-m-d') ?>">
                  <button type="submit">Guardar</button>
                </div>
              </form>
              <div class="bday-info <?= $msg_bday === 'ok' ? 'bday-ok' : '' ?>">
                <i class="ti ti-<?= $msg_bday === 'ok' ? 'check' : 'info-circle' ?>"></i>
                <?php if ($msg_bday === 'ok'): ?>
                  <span>¡Fecha guardada! Recibirás 100 puntos cada año en tu cumpleaños.</span>
                <?php elseif ($usuario['fecha_nacimiento']): ?>
                  <?php $d = new DateTime($usuario['fecha_nacimiento']); ?>
                  <span>Cumpleaños registrado: <strong><?= $d->format('d') . ' de ' . $meses_es[(int)$d->format('n')-1] ?></strong></span>
                <?php else: ?>
                  <span>Ingresa tu fecha para recibir puntos cada año en tu cumpleaños</span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Acciones rápidas -->
          <div>
            <div class="sec-title"><i class="ti ti-bolt"></i> Acciones rápidas</div>
            <div class="actions-grid">
              <a href="#pedidos" class="action-btn">
                <i class="ti ti-refresh"></i>
                <div><div class="action-txt">Repetir pedido</div><div class="action-sub">Último pedido</div></div>
              </a>
              <a href="#favoritos" class="action-btn">
                <i class="ti ti-heart"></i>
                <div><div class="action-txt">Ver favoritos</div><div class="action-sub"><?= $num_favoritos ?> productos</div></div>
              </a>
              <a href="#direcciones" class="action-btn">
                <i class="ti ti-map-pin"></i>
                <div><div class="action-txt">Mis direcciones</div><div class="action-sub"><?= count($direcciones_list) ?> guardadas</div></div>
              </a>
              <a href="/cafe/index.php" class="action-btn">
                <i class="ti ti-tag"></i>
                <div><div class="action-txt">Ver productos</div><div class="action-sub">Tienda completa</div></div>
              </a>
            </div>
          </div>

        </div>
      </div>

      <!-- Pedidos recientes -->
      <div id="pedidos">
        <div class="sec-title"><i class="ti ti-clock"></i> Pedidos recientes</div>
        <?php if ($pedidos_list): ?>
        <div class="orders-list">
          <?php foreach ($pedidos_list as $p):
            $num   = $p['numero_pedido'] ?: '#' . str_pad($p['id'], 4, '0', STR_PAD_LEFT);
            $est   = $estados_label[$p['estado']] ?? ['texto' => ucfirst($p['estado']), 'clase' => 'proceso'];
            $prods = $p['productos'] ?: 'Sin detalle';
          ?>
          <div class="order-row">
            <div class="order-num"><?= htmlspecialchars($num) ?></div>
            <div class="order-info">
              <div class="order-prod"><?= htmlspecialchars($prods) ?></div>
              <div class="order-date"><?= date('d M Y', strtotime($p['fecha'])) ?></div>
            </div>
            <div class="order-price">$<?= number_format($p['total'], 0, ',', '.') ?></div>
            <div class="order-status s-<?= $est['clase'] ?>"><?= $est['texto'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="ti ti-shopping-bag"></i> Aún no tienes pedidos</div>
        <?php endif; ?>
      </div>

      <!-- Direcciones -->
      <div id="direcciones">
        <div class="sec-title"><i class="ti ti-map-pin"></i> Direcciones guardadas</div>
        <div class="addr-grid">
          <?php foreach ($direcciones_list as $dir):
            $icono = $tipos_icono[$dir['tipo']] ?? 'ti-map-pin';
          ?>
          <div class="addr-card">
            <div class="addr-type"><i class="ti <?= $icono ?>"></i> <?= htmlspecialchars($dir['tipo']) ?></div>
            <div class="addr-line"><?= htmlspecialchars($dir['direccion']) ?></div>
            <div class="addr-city"><?= htmlspecialchars($dir['ciudad'] . ($dir['departamento'] ? ', ' . $dir['departamento'] : '')) ?></div>
          </div>
          <?php endforeach; ?>
          <div class="addr-card add-new">
            <i class="ti ti-plus" style="font-size:22px;color:#bbb"></i>
            <div style="font-size:12px;color:#aaa">Agregar dirección</div>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<script>
document.querySelectorAll('.sidebar-nav .nav-item').forEach(el => {
  el.addEventListener('click', function() {
    document.querySelectorAll('.sidebar-nav .nav-item').forEach(x => x.classList.remove('active'));
    this.classList.add('active');
  });
});
</script>

</body>
</html>