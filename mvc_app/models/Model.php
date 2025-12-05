<?php
namespace Models;

class Model {
    private $db;
    private $table = "app_data";
    
    public function __construct() {
        $database = new \Database();
        $this->db = $database->getConnection();
    }
    
    // REST методы
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (data_string) VALUES (?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$data['data_string']]);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET data_string = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$data['data_string'], $id]);
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>