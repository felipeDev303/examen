<?php

// Helper para respuestas JSON
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
    exit;
}

require_once __DIR__ . '/models/Camiseta.php';
require_once __DIR__ . '/models/Cliente.php';
require_once __DIR__ . '/models/Talla.php';

// Obtener la ruta y método
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/api', '', $uri); // Ajusta si tu endpoint es /api
$metodo = $_SERVER['REQUEST_METHOD'];

// Parsear input JSON para POST/PUT/PATCH
$input = json_decode(file_get_contents('php://input'), true);

// Rutas principales
if (preg_match('#^/camisetas/?$#', $uri)) {
    if ($metodo === 'GET') {
        jsonResponse(Camiseta::getAll());
    } elseif ($metodo === 'POST') {
        $nueva = Camiseta::create($input);
        jsonResponse($nueva, 201);
    } else {
        jsonResponse(["error" => "Método no permitido"], 405);
    }
} elseif (preg_match('#^/camisetas/(\d+)$#', $uri, $matches)) {
    $id = (int)$matches[1];
    if ($metodo === 'GET') {
        $cliente_id = isset($_GET['cliente_id']) ? (int)$_GET['cliente_id'] : null;
        if ($cliente_id) {
            $camiseta = Camiseta::findByIdForClient($id, $cliente_id);
        } else {
            $camiseta = Camiseta::findById($id);
        }
        if ($camiseta) jsonResponse($camiseta);
        else jsonResponse(["error" => "No encontrada"], 404);
    } elseif ($metodo === 'PUT') {
        $actualizada = Camiseta::update($id, $input);
        jsonResponse($actualizada);
    } elseif ($metodo === 'DELETE') {
        $ok = Camiseta::delete($id);
        if ($ok) jsonResponse(null, 204);
        else jsonResponse(["error" => "No encontrada"], 404);
    } else {
        jsonResponse(["error" => "Método no permitido"], 405);
    }
} elseif (preg_match('#^/camisetas/(\d+)/tallas/?$#', $uri, $matches)) {
    $camiseta_id = (int)$matches[1];
    if ($metodo === 'POST') {
        if (!isset($input['talla_id'])) jsonResponse(["error" => "talla_id requerido"], 400);
        $ok = Talla::addTallaToCamiseta($camiseta_id, $input['talla_id']);
        if ($ok) jsonResponse(["message" => "Talla asociada"]);
        else jsonResponse(["error" => "No se pudo asociar"], 400);
    } else {
        jsonResponse(["error" => "Método no permitido"], 405);
    }
} elseif (preg_match('#^/camisetas/(\d+)/tallas/(\d+)$#', $uri, $matches)) {
    $camiseta_id = (int)$matches[1];
    $talla_id = (int)$matches[2];
    if ($metodo === 'DELETE') {
        $ok = Talla::removeTallaFromCamiseta($camiseta_id, $talla_id);
        if ($ok) jsonResponse(null, 204);
        else jsonResponse(["error" => "No se pudo desasociar"], 400);
    } else {
        jsonResponse(["error" => "Método no permitido"], 405);
    }
} elseif (preg_match('#^/clientes/?$#', $uri)) {
    if ($metodo === 'GET') {
        jsonResponse(Cliente::getAll());
    } elseif ($metodo === 'POST') {
        $nuevo = Cliente::create($input);
        jsonResponse($nuevo, 201);
    } else {
        jsonResponse(["error" => "Método no permitido"], 405);
    }
} elseif (preg_match('#^/clientes/(\d+)$#', $uri, $matches)) {
    $id = (int)$matches[1];
    if ($metodo === 'GET') {
        $cliente = Cliente::findById($id);
        if ($cliente) jsonResponse($cliente);
        else jsonResponse(["error" => "No encontrado"], 404);
    } elseif ($metodo === 'PUT') {
        $actualizado = Cliente::update($id, $input);
        jsonResponse($actualizado);
    } elseif ($metodo === 'DELETE') {
        // Validar que no tenga ofertas activas
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM ofertas WHERE cliente_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            jsonResponse(["error" => "No se puede eliminar: cliente con ofertas activas"], 400);
        }
        $ok = Cliente::delete($id);
        if ($ok) jsonResponse(null, 204);
        else jsonResponse(["error" => "No encontrado"], 404);
    } else {
        jsonResponse(["error" => "Método no permitido"], 405);
    }
} elseif (preg_match('#^/tallas/?$#', $uri)) {
    if ($metodo === 'GET') {
        jsonResponse(Talla::getAll());
    } elseif ($metodo === 'POST') {
        if (!isset($input['nombre'])) jsonResponse(["error" => "nombre requerido"], 400);
        $nueva = Talla::create($input['nombre']);
        jsonResponse($nueva, 201);
    } else {
        jsonResponse(["error" => "Método no permitido"], 405);
    }
} elseif (preg_match('#^/tallas/(\d+)$#', $uri, $matches)) {
    $id = (int)$matches[1];
    if ($metodo === 'GET') {
        $talla = Talla::findById($id);
        if ($talla) jsonResponse($talla);
        else jsonResponse(["error" => "No encontrada"], 404);
    } elseif ($metodo === 'DELETE') {
        $ok = Talla::delete($id);
        if ($ok) jsonResponse(null, 204);
        else jsonResponse(["error" => "No encontrada"], 404);
    } else {
        jsonResponse(["error" => "Método no permitido"], 405);
    }
} else {
    jsonResponse(["error" => "Ruta no encontrada"], 404);
}

?>