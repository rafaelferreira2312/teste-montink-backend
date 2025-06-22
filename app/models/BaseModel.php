<?php
class BaseModel {
    protected $conn;
    protected $table;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Buscar todos os registros
     */
    public function findAll($orderBy = 'id DESC') {
        $query = "SELECT * FROM " . $this->table . " ORDER BY " . $orderBy;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar por ID
     */
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Deletar registro
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Contar registros
     */
    public function count($where = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        if ($where) {
            $query .= " WHERE " . $where;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Executar query personalizada
     */
    protected function executeQuery($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Buscar com query personalizada
     */
    protected function fetchQuery($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar um registro com query personalizada
     */
    protected function fetchOne($query, $params = []) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obter último ID inserido
     */
    protected function getLastInsertId() {
        return $this->conn->lastInsertId();
    }

    /**
     * Iniciar transação
     */
    protected function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Confirmar transação
     */
    protected function commit() {
        return $this->conn->commit();
    }

    /**
     * Reverter transação
     */
    protected function rollback() {
        return $this->conn->rollback();
    }
}
?>
