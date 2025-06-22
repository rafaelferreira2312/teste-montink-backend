<?php
class Pedido extends BaseModel {
    protected $table = 'pedidos';
    
    /**
     * Criar novo pedido
     */
    public function create($data) {
        $numero_pedido = $this->gerarNumeroPedido();
        
        $query = "INSERT INTO " . $this->table . " 
                  (numero_pedido, subtotal, desconto, frete, total, cupom_id, 
                   cliente_nome, cliente_email, cliente_telefone,
                   cep, endereco, numero, complemento, bairro, cidade, estado, observacoes) 
                  VALUES (:numero_pedido, :subtotal, :desconto, :frete, :total, :cupom_id,
                          :cliente_nome, :cliente_email, :cliente_telefone,
                          :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado, :observacoes)";
        
        $params = array_merge([':numero_pedido' => $numero_pedido], $data);
        
        if ($this->executeQuery($query, $params)) {
            return $this->getLastInsertId();
        }
        return false;
    }
    
    /**
     * Adicionar itens ao pedido
     */
    public function adicionarItens($pedido_id, $itens) {
        foreach ($itens as $item) {
            $query = "INSERT INTO pedido_itens 
                      (pedido_id, produto_id, variacao_id, quantidade, preco_unitario, preco_total) 
                      VALUES (:pedido_id, :produto_id, :variacao_id, :quantidade, :preco_unitario, :preco_total)";
            
            $this->executeQuery($query, [
                ':pedido_id' => $pedido_id,
                ':produto_id' => $item['produto_id'],
                ':variacao_id' => $item['variacao_id'],
                ':quantidade' => $item['quantidade'],
                ':preco_unitario' => $item['preco_unitario'],
                ':preco_total' => $item['preco_total']
            ]);
        }
        return true;
    }
    
    /**
     * Atualizar status do pedido
     */
    public function atualizarStatus($id, $status) {
        $query = "UPDATE " . $this->table . " 
                  SET status = :status, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = :id";
        return $this->executeQuery($query, [':id' => $id, ':status' => $status]);
    }
    
    /**
     * Calcular frete baseado no subtotal
     */
    public function calcularFrete($subtotal) {
        if ($subtotal >= FRETE_GRATIS_MIN) {
            return 0.00;
        } elseif ($subtotal >= FRETE_INTERMEDIARIO_MIN && $subtotal <= FRETE_INTERMEDIARIO_MAX) {
            return FRETE_INTERMEDIARIO;
        } else {
            return FRETE_PADRAO;
        }
    }
    
    /**
     * Gerar número único para o pedido
     */
    private function gerarNumeroPedido() {
        $ano = date('Y');
        $mes = date('m');
        
        // Buscar último número do mês
        $query = "SELECT numero_pedido FROM " . $this->table . " 
                  WHERE numero_pedido LIKE :pattern 
                  ORDER BY numero_pedido DESC LIMIT 1";
        
        $pattern = $ano . $mes . '%';
        $result = $this->fetchOne($query, [':pattern' => $pattern]);
        
        if ($result) {
            $ultimo = (int)substr($result['numero_pedido'], -5);
            $sequencial = str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $sequencial = '00001';
        }
        
        return $ano . $mes . $sequencial;
    }
    
    /**
     * Buscar pedido completo com itens
     */
    public function getPedidoCompleto($id) {
        $query = "SELECT p.*, c.codigo as cupom_codigo 
                  FROM " . $this->table . " p 
                  LEFT JOIN cupons c ON p.cupom_id = c.id 
                  WHERE p.id = :id";
        
        $pedido = $this->fetchOne($query, [':id' => $id]);
        
        if ($pedido) {
            $query_itens = "SELECT pi.*, pr.nome as produto_nome, pv.nome as variacao_nome 
                           FROM pedido_itens pi 
                           INNER JOIN produtos pr ON pi.produto_id = pr.id 
                           LEFT JOIN produto_variacoes pv ON pi.variacao_id = pv.id 
                           WHERE pi.pedido_id = :pedido_id
                           ORDER BY pi.id";
            
            $pedido['itens'] = $this->fetchQuery($query_itens, [':pedido_id' => $id]);
        }
        
        return $pedido;
    }
    
    /**
     * Buscar pedidos por status
     */
    public function getByStatus($status) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE status = :status 
                  ORDER BY created_at DESC";
        
        return $this->fetchQuery($query, [':status' => $status]);
    }
    
    /**
     * Buscar pedidos por período
     */
    public function getByPeriodo($data_inicio, $data_fim) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE DATE(created_at) BETWEEN :data_inicio AND :data_fim 
                  ORDER BY created_at DESC";
        
        return $this->fetchQuery($query, [
            ':data_inicio' => $data_inicio,
            ':data_fim' => $data_fim
        ]);
    }
    
    /**
     * Buscar pedido por número
     */
    public function getByNumero($numero_pedido) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE numero_pedido = :numero_pedido LIMIT 1";
        
        return $this->fetchOne($query, [':numero_pedido' => $numero_pedido]);
    }
    
    /**
     * Cancelar pedido e devolver estoque
     */
    public function cancelar($id) {
        $this->beginTransaction();
        
        try {
            $pedido = $this->getPedidoCompleto($id);
            
            if (!$pedido || $pedido['status'] === 'cancelado') {
                throw new Exception('Pedido não encontrado ou já cancelado');
            }
            
            // Devolver itens ao estoque
            $estoqueModel = new Estoque();
            
            foreach ($pedido['itens'] as $item) {
                $estoqueModel->aumentarEstoque(
                    $item['produto_id'],
                    $item['variacao_id'],
                    $item['quantidade'],
                    'Cancelamento do pedido ' . $pedido['numero_pedido']
                );
            }
            
            // Atualizar status do pedido
            $this->atualizarStatus($id, 'cancelado');
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Calcular estatísticas de vendas
     */
    public function getEstatisticas($periodo = 30) {
        $data_inicio = date('Y-m-d', strtotime("-{$periodo} days"));
        
        $query = "SELECT 
                    COUNT(*) as total_pedidos,
                    SUM(total) as total_vendas,
                    AVG(total) as ticket_medio,
                    SUM(CASE WHEN status = 'entregue' THEN 1 ELSE 0 END) as pedidos_entregues
                  FROM " . $this->table . " 
                  WHERE DATE(created_at) >= :data_inicio";
        
        return $this->fetchOne($query, [':data_inicio' => $data_inicio]);
    }
}
?>
