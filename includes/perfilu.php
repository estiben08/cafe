<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

require_once __DIR__ . '/auth_helper.php';

$user = getAuthenticatedUser();
if (!$user || empty($user['uid'])) {
    header('Location: /cafe/includes/loginu.php?error=' . urlencode('Sesión expirada o no iniciada.'));
    exit;
}

$firebase_uid = $user['uid'];
$fb_email     = $user['email'] ?? '';
$fb_nombre    = $user['nombre'] ?? explode('@', $fb_email)[0];
$fb_foto      = $user['foto'] ?? '';

$pdo = getCafePdo();

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

$favoritos_q = $pdo->prepare("
    SELECT p.id, p.nombre, p.precio, p.imagen, p.icono, p.descripcion
    FROM usuario_favoritos uf
    JOIN productos p ON p.id = uf.producto_id
    WHERE uf.usuario_id = ?
    ORDER BY uf.id DESC
");
$favoritos_q->execute([$usuario_id]);
$favoritos_list = $favoritos_q->fetchAll();

$total_pedidos = $pdo->prepare("SELECT COUNT(*) FROM pedidos WHERE firebase_uid = ?");
$total_pedidos->execute([$firebase_uid]);
$num_pedidos = (int)$total_pedidos->fetchColumn();

$num_favoritos = count($favoritos_list);

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
  <title>Mi perfil — Tantico</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Goudy+Bookletter+1911&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,500&display=swap" rel="stylesheet">
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

    /* ══ LAYOUT ══ */
    .dashboard { display: grid; grid-template-columns: 220px 1fr; min-height: 100vh; }

    /* ══ SIDEBAR ══ */
    .sidebar { background: var(--espresso); display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
    .sidebar-logo { padding: 20px 18px 16px; border-bottom: 0.5px solid rgba(252,246,219,0.08); }
    .sidebar-logo-sub { font-size: 11px; color: rgba(252,246,219,0.35); margin-top: 2px; }

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

    /* ══ MAIN ══ */
    .main-content { overflow-y: auto; }

    .hero { background: var(--espresso); padding: 22px 28px; position: relative; overflow: hidden; }
    .hero::before { content: ''; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(74,11,21,0.35); }
    .hero::after  { content: ''; position: absolute; bottom: -70px; right: 80px; width: 140px; height: 140px; border-radius: 50%; background: rgba(107,58,42,0.18); }
    .hero-inner { display: flex; align-items: center; gap: 18px; position: relative; z-index: 1; flex-wrap: wrap; }
    .avatar-lg { width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 600; color: var(--crema); border: 2.5px solid rgba(252,246,219,0.2); flex-shrink: 0; overflow: hidden; background: var(--vino2); }
    .avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
    .hero-info { flex: 1; min-width: 0; }
    .hero-name  { font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 400; color: var(--crema); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: 0.3px; }
    .hero-email { font-size: 13px; color: rgba(252,246,219,0.45); margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hero-since { font-size: 11px; color: rgba(252,246,219,0.3); margin-top: 5px; display: flex; align-items: center; gap: 5px; }
    .badge-nivel { background: rgba(196,149,106,0.18); border: 0.5px solid rgba(196,149,106,0.35); color: var(--latte); font-size: 11px; padding: 4px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }

    .content { padding: 22px 28px; display: flex; flex-direction: column; gap: 22px; }

    /* ══ STATS ══ */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
    .stat-card { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 12px; padding: 16px; }
    .stat-icon { font-size: 20px; color: var(--latte); margin-bottom: 8px; }
    .stat-val  { font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif; font-size: 28px; font-weight: 400; color: #1a1a1a; }
    .stat-lbl  { font-size: 11px; color: #888; margin-top: 2px; }

    .sec-title { font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif; font-size: 17px; font-weight: 400; color: #1a1a1a; margin-bottom: 12px; display: flex; align-items: center; gap: 7px; letter-spacing: 0.2px; }
    .sec-title i { font-size: 16px; color: var(--latte); }

    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .card-box { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 12px; padding: 18px; }

    /* ══ PUNTOS ══ */
    .pts-big  { font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif; font-size: 38px; font-weight: 400; color: #1a1a1a; line-height: 1; }
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

    /* ══ CUMPLEAÑOS ══ */
    .bday-top   { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px; }
    .bday-top i { font-size: 22px; color: var(--latte); margin-top: 2px; }
    .bday-title { font-size: 14px; font-weight: 600; color: #1a1a1a; }
    .bday-sub   { font-size: 11px; color: #888; margin-top: 2px; }
    .bday-form  { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .bday-form input  { flex: 1; min-width: 140px; height: 36px; border: 0.5px solid #d0c4bc; border-radius: 8px; padding: 0 10px; font-size: 13px; font-family: 'Inter', sans-serif; background: #faf6f0; color: #333; outline: none; }
    .bday-form input:focus { border-color: var(--latte); }
    .bday-form button { height: 36px; padding: 0 16px; border-radius: 8px; background: var(--espresso); border: none; color: var(--latte); font-size: 12px; font-weight: 500; cursor: pointer; white-space: nowrap; font-family: 'Inter', sans-serif; transition: background 0.15s; }
    .bday-form button:hover { background: var(--vino2); }
    .bday-info { margin-top: 10px; font-size: 11px; color: #666; display: flex; align-items: flex-start; gap: 6px; padding: 9px 11px; background: #faf6f0; border-radius: 7px; }
    .bday-info i { color: var(--latte); font-size: 14px; flex-shrink: 0; margin-top: 1px; }
    .bday-ok    { border-left: 2px solid #2e7d32; background: #f1f8f1; }
    .bday-ok i  { color: #2e7d32; }

    /* ══ ACCIONES ══ */
    .actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .action-btn { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.15s; text-decoration: none; }
    .action-btn:hover { border-color: #c4956a55; background: #fdf5ee; }
    .action-btn i { font-size: 18px; color: var(--latte); width: 20px; flex-shrink: 0; }
    .action-txt  { font-size: 12px; font-weight: 500; color: #333; }
    .action-sub  { font-size: 11px; color: #aaa; margin-top: 1px; }

    /* ══ PEDIDOS ══ */
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

    /* ══ DIRECCIONES ══ */
    .addr-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .addr-card { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 10px; padding: 14px; cursor: pointer; transition: border-color 0.15s; }
    .addr-card:hover { border-color: #c4956a55; }
    .addr-card.add-new { border-style: dashed; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 5px; min-height: 74px; }
    .addr-card.add-new:hover { background: #fdf5ee; }
    .addr-type { font-size: 11px; font-weight: 600; color: var(--latte); display: flex; align-items: center; gap: 5px; margin-bottom: 6px; }
    .addr-line { font-size: 12px; color: #333; }
    .addr-city { font-size: 11px; color: #aaa; margin-top: 2px; }

    /* ══ FAVORITOS ══ */
    .favoritos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 14px; }
    .fav-card { background: #fff; border: 0.5px solid #e8ddd5; border-radius: 12px; overflow: hidden; transition: border-color 0.15s, box-shadow 0.15s; }
    .fav-card:hover { border-color: #c4956a55; box-shadow: 0 2px 12px rgba(196,149,106,0.12); }
    .fav-img { height: 120px; background: #f8f3ec; position: relative; overflow: hidden; }
    .fav-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .fav-card:hover .fav-img img { transform: scale(1.05); }
    .fav-img-fallback { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #c4956a; font-size: 28px; }
    .fav-info   { padding: 10px 12px 12px; }
    .fav-nombre { font-size: 13px; font-weight: 600; color: #1a1a1a; margin-bottom: 3px; }
    .fav-desc   { font-size: 11px; color: #888; margin-bottom: 8px; line-height: 1.4; }
    .fav-footer { display: flex; align-items: center; justify-content: space-between; }
    .fav-precio { font-size: 13px; font-weight: 600; color: #1a1a1a; }
    .fav-acciones { display: flex; gap: 6px; }
    .fav-btn-comprar,
    .fav-btn-quitar { width: 28px; height: 28px; border-radius: 7px; border: 0.5px solid #e8ddd5; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; color: #888; text-decoration: none; transition: all 0.15s; }
    .fav-btn-comprar:hover { background: #fdf5ee; color: var(--latte); border-color: var(--latte); }
    .fav-btn-quitar:hover  { background: #fff5f5; color: #e57373; border-color: #e57373; }
    .fav-card.quitando { opacity: 0.4; pointer-events: none; transition: opacity 0.2s; }

    /* ══ BARRA NAV MÓVIL ══ */
    .mobile-nav {
      display: none;
      position: fixed;
      bottom: 0; left: 0; right: 0;
      background: var(--espresso);
      border-top: 0.5px solid rgba(252,246,219,0.1);
      z-index: 100;
      padding-bottom: env(safe-area-inset-bottom);
    }
    .mobile-nav-inner {
      display: flex;
      justify-content: space-around;
      align-items: stretch;
    }
    .mobile-nav-btn {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 3px;
      padding: 10px 4px;
      color: rgba(252,246,219,0.4);
      font-size: 9px;
      text-decoration: none;
      background: none;
      border: none;
      cursor: pointer;
      font-family: 'Inter', sans-serif;
      transition: color 0.15s;
      -webkit-tap-highlight-color: transparent;
    }
    .mobile-nav-btn i { font-size: 20px; }
    .mobile-nav-btn span { font-size: 8px; line-height: 1.2; text-align: center; }
    .mobile-nav-btn.active { color: var(--latte); }
    .mobile-nav-btn.logout-mob { color: rgba(220,80,80,0.55); }
    .mobile-nav-btn.logout-mob:active { color: #e57373; }

    /* ══ TABLET (sidebar mini con sólo iconos) ══ */
    @media (max-width: 1100px) and (min-width: 769px) {
      .dashboard { grid-template-columns: 64px 1fr; }
      .sidebar { height: 100vh; position: sticky; top: 0; overflow: hidden; }
      .sidebar-logo { padding: 12px 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
      .sidebar-logo a { justify-content: center; }
      .sidebar-logo img { height: 36px; width: 36px; object-fit: cover; border-radius: 8px; }
      .sidebar-logo-sub { display: none; }
      .btn-volver { justify-content: center; padding: 10px; }
      .btn-volver span { display: none; }
      .sidebar-user { padding: 10px; justify-content: center; }
      .sidebar-user > div:last-child { display: none; }
      .nav-section { display: none; }
      nav.sidebar-nav .nav-item { justify-content: center; padding: 10px; border-left: none; font-size: 0; }
      nav.sidebar-nav .nav-item i { font-size: 18px; width: auto; margin: 0; }
      nav.sidebar-nav .nav-item span { display: none; }
      nav.sidebar-nav .nav-item.active { border-left: none; border-right: 2px solid var(--latte); background: rgba(74,11,21,0.55); }
      #btn-logout-perfil { justify-content: center; padding: 10px; font-size: 0; }
      #btn-logout-perfil i { font-size: 18px; }
      #btn-logout-perfil span { display: none; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .two-col { grid-template-columns: 1fr; }
      .content { padding: 20px 24px; }
      .hero { padding: 20px 24px; }
      .hero-name { font-size: 18px; }
      .hero-inner { flex-wrap: nowrap; gap: 14px; }
      .favoritos-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
    }

    /* ══ TOPBAR MÓVIL (logo + volver) ══ */
    .mobile-topbar {
      display: none;
      background: var(--espresso);
      padding: 10px 16px;
      align-items: center;
      justify-content: space-between;
      border-bottom: 0.5px solid rgba(252,246,219,0.08);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 50;
    }
    .mobile-topbar img { height: 32px; width: auto; object-fit: contain; }
    .mobile-topbar-back {
      display: flex; align-items: center; gap: 6px;
      color: rgba(252,246,219,0.5); font-size: 12px;
      text-decoration: none; transition: color 0.15s;
    }
    .mobile-topbar-back:hover { color: var(--crema); }
    .mobile-topbar-back i { font-size: 15px; }

    /* ══ MOBILE ══ */
    @media (max-width: 768px) {
      .dashboard { grid-template-columns: 1fr; }
      .sidebar { display: none; }
      .mobile-nav { display: block; }
      .mobile-topbar { display: flex; }
      .main-content { padding-bottom: 70px; overflow-y: visible; }
      .hero { padding: 14px 16px; margin-top: 0; }
      body { padding-top: 54px; background: var(--espresso); }
      .dashboard { min-height: calc(100vh - 54px); background: #f4ede4; }
      .hero::before, .hero::after { display: none; }
      .avatar-lg { width: 52px; height: 52px; font-size: 20px; }
      .hero-name  { font-size: 17px; }
      .hero-email { font-size: 11px; }
      .badge-nivel { font-size: 10px; padding: 3px 9px; }

      .content { padding: 14px; gap: 16px; }

      .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
      .stat-card  { padding: 12px; }
      .stat-val   { font-size: 20px; }
      .stat-lbl   { font-size: 10px; }

      .two-col { grid-template-columns: 1fr; gap: 16px; }

      .card-box { padding: 14px; }
      .pts-big  { font-size: 28px; }
      .sec-title { font-size: 13px; }

      .order-prod  { max-width: 160px; }
      .order-price { min-width: 60px; font-size: 12px; }

      .addr-grid { grid-template-columns: 1fr; }

      .favoritos-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; }
      .fav-img { height: 100px; }

      .actions-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
      .action-btn { padding: 10px; gap: 8px; }
      .action-txt { font-size: 11px; }
      .action-sub { font-size: 10px; }

      #resumen, #pedidos, #puntos, #direcciones, #favoritos { scroll-margin-top: 16px; }
    }

    @media (max-width: 380px) {
      .favoritos-grid { grid-template-columns: 1fr 1fr; }
      .hero-name { font-size: 15px; }
    }
  </style>
</head>
<body>

<div class="dashboard">

  <!-- ══ SIDEBAR ══ -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <a href="/cafe/index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
        <img src="/cafe/assets/imagenes/banner20.png" alt="Tantico" style="height:36px;width:auto;object-fit:contain;">
      </a>
      <div class="sidebar-logo-sub" style="margin-top:6px;">Mi cuenta</div>
    </div>
    <a href="/cafe/index.php" class="btn-volver">
      <i class="ti ti-arrow-left"></i> <span>Volver al inicio</span>
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
      <a href="#resumen"    class="nav-item active"><i class="ti ti-layout-dashboard"></i> <span>Resumen</span></a>
      <a href="#pedidos"    class="nav-item"><i class="ti ti-shopping-bag"></i> <span>Mis pedidos</span></a>
      <a href="#puntos"     class="nav-item"><i class="ti ti-award"></i> <span>Mis puntos</span></a>
      <div class="nav-section">Cuenta</div>
      <a href="#direcciones" class="nav-item"><i class="ti ti-map-pin"></i> <span>Direcciones</span></a>
      <a href="#favoritos"   class="nav-item"><i class="ti ti-heart"></i> <span>Favoritos</span></a>
      <a href="#"            class="nav-item"><i class="ti ti-credit-card"></i> <span>Métodos de pago</span></a>
      <a href="#"            class="nav-item"><i class="ti ti-settings"></i> <span>Configuración</span></a>
    </nav>
    <button id="btn-logout-perfil" class="nav-item" style="background:none;border:none;width:100%;text-align:left;cursor:pointer;color:rgba(220,80,80,0.65);padding:12px 18px;border-top:0.5px solid rgba(252,246,219,0.08);">
      <i class="ti ti-logout"></i> <span>Cerrar sesión</span>
    </button>
  </aside>

  <!-- ══ MAIN ══ -->
  <main class="main-content">

    <!-- Topbar solo visible en mobile -->
    <div class="mobile-topbar">
      <img src="/cafe/assets/imagenes/banner20.png" alt="Tantico">
      <a href="/cafe/index.php" class="mobile-topbar-back">
        <i class="ti ti-arrow-left"></i> Volver al inicio
      </a>
    </div>

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
          <div class="stat-val" id="stat-favoritos"><?= $num_favoritos ?></div>
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
              <div class="progress-lbl">
                <span>Progreso al nivel <?= $nivel_actual === 'Espresso' ? 'Plata' : 'Oro' ?></span>
                <span><?= $puntos ?> / <?= $pts_siguiente ?></span>
              </div>
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

        <div style="display:flex;flex-direction:column;gap:16px">
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

      <!-- Favoritos -->
      <div id="favoritos">
        <div class="sec-title"><i class="ti ti-heart"></i> Mis favoritos</div>
        <?php if ($favoritos_list): ?>
        <div class="favoritos-grid">
          <?php foreach ($favoritos_list as $fav):
            $precio_fmt = '$' . number_format($fav['precio'], 0, ',', '.');
            $imagen_src = $fav['imagen'] ? '/cafe/' . htmlspecialchars($fav['imagen']) : '';
            $icono_fav  = htmlspecialchars($fav['icono'] ?: 'fa-mug-hot');
            $nombre_fav = htmlspecialchars($fav['nombre']);
            $desc_fav   = htmlspecialchars(mb_strimwidth($fav['descripcion'] ?? '', 0, 60, '…'));
          ?>
          <div class="fav-card" data-id="<?= $fav['id'] ?>">
            <div class="fav-img">
              <?php if ($imagen_src): ?>
                <img src="<?= $imagen_src ?>" alt="<?= $nombre_fav ?>"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="fav-img-fallback" style="display:none;">
                  <i class="fas <?= $icono_fav ?>"></i>
                </div>
              <?php else: ?>
                <div class="fav-img-fallback">
                  <i class="fas <?= $icono_fav ?>"></i>
                </div>
              <?php endif; ?>
            </div>
            <div class="fav-info">
              <div class="fav-nombre"><?= $nombre_fav ?></div>
              <div class="fav-desc"><?= $desc_fav ?></div>
              <div class="fav-footer">
                <span class="fav-precio"><?= $precio_fmt ?></span>
                <div class="fav-acciones">
                  <a href="/cafe/includes/servicios.php" class="fav-btn-comprar" title="Ver en tienda">
                    <i class="ti ti-shopping-bag"></i>
                  </a>
                  <button class="fav-btn-quitar" data-id="<?= $fav['id'] ?>" title="Quitar de favoritos">
                    <i class="ti ti-heart-off"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <i class="ti ti-heart"></i>
          No tienes productos favoritos aún.<br>
          <a href="/cafe/includes/servicios.php" style="color:var(--latte);font-size:12px;margin-top:6px;display:inline-block;">
            Explorar productos →
          </a>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>

<!-- ══ BARRA NAV MÓVIL ══ -->
<nav class="mobile-nav">
  <div class="mobile-nav-inner">
    <a href="#resumen" class="mobile-nav-btn active">
      <i class="ti ti-layout-dashboard"></i>
      <span>Inicio</span>
    </a>
    <a href="#pedidos" class="mobile-nav-btn">
      <i class="ti ti-shopping-bag"></i>
      <span>Pedidos</span>
    </a>
    <a href="#puntos" class="mobile-nav-btn">
      <i class="ti ti-award"></i>
      <span>Puntos</span>
    </a>
    <a href="#favoritos" class="mobile-nav-btn">
      <i class="ti ti-heart"></i>
      <span>Favoritos</span>
    </a>
    <button id="btn-logout-mob" class="mobile-nav-btn logout-mob">
      <i class="ti ti-logout"></i>
      <span>Cerrar sesión</span>
    </button>
  </div>
</nav>

<script>
// Navegación sidebar — resaltar ítem activo
document.querySelectorAll('.sidebar-nav .nav-item').forEach(el => {
  el.addEventListener('click', function() {
    document.querySelectorAll('.sidebar-nav .nav-item').forEach(x => x.classList.remove('active'));
    this.classList.add('active');
  });
});

// Quitar favorito desde el perfil
document.querySelectorAll('.fav-btn-quitar').forEach(btn => {
  btn.addEventListener('click', async function () {
    const productoId = parseInt(this.dataset.id, 10);
    const card = document.querySelector(`.fav-card[data-id="${productoId}"]`);
    if (!card) return;
    card.classList.add('quitando');
    try {
      const res = await fetch('/cafe/api/favoritos.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ producto_id: productoId })
      });
      const data = await res.json();
      if (data.success && !data.favorito) {
        card.style.transition = 'all 0.3s';
        card.style.transform  = 'scale(0.9)';
        card.style.opacity    = '0';
        setTimeout(() => {
          card.remove();
          const grid = document.querySelector('.favoritos-grid');
          if (grid && grid.children.length === 0) {
            grid.outerHTML = `<div class="empty-state">
              <i class="ti ti-heart"></i>
              No tienes productos favoritos aún.<br>
              <a href="/cafe/includes/servicios.php" style="color:var(--latte);font-size:12px;margin-top:6px;display:inline-block;">
                Explorar productos →
              </a>
            </div>`;
          }
          const statVal = document.getElementById('stat-favoritos');
          if (statVal) statVal.textContent = Math.max(0, parseInt(statVal.textContent) - 1);
        }, 300);
      } else {
        card.classList.remove('quitando');
      }
    } catch (_) {
      card.classList.remove('quitando');
    }
  });
});

// Resaltar nav móvil según sección visible
const mobileSections = ['resumen','pedidos','puntos','favoritos'];
const mobileNavBtns  = document.querySelectorAll('.mobile-nav-btn[href]');
const sectionObserver = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      mobileNavBtns.forEach(b => b.classList.remove('active'));
      const active = document.querySelector(`.mobile-nav-btn[href="#${e.target.id}"]`);
      if (active) active.classList.add('active');
    }
  });
}, { threshold: 0.3 });
mobileSections.forEach(id => {
  const el = document.getElementById(id);
  if (el) sectionObserver.observe(el);
});
</script>

<script type="module">
  import { cerrarSesion } from '/cafe/js/auth.js';

  // Logout desktop
  document.getElementById('btn-logout-perfil').addEventListener('click', async () => {
    try { await cerrarSesion(); } catch(e) {}
    await fetch('/cafe/includes/cerrar_sesion.php');
    window.location.href = '/cafe/includes/loginu.php';
  });

  // Logout móvil
  document.getElementById('btn-logout-mob').addEventListener('click', async () => {
    try { await cerrarSesion(); } catch(e) {}
    await fetch('/cafe/includes/cerrar_sesion.php');
    window.location.href = '/cafe/includes/loginu.php';
  });
</script>

</body>
</html>