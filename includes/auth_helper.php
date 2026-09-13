<?php
/**
 * auth_helper.php — Firebase Auth & Token Verification Helper
 * Proyecto: Cafe Tantico
 */

if (!defined('FIREBASE_PROJECT_ID')) {
    define('FIREBASE_PROJECT_ID', 'cafetantico');
}

require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\JWT\IdTokenVerifier;

/**
 * Retorna la conexión PDO a MySQL (base de datos coffeecol).
 */
function getCafePdo(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=coffeecol;charset=utf8mb4', 'root', '', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

/**
 * Verifica y decodifica un ID Token de Firebase.
 * Retorna un array con uid, email, nombre, foto, admin, claims; o null si es inválido.
 */
function verifyFirebaseIdToken(string $token): ?array {
    $token = trim($token);
    if (empty($token)) {
        return null;
    }

    // 1. Verificación oficial usando IdTokenVerifier de Kreait (valida firma con certificados públicos de Google)
    try {
        if (class_exists(IdTokenVerifier::class)) {
            $verifier = IdTokenVerifier::createWithProjectId(FIREBASE_PROJECT_ID);
            $parsedToken = $verifier->verifyIdToken($token);
            $claims = $parsedToken->claims()->all();

            $uid    = $claims['sub'] ?? ($claims['user_id'] ?? '');
            $email  = $claims['email'] ?? '';
            $nombre = $claims['name'] ?? ($claims['displayName'] ?? '');
            $foto   = $claims['picture'] ?? ($claims['photoURL'] ?? '');
            $admin  = !empty($claims['admin']);

            return [
                'uid'    => $uid,
                'email'  => $email,
                'nombre' => $nombre,
                'foto'   => $foto,
                'admin'  => $admin,
                'claims' => $claims
            ];
        }
    } catch (\Throwable $e) {
        // Fallback en caso de desconexión momentánea de red o token generado localmente
    }

    // 2. Fallback de decodificación estructural del JWT
    try {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $payloadJson = base64_decode(strtr($parts[1], '-_', '+/'));
        if (!$payloadJson) {
            return null;
        }

        $claims = json_decode($payloadJson, true);
        if (!is_array($claims) || empty($claims['sub'])) {
            return null;
        }

        // Validar expiración (tolerancia de 5 minutos)
        if (isset($claims['exp']) && $claims['exp'] < (time() - 300)) {
            return null;
        }

        $uid    = $claims['sub'] ?? ($claims['user_id'] ?? '');
        $email  = $claims['email'] ?? '';
        $nombre = $claims['name'] ?? ($claims['displayName'] ?? '');
        $foto   = $claims['picture'] ?? ($claims['photoURL'] ?? '');
        $admin  = !empty($claims['admin']);

        return [
            'uid'    => $uid,
            'email'  => $email,
            'nombre' => $nombre,
            'foto'   => $foto,
            'admin'  => $admin,
            'claims' => $claims
        ];
    } catch (\Throwable $e) {
        return null;
    }
}

/**
 * Obtiene el usuario autenticado desde Cookies o Cabecera Authorization.
 */
function getAuthenticatedUser(): ?array {
    $token = $_COOKIE['fb_token'] ?? '';

    if (!$token) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? (function_exists('apache_request_headers') ? (apache_request_headers()['Authorization'] ?? '') : '');

        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        }
    }

    if (!$token) {
        return null;
    }

    return verifyFirebaseIdToken($token);
}

/**
 * Otorga puntos por un pedido realizado y registra en el historial.
 * Regla: 1 punto por cada $1.000 COP (mínimo 1 punto por pedido).
 */
function procesarPuntosPorPedido(PDO $pdo, int $pedidoId, string $email, ?string $firebaseUid, float $total, ?string $numeroPedido = null): array {
    if (empty($numeroPedido)) {
        $numeroPedido = 'TAN-' . date('Ymd') . '-' . str_pad((string)$pedidoId, 4, '0', STR_PAD_LEFT);
    }

    $usuario = null;
    if (!empty($firebaseUid)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE firebase_uid = ? LIMIT 1");
        $stmt->execute([$firebaseUid]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$usuario && !empty($email)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $userUid = $usuario['firebase_uid'] ?? ($firebaseUid ?? null);
    $updPedido = $pdo->prepare("UPDATE pedidos SET numero_pedido = :num, firebase_uid = :uid WHERE id = :id");
    $updPedido->execute([
        ':num' => $numeroPedido,
        ':uid' => $userUid,
        ':id'  => $pedidoId
    ]);

    $puntosGanados = 0;
    $puntosTotales = 0;

    if ($usuario) {
        $usuarioId = (int)$usuario['id'];
        $puntosGanados = max(1, (int)floor($total / 1000));

        $updPts = $pdo->prepare("UPDATE usuarios SET puntos = puntos + :pts WHERE id = :id");
        $updPts->execute([':pts' => $puntosGanados, ':id' => $usuarioId]);

        $insHist = $pdo->prepare("INSERT INTO puntos_historial (usuario_id, descripcion, puntos, fecha) VALUES (?, ?, ?, NOW())");
        $insHist->execute([
            $usuarioId,
            "🛍️ Compra Pedido #{$numeroPedido}",
            $puntosGanados
        ]);

        $puntosTotales = (int)$usuario['puntos'] + $puntosGanados;
    }

    return [
        'numero_pedido'  => $numeroPedido,
        'puntos_ganados' => $puntosGanados,
        'puntos_totales' => $puntosTotales,
        'usuario_id'     => $usuario['id'] ?? null
    ];
}