<?php
class PedidoController extends BaseController {
    private $pedidoModel;
    
    public function __construct() {
        $this->pedidoModel = new Pedido();
    }
    
    public function index() {
        try {
            $pedidos = $this->pedidoModel->findAll();
            
            $this->view('pedidos/index', [
                'title' => 'Pedidos - ' . APP_NAME,
                'pedidos' => $pedidos
            ]);
        } catch (Exception $e) {
            $this->setError('Erro ao carregar pedidos');
            $this->redirect('/');
        }
    }
    
    public function finalizar() {
        echo json_encode(['success' => true, 'message' => 'Pedido finalizado']);
    }
    
    public function detalhes() {
        $id = $_GET['id'] ?? 1;
        echo json_encode(['id' => $id, 'status' => 'pendente']);
    }
}
?>
