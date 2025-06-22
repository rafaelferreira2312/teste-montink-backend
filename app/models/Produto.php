<?php
class Produto extends BaseModel {
    protected $table = 'produtos';
    
    /**
     * Criar novo produto
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (nome, preco, descricao, imagem) 
                  VALUES (:nome, :preco, :descricao, :imagem)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':preco', $data['preco']);
        $stmt->bindParam(':descricao', $data['descricao']);
        $stmt->bindParam(':imagem', $data['imagem']);
        
        if ($stmt->execute()) {
            return $this->getLastInsertId();
        }
        return false;
    }
    
    /**
     * Atualizar produto
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET nome = :nome, preco = :preco, descricao = :descricao, imagem = :imagem 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $data['nome']);
        $stmt->bindParam(':preco', $data['preco']);
        $stmt->bindParam(':descricao', $data['descricao']);
        $stmt->bindParam(':imagem', $data['imagem']);
        
        return $stmt->execute();
    }
    
    /**
     * Buscar produtos com informações de estoque
     */
    public function getWithEstoque() {
        $query = "SELECT p.*, 
                         COALESCE(SUM(e.quantidade), 0) as total_estoque,
                         MIN(e.quantidade) as menor_estoque
                  FROM produtos p 
                  LEFT JOIN estoque e ON p.id = e.produto_id 
                  WHERE p.ativo = 1
                  GROUP BY p.id, p.nome, p.preco, p.descricao, p.imagem, p.ativo, p.created_at, p.updated_at
                  ORDER BY p.nome";
        
        return $this->fetchQuery($query);
    }
    
    /**
     * Buscar variações de um produto
     */
    public function getVariacoes($produto_id) {
        $query = "SELECT pv.*, 
                         COALESCE(e.quantidade, 0) as quantidade_estoque
                  FROM produto_variacoes pv
                  LEFT JOIN estoque e ON pv.id = e.variacao_id
                  WHERE pv.produto_id = :produto_id AND pv.ativo = 1
                  ORDER BY pv.nome";
        
        return $this->fetchQuery($query, [':produto_id' => $produto_id]);
    }
    
    /**
     * Criar variação de produto
     */
    public function createVariacao($produto_id, $data) {
        $query = "INSERT INTO produto_variacoes 
                  (produto_id, nome, valor_adicional) 
                  VALUES (:produto_id, :nome, :valor_adicional)";
        
        return $this->executeQuery($query, [
            ':produto_id' => $produto_id,
            ':nome' => $data['nome'],
            ':valor_adicional' => $data['valor_adicional'] ?? 0
        ]);
    }
    
    /**
     * Buscar produtos com estoque baixo
     */
    public function getComEstoqueBaixo() {
        $query = "SELECT p.*, e.quantidade, e.quantidade_minima
                  FROM produtos p
                  INNER JOIN estoque e ON p.id = e.produto_id
                  WHERE p.ativo = 1 AND e.quantidade <= e.quantidade_minima
                  ORDER BY e.quantidade ASC";
        
        return $this->fetchQuery($query);
    }
    
    /**
     * Buscar produto completo com variações e estoque
     */
    public function getCompleto($id) {
        $produto = $this->findById($id);
        if ($produto) {
            $produto['variacoes'] = $this->getVariacoes($id);
        }
        return $produto;
    }
    
    /**
     * Verificar se produto existe e está ativo
     */
    public function isActive($id) {
        $query = "SELECT ativo FROM " . $this->table . " WHERE id = :id";
        $result = $this->fetchOne($query, [':id' => $id]);
        return $result && $result['ativo'];
    }
}
?>
