<?php
/**
 * recompensas.php — API de canje de recompensas y puntos Tantico
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../includes/auth_helper.php';

$user = getAuthenticatedUser();
if (!$user || empty($user['uid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado o sesión expirada']);
    exit;
}

$pdo = getCafePdo();

$stmtU = $pdo->prepare("SELECT id, nombre, email, puntos FROM usuarios WHERE firebase_uid = ? LIMIT 1");
$stmtU->execute([$user['uid']]);
$usuario = $stmtU->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit;
}

$usuarioId   = (int)$usuario['id'];
$puntosTotal = (int)$usuario['puntos'];

// Definición oficial de recompensas
$RECOMPENSAS = [
    'descuento_10' => [
        'id'          => 'descuento_10',
        'nombre'      => 'Bono 10% de Descuento',
        'puntos'      => 300,
        'descripcion' => 'Aplica un 10% de descuento en tu próxima compra de café especial.',
        'icono'       => 'ti-discount',
        'prefijo'     => 'TAN-DESC10'
    ],
    'cafe_gratis' => [
        'id'          => 'cafe_gratis',
        'nombre'      => 'Bono Café de la Casa Gratis',
        'puntos'      => 500,
        'descripcion' => 'Válido por un café filtrado o espresso gratis en tienda.',
        'icono'       => 'ti-coffee',
        'prefijo'     => 'TAN-CAFE'
    ],
    'kit_premium' => [
        'id'          => 'kit_premium',
        'nombre'      => 'Kit Catador Premium Tantico',
        'puntos'      => 1000,
        'descripcion' => 'Kit de lujo con café Geisha 250g y taza artesanal Tantico.',
        'icono'       => 'ti-gift',
        'prefijo'     => 'TAN-KIT'
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lista = [];
    foreach ($RECOMPENSAS as $r) {
        $r['desbloqueado'] = ($puntosTotal >= $r['puntos']);
        $lista[] = $r;
    }
    echo json_encode([
        'success'        => true,
        'puntos_actuales'=> $puntosTotal,
        'recompensas'    => $lista
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $recId = $data['recompensa_id'] ?? '';

    if (!isset($RECOMPENSAS[$recId])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Recompensa no válida']);
        exit;
    }

    $rec = $RECOMPENSAS[$recId];
    $costo = (int)$rec['puntos'];

    if ($puntosTotal < $costo) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error'   => "Te faltan " . ($costo - $puntosTotal) . " puntos para canjear {$rec['nombre']}."
        ]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Descontar puntos
        $stmtUpd = $pdo->prepare("UPDATE usuarios SET puntos = puntos - ? WHERE id = ? AND puntos >= ?");
        $stmtUpd->execute([$costo, $usuarioId, $costo]);

        if ($stmtUpd->rowCount() === 0) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Saldo insuficiente de puntos.']);
            exit;
        }

        // Generar cupón único
        $cupon = $rec['prefijo'] . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        // Registrar en historial de puntos
        $stmtHist = $pdo->prepare("
            INSERT INTO puntos_historial (usuario_id, descripcion, puntos, fecha)
            VALUES (?, ?, ?, NOW())
        ");
        $stmtHist->execute([
            $usuarioId,
            "🎁 Canje: {$rec['nombre']} (Cupón: {$cupon})",
            -$costo
        ]);

        $pdo->commit();

        $nuevosPuntos = $puntosTotal - $costo;

        echo json_encode([
            'success'          => true,
            'mensaje'          => "¡Canje exitoso! Disfruta tu {$rec['nombre']}.",
            'cupon'            => $cupon,
            'recompensa'       => $rec['nombre'],
            'puntos_gastados'  => $costo,
            'puntos_restantes' => $nuevosPuntos
        ]);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al procesar el canje']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Método no permitido']);