<?php
class ApiController extends BaseController {
    
    public function consultarCep() {
        $cep = $_GET['cep'] ?? '';
        echo json_encode([
            'cep' => $cep,
            'logradouro' => 'Rua Exemplo',
            'bairro' => 'Centro',
            'localidade' => 'São Paulo',
            'uf' => 'SP'
        ]);
    }
    
    public function buscarProdutos() {
        echo json_encode(['produtos' => []]);
    }
    
    public function verificarEstoque() {
        echo json_encode(['disponivel' => true, 'quantidade' => 10]);
    }
    
    public function calcularFrete() {
        $subtotal = $_GET['subtotal'] ?? 0;
        $frete = $subtotal > 200 ? 0 : 20;
        echo json_encode(['frete' => $frete]);
    }
    
    public function status() {
        echo json_encode(['status' => 'online', 'timestamp' => date('c')]);
    }
}
?>
