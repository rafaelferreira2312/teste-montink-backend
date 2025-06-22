<?php
class CarrinhoController extends BaseController {
    
    public function index() {
        $carrinho = $_SESSION['carrinho'] ?? [];
        $totais = [
            'subtotal' => 100,
            'frete' => 20,
            'total' => 120
        ];
        
        $this->view('carrinho/index', [
            'title' => 'Carrinho - ' . APP_NAME,
            'carrinho' => $carrinho,
            'totais' => $totais
        ]);
    }
    
    public function adicionar() {
        $produto_id = $_POST['produto_id'] ?? 0;
        if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];
        $_SESSION['carrinho'][] = ['produto_id' => $produto_id, 'quantidade' => 1];
        
        echo json_encode(['success' => true, 'total_itens' => count($_SESSION['carrinho'])]);
    }
    
    public function remover() {
        $_SESSION['carrinho'] = [];
        $this->redirect('/carrinho');
    }
    
    public function atualizar() {
        echo json_encode(['success' => true]);
    }
    
    public function limpar() {
        $_SESSION['carrinho'] = [];
        $this->redirect('/carrinho');
    }
    
    public function aplicarCupom() {
        echo json_encode(['success' => true, 'desconto' => 10]);
    }
}
?>
