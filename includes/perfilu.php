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

// Otorgar puntos de cumpleaños si aplica
if (!empty($usuario['fecha_nacimiento'])) {
    $hoy        = new DateTime();
    $cumple     = new DateTime($usuario['fecha_nacimiento']);
    $esCumple   = ($hoy->format('m-d') === $cumple->format('m-d'));
    $yaOtorgado = ((int)$usuario['puntos_cumple_otorgados'] === (int)$hoy->format('Y'));

    if ($esCumple && !$yaOtorgado) {
        $pdo->prepare("UPDATE usuarios SET puntos = puntos + 100, puntos_cumple_otorgados = ? WHERE id = ?")
            ->execute([$hoy->format('Y'), $usuario_id]);
        $pdo->prepare("INSERT INTO puntos_historial (usuario_id, descripcion, puntos) VALUES (?, ?, ?)")
            ->execute([$usuario_id, '🎂 Puntos de cumpleaños', 100]);
        $usuario['puntos'] += 100;
    }
}

// Procesar formulario POST clásico de cumpleaños si se envía directamente
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

// ── CONSULTAR TODOS LOS PEDIDOS DEL CLIENTE ──
$pedidos = $pdo->prepare("
    SELECT p.id, p.numero_pedido, p.total, p.fecha, p.estado, p.direccion, p.ciudad, p.telefono, p.nombre, p.apellido,
           GROUP_CONCAT(CONCAT(pi.cantidad, 'x ', prod.nombre) SEPARATOR ' + ') AS productos_resumen
    FROM pedidos p
    LEFT JOIN pedido_items pi ON pi.pedido_id = p.id
    LEFT JOIN productos prod  ON prod.id = pi.producto_id
    WHERE p.firebase_uid = ? OR (p.email = ? AND ? != '')
    GROUP BY p.id
    ORDER BY p.fecha DESC
");
$pedidos->execute([$firebase_uid, $fb_email, $fb_email]);
$pedidos_list = $pedidos->fetchAll(PDO::FETCH_ASSOC);

// Cargar items de cada pedido
$pedido_ids = array_column($pedidos_list, 'id');
$items_por_pedido = [];
if (!empty($pedido_ids)) {
    $ids_str = implode(',', array_map('intval', $pedido_ids));
    $stmtItems = $pdo->query("
        SELECT pi.pedido_id, pi.producto_id, pi.cantidad, pi.precio_unitario,
               prod.nombre, prod.imagen, prod.icono, prod.descripcion
        FROM pedido_items pi
        LEFT JOIN productos prod ON prod.id = pi.producto_id
        WHERE pi.pedido_id IN ($ids_str)
    ");
    while ($it = $stmtItems->fetch(PDO::FETCH_ASSOC)) {
        $items_por_pedido[$it['pedido_id']][] = $it;
    }
}
foreach ($pedidos_list as &$p) {
    $p['items'] = $items_por_pedido[$p['id']] ?? [];
}
unset($p);

// Historial de puntos
$historial = $pdo->prepare("
    SELECT descripcion, puntos, fecha FROM puntos_historial
    WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 15
");
$historial->execute([$usuario_id]);
$historial_list = $historial->fetchAll(PDO::FETCH_ASSOC);

// Direcciones guardadas
$direcciones = $pdo->prepare("SELECT * FROM usuario_direcciones WHERE usuario_id = ? ORDER BY principal DESC, id DESC");
$direcciones->execute([$usuario_id]);
$direcciones_list = $direcciones->fetchAll(PDO::FETCH_ASSOC);

// Favoritos
$favoritos_q = $pdo->prepare("
    SELECT p.id, p.nombre, p.precio, p.imagen, p.icono, p.descripcion
    FROM usuario_favoritos uf
    JOIN productos p ON p.id = uf.producto_id
    WHERE uf.usuario_id = ?
    ORDER BY uf.id DESC
");
$favoritos_q->execute([$usuario_id]);
$favoritos_list = $favoritos_q->fetchAll(PDO::FETCH_ASSOC);

$num_pedidos   = count($pedidos_list);
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
$telefono_display = $usuario['telefono'] ?? '';
$metodo_pago_preferido = $usuario['metodo_pago_preferido'] ?? 'Contra entrega en efectivo';
$iniciales = strtoupper(substr($nombre_display, 0, 1));

$estados_label = [
    'pendiente'  => ['texto' => 'Pendiente',  'clase' => 'proceso', 'icono' => 'ti-clock'],
    'procesando' => ['texto' => 'En preparación', 'clase' => 'proceso', 'icono' => 'ti-flame'],
    'proceso'    => ['texto' => 'En preparación', 'clase' => 'proceso', 'icono' => 'ti-flame'],
    'enviado'    => ['texto' => 'En camino',  'clase' => 'camino',  'icono' => 'ti-truck-delivery'],
    'entregado'  => ['texto' => 'Entregado',  'clase' => 'entregado', 'icono' => 'ti-circle-check'],
    'cancelado'  => ['texto' => 'Cancelado',  'clase' => 'cancelado', 'icono' => 'ti-circle-x'],
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
  <title>Mi Perfil — Tantico Café Especial</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Goudy+Bookletter+1911&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,500&display=swap" rel="stylesheet">
  <link rel="icon" href="/cafe/assets/imagenes/banner1.png" type="image/x-icon">
  <style>
    :root {
      --vino:     #28040A;
      --vino2:    #4a0b15;
      --crema:    #FCF6DB;
      --rosa:     #fdf0f0;
      --latte:    #c4956a;
      --latte-dk: #9e6d42;
      --espresso: #1a0408;
      --marron:   #6b3a2a;
      --bg-dash:  #f4ede4;
      --borde:    #e8ddd5;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background: var(--bg-dash); min-height: 100vh; color: #222; }

    /* ══ LAYOUT ══ */
    .dashboard { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }

    /* ══ SIDEBAR ══ */
    .sidebar { background: var(--espresso); display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; z-index: 40; }
    .sidebar-logo { padding: 22px 20px 16px; border-bottom: 0.5px solid rgba(252,246,219,0.08); }
    .sidebar-logo-sub { font-size: 11px; color: rgba(252,246,219,0.35); margin-top: 4px; letter-spacing: 0.5px; }

    .btn-volver { display: flex; align-items: center; gap: 8px; padding: 11px 20px; color: rgba(252,246,219,0.45); font-size: 12.5px; text-decoration: none; border-bottom: 0.5px solid rgba(252,246,219,0.08); transition: all 0.15s; }
    .btn-volver:hover { color: var(--crema); background: rgba(252,246,219,0.06); }
    .btn-volver i { font-size: 15px; }

    .sidebar-user { padding: 16px 20px; border-bottom: 0.5px solid rgba(252,246,219,0.08); display: flex; align-items: center; gap: 12px; }
    .avatar-sm { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 600; color: var(--crema); flex-shrink: 0; border: 1.5px solid rgba(252,246,219,0.25); overflow: hidden; background: var(--vino2); }
    .avatar-sm img { width: 100%; height: 100%; object-fit: cover; }
    .user-name-sm  { font-size: 13.5px; font-weight: 600; color: var(--crema); }
    .user-email-sm { font-size: 11px; color: rgba(252,246,219,0.4); margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 145px; }

    nav.sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
    .nav-section { font-size: 10px; color: rgba(252,246,219,0.28); padding: 12px 20px 4px; letter-spacing: 1.2px; text-transform: uppercase; font-weight: 600; }
    .nav-item { display: flex; align-items: center; gap: 11px; padding: 10px 20px; color: rgba(252,246,219,0.5); font-size: 13px; text-decoration: none; border-left: 3px solid transparent; transition: all 0.15s; cursor: pointer; }
    .nav-item:hover { color: var(--crema); background: rgba(252,246,219,0.05); border-left-color: rgba(252,246,219,0.25); }
    .nav-item.active { color: var(--crema); background: rgba(74,11,21,0.55); border-left-color: var(--latte); font-weight: 500; }
    .nav-item i { font-size: 17px; width: 20px; text-align: center; }
    .nav-logout { padding: 12px 20px; border-top: 0.5px solid rgba(252,246,219,0.08); }
    .nav-logout .nav-item { color: rgba(230,90,90,0.75); border-radius: 8px; border-left: none; padding: 10px 14px; }
    .nav-logout .nav-item:hover { color: #f28b82; background: rgba(220,80,80,0.1); }

    /* ══ MAIN ══ */
    .main-content { overflow-y: auto; }

    .hero { background: var(--espresso); padding: 26px 32px; position: relative; overflow: hidden; }
    .hero::before { content: ''; position: absolute; top: -50px; right: -50px; width: 220px; height: 220px; border-radius: 50%; background: rgba(74,11,21,0.35); pointer-events: none; }
    .hero::after  { content: ''; position: absolute; bottom: -70px; right: 80px; width: 150px; height: 150px; border-radius: 50%; background: rgba(107,58,42,0.2); pointer-events: none; }
    .hero-inner { display: flex; align-items: center; gap: 20px; position: relative; z-index: 1; flex-wrap: wrap; }
    .avatar-lg { width: 76px; height: 76px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 600; color: var(--crema); border: 2.5px solid rgba(196,149,106,0.35); flex-shrink: 0; overflow: hidden; background: var(--vino2); box-shadow: 0 4px 16px rgba(0,0,0,0.3); }
    .avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
    .hero-info { flex: 1; min-width: 0; }
    .hero-name  { font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif; font-size: 26px; font-weight: 400; color: var(--crema); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: 0.3px; }
    .hero-email { font-size: 13.5px; color: rgba(252,246,219,0.5); margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hero-meta  { display: flex; align-items: center; gap: 14px; margin-top: 8px; flex-wrap: wrap; }
    .hero-since { font-size: 12px; color: rgba(252,246,219,0.4); display: flex; align-items: center; gap: 5px; }
    .badge-nivel { background: rgba(196,149,106,0.18); border: 1px solid rgba(196,149,106,0.35); color: var(--latte); font-size: 12px; font-weight: 600; padding: 5px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
    .btn-edit-perfil { background: rgba(252,246,219,0.1); border: 1px solid rgba(252,246,219,0.2); color: var(--crema); font-size: 12px; padding: 6px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; cursor: pointer; transition: all 0.2s; }
    .btn-edit-perfil:hover { background: rgba(252,246,219,0.2); color: #fff; }

    .content { padding: 26px 32px; display: flex; flex-direction: column; gap: 26px; }

    /* ══ STATS ══ */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    .stat-card { background: #fff; border: 1px solid var(--borde); border-radius: 12px; padding: 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); border-color: rgba(196,149,106,0.4); }
    .stat-icon { font-size: 22px; color: var(--latte); margin-bottom: 8px; }
    .stat-val  { font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif; font-size: 32px; font-weight: 400; color: #1a1a1a; line-height: 1; }
    .stat-lbl  { font-size: 12px; color: #777; margin-top: 4px; }

    .sec-title { font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif; font-size: 19px; font-weight: 400; color: #1a1a1a; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; letter-spacing: 0.2px; }
    .sec-title i { font-size: 18px; color: var(--latte); }

    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .card-box { background: #fff; border: 1px solid var(--borde); border-radius: 14px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }

    /* ══ PUNTOS ══ */
    .pts-big  { font-family: 'Goudy Bookletter 1911', 'Playfair Display', Georgia, serif; font-size: 42px; font-weight: 400; color: #1a1a1a; line-height: 1; }
    .pts-sub  { font-size: 13px; color: #888; margin-top: 2px; }
    .pts-top  { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .progress-wrap { margin-bottom: 12px; }
    .progress-lbl  { display: flex; justify-content: space-between; font-size: 11.5px; color: #777; margin-bottom: 6px; font-weight: 500; }
    .progress-bar  { height: 7px; background: #ede3d8; border-radius: 4px; overflow: hidden; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, var(--vino2), var(--latte)); border-radius: 4px; transition: width 0.4s ease; }
    .pts-msg { font-size: 12.5px; color: #444; background: #fdf5ee; padding: 10px 14px; border-radius: 8px; border-left: 3px solid var(--latte); }

    .rewards-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-top: 16px; }
    .reward-card { background: #faf6f0; border: 1px solid var(--borde); border-radius: 10px; padding: 12px 10px; text-align: center; position: relative; transition: all 0.2s; display: flex; flex-direction: column; justify-content: space-between; }
    .reward-card.unlocked { border-color: rgba(196,149,106,0.5); background: #fffcf8; box-shadow: 0 3px 10px rgba(196,149,106,0.12); }
    .reward-card.locked { opacity: 0.55; }
    .reward-icon { font-size: 22px; color: var(--latte); margin-bottom: 4px; }
    .reward-name { font-size: 12px; font-weight: 600; color: #222; margin-bottom: 2px; }
    .reward-pts  { font-size: 11px; color: #777; margin-bottom: 8px; font-weight: 500; }
    .btn-canjear { background: var(--vino); color: var(--crema); border: none; border-radius: 6px; padding: 5px 8px; font-size: 11px; font-weight: 600; cursor: pointer; transition: background 0.15s; width: 100%; }
    .btn-canjear:hover { background: var(--vino2); }
    .reward-card.locked .btn-canjear { background: #d0c4bc; color: #666; cursor: not-allowed; }

    .hist-item { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; border-bottom: 0.5px solid #f0e8e0; }
    .hist-item:last-child { border-bottom: none; }
    .hist-desc { font-size: 12.5px; color: #333; font-weight: 500; }
    .hist-date { font-size: 11px; color: #999; margin-top: 2px; }
    .hist-pts  { font-size: 13.5px; font-weight: 700; white-space: nowrap; }
    .pts-pos   { color: #2e7d32; }
    .pts-neg   { color: #c62828; }

    /* ══ CUMPLEAÑOS ══ */
    .bday-top   { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .bday-top i { font-size: 24px; color: var(--latte); margin-top: 2px; }
    .bday-title { font-size: 14.5px; font-weight: 600; color: #1a1a1a; }
    .bday-sub   { font-size: 12px; color: #777; margin-top: 2px; }
    .bday-form  { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .bday-form input  { flex: 1; min-width: 140px; height: 38px; border: 1px solid #d0c4bc; border-radius: 8px; padding: 0 12px; font-size: 13px; font-family: 'Inter', sans-serif; background: #faf6f0; color: #333; outline: none; }
    .bday-form input:focus { border-color: var(--latte); background: #fff; }
    .bday-form button { height: 38px; padding: 0 18px; border-radius: 8px; background: var(--espresso); border: none; color: var(--crema); font-size: 12.5px; font-weight: 600; cursor: pointer; white-space: nowrap; font-family: 'Inter', sans-serif; transition: background 0.15s; }
    .bday-form button:hover { background: var(--vino2); }
    .bday-info { margin-top: 12px; font-size: 12px; color: #555; display: flex; align-items: flex-start; gap: 8px; padding: 10px 12px; background: #faf6f0; border-radius: 8px; }
    .bday-info i { color: var(--latte); font-size: 15px; flex-shrink: 0; margin-top: 1px; }
    .bday-ok    { border-left: 3px solid #2e7d32; background: #f1f8f1; color: #1b5e20; }
    .bday-ok i  { color: #2e7d32; }

    /* ══ ACCIONES ══ */
    .actions-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .action-btn { background: #fff; border: 1px solid var(--borde); border-radius: 10px; padding: 14px; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: all 0.15s; text-decoration: none; }
    .action-btn:hover { border-color: rgba(196,149,106,0.5); background: #fdf5ee; transform: translateY(-1px); }
    .action-btn i { font-size: 20px; color: var(--latte); width: 22px; flex-shrink: 0; }
    .action-txt  { font-size: 13px; font-weight: 600; color: #222; }
    .action-sub  { font-size: 11px; color: #888; margin-top: 2px; }

    /* ══ PEDIDOS ══ */
    .orders-list { display: flex; flex-direction: column; gap: 10px; }
    .order-card { background: #fff; border: 1px solid var(--borde); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; transition: border-color 0.2s; }
    .order-card:hover { border-color: rgba(196,149,106,0.5); }
    .order-left { display: flex; align-items: center; gap: 16px; flex: 1; min-width: 240px; }
    .order-icon-wrap { width: 44px; height: 44px; border-radius: 10px; background: #faf6f0; display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--latte); flex-shrink: 0; }
    .order-num  { font-size: 14px; font-weight: 700; color: #1a1a1a; letter-spacing: 0.3px; }
    .order-prod { font-size: 12.5px; color: #555; margin-top: 3px; max-width: 380px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .order-date { font-size: 11.5px; color: #999; margin-top: 3px; display: flex; align-items: center; gap: 4px; }
    .order-right { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
    .order-price { font-size: 15px; font-weight: 700; color: #1a1a1a; min-width: 90px; text-align: right; }
    .order-status { font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; }
    .s-entregado { background: #e8f5e9; color: #2e7d32; }
    .s-camino    { background: #e3f2fd; color: #1565c0; }
    .s-proceso   { background: #fff8e1; color: #e65100; }
    .s-cancelado { background: #ffebee; color: #c62828; }
    .btn-ver-pedido { background: #faf6f0; border: 1px solid var(--borde); color: #333; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 5px; }
    .btn-ver-pedido:hover { background: var(--espresso); color: var(--crema); border-color: var(--espresso); }
    .btn-repetir-pedido { background: rgba(196,149,106,0.15); border: 1px solid rgba(196,149,106,0.3); color: var(--latte-dk); padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 5px; }
    .btn-repetir-pedido:hover { background: var(--latte); color: #fff; }

    /* ══ DIRECCIONES ══ */
    .addr-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .addr-card { background: #fff; border: 1px solid var(--borde); border-radius: 12px; padding: 16px; position: relative; transition: all 0.2s; display: flex; flex-direction: column; justify-content: space-between; min-height: 120px; }
    .addr-card:hover { border-color: rgba(196,149,106,0.5); transform: translateY(-2px); }
    .addr-card.add-new { border-style: dashed; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 8px; cursor: pointer; background: #faf6f0; }
    .addr-card.add-new:hover { background: #fdf5ee; border-color: var(--latte); }
    .addr-type { font-size: 12px; font-weight: 700; color: var(--latte); display: flex; align-items: center; gap: 6px; margin-bottom: 6px; }
    .addr-line { font-size: 13px; color: #222; font-weight: 500; }
    .addr-city { font-size: 11.5px; color: #888; margin-top: 3px; }
    .addr-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 12px; padding-top: 10px; border-top: 0.5px solid #f0e8e0; }
    .badge-principal { font-size: 10px; background: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 12px; font-weight: 600; }
    .btn-del-addr { background: none; border: none; color: #aaa; cursor: pointer; font-size: 14px; transition: color 0.15s; }
    .btn-del-addr:hover { color: #e53935; }

    /* ══ FAVORITOS ══ */
    .favoritos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
    .fav-card { background: #fff; border: 1px solid var(--borde); border-radius: 12px; overflow: hidden; transition: all 0.2s; display: flex; flex-direction: column; }
    .fav-card:hover { border-color: rgba(196,149,106,0.5); box-shadow: 0 4px 14px rgba(196,149,106,0.12); transform: translateY(-2px); }
    .fav-img { height: 130px; background: #f8f3ec; position: relative; overflow: hidden; }
    .fav-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .fav-card:hover .fav-img img { transform: scale(1.06); }
    .fav-img-fallback { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #c4956a; font-size: 32px; }
    .fav-info   { padding: 12px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
    .fav-nombre { font-size: 13.5px; font-weight: 600; color: #1a1a1a; margin-bottom: 4px; }
    .fav-desc   { font-size: 11.5px; color: #777; margin-bottom: 10px; line-height: 1.4; }
    .fav-footer { display: flex; align-items: center; justify-content: space-between; }
    .fav-precio { font-size: 14px; font-weight: 700; color: #1a1a1a; }
    .fav-acciones { display: flex; gap: 6px; }
    .fav-btn-comprar, .fav-btn-quitar { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--borde); background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 15px; color: #666; text-decoration: none; transition: all 0.15s; }
    .fav-btn-comprar:hover { background: var(--vino); color: var(--crema); border-color: var(--vino); }
    .fav-btn-quitar:hover  { background: #ffebee; color: #e53935; border-color: #ef9a9a; }
    .fav-card.quitando { opacity: 0.3; pointer-events: none; }

    /* ══ MÉTODOS DE PAGO ══ */
    .pagos-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .pago-card { background: #fff; border: 1px solid var(--borde); border-radius: 12px; padding: 16px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 14px; }
    .pago-card:hover { border-color: var(--latte); background: #fdf5ee; }
    .pago-card.selected { border-color: var(--latte); background: #fdf5ee; box-shadow: 0 0 0 1px var(--latte); }
    .pago-icon { width: 40px; height: 40px; border-radius: 10px; background: #faf6f0; display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--latte); flex-shrink: 0; }
    .pago-title { font-size: 13.5px; font-weight: 600; color: #222; }
    .pago-sub   { font-size: 11.5px; color: #777; margin-top: 2px; }

    /* ══ MODALES ══ */
    .custom-modal-overlay {
      position: fixed; inset: 0; background: rgba(22, 10, 7, 0.7); backdrop-filter: blur(6px);
      z-index: 2000; display: none; align-items: center; justify-content: center; padding: 16px;
    }
    .custom-modal-overlay.open { display: flex; animation: fadeInModal 0.2s ease; }
    @keyframes fadeInModal { from { opacity: 0; } to { opacity: 1; } }
    .custom-modal-box {
      background: #fff; width: 100%; max-width: 520px; border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,0.3);
      overflow: hidden; animation: popUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes popUp { from { transform: scale(0.92); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-header-cust { background: var(--espresso); padding: 16px 22px; color: var(--crema); display: flex; justify-content: space-between; align-items: center; }
    .modal-title-cust { font-family: 'Goudy Bookletter 1911', Georgia, serif; font-size: 18px; margin: 0; color: var(--crema); display: flex; align-items: center; gap: 8px; }
    .modal-close-cust { background: none; border: none; color: rgba(252,246,219,0.6); font-size: 20px; cursor: pointer; transition: color 0.15s; }
    .modal-close-cust:hover { color: #fff; }
    .modal-body-cust { padding: 22px; max-height: 80vh; overflow-y: auto; }
    .form-group-cust { margin-bottom: 14px; }
    .form-label-cust { display: block; font-size: 12px; font-weight: 600; color: #444; margin-bottom: 5px; }
    .form-input-cust { width: 100%; height: 40px; border: 1px solid #d0c4bc; border-radius: 8px; padding: 0 12px; font-size: 13.5px; font-family: 'Inter', sans-serif; outline: none; background: #faf6f0; }
    .form-input-cust:focus { border-color: var(--latte); background: #fff; }
    .btn-submit-cust { width: 100%; height: 42px; border-radius: 8px; background: var(--espresso); color: var(--crema); font-size: 13.5px; font-weight: 600; border: none; cursor: pointer; transition: background 0.15s; margin-top: 10px; }
    .btn-submit-cust:hover { background: var(--vino2); }

    /* Items table in modal */
    .modal-items-table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 13px; }
    .modal-items-table th { background: #faf6f0; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; color: #777; border-bottom: 1px solid #e8ddd5; }
    .modal-items-table td { padding: 10px; border-bottom: 1px solid #f0e8e0; vertical-align: middle; }
    .modal-timeline { display: flex; justify-content: space-between; position: relative; margin: 18px 0 24px; }
    .modal-timeline::before { content:''; position: absolute; top: 14px; left: 10%; right: 10%; height: 2px; background: #e0d0c4; z-index: 1; }
    .timeline-step { position: relative; z-index: 2; text-align: center; flex: 1; }
    .timeline-dot { width: 28px; height: 28px; border-radius: 50%; background: #ede3d8; color: #888; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; margin-bottom: 4px; font-weight: bold; }
    .timeline-step.active .timeline-dot { background: var(--latte); color: #fff; box-shadow: 0 0 0 3px rgba(196,149,106,0.3); }
    .timeline-step.done .timeline-dot { background: #2e7d32; color: #fff; }
    .timeline-label { font-size: 10.5px; color: #777; font-weight: 500; }
    .timeline-step.active .timeline-label { color: #1a1a1a; font-weight: 700; }

    /* ══ TOAST ══ */
    .toast-popup {
      position: fixed; bottom: 24px; right: 24px; background: var(--espresso); color: var(--crema);
      padding: 12px 20px; border-radius: 10px; box-shadow: 0 6px 20px rgba(0,0,0,0.3);
      font-size: 13px; display: none; align-items: center; gap: 10px; z-index: 3000; border: 1px solid rgba(196,149,106,0.4);
    }
    .toast-popup.visible { display: flex; animation: slideUpToast 0.3s ease; }
    @keyframes slideUpToast { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    /* ══ BARRA NAV MÓVIL ══ */
    .mobile-nav {
      display: none; position: fixed; bottom: 0; left: 0; right: 0;
      background: var(--espresso); border-top: 0.5px solid rgba(252,246,219,0.1);
      z-index: 100; padding-bottom: env(safe-area-inset-bottom);
    }
    .mobile-nav-inner { display: flex; justify-content: space-around; align-items: stretch; }
    .mobile-nav-btn {
      flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center;
      gap: 3px; padding: 10px 4px; color: rgba(252,246,219,0.4); font-size: 9px;
      text-decoration: none; background: none; border: none; cursor: pointer;
      font-family: 'Inter', sans-serif; transition: color 0.15s; -webkit-tap-highlight-color: transparent;
    }
    .mobile-nav-btn i { font-size: 20px; }
    .mobile-nav-btn span { font-size: 8.5px; line-height: 1.2; text-align: center; }
    .mobile-nav-btn.active { color: var(--latte); }
    .mobile-nav-btn.logout-mob { color: rgba(220,80,80,0.65); }

    /* ══ TOPBAR MÓVIL ══ */
    .mobile-topbar {
      display: none; background: var(--espresso); padding: 10px 16px; align-items: center;
      justify-content: space-between; border-bottom: 0.5px solid rgba(252,246,219,0.08);
      position: fixed; top: 0; left: 0; right: 0; z-index: 50;
    }
    .mobile-topbar img { height: 32px; width: auto; object-fit: contain; }
    .mobile-topbar-back { display: flex; align-items: center; gap: 6px; color: rgba(252,246,219,0.55); font-size: 12px; text-decoration: none; transition: color 0.15s; }
    .mobile-topbar-back:hover { color: var(--crema); }

    @media (max-width: 991px) {
      .dashboard { grid-template-columns: 1fr; }
      .sidebar { display: none; }
      .mobile-nav { display: block; }
      .mobile-topbar { display: flex; }
      .main-content { padding-bottom: 80px; }
      .hero { padding: 16px 20px; margin-top: 0; }
      body { padding-top: 54px; }
      .avatar-lg { width: 60px; height: 60px; font-size: 22px; }
      .hero-name { font-size: 20px; }
      .content { padding: 16px; gap: 20px; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
      .two-col { grid-template-columns: 1fr; gap: 20px; }
      .addr-grid { grid-template-columns: 1fr; }
      .pagos-grid { grid-template-columns: 1fr; }
      .favoritos-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
      .order-left { min-width: 100%; }
      .order-right { width: 100%; justify-content: space-between; }
    }
  </style>
</head>
<body>

<div class="dashboard">

  <!-- ══ SIDEBAR ══ -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <a href="/cafe/index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
        <img src="/cafe/assets/imagenes/tantico.png" alt="Tantico" style="height:100px;width:auto;object-fit:contain;">
      </a>
      <div class="sidebar-logo-sub">Mi Cuenta Tantico</div>
    </div>
    <a href="/cafe/index.php" class="btn-volver">
      <i class="ti ti-arrow-left"></i> <span>Volver a la tienda</span>
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
        <div class="user-name-sm" id="sidebar-user-name"><?= htmlspecialchars($nombre_display) ?></div>
        <div class="user-email-sm"><?= htmlspecialchars($fb_email) ?></div>
      </div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Menú Principal</div>
      <a href="#resumen"     class="nav-item active"><i class="ti ti-layout-dashboard"></i> <span>Resumen</span></a>
      <a href="#pedidos"     class="nav-item"><i class="ti ti-shopping-bag"></i> <span>Mis pedidos (<?= $num_pedidos ?>)</span></a>
      <a href="#puntos"      class="nav-item"><i class="ti ti-award"></i> <span>Mis puntos (<?= $puntos ?>)</span></a>
      <div class="nav-section">Mi Cuenta</div>
      <a href="#direcciones" class="nav-item"><i class="ti ti-map-pin"></i> <span>Direcciones</span></a>
      <a href="#favoritos"   class="nav-item"><i class="ti ti-heart"></i> <span>Favoritos</span></a>
      <a href="#pagos"       class="nav-item"><i class="ti ti-credit-card"></i> <span>Métodos de pago</span></a>
      <a href="#config"      class="nav-item" onclick="abrirModalPerfil()"><i class="ti ti-settings"></i> <span>Configuración</span></a>
    </nav>
    <div class="nav-logout">
      <button id="btn-logout-perfil" class="nav-item" style="background:none;border:none;width:100%;text-align:left;cursor:pointer;">
        <i class="ti ti-logout"></i> <span>Cerrar sesión</span>
      </button>
    </div>
  </aside>

  <!-- ══ MAIN ══ -->
  <main class="main-content">

    <!-- Topbar solo visible en mobile -->
    <div class="mobile-topbar">
      <img src="/cafe/assets/imagenes/banner20.png" alt="Tantico">
      <a href="/cafe/index.php" class="mobile-topbar-back">
        <i class="ti ti-arrow-left"></i> Tienda
      </a>
    </div>

    <!-- Hero Banner -->
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
          <div class="hero-name" id="hero-user-name"><?= htmlspecialchars($nombre_display) ?></div>
          <div class="hero-email"><?= htmlspecialchars($fb_email) ?></div>
          <div class="hero-meta">
            <div class="hero-since"><i class="ti ti-calendar"></i> Miembro desde <?= $desde ?></div>
            <button class="btn-edit-perfil" onclick="abrirModalPerfil()">
              <i class="ti ti-edit"></i> Editar datos
            </button>
          </div>
        </div>
        <div class="badge-nivel"><i class="ti ti-star"></i> Nivel <?= $nivel_actual ?></div>
      </div>
    </div>

    <div class="content">

      <!-- Stats Grid -->
      <div id="resumen" class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon"><i class="ti ti-award"></i></div>
          <div class="stat-val" id="stat-puntos-val"><?= $puntos ?></div>
          <div class="stat-lbl">Puntos disponibles</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><i class="ti ti-shopping-bag"></i></div>
          <div class="stat-val" id="stat-pedidos-val"><?= $num_pedidos ?></div>
          <div class="stat-lbl">Pedidos realizados</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><i class="ti ti-heart"></i></div>
          <div class="stat-val" id="stat-favoritos"><?= $num_favoritos ?></div>
          <div class="stat-lbl">Favoritos guardados</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon"><i class="ti ti-map-pin"></i></div>
          <div class="stat-val" id="stat-direcciones"><?= count($direcciones_list) ?></div>
          <div class="stat-lbl">Direcciones guardadas</div>
        </div>
      </div>

      <!-- Dos Columnas: Puntos & Recompensas | Cumpleaños & Acciones -->
      <div class="two-col">

        <!-- Puntos -->
        <div id="puntos">
          <div class="sec-title"><i class="ti ti-award"></i> Puntos y recompensas</div>
          <div class="card-box">
            <div class="pts-top">
              <div>
                <div class="pts-big" id="box-puntos-val"><?= $puntos ?></div>
                <div class="pts-sub">Puntos Tantico acumulados</div>
              </div>
              <div class="badge-nivel"><i class="ti ti-flame"></i> Nivel <?= $nivel_actual ?></div>
            </div>
            <div class="progress-wrap">
              <div class="progress-lbl">
                <span>Progreso a nivel <?= $nivel_actual === 'Espresso' ? 'Plata' : 'Oro' ?></span>
                <span id="progreso-pts-txt"><?= $puntos ?> / <?= $pts_siguiente ?> pts</span>
              </div>
              <div class="progress-bar"><div class="progress-fill" id="progreso-fill" style="width:<?= $progreso_pct ?>%"></div></div>
            </div>
            <?php if ($pts_faltan > 0): ?>
            <div class="pts-msg" id="pts-msg-box"><i class="ti ti-coffee"></i> Te faltan <strong><?= $pts_faltan ?> puntos</strong> para el siguiente nivel</div>
            <?php else: ?>
            <div class="pts-msg" id="pts-msg-box"><i class="ti ti-trophy"></i> ¡Alcanzaste el nivel máximo de cliente VIP Oro!</div>
            <?php endif; ?>

            <!-- Recompensas con Canje Funcional -->
            <div class="rewards-grid">
              <div class="reward-card <?= $puntos >= 300 ? 'unlocked' : 'locked' ?>" id="rec-card-300">
                <div class="reward-icon"><i class="ti ti-discount"></i></div>
                <div class="reward-name">10% Descuento</div>
                <div class="reward-pts">300 pts</div>
                <button class="btn-canjear" onclick="canjearRecompensa('descuento_10', '10% de Descuento', 300)">
                  <?= $puntos >= 300 ? 'Canjear' : 'Bloqueado' ?>
                </button>
              </div>
              <div class="reward-card <?= $puntos >= 500 ? 'unlocked' : 'locked' ?>" id="rec-card-500">
                <div class="reward-icon"><i class="ti ti-coffee"></i></div>
                <div class="reward-name">Café Gratis</div>
                <div class="reward-pts">500 pts</div>
                <button class="btn-canjear" onclick="canjearRecompensa('cafe_gratis', 'Café Gratis', 500)">
                  <?= $puntos >= 500 ? 'Canjear' : 'Bloqueado' ?>
                </button>
              </div>
              <div class="reward-card <?= $puntos >= 1000 ? 'unlocked' : 'locked' ?>" id="rec-card-1000">
                <div class="reward-icon"><i class="ti ti-gift"></i></div>
                <div class="reward-name">Kit Premium</div>
                <div class="reward-pts">1000 pts</div>
                <button class="btn-canjear" onclick="canjearRecompensa('kit_premium', 'Kit Catador Premium', 1000)">
                  <?= $puntos >= 1000 ? 'Canjear' : 'Bloqueado' ?>
                </button>
              </div>
            </div>

            <!-- Historial de Puntos -->
            <div style="margin-top:18px">
              <div style="font-size:11px;color:#999;text-transform:uppercase;letter-spacing:0.6px;margin-bottom:8px;font-weight:700;">Historial de Puntos</div>
              <div id="historial-puntos-lista">
                <?php if ($historial_list): ?>
                  <?php foreach ($historial_list as $h): ?>
                  <div class="hist-item">
                    <div>
                      <div class="hist-desc"><?= htmlspecialchars($h['descripcion']) ?></div>
                      <div class="hist-date"><?= date('d M Y, h:i A', strtotime($h['fecha'])) ?></div>
                    </div>
                    <div class="hist-pts <?= $h['puntos'] > 0 ? 'pts-pos' : 'pts-neg' ?>">
                      <?= $h['puntos'] > 0 ? '+' : '' ?><?= $h['puntos'] ?> pts
                    </div>
                  </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div style="font-size:12px;color:#aaa;padding:8px 0;">No tienes movimientos de puntos aún.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Columna Derecha: Cumpleaños + Acciones Rápidas -->
        <div style="display:flex;flex-direction:column;gap:20px">
          <div>
            <div class="sec-title"><i class="ti ti-cake"></i> Regalo de cumpleaños</div>
            <div class="card-box">
              <div class="bday-top">
                <i class="ti ti-gift"></i>
                <div>
                  <div class="bday-title">100 puntos en tu cumpleaños</div>
                  <div class="bday-sub">Recíbelos automáticamente cada año en tu fecha especial</div>
                </div>
              </div>
              <form id="form-bday" onsubmit="guardarCumpleanos(event)">
                <div class="bday-form">
                  <input type="date" id="input-bday" name="fecha_nacimiento"
                    value="<?= htmlspecialchars($usuario['fecha_nacimiento'] ?? '') ?>"
                    max="<?= date('Y-m-d') ?>" required>
                  <button type="submit" id="btn-save-bday">Guardar</button>
                </div>
              </form>
              <div class="bday-info <?= !empty($usuario['fecha_nacimiento']) ? 'bday-ok' : '' ?>" id="bday-info-box">
                <i class="ti ti-<?= !empty($usuario['fecha_nacimiento']) ? 'check' : 'info-circle' ?>"></i>
                <span id="bday-info-txt">
                  <?php if (!empty($usuario['fecha_nacimiento'])): ?>
                    <?php $d = new DateTime($usuario['fecha_nacimiento']); ?>
                    Cumpleaños registrado: <strong><?= $d->format('d') . ' de ' . $meses_es[(int)$d->format('n')-1] ?></strong>. Recibirás 100 puntos en tu día.
                  <?php else: ?>
                    Ingresa tu fecha de cumpleaños para recibir 100 puntos anuales automáticamente.
                  <?php endif; ?>
                </span>
              </div>
            </div>
          </div>

          <div>
            <div class="sec-title"><i class="ti ti-bolt"></i> Acciones rápidas</div>
            <div class="actions-grid">
              <div class="action-btn" onclick="repetirUltimoPedido()">
                <i class="ti ti-refresh"></i>
                <div><div class="action-txt">Repetir pedido</div><div class="action-sub">Pedir lo último de nuevo</div></div>
              </div>
              <a href="#favoritos" class="action-btn">
                <i class="ti ti-heart"></i>
                <div><div class="action-txt">Ver favoritos</div><div class="action-sub"><?= $num_favoritos ?> productos</div></div>
              </a>
              <div class="action-btn" onclick="abrirModalDireccion()">
                <i class="ti ti-plus"></i>
                <div><div class="action-txt">Nueva dirección</div><div class="action-sub">Agregar lugar de entrega</div></div>
              </div>
              <a href="/cafe/includes/servicios.php" class="action-btn">
                <i class="ti ti-coffee"></i>
                <div><div class="action-txt">Ir a la tienda</div><div class="action-sub">Explorar carta completa</div></div>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección Mis Pedidos (Detallado y unificado) -->
      <div id="pedidos">
        <div class="sec-title"><i class="ti ti-clock"></i> Mis pedidos realizados</div>
        <div id="contenedor-pedidos-cliente">
        <?php if (!empty($pedidos_list)): ?>
        <div class="orders-list" id="lista-pedidos-cliente">
          <?php foreach ($pedidos_list as $p):
            $num   = $p['numero_pedido'] ?: '#PED-' . str_pad($p['id'], 4, '0', STR_PAD_LEFT);
            $est_key = strtolower($p['estado']);
            $est   = $estados_label[$est_key] ?? ['texto' => ucfirst($p['estado']), 'clase' => 'proceso', 'icono' => 'ti-clock'];
            $prods = $p['productos_resumen'] ?: 'Productos de especialidad Tantico';
            $p_json = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
          ?>
          <div class="order-card" id="pedido-card-<?= $p['id'] ?>">
            <div class="order-left">
              <div class="order-icon-wrap"><i class="ti ti-coffee"></i></div>
              <div>
                <div class="order-num"><?= htmlspecialchars($num) ?></div>
                <div class="order-prod" title="<?= htmlspecialchars($prods) ?>"><?= htmlspecialchars($prods) ?></div>
                <div class="order-date"><i class="ti ti-calendar"></i> <?= date('d M Y, h:i A', strtotime($p['fecha'])) ?></div>
              </div>
            </div>
            <div class="order-right">
              <div class="order-price">$<?= number_format($p['total'], 0, ',', '.') ?></div>
              <div class="order-status s-<?= $est['clase'] ?>">
                <i class="ti <?= $est['icono'] ?>"></i> <?= $est['texto'] ?>
              </div>
              <button class="btn-ver-pedido" onclick='verDetallePedido(<?= $p_json ?>)'>
                <i class="ti ti-eye"></i> Detalle
              </button>
              <button class="btn-repetir-pedido" onclick='repetirPedidoEspecifico(<?= $p_json ?>)'>
                <i class="ti ti-repeat"></i> Pedir de nuevo
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="card-box" id="pedidos-vacio-box" style="text-align:center;padding:40px 20px;">
          <i class="ti ti-shopping-bag" style="font-size:36px;color:#d0c4bc;display:block;margin-bottom:10px;"></i>
          <div style="font-size:15px;font-weight:600;color:#333;">Aún no tienes pedidos registrados</div>
          <p style="font-size:12.5px;color:#888;margin:6px 0 16px;">¡Explora nuestra carta de cafés especiales del Huila y acumula tus primeros puntos!</p>
          <a href="/cafe/includes/servicios.php" class="btn-submit-cust" style="display:inline-block;width:auto;padding:0 24px;line-height:42px;text-decoration:none;">
            Explorar Productos →
          </a>
        </div>
        <?php endif; ?>
        </div>
      </div>

      <!-- Sección Direcciones -->
      <div id="direcciones">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
          <div class="sec-title" style="margin:0;"><i class="ti ti-map-pin"></i> Direcciones de entrega</div>
          <button class="btn-edit-perfil" style="background:var(--espresso);color:var(--crema);" onclick="abrirModalDireccion()">
            <i class="ti ti-plus"></i> Nueva dirección
          </button>
        </div>
        <div class="addr-grid" id="grid-direcciones">
          <?php foreach ($direcciones_list as $dir):
            $icono = $tipos_icono[$dir['tipo']] ?? 'ti-map-pin';
          ?>
          <div class="addr-card" id="addr-card-<?= $dir['id'] ?>">
            <div>
              <div class="addr-type"><i class="ti <?= $icono ?>"></i> <?= htmlspecialchars($dir['tipo']) ?></div>
              <div class="addr-line"><?= htmlspecialchars($dir['direccion']) ?></div>
              <div class="addr-city"><?= htmlspecialchars($dir['ciudad'] . ($dir['departamento'] ? ', ' . $dir['departamento'] : '')) ?></div>
            </div>
            <div class="addr-footer">
              <?php if (!empty($dir['principal'])): ?>
                <span class="badge-principal"><i class="ti ti-check"></i> Principal</span>
              <?php else: ?>
                <span></span>
              <?php endif; ?>
              <button class="btn-del-addr" onclick="eliminarDireccion(<?= $dir['id'] ?>)" title="Eliminar dirección">
                <i class="ti ti-trash"></i>
              </button>
            </div>
          </div>
          <?php endforeach; ?>
          <div class="addr-card add-new" onclick="abrirModalDireccion()">
            <i class="ti ti-plus" style="font-size:24px;color:var(--latte)"></i>
            <div style="font-size:13px;font-weight:600;color:#555;">Agregar dirección</div>
            <div style="font-size:11px;color:#999;">Para envíos más rápidos</div>
          </div>
        </div>
      </div>

      <!-- Métodos de Pago Preferidos -->
      <div id="pagos">
        <div class="sec-title"><i class="ti ti-credit-card"></i> Método de pago preferido</div>
        <div class="card-box">
          <p style="font-size:12.5px;color:#777;margin-bottom:14px;">Selecciona cómo prefieres pagar tus pedidos en Tantico:</p>
          <div class="pagos-grid">
            <div class="pago-card <?= $metodo_pago_preferido === 'Contra entrega en efectivo' ? 'selected' : '' ?>" onclick="seleccionarMetodoPago('Contra entrega en efectivo', this)">
              <div class="pago-icon"><i class="ti ti-cash"></i></div>
              <div>
                <div class="pago-title">Efectivo contra entrega</div>
                <div class="pago-sub">Pagas al recibir en tu domicilio</div>
              </div>
            </div>
            <div class="pago-card <?= $metodo_pago_preferido === 'Transferencia Nequi / Daviplata' ? 'selected' : '' ?>" onclick="seleccionarMetodoPago('Transferencia Nequi / Daviplata', this)">
              <div class="pago-icon"><i class="ti ti-device-mobile"></i></div>
              <div>
                <div class="pago-title">Nequi / Daviplata</div>
                <div class="pago-sub">Transferencia directa sin recargo</div>
              </div>
            </div>
            <div class="pago-card <?= $metodo_pago_preferido === 'Tarjeta de crédito / débito' ? 'selected' : '' ?>" onclick="seleccionarMetodoPago('Tarjeta de crédito / débito', this)">
              <div class="pago-icon"><i class="ti ti-credit-card"></i></div>
              <div>
                <div class="pago-title">Tarjeta Crédito / Débito</div>
                <div class="pago-sub">Datafono inalámbrico a domicilio</div>
              </div>
            </div>
            <div class="pago-card <?= $metodo_pago_preferido === 'Recoger y pagar en tienda Tantico' ? 'selected' : '' ?>" onclick="seleccionarMetodoPago('Recoger y pagar en tienda Tantico', this)">
              <div class="pago-icon"><i class="ti ti-building-store"></i></div>
              <div>
                <div class="pago-title">Pagar en tienda física</div>
                <div class="pago-sub">Recoges tu pedido en cafetería</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sección Favoritos -->
      <div id="favoritos">
        <div class="sec-title"><i class="ti ti-heart"></i> Mis productos favoritos</div>
        <?php if (!empty($favoritos_list)): ?>
        <div class="favoritos-grid" id="grid-favoritos">
          <?php foreach ($favoritos_list as $fav):
            $precio_fmt = '$' . number_format($fav['precio'], 0, ',', '.');
            $imagen_src = $fav['imagen'] ? '/cafe/' . htmlspecialchars($fav['imagen']) : '';
            $icono_fav  = htmlspecialchars($fav['icono'] ?: 'fa-mug-hot');
            $nombre_fav = htmlspecialchars($fav['nombre']);
            $desc_fav   = htmlspecialchars(mb_strimwidth($fav['descripcion'] ?? '', 0, 60, '…'));
            $fav_json   = htmlspecialchars(json_encode($fav), ENT_QUOTES, 'UTF-8');
          ?>
          <div class="fav-card" data-id="<?= $fav['id'] ?>">
            <div class="fav-img">
              <?php if ($imagen_src): ?>
                <img src="<?= $imagen_src ?>" alt="<?= $nombre_fav ?>"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="fav-img-fallback" style="display:none;"><i class="fas <?= $icono_fav ?>"></i></div>
              <?php else: ?>
                <div class="fav-img-fallback"><i class="fas <?= $icono_fav ?>"></i></div>
              <?php endif; ?>
            </div>
            <div class="fav-info">
              <div>
                <div class="fav-nombre"><?= $nombre_fav ?></div>
                <div class="fav-desc"><?= $desc_fav ?></div>
              </div>
              <div class="fav-footer">
                <span class="fav-precio"><?= $precio_fmt ?></span>
                <div class="fav-acciones">
                  <button class="fav-btn-comprar" onclick='comprarFavorito(<?= $fav_json ?>)' title="Agregar al carrito">
                    <i class="ti ti-shopping-bag"></i>
                  </button>
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
        <div class="card-box" style="text-align:center;padding:30px;">
          <i class="ti ti-heart" style="font-size:32px;color:#d0c4bc;margin-bottom:8px;display:block;"></i>
          No tienes productos favoritos aún.<br>
          <a href="/cafe/includes/servicios.php" style="color:var(--latte);font-size:13px;margin-top:8px;display:inline-block;font-weight:600;">
            Explorar carta de productos →
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
    <a href="#resumen"     class="mobile-nav-btn active"><i class="ti ti-layout-dashboard"></i><span>Inicio</span></a>
    <a href="#pedidos"     class="mobile-nav-btn"><i class="ti ti-shopping-bag"></i><span>Pedidos</span></a>
    <a href="#puntos"      class="mobile-nav-btn"><i class="ti ti-award"></i><span>Puntos</span></a>
    <a href="#direcciones" class="mobile-nav-btn"><i class="ti ti-map-pin"></i><span>Dirección</span></a>
    <a href="#favoritos"   class="mobile-nav-btn"><i class="ti ti-heart"></i><span>Favoritos</span></a>
    <button id="btn-logout-mob" class="mobile-nav-btn logout-mob"><i class="ti ti-logout"></i><span>Salir</span></button>
  </div>
</nav>

<!-- ══ MODAL EDITAR PERFIL / CONFIGURACIÓN ══ -->
<div class="custom-modal-overlay" id="modal-perfil">
  <div class="custom-modal-box">
    <div class="modal-header-cust">
      <h3 class="modal-title-cust"><i class="ti ti-user-edit"></i> Configurar Perfil</h3>
      <button class="modal-close-cust" onclick="cerrarModalPerfil()"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body-cust">
      <form id="form-edit-perfil" onsubmit="guardarPerfil(event)">
        <div class="form-group-cust">
          <label class="form-label-cust">Nombre Completo</label>
          <input type="text" id="perfil-nombre" class="form-input-cust" value="<?= htmlspecialchars($nombre_display) ?>" required>
        </div>
        <div class="form-group-cust">
          <label class="form-label-cust">Correo Electrónico (Google / Firebase)</label>
          <input type="email" class="form-input-cust" value="<?= htmlspecialchars($fb_email) ?>" disabled style="opacity:0.65;cursor:not-allowed;">
        </div>
        <div class="form-group-cust">
          <label class="form-label-cust">Teléfono / WhatsApp</label>
          <input type="tel" id="perfil-telefono" class="form-input-cust" value="<?= htmlspecialchars($telefono_display) ?>" placeholder="Ej: 310 123 4567">
        </div>
        <div class="form-group-cust">
          <label class="form-label-cust">Fecha de Nacimiento</label>
          <input type="date" id="perfil-cumple" class="form-input-cust" value="<?= htmlspecialchars($usuario['fecha_nacimiento'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
        </div>
        <button type="submit" class="btn-submit-cust" id="btn-guardar-perfil">Guardar Cambios</button>
      </form>
    </div>
  </div>
</div>

<!-- ══ MODAL AGREGAR DIRECCIÓN ══ -->
<div class="custom-modal-overlay" id="modal-direccion">
  <div class="custom-modal-box">
    <div class="modal-header-cust">
      <h3 class="modal-title-cust"><i class="ti ti-map-pin"></i> Nueva Dirección</h3>
      <button class="modal-close-cust" onclick="cerrarModalDireccion()"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body-cust">
      <form id="form-add-direccion" onsubmit="guardarDireccion(event)">
        <div class="form-group-cust">
          <label class="form-label-cust">Tipo de Lugar</label>
          <select id="dir-tipo" class="form-input-cust">
            <option value="Casa">🏠 Casa / Residencia</option>
            <option value="Trabajo">🏢 Oficina / Trabajo</option>
            <option value="Otra">📍 Otra ubicación</option>
          </select>
        </div>
        <div class="form-group-cust">
          <label class="form-label-cust">Dirección Exacta (Calle, Carrera, Apto, Edificio)</label>
          <input type="text" id="dir-linea" class="form-input-cust" placeholder="Ej: Calle 14 # 7-42 Apto 301" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="form-group-cust">
            <label class="form-label-cust">Ciudad / Municipio</label>
            <input type="text" id="dir-ciudad" class="form-input-cust" placeholder="Ej: Neiva, Pitalito, Garzón..." value="Neiva" required>
          </div>
          <div class="form-group-cust">
            <label class="form-label-cust">Departamento</label>
            <input type="text" id="dir-depto" class="form-input-cust" placeholder="Huila" value="Huila" required>
          </div>
        </div>
        <div style="margin:10px 0 14px;display:flex;align-items:center;gap:8px;">
          <input type="checkbox" id="dir-principal" style="width:16px;height:16px;accent-color:var(--latte);">
          <label for="dir-principal" style="font-size:12.5px;color:#444;cursor:pointer;">Marcar como dirección principal de entrega</label>
        </div>
        <button type="submit" class="btn-submit-cust" id="btn-save-dir">Guardar Dirección</button>
      </form>
    </div>
  </div>
</div>

<!-- ══ MODAL DETALLE DE PEDIDO & TRACKING ══ -->
<div class="custom-modal-overlay" id="modal-pedido-detalle">
  <div class="custom-modal-box" style="max-width:580px;">
    <div class="modal-header-cust">
      <h3 class="modal-title-cust" id="modal-ped-codigo"><i class="ti ti-receipt"></i> Detalle de Pedido</h3>
      <button class="modal-close-cust" onclick="cerrarModalPedido()"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body-cust" id="modal-ped-content">
      <!-- Se inyecta dinámicamente -->
    </div>
  </div>
</div>

<!-- ══ MODAL CANJE DE RECOMPENSAS / CUPÓN ══ -->
<div class="custom-modal-overlay" id="modal-cupon">
  <div class="custom-modal-box" style="text-align:center;">
    <div class="modal-header-cust" style="background:#2e7d32;">
      <h3 class="modal-title-cust"><i class="ti ti-gift"></i> ¡Recompensa Canjeada!</h3>
      <button class="modal-close-cust" onclick="cerrarModalCupon()"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body-cust" style="padding:28px 22px;">
      <i class="ti ti-circle-check" style="font-size:54px;color:#2e7d32;display:block;margin-bottom:12px;"></i>
      <h4 style="font-size:18px;font-weight:700;color:#1a1a1a;" id="cupon-titulo-rec">Bono Canjeado</h4>
      <p style="font-size:13px;color:#666;margin:8px 0 18px;">Usa este código en la tienda o menciónalo al ordenar:</p>
      <div style="background:#fdf5ee;border:2px dashed var(--latte);padding:14px;border-radius:12px;display:inline-flex;align-items:center;gap:12px;margin-bottom:18px;">
        <span style="font-size:20px;font-weight:800;letter-spacing:1px;color:var(--vino);" id="cupon-codigo-val">TAN-XXXX-XXXX</span>
        <button onclick="copiarCupon()" style="background:var(--latte);border:none;color:#fff;border-radius:6px;padding:6px 12px;font-size:12px;cursor:pointer;font-weight:600;">
          <i class="ti ti-copy"></i> Copiar
        </button>
      </div>
      <p style="font-size:12px;color:#888;">Tus puntos restantes: <strong id="cupon-pts-restantes">0</strong> pts</p>
      <a href="/cafe/includes/servicios.php" class="btn-submit-cust" style="display:inline-block;text-decoration:none;margin-top:10px;line-height:42px;">
        Ir a la Tienda a Usarlo
      </a>
    </div>
  </div>
</div>

<!-- Toast Popup -->
<div class="toast-popup" id="toast-notif">
  <i class="ti ti-check" id="toast-icon"></i>
  <span id="toast-msg">Mensaje</span>
</div>

<script>
// Notificación Toast
function mostrarToast(msg, icon = 'ti-check') {
  const t = document.getElementById('toast-notif');
  const txt = document.getElementById('toast-msg');
  const ic = document.getElementById('toast-icon');
  if (!t) return;
  txt.textContent = msg;
  ic.className = 'ti ' + icon;
  t.classList.add('visible');
  setTimeout(() => t.classList.remove('visible'), 3500);
}

// ── MODAL EDITAR PERFIL ──
function abrirModalPerfil() {
  document.getElementById('modal-perfil')?.classList.add('open');
}
function cerrarModalPerfil() {
  document.getElementById('modal-perfil')?.classList.remove('open');
}

async function guardarPerfil(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-guardar-perfil');
  const nombre = document.getElementById('perfil-nombre').value.trim();
  const telefono = document.getElementById('perfil-telefono').value.trim();
  const cumple = document.getElementById('perfil-cumple').value;

  btn.disabled = true;
  btn.textContent = 'Guardando...';

  try {
    const res = await fetch('/cafe/api/perfil.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nombre, telefono, fecha_nacimiento: cumple })
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById('hero-user-name').textContent = nombre;
      document.getElementById('sidebar-user-name').textContent = nombre;
      document.getElementById('input-bday').value = cumple;
      cerrarModalPerfil();
      mostrarToast('¡Perfil actualizado con éxito!');
    } else {
      alert(data.error || 'Error al guardar');
    }
  } catch (err) {
    alert('Error de conexión con el servidor');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Guardar Cambios';
  }
}

// ── CUMPLEAÑOS RÁPIDO ──
async function guardarCumpleanos(e) {
  e.preventDefault();
  const fn = document.getElementById('input-bday').value;
  const btn = document.getElementById('btn-save-bday');
  btn.disabled = true;
  btn.textContent = 'Guardando...';
  try {
    const res = await fetch('/cafe/api/perfil.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ fecha_nacimiento: fn })
    });
    const data = await res.json();
    if (data.success) {
      const bdayBox = document.getElementById('bday-info-box');
      const bdayTxt = document.getElementById('bday-info-txt');
      bdayBox.className = 'bday-info bday-ok';
      bdayTxt.innerHTML = `Cumpleaños registrado: <strong>${fn}</strong>. Recibirás 100 puntos en tu día.`;
      mostrarToast('¡Fecha de cumpleaños guardada!');
    }
  } catch (_) {
    alert('Error al guardar fecha');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Guardar';
  }
}

// ── MODAL DIRECCIÓN ──
function abrirModalDireccion() {
  document.getElementById('modal-direccion')?.classList.add('open');
}
function cerrarModalDireccion() {
  document.getElementById('modal-direccion')?.classList.remove('open');
}

async function guardarDireccion(e) {
  e.preventDefault();
  const btn = document.getElementById('btn-save-dir');
  const tipo = document.getElementById('dir-tipo').value;
  const direccion = document.getElementById('dir-linea').value.trim();
  const ciudad = document.getElementById('dir-ciudad').value.trim();
  const departamento = document.getElementById('dir-depto').value.trim();
  const principal = document.getElementById('dir-principal').checked ? 1 : 0;

  btn.disabled = true;
  btn.textContent = 'Guardando...';

  try {
    const res = await fetch('/cafe/api/direcciones.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ tipo, direccion, ciudad, departamento, principal })
    });
    const data = await res.json();
    if (data.success) {
      cerrarModalDireccion();
      mostrarToast('¡Dirección agregada!');
      setTimeout(() => location.reload(), 800);
    } else {
      alert(data.error || 'Error al guardar dirección');
    }
  } catch (_) {
    alert('Error de conexión');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Guardar Dirección';
  }
}

async function eliminarDireccion(id) {
  if (!confirm('¿Deseas eliminar esta dirección guardada?')) return;
  try {
    const res = await fetch('/cafe/api/direcciones.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'eliminar', id })
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById('addr-card-' + id)?.remove();
      const statDir = document.getElementById('stat-direcciones');
      if (statDir) statDir.textContent = Math.max(0, parseInt(statDir.textContent) - 1);
      mostrarToast('Dirección eliminada');
    }
  } catch (_) {
    alert('Error al eliminar');
  }
}

// ── CANJE DE RECOMPENSAS ──
async function canjearRecompensa(recId, nombre, costo) {
  const puntosActuales = parseInt(document.getElementById('stat-puntos-val')?.textContent || '0');
  if (puntosActuales < costo) {
    alert(`Te faltan ${costo - puntosActuales} puntos para canjear ${nombre}.`);
    return;
  }
  if (!confirm(`¿Confirmas canjear ${costo} puntos por ${nombre}?`)) return;

  try {
    const res = await fetch('/cafe/api/recompensas.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ recompensa_id: recId })
    });
    const data = await res.json();
    if (data.success) {
      document.getElementById('cupon-titulo-rec').textContent = data.recompensa;
      document.getElementById('cupon-codigo-val').textContent = data.cupon;
      document.getElementById('cupon-pts-restantes').textContent = data.puntos_restantes;
      document.getElementById('stat-puntos-val').textContent = data.puntos_restantes;
      document.getElementById('box-puntos-val').textContent = data.puntos_restantes;
      document.getElementById('modal-cupon')?.classList.add('open');
      mostrarToast('¡Canje realizado con éxito!');
    } else {
      alert(data.error || 'No se pudo realizar el canje.');
    }
  } catch (_) {
    alert('Error al procesar el canje');
  }
}
function cerrarModalCupon() { document.getElementById('modal-cupon')?.classList.remove('open'); }
function copiarCupon() {
  const codigo = document.getElementById('cupon-codigo-val')?.textContent || '';
  navigator.clipboard.writeText(codigo).then(() => mostrarToast('¡Cupón copiado al portapapeles!'));
}

// ── MÉTODOS DE PAGO ──
async function seleccionarMetodoPago(metodo, cardEl) {
  document.querySelectorAll('.pago-card').forEach(c => c.classList.remove('selected'));
  cardEl.classList.add('selected');
  try {
    await fetch('/cafe/api/perfil.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'metodo_pago', metodo_pago: metodo })
    });
    mostrarToast(`Método preferido: ${metodo}`);
  } catch (_) {}
}

// ── DETALLE DE PEDIDO & TRACKING EN VIVO ──
let misPedidosActuales = <?= json_encode($pedidos_list) ?>;
let pedidoDetalleAbiertoId = null;
let syncEnProgreso = false;

const MAPA_ESTADOS = {
  pendiente:  { texto: 'Pendiente',      clase: 'proceso',   icono: 'ti-hourglass-low' },
  procesando: { texto: 'En preparación', clase: 'proceso',   icono: 'ti-flame' },
  proceso:    { texto: 'En preparación', clase: 'proceso',   icono: 'ti-flame' },
  enviado:    { texto: 'En camino',      clase: 'camino',    icono: 'ti-truck-delivery' },
  entregado:  { texto: 'Entregado',      clase: 'entregado', icono: 'ti-circle-check' },
  cancelado:  { texto: 'Cancelado',      clase: 'cancelado', icono: 'ti-circle-x' },
};

function getEstadoInfoCliente(estado) {
  const k = (estado || 'pendiente').toLowerCase();
  return MAPA_ESTADOS[k] || { texto: estado ? estado.charAt(0).toUpperCase() + estado.slice(1) : 'Pendiente', clase: 'proceso', icono: 'ti-clock' };
}

function verDetallePedido(p) {
  pedidoDetalleAbiertoId = p.id;
  const modal = document.getElementById('modal-pedido-detalle');
  const box = document.getElementById('modal-ped-content');
  const codigo = p.numero_pedido || ('#PED-' + p.id);
  document.getElementById('modal-ped-codigo').innerHTML = `<i class="ti ti-receipt"></i> Pedido ${codigo}`;

  const estKey = (p.estado || 'pendiente').toLowerCase();
  let step1 = 'done', step2 = '', step3 = '', step4 = '';
  if (estKey === 'procesando' || estKey === 'proceso') { step2 = 'active'; }
  else if (estKey === 'enviado') { step2 = 'done'; step3 = 'active'; }
  else if (estKey === 'entregado') { step2 = 'done'; step3 = 'done'; step4 = 'done active'; }

  let itemsRows = '';
  if (p.items && p.items.length) {
    itemsRows = p.items.map(it => `
      <tr>
        <td><strong>${it.nombre || 'Producto #' + it.producto_id}</strong></td>
        <td style="text-align:center;">${it.cantidad}</td>
        <td style="text-align:right;">$${parseInt(it.precio_unitario).toLocaleString('es-CO')}</td>
        <td style="text-align:right;"><strong>$${(it.cantidad * it.precio_unitario).toLocaleString('es-CO')}</strong></td>
      </tr>
    `).join('');
  } else {
    itemsRows = `<tr><td colspan="4" style="text-align:center;color:#888;">${p.productos_resumen || 'Detalle no disponible'}</td></tr>`;
  }

  const pJson = JSON.stringify(p).replace(/"/g, '&quot;');

  box.innerHTML = `
    <div class="modal-timeline">
      <div class="timeline-step ${step1}"><div class="timeline-dot"><i class="ti ti-check"></i></div><div class="timeline-label">Confirmado</div></div>
      <div class="timeline-step ${step2}"><div class="timeline-dot"><i class="ti ti-flame"></i></div><div class="timeline-label">En preparación</div></div>
      <div class="timeline-step ${step3}"><div class="timeline-dot"><i class="ti ti-truck-delivery"></i></div><div class="timeline-label">En camino</div></div>
      <div class="timeline-step ${step4}"><div class="timeline-dot"><i class="ti ti-circle-check"></i></div><div class="timeline-label">Entregado</div></div>
    </div>

    <div style="background:#faf6f0;padding:14px;border-radius:10px;margin-bottom:16px;font-size:12.5px;">
      <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
        <span style="color:#777;">Fecha de orden:</span>
        <strong>${new Date(p.fecha).toLocaleDateString('es-CO', {day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})}</strong>
      </div>
      <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
        <span style="color:#777;">Destino de entrega:</span>
        <strong>${p.direccion || 'Dirección registrada'}, ${p.ciudad || 'Neiva'}</strong>
      </div>
      <div style="display:flex;justify-content:space-between;">
        <span style="color:#777;">Destinatario:</span>
        <strong>${p.nombre || ''} ${p.apellido || ''} ${p.telefono ? '(' + p.telefono + ')' : ''}</strong>
      </div>
    </div>

    <div style="font-size:12px;font-weight:700;text-transform:uppercase;color:#777;margin-bottom:6px;">Productos Ordenados</div>
    <table class="modal-items-table">
      <thead>
        <tr>
          <th>Producto</th>
          <th style="text-align:center;">Cant.</th>
          <th style="text-align:right;">Precio</th>
          <th style="text-align:right;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        ${itemsRows}
      </tbody>
    </table>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding-top:12px;border-top:2px solid #e8ddd5;">
      <span style="font-size:15px;font-weight:700;color:#222;">Total del Pedido:</span>
      <span style="font-size:18px;font-weight:800;color:var(--vino);">$${parseFloat(p.total).toLocaleString('es-CO')} COP</span>
    </div>

    <div style="margin-top:16px;">
      <button class="btn-submit-cust" onclick='repetirPedidoEspecifico(${pJson})'>
        <i class="ti ti-repeat"></i> Volver a Pedir Estos Productos
      </button>
    </div>
  `;

  modal.classList.add('open');
}

function cerrarModalPedido() {
  pedidoDetalleAbiertoId = null;
  document.getElementById('modal-pedido-detalle')?.classList.remove('open');
}

function generarHTMLTarjetaPedido(p) {
  const num = p.numero_pedido || ('#PED-' + String(p.id).padStart(4, '0'));
  const est = getEstadoInfoCliente(p.estado);
  const prods = p.productos_resumen || (p.items && p.items.length ? p.items.map(i => `${i.cantidad}x ${i.nombre}`).join(' + ') : 'Productos de especialidad Tantico');
  const pJson = JSON.stringify(p).replace(/"/g, '&quot;');
  const fechaStr = new Date(p.fecha).toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

  return `
    <div class="order-card" id="pedido-card-${p.id}" style="animation:fadeInModal 0.3s ease;">
      <div class="order-left">
        <div class="order-icon-wrap"><i class="ti ti-coffee"></i></div>
        <div>
          <div class="order-num">${num}</div>
          <div class="order-prod" title="${prods}">${prods}</div>
          <div class="order-date"><i class="ti ti-calendar"></i> ${fechaStr}</div>
        </div>
      </div>
      <div class="order-right">
        <div class="order-price">$${parseInt(p.total).toLocaleString('es-CO')}</div>
        <div class="order-status s-${est.clase}">
          <i class="ti ${est.icono}"></i> ${est.texto}
        </div>
        <button class="btn-ver-pedido" onclick='verDetallePedido(${pJson})'>
          <i class="ti ti-eye"></i> Detalle
        </button>
        <button class="btn-repetir-pedido" onclick='repetirPedidoEspecifico(${pJson})'>
          <i class="ti ti-repeat"></i> Pedir de nuevo
        </button>
      </div>
    </div>
  `;
}

function renderizarTodosLosPedidosCliente(pedidos) {
  const cont = document.getElementById('contenedor-pedidos-cliente');
  if (!cont) return;
  if (!pedidos || !pedidos.length) {
    cont.innerHTML = `
      <div class="card-box" id="pedidos-vacio-box" style="text-align:center;padding:40px 20px;">
        <i class="ti ti-shopping-bag" style="font-size:36px;color:#d0c4bc;display:block;margin-bottom:10px;"></i>
        <div style="font-size:15px;font-weight:600;color:#333;">Aún no tienes pedidos registrados</div>
        <p style="font-size:12.5px;color:#888;margin:6px 0 16px;">¡Explora nuestra carta de cafés especiales del Huila y acumula tus primeros puntos!</p>
        <a href="/cafe/includes/servicios.php" class="btn-submit-cust" style="display:inline-block;width:auto;padding:0 24px;line-height:42px;text-decoration:none;">
          Explorar Productos →
        </a>
      </div>
    `;
    return;
  }
  cont.innerHTML = `
    <div class="orders-list" id="lista-pedidos-cliente">
      ${pedidos.map(p => generarHTMLTarjetaPedido(p)).join('')}
    </div>
  `;
}

// ── Sincronización continua en vivo (sin recargar la página) ──
async function sincronizarPedidosEnVivo() {
  if (syncEnProgreso) return;
  syncEnProgreso = true;

  try {
    const res = await fetch('/cafe/api/orders.php?mis_pedidos=1');
    const data = await res.json();

    if (data.success && Array.isArray(data.pedidos)) {
      const nuevosPedidos = data.pedidos;
      const idsPrevios = new Set(misPedidosActuales.map(p => parseInt(p.id)));

      // 1. Detectar pedidos nuevos agregados
      const pedidosNuevos = nuevosPedidos.filter(p => !idsPrevios.has(parseInt(p.id)));
      if (pedidosNuevos.length > 0 || nuevosPedidos.length !== misPedidosActuales.length) {
        renderizarTodosLosPedidosCliente(nuevosPedidos);
        pedidosNuevos.forEach(p => {
          const cod = p.numero_pedido || ('#' + p.id);
          mostrarToast(`✨ ¡Nuevo pedido ${cod} registrado!`);
        });
      } else {
        // 2. Detectar cambios de estado en pedidos ya existentes
        nuevosPedidos.forEach(pNuevo => {
          const pAntiguo = misPedidosActuales.find(p => parseInt(p.id) === parseInt(pNuevo.id));
          if (pAntiguo && pAntiguo.estado !== pNuevo.estado) {
            const estInfo = getEstadoInfoCliente(pNuevo.estado);
            const card = document.getElementById(`pedido-card-${pNuevo.id}`);
            if (card) {
              const statusEl = card.querySelector('.order-status');
              if (statusEl) {
                statusEl.className = `order-status s-${estInfo.clase}`;
                statusEl.innerHTML = `<i class="ti ${estInfo.icono}"></i> ${estInfo.texto}`;
              }
              const btnVer = card.querySelector('.btn-ver-pedido');
              if (btnVer) {
                btnVer.onclick = () => verDetallePedido(pNuevo);
              }
              const btnRep = card.querySelector('.btn-repetir-pedido');
              if (btnRep) {
                btnRep.onclick = () => repetirPedidoEspecifico(pNuevo);
              }
            }

            const cod = pNuevo.numero_pedido || ('#' + pNuevo.id);
            mostrarToast(`📦 Tu pedido ${cod} ahora está: ${estInfo.texto.toUpperCase()}`);

            if (pedidoDetalleAbiertoId === pNuevo.id) {
              verDetallePedido(pNuevo);
            }
          }
        });
      }

      // Actualizar contador total de pedidos en la tarjeta de estadísticas
      const statPedidos = document.getElementById('stat-pedidos-val');
      if (statPedidos) statPedidos.textContent = nuevosPedidos.length;

      misPedidosActuales = nuevosPedidos;
    }

    // 3. Sincronizar puntos y balance en segundo plano
    const resPerfil = await fetch('/cafe/api/perfil.php');
    const dataPerfil = await resPerfil.json();
    if (dataPerfil.success && dataPerfil.usuario) {
      actualizarPuntosEnUI(dataPerfil.usuario.puntos);
    }

  } catch (err) {
    console.error('[Perfil] Error sincronizando pedidos:', err);
  } finally {
    syncEnProgreso = false;
  }
}

function actualizarPuntosEnUI(pts) {
  pts = parseInt(pts) || 0;

  const statPts = document.getElementById('stat-puntos-val');
  if (statPts) statPts.textContent = pts;
  const boxPts = document.getElementById('box-puntos-val');
  if (boxPts) boxPts.textContent = pts;

  const nivel = pts >= 1000 ? 'Oro' : (pts >= 500 ? 'Plata' : 'Espresso');
  const ptsSiguiente = pts >= 1000 ? 1000 : (pts >= 500 ? 1000 : 500);
  const progresoPct = Math.min(100, Math.round((pts / ptsSiguiente) * 100));
  const ptsFaltan = Math.max(0, ptsSiguiente - pts);

  const fillEl = document.getElementById('progreso-fill');
  if (fillEl) fillEl.style.width = progresoPct + '%';

  const txtEl = document.getElementById('progreso-pts-txt');
  if (txtEl) txtEl.textContent = `${pts} / ${ptsSiguiente} pts`;

  const msgBox = document.getElementById('pts-msg-box');
  if (msgBox) {
    if (ptsFaltan > 0) {
      msgBox.innerHTML = `<i class="ti ti-coffee"></i> Te faltan <strong>${ptsFaltan} puntos</strong> para el siguiente nivel`;
    } else {
      msgBox.innerHTML = `<i class="ti ti-trophy"></i> ¡Alcanzaste el nivel máximo de cliente VIP Oro!`;
    }
  }

  // Recompensas
  const r300 = document.getElementById('rec-card-300');
  if (r300) {
    r300.className = `reward-card ${pts >= 300 ? 'unlocked' : 'locked'}`;
    const b = r300.querySelector('.btn-canjear');
    if (b) b.textContent = pts >= 300 ? 'Canjear' : 'Bloqueado';
  }
  const r500 = document.getElementById('rec-card-500');
  if (r500) {
    r500.className = `reward-card ${pts >= 500 ? 'unlocked' : 'locked'}`;
    const b = r500.querySelector('.btn-canjear');
    if (b) b.textContent = pts >= 500 ? 'Canjear' : 'Bloqueado';
  }
  const r1000 = document.getElementById('rec-card-1000');
  if (r1000) {
    r1000.className = `reward-card ${pts >= 1000 ? 'unlocked' : 'locked'}`;
    const b = r1000.querySelector('.btn-canjear');
    if (b) b.textContent = pts >= 1000 ? 'Canjear' : 'Bloqueado';
  }
}

// Iniciar sondeo en segundo plano cada 2 segundos
setInterval(sincronizarPedidosEnVivo, 2000);

// Escuchar eventos en vivo desde otras pestañas (checkout, panel admin) con latencia cero
try {
  const perfilBC = new BroadcastChannel('tantico_channel');
  perfilBC.onmessage = (e) => {
    if (e.data) {
      if (e.data.puntos_totales !== undefined && e.data.puntos_totales !== null) {
        actualizarPuntosEnUI(e.data.puntos_totales);
      }
      if (e.data.type === 'pedido_actualizado' || e.data.type === 'nuevo_pedido' || e.data.type === 'puntos_actualizados') {
        sincronizarPedidosEnVivo();
      }
    }
  };
} catch (_) {}

window.addEventListener('storage', (e) => {
  if (e.key === 'tantico_puntos_actualizados') {
    try {
      const pData = JSON.parse(e.newValue);
      if (pData && pData.puntos !== undefined) actualizarPuntosEnUI(pData.puntos);
    } catch (_) {}
  }
  if (e.key === 'tantico_pedido_actualizado' || e.key === 'tantico_nuevo_pedido' || e.key === 'tantico_puntos_actualizados') {
    sincronizarPedidosEnVivo();
  }
});

// ── REPETIR PEDIDOS EN CARRITO ──
const CART_KEY = 'coffeecol_carrito_v2';

function repetirPedidoEspecifico(p) {
  if (!p || !p.items || !p.items.length) {
    window.location.href = '/cafe/includes/servicios.php';
    return;
  }
  try {
    const carrito = JSON.parse(localStorage.getItem(CART_KEY) || '{}');
    p.items.forEach(it => {
      const pid = it.producto_id || it.id;
      if (pid) {
        carrito[pid] = {
          id: parseInt(pid),
          nombre: it.nombre || 'Café Tantico',
          precio: parseFloat(it.precio_unitario),
          cantidad: (carrito[pid]?.cantidad || 0) + (parseInt(it.cantidad) || 1),
          imagen: it.imagen || '',
          icono: it.icono || 'fa-mug-hot'
        };
      }
    });
    localStorage.setItem(CART_KEY, JSON.stringify(carrito));
    sessionStorage.setItem('abrir_carrito', '1');
    mostrarToast('¡Productos agregados al carrito!');
    setTimeout(() => { window.location.href = '/cafe/includes/servicios.php'; }, 700);
  } catch (e) {
    window.location.href = '/cafe/includes/servicios.php';
  }
}

function repetirUltimoPedido() {
  <?php if (!empty($pedidos_list[0])): ?>
    repetirPedidoEspecifico(<?= json_encode($pedidos_list[0]) ?>);
  <?php else: ?>
    mostrarToast('Aún no tienes pedidos previos para repetir', 'ti-info-circle');
    setTimeout(() => { window.location.href = '/cafe/includes/servicios.php'; }, 800);
  <?php endif; ?>
}

function comprarFavorito(fav) {
  try {
    const carrito = JSON.parse(localStorage.getItem(CART_KEY) || '{}');
    const pid = fav.id;
    carrito[pid] = {
      id: parseInt(pid),
      nombre: fav.nombre,
      precio: parseFloat(fav.precio),
      cantidad: (carrito[pid]?.cantidad || 0) + 1,
      imagen: fav.imagen || '',
      icono: fav.icono || 'fa-mug-hot'
    };
    localStorage.setItem(CART_KEY, JSON.stringify(carrito));
    sessionStorage.setItem('abrir_carrito', '1');
    mostrarToast(`¡${fav.nombre} agregado al carrito!`);
    setTimeout(() => { window.location.href = '/cafe/includes/servicios.php'; }, 600);
  } catch (_) {
    window.location.href = '/cafe/includes/servicios.php';
  }
}

// Quitar favorito con animación
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
        card.style.transform  = 'scale(0.85)';
        card.style.opacity    = '0';
        setTimeout(() => {
          card.remove();
          const grid = document.getElementById('grid-favoritos');
          if (grid && grid.children.length === 0) {
            grid.outerHTML = `<div class="card-box" style="text-align:center;padding:30px;">
              <i class="ti ti-heart" style="font-size:32px;color:#d0c4bc;margin-bottom:8px;display:block;"></i>
              No tienes productos favoritos aún.<br>
              <a href="/cafe/includes/servicios.php" style="color:var(--latte);font-size:13px;margin-top:8px;display:inline-block;font-weight:600;">
                Explorar carta de productos →
              </a>
            </div>`;
          }
          const statVal = document.getElementById('stat-favoritos');
          if (statVal) statVal.textContent = Math.max(0, parseInt(statVal.textContent) - 1);
          mostrarToast('Producto eliminado de tus favoritos');
        }, 300);
      } else {
        card.classList.remove('quitando');
      }
    } catch (_) {
      card.classList.remove('quitando');
    }
  });
});

// Cerrar modales con tecla Escape o click afuera
document.querySelectorAll('.custom-modal-overlay').forEach(modal => {
  modal.addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') document.querySelectorAll('.custom-modal-overlay').forEach(m => m.classList.remove('open'));
});
</script>

<script type="module">
  import { cerrarSesion } from '/cafe/js/auth.js';

  document.getElementById('btn-logout-perfil')?.addEventListener('click', async () => {
    try { await cerrarSesion(); } catch(e) {}
    await fetch('/cafe/includes/cerrar_sesion.php');
    window.location.href = '/cafe/includes/loginu.php';
  });

  document.getElementById('btn-logout-mob')?.addEventListener('click', async () => {
    try { await cerrarSesion(); } catch(e) {}
    await fetch('/cafe/includes/cerrar_sesion.php');
    window.location.href = '/cafe/includes/loginu.php';
  });
</script>

</body>
</html>