<?php
class Estoque extends BaseModel {
    protected $table = 'estoque';
    
    /**
     * Atualizar quantidade em estoque
     */
    public function atualizarEstoque($produto_id, $variacao_id, $quantidade) {
        $query = "INSERT INTO " . $this->table . " 
                  (produto_id, variacao_id, quantidade) 
                  VALUES (:produto_id, :variacao_id, :quantidade)
                  ON DUPLICATE KEY UPDATE 
                  quantidade = :quantidade, updated_at = CURRENT_TIMESTAMP";
        
        return $this->executeQuery($query, [
            ':produto_id' => $produto_id,
            ':variacao_id' => $variacao_id,
            ':quantidade' => $quantidade
        ]);
    }
    
    /**
     * Verificar quantidade disponível em estoque
     */
    public function verificarEstoque($produto_id, $variacao_id = null) {
        $query = "SELECT quantidade FROM " . $this->table . " 
                  WHERE produto_id = :produto_id";
        
        $params = [':produto_id' => $produto_id];
        
        if ($variacao_id) {
            $query .= " AND variacao_id = :variacao_id";
            $params[':variacao_id'] = $variacao_id;
        } else {
            $query .= " AND variacao_id IS NULL";
        }
        
        $result = $this->fetchOne($query, $params);
        return $result ? (int)$result['quantidade'] : 0;
    }
    
    /**
     * Reduzir quantidade do estoque
     */
    public function reduzirEstoque($produto_id, $variacao_id, $quantidade) {
        // Verificar se há estoque suficiente
        $estoque_atual = $this->verificarEstoque($produto_id, $variacao_id);
        
        if ($estoque_atual < $quantidade) {
            throw new Exception("Estoque insuficiente. Disponível: {$estoque_atual}, Solicitado: {$quantidade}");
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET quantidade = quantidade - :quantidade, updated_at = CURRENT_TIMESTAMP
                  WHERE produto_id = :produto_id";
        
        $params = [
            ':quantidade' => $quantidade,
            ':produto_id' => $produto_id
        ];
        
        if ($variacao_id) {
            $query .= " AND variacao_id = :variacao_id";
            $params[':variacao_id'] = $variacao_id;
        } else {
            $query .= " AND variacao_id IS NULL";
        }
        
        $success = $this->executeQuery($query, $params);
        
        if ($success) {
            // Registrar movimentação
            $this->registrarMovimentacao(
                $produto_id, 
                $variacao_id, 
                'saida', 
                $estoque_atual, 
                $quantidade, 
                $estoque_atual - $quantidade,
                'Venda de produto'
            );
        }
        
        return $success;
    }
    
    /**
     * Aumentar quantidade do estoque
     */
    public function aumentarEstoque($produto_id, $variacao_id, $quantidade, $motivo = 'Entrada de estoque') {
        $estoque_atual = $this->verificarEstoque($produto_id, $variacao_id);
        
        if ($estoque_atual > 0) {
            // Atualizar estoque existente
            $query = "UPDATE " . $this->table . " 
                      SET quantidade = quantidade + :quantidade, updated_at = CURRENT_TIMESTAMP
                      WHERE produto_id = :produto_id";
            
            $params = [
                ':quantidade' => $quantidade,
                ':produto_id' => $produto_id
            ];
            
            if ($variacao_id) {
                $query .= " AND variacao_id = :variacao_id";
                $params[':variacao_id'] = $variacao_id;
            } else {
                $query .= " AND variacao_id IS NULL";
            }
            
            $success = $this->executeQuery($query, $params);
        } else {
            // Criar novo registro de estoque
            $success = $this->atualizarEstoque($produto_id, $variacao_id, $quantidade);
        }
        
        if ($success) {
            $this->registrarMovimentacao(
                $produto_id, 
                $variacao_id, 
                'entrada', 
                $estoque_atual, 
                $quantidade, 
                $estoque_atual + $quantidade,
                $motivo
            );
        }
        
        return $success;
    }
    
    /**
     * Registrar movimentação de estoque
     */
    public function registrarMovimentacao($produto_id, $variacao_id, $tipo, $quantidade_anterior, $quantidade_movimentada, $quantidade_atual, $motivo, $pedido_id = null) {
        $query = "INSERT INTO estoque_movimentacao 
                  (produto_id, variacao_id, tipo, quantidade_anterior, quantidade_movimentada, quantidade_atual, motivo, pedido_id) 
                  VALUES (:produto_id, :variacao_id, :tipo, :quantidade_anterior, :quantidade_movimentada, :quantidade_atual, :motivo, :pedido_id)";
        
        return $this->executeQuery($query, [
            ':produto_id' => $produto_id,
            ':variacao_id' => $variacao_id,
            ':tipo' => $tipo,
            ':quantidade_anterior' => $quantidade_anterior,
            ':quantidade_movimentada' => $quantidade_movimentada,
            ':quantidade_atual' => $quantidade_atual,
            ':motivo' => $motivo,
            ':pedido_id' => $pedido_id
        ]);
    }
    
    /**
     * Buscar movimentações de estoque
     */
    public function getMovimentacoes($produto_id = null, $limit = 50) {
        $query = "SELECT em.*, p.nome as produto_nome, pv.nome as variacao_nome
                  FROM estoque_movimentacao em
                  INNER JOIN produtos p ON em.produto_id = p.id
                  LEFT JOIN produto_variacoes pv ON em.variacao_id = pv.id";
        
        $params = [];
        if ($produto_id) {
            $query .= " WHERE em.produto_id = :produto_id";
            $params[':produto_id'] = $produto_id;
        }
        
        $query .= " ORDER BY em.created_at DESC LIMIT :limit";
        $params[':limit'] = $limit;
        
        return $this->fetchQuery($query, $params);
    }
    
    /**
     * Verificar produtos com estoque baixo
     */
    public function getProdutosEstoqueBaixo() {
        $query = "SELECT p.nome, e.quantidade, e.quantidade_minima, pv.nome as variacao_nome
                  FROM estoque e
                  INNER JOIN produtos p ON e.produto_id = p.id
                  LEFT JOIN produto_variacoes pv ON e.variacao_id = pv.id
                  WHERE e.quantidade <= e.quantidade_minima
                  ORDER BY e.quantidade ASC";
        
        return $this->fetchQuery($query);
    }
}
?>
