<?php
/**
 * ══════════════════════════════════════════════════
 *  CoffeeCol — API de Reportes (admin)
 *  GET /api/reports.php?desde=YYYY-MM-DD&hasta=YYYY-MM-DD
 * ══════════════════════════════════════════════════
 *
 *  Devuelve JSON con:
 *    - kpis: ventas_hoy, ventas_mes, pedidos_hoy, ticket_promedio, producto_top
 *    - ventas_diarias: [{dia, pedidos, ingresos}]
 *    - productos_top: [{nombre, vendidos, total}]
 *    - ventas_categoria: [{categoria, total}]
 *    - tendencia_mensual: [{mes, pedidos, ingresos}]
 *    - tabla_detalle: [{fecha, pedidos, ingresos, ticket_promedio, estado_resumen}]
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

/* ── Conexión DB ── */
$vendorPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorPath)) {
    require_once $vendorPath;
    $envFile = __DIR__ . '/..';
    if (class_exists('Dotenv\\Dotenv') && file_exists($envFile . '/.env')) {
        $dotenv = \Dotenv\Dotenv::createImmutable($envFile);
        $dotenv->safeLoad();
    }
}

try {
    $pdo = new PDO(
        'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';dbname=' . ($_ENV['DB_NAME'] ?? 'coffeecol') . ';charset=utf8mb4',
        $_ENV['DB_USER'] ?? 'root',
        $_ENV['DB_PASS'] ?? '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
    exit;
}

/* ── Verificar JWT admin ── */
function verificarAdmin(): void {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION']
        ?? getallheaders()['Authorization']
        ?? '';

    if (!str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Token requerido.']);
        exit;
    }

    $token = substr($authHeader, 7);

    $vendorJWT = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($vendorJWT)) {
        require_once $vendorJWT;

        $envFile = __DIR__ . '/..';
        if (class_exists('Dotenv\\Dotenv') && file_exists($envFile . '/.env')) {
            $dotenv = \Dotenv\Dotenv::createImmutable($envFile);
            $dotenv->safeLoad();
        }

        try {
            $secret = $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?? '';
            if ($secret) {
                \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($secret, 'HS256'));
                return;
            }
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Token inválido.']);
            exit;
        }
    }

    // Fallback: validación manual
    try {
        $parts = explode('.', $token);
        if (count($parts) !== 3) throw new RuntimeException('Token malformado');
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!$payload || empty($payload['sub'])) throw new RuntimeException('Payload inválido');
        if (!empty($payload['exp']) && $payload['exp'] < time()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Token expirado.']);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Token inválido.']);
        exit;
    }
}

verificarAdmin();

/* ── Solo GET ── */
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

/* ── Parámetros de fecha ── */
$desde = $_GET['desde'] ?? date('Y-m-01');       // Primer día del mes actual
$hasta = $_GET['hasta'] ?? date('Y-m-d');         // Hoy

// Validar formato YYYY-MM-DD
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Formato de fecha inválido. Usar YYYY-MM-DD.']);
    exit;
}

// Ajustar 'hasta' para incluir todo el día
$hastaFull = $hasta . ' 23:59:59';

/* ══════════════════════════════════════════════════
   1. KPIs
══════════════════════════════════════════════════ */

// Ventas totales del día
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(total), 0) AS ventas_hoy
    FROM pedidos
    WHERE DATE(fecha) = CURDATE() AND estado != 'cancelado'
");
$stmt->execute();
$ventasHoy = (float) $stmt->fetchColumn();

// Pedidos del día
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS pedidos_hoy
    FROM pedidos
    WHERE DATE(fecha) = CURDATE() AND estado != 'cancelado'
");
$stmt->execute();
$pedidosHoy = (int) $stmt->fetchColumn();

// Ventas totales del mes
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(total), 0) AS ventas_mes
    FROM pedidos
    WHERE YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE()) AND estado != 'cancelado'
");
$stmt->execute();
$ventasMes = (float) $stmt->fetchColumn();

// Pedidos del mes (para ticket promedio)
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS pedidos_mes
    FROM pedidos
    WHERE YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE()) AND estado != 'cancelado'
");
$stmt->execute();
$pedidosMes = (int) $stmt->fetchColumn();

// Ticket promedio (del rango seleccionado)
$stmt = $pdo->prepare("
    SELECT COALESCE(AVG(total), 0) AS ticket_promedio
    FROM pedidos
    WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'
");
$stmt->execute([$desde, $hastaFull]);
$ticketPromedio = (float) $stmt->fetchColumn();

// Producto más vendido (del rango)
$stmt = $pdo->prepare("
    SELECT p.nombre, SUM(pi.cantidad) AS vendidos
    FROM pedido_items pi
    JOIN productos p ON p.id = pi.producto_id
    JOIN pedidos pe ON pe.id = pi.pedido_id
    WHERE pe.fecha BETWEEN ? AND ? AND pe.estado != 'cancelado'
    GROUP BY pi.producto_id, p.nombre
    ORDER BY vendidos DESC
    LIMIT 1
");
$stmt->execute([$desde, $hastaFull]);
$productoTop = $stmt->fetch();

$kpis = [
    'ventas_hoy'      => $ventasHoy,
    'ventas_mes'       => $ventasMes,
    'pedidos_hoy'      => $pedidosHoy,
    'ticket_promedio'  => round($ticketPromedio, 0),
    'producto_top'     => $productoTop ? $productoTop['nombre'] : '—',
    'producto_top_qty' => $productoTop ? (int) $productoTop['vendidos'] : 0,
];

/* ══════════════════════════════════════════════════
   2. Ventas diarias (gráfico de líneas)
══════════════════════════════════════════════════ */
$stmt = $pdo->prepare("
    SELECT DATE(fecha) AS dia,
           COUNT(*)    AS pedidos,
           COALESCE(SUM(total), 0) AS ingresos
    FROM pedidos
    WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'
    GROUP BY DATE(fecha)
    ORDER BY dia ASC
");
$stmt->execute([$desde, $hastaFull]);
$ventasDiarias = $stmt->fetchAll();

// Castear tipos
foreach ($ventasDiarias as &$v) {
    $v['pedidos']  = (int) $v['pedidos'];
    $v['ingresos'] = (float) $v['ingresos'];
}
unset($v);

/* ══════════════════════════════════════════════════
   3. Productos más vendidos (gráfico de barras)
══════════════════════════════════════════════════ */
$stmt = $pdo->prepare("
    SELECT p.nombre,
           SUM(pi.cantidad) AS vendidos,
           COALESCE(SUM(pi.cantidad * pi.precio_unitario), 0) AS total
    FROM pedido_items pi
    JOIN productos p ON p.id = pi.producto_id
    JOIN pedidos pe ON pe.id = pi.pedido_id
    WHERE pe.fecha BETWEEN ? AND ? AND pe.estado != 'cancelado'
    GROUP BY pi.producto_id, p.nombre
    ORDER BY vendidos DESC
    LIMIT 10
");
$stmt->execute([$desde, $hastaFull]);
$productosTop = $stmt->fetchAll();

foreach ($productosTop as &$pt) {
    $pt['vendidos'] = (int) $pt['vendidos'];
    $pt['total']    = (float) $pt['total'];
}
unset($pt);

/* ══════════════════════════════════════════════════
   4. Ventas por categoría (gráfico circular)
══════════════════════════════════════════════════ */
$stmt = $pdo->prepare("
    SELECT COALESCE(p.categoria, 'sin-categoria') AS categoria,
           COALESCE(SUM(pi.cantidad * pi.precio_unitario), 0) AS total
    FROM pedido_items pi
    JOIN productos p ON p.id = pi.producto_id
    JOIN pedidos pe ON pe.id = pi.pedido_id
    WHERE pe.fecha BETWEEN ? AND ? AND pe.estado != 'cancelado'
    GROUP BY p.categoria
    ORDER BY total DESC
");
$stmt->execute([$desde, $hastaFull]);
$ventasCategoria = $stmt->fetchAll();

foreach ($ventasCategoria as &$vc) {
    $vc['total'] = (float) $vc['total'];
}
unset($vc);

/* ══════════════════════════════════════════════════
   5. Tendencia mensual (últimos 12 meses)
══════════════════════════════════════════════════ */
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes,
           COUNT(*)    AS pedidos,
           COALESCE(SUM(total), 0) AS ingresos
    FROM pedidos
    WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND estado != 'cancelado'
    GROUP BY DATE_FORMAT(fecha, '%Y-%m')
    ORDER BY mes ASC
");
$stmt->execute();
$tendenciaMensual = $stmt->fetchAll();

foreach ($tendenciaMensual as &$tm) {
    $tm['pedidos']  = (int) $tm['pedidos'];
    $tm['ingresos'] = (float) $tm['ingresos'];
}
unset($tm);

/* ══════════════════════════════════════════════════
   6. Tabla detalle por fecha
══════════════════════════════════════════════════ */
$stmt = $pdo->prepare("
    SELECT DATE(fecha) AS fecha,
           COUNT(*)    AS pedidos,
           COALESCE(SUM(total), 0) AS ingresos,
           COALESCE(AVG(total), 0) AS ticket_promedio,
           SUM(CASE WHEN estado = 'pendiente'  THEN 1 ELSE 0 END) AS pendientes,
           SUM(CASE WHEN estado = 'procesando' THEN 1 ELSE 0 END) AS procesando,
           SUM(CASE WHEN estado = 'enviado'    THEN 1 ELSE 0 END) AS enviados,
           SUM(CASE WHEN estado = 'entregado'  THEN 1 ELSE 0 END) AS entregados,
           SUM(CASE WHEN estado = 'cancelado'  THEN 1 ELSE 0 END) AS cancelados
    FROM pedidos
    WHERE fecha BETWEEN ? AND ?
    GROUP BY DATE(fecha)
    ORDER BY fecha DESC
");
$stmt->execute([$desde, $hastaFull]);
$tablaDetalle = $stmt->fetchAll();

foreach ($tablaDetalle as &$td) {
    $td['pedidos']         = (int) $td['pedidos'];
    $td['ingresos']        = (float) $td['ingresos'];
    $td['ticket_promedio'] = round((float) $td['ticket_promedio'], 0);
    $td['pendientes']      = (int) $td['pendientes'];
    $td['procesando']      = (int) $td['procesando'];
    $td['enviados']        = (int) $td['enviados'];
    $td['entregados']      = (int) $td['entregados'];
    $td['cancelados']      = (int) $td['cancelados'];
}
unset($td);

/* ══════════════════════════════════════════════════
   7. Totales del rango seleccionado
══════════════════════════════════════════════════ */
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS total_pedidos,
           COALESCE(SUM(total), 0) AS total_ingresos
    FROM pedidos
    WHERE fecha BETWEEN ? AND ? AND estado != 'cancelado'
");
$stmt->execute([$desde, $hastaFull]);
$totalesRango = $stmt->fetch();

/* ══════════════════════════════════════════════════
   Respuesta JSON
══════════════════════════════════════════════════ */
echo json_encode([
    'success'           => true,
    'rango'             => ['desde' => $desde, 'hasta' => $hasta],
    'kpis'              => $kpis,
    'totales_rango'     => [
        'pedidos'  => (int) $totalesRango['total_pedidos'],
        'ingresos' => (float) $totalesRango['total_ingresos'],
    ],
    'ventas_diarias'    => $ventasDiarias,
    'productos_top'     => $productosTop,
    'ventas_categoria'  => $ventasCategoria,
    'tendencia_mensual' => $tendenciaMensual,
    'tabla_detalle'     => $tablaDetalle,
], JSON_UNESCAPED_UNICODE);
