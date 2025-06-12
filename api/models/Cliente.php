<?php
// Modelo de Cliente
// Funcionalidades:
// - CRUD completo (getAll, findById, create, update, delete)
// - Validaciones de datos obligatorios
// - Manejo de porcentaje de oferta para descuentos generales

require_once __DIR__ . '/../../core/Database.php';

class Cliente {
    public static function getAll() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM clientes");
        return $stmt->fetchAll();
    }

    public static function findById($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO clientes (nombre_comercial, rut, direccion, categoria, contacto_nombre, contacto_email, porcentaje_oferta) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['nombre_comercial'],
            $data['rut'],
            $data['direccion'] ?? null,
            $data['categoria'],
            $data['contacto_nombre'] ?? null,
            $data['contacto_email'] ?? null,
            $data['porcentaje_oferta'] ?? 0
        ]);
        return self::findById($db->lastInsertId());
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE clientes SET nombre_comercial=?, rut=?, direccion=?, categoria=?, contacto_nombre=?, contacto_email=?, porcentaje_oferta=? WHERE id=?");
        $stmt->execute([
            $data['nombre_comercial'],
            $data['rut'],
            $data['direccion'] ?? null,
            $data['categoria'],
            $data['contacto_nombre'] ?? null,
            $data['contacto_email'] ?? null,
            $data['porcentaje_oferta'] ?? 0,
            $id
        ]);
        return self::findById($id);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM clientes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Nueva función para encapsular la lógica de negocio
    public static function hasActiveOffers($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM ofertas WHERE cliente_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}
