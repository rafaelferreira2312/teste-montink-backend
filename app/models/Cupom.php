<?php
class Cupom extends BaseModel {
    protected $table = 'cupons';
    
    /**
     * Criar novo cupom
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (codigo, tipo, valor, valor_minimo_pedido, data_inicio, data_fim, limite_uso) 
                  VALUES (:codigo, :tipo, :valor, :valor_minimo_pedido, :data_inicio, :data_fim, :limite_uso)";
        
        return $this->executeQuery($query, [
            ':codigo' => strtoupper($data['codigo']),
            ':tipo' => $data['tipo'],
            ':valor' => $data['valor'],
            ':valor_minimo_pedido' => $data['valor_minimo_pedido'] ?? 0,
            ':data_inicio' => $data['data_inicio'],
            ':data_fim' => $data['data_fim'],
            ':limite_uso' => $data['limite_uso'] ?? null
        ]);
    }
    
    /**
     * Validar cupom para uso
     */
    public function validarCupom($codigo, $valor_pedido) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE codigo = :codigo 
                  AND ativo = 1 
                  AND data_inicio <= CURDATE() 
                  AND data_fim >= CURDATE() 
                  AND valor_minimo_pedido <= :valor_pedido
                  AND (limite_uso IS NULL OR usado < limite_uso)
                  LIMIT 1";
        
        return $this->fetchOne($query, [
            ':codigo' => strtoupper($codigo),
            ':valor_pedido' => $valor_pedido
        ]);
    }
    
    /**
     * Calcular valor do desconto
     */
    public function calcularDesconto($cupom, $valor_pedido) {
        if ($cupom['tipo'] === 'percentual') {
            $desconto = ($valor_pedido * $cupom['valor']) / 100;
        } else {
            $desconto = $cupom['valor'];
        }
        
        // Garantir que o desconto não seja maior que o valor do pedido
        return min($desconto, $valor_pedido);
    }
    
    /**
     * Marcar cupom como usado
     */
    public function marcarComoUsado($id) {
        $query = "UPDATE " . $this->table . " 
                  SET usado = usado + 1, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = :id";
        return $this->executeQuery($query, [':id' => $id]);
    }
    
    /**
     * Buscar cupons ativos
     */
    public function getAtivos() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE ativo = 1 
                  AND data_fim >= CURDATE()
                  ORDER BY created_at DESC";
        
        return $this->fetchQuery($query);
    }
    
    /**
     * Buscar cupons por código
     */
    public function getByCodigo($codigo) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE codigo = :codigo LIMIT 1";
        
        return $this->fetchOne($query, [':codigo' => strtoupper($codigo)]);
    }
    
    /**
     * Desativar cupom
     */
    public function desativar($id) {
        $query = "UPDATE " . $this->table . " 
                  SET ativo = 0, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = :id";
        return $this->executeQuery($query, [':id' => $id]);
    }
    
    /**
     * Verificar se cupom pode ser usado
     */
    public function podeSerUsado($cupom) {
        if (!$cupom['ativo']) return false;
        if (date('Y-m-d') < $cupom['data_inicio']) return false;
        if (date('Y-m-d') > $cupom['data_fim']) return false;
        if ($cupom['limite_uso'] && $cupom['usado'] >= $cupom['limite_uso']) return false;
        
        return true;
    }
}
?>
