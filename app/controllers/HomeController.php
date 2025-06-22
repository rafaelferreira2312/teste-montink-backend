<?php
class HomeController extends BaseController {
    
    public function index() {
        try {
            $produtoModel = new Produto();
            $pedidoModel = new Pedido();
            $estoqueModel = new Estoque();
            
            // Buscar dados para dashboard
            $produtos = $produtoModel->getWithEstoque();
            $estatisticas = $pedidoModel->getEstatisticas();
            $produtosEstoqueBaixo = $estoqueModel->getProdutosEstoqueBaixo();
            
            $this->view('home', [
                'title' => 'Dashboard - ' . APP_NAME,
                'produtos' => $produtos,
                'estatisticas' => $estatisticas,
                'produtos_estoque_baixo' => $produtosEstoqueBaixo
            ]);
            
        } catch (Exception $e) {
            $this->log("Erro no dashboard: " . $e->getMessage(), 'ERROR');
            $this->setError('Erro ao carregar dashboard');
            $this->view('home', [
                'title' => 'Dashboard - ' . APP_NAME,
                'produtos' => [],
                'estatisticas' => [],
                'produtos_estoque_baixo' => []
            ]);
        }
    }
}
?>
