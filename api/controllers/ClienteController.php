<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private static function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($data);
        exit;
    }

    public static function index() {
        self::jsonResponse(Cliente::getAll());
    }

    public static function show($id) {
        $cliente = Cliente::findById($id);
        if ($cliente) {
            self::jsonResponse($cliente);
        } else {
            self::jsonResponse(["error" => "Cliente no encontrado"], 404);
        }
    }

    public static function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            self::jsonResponse(["error" => "Datos de entrada inválidos"], 400);
        }
        $nuevo = Cliente::create($input);
        self::jsonResponse($nuevo, 201);
    }

    public static function update($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            self::jsonResponse(["error" => "Datos de entrada inválidos"], 400);
        }
        $actualizado = Cliente::update($id, $input);
        if ($actualizado) {
            self::jsonResponse($actualizado);
        } else {
            self::jsonResponse(["error" => "Cliente no encontrado para actualizar"], 404);
        }
    }

    public static function destroy($id) {
        if (Cliente::hasActiveOffers($id)) {
            self::jsonResponse(["error" => "No se puede eliminar: cliente con ofertas activas"], 400);
        }
        $ok = Cliente::delete($id);
        if ($ok) {
            self::jsonResponse(null, 204);
        } else {
            self::jsonResponse(["error" => "Cliente no encontrado"], 404);
        }
    }
}
