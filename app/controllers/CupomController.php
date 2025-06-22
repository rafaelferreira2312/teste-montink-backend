<?php
class CupomController extends BaseController {
    private $cupomModel;
    
    public function __construct() {
        $this->cupomModel = new Cupom();
    }
    
    public function index() {
        try {
            $cupons = $this->cupomModel->findAll();
            
            $this->view('cupons/index', [
                'title' => 'Cupons - ' . APP_NAME,
                'cupons' => $cupons
            ]);
        } catch (Exception $e) {
            $this->setError('Erro ao carregar cupons');
            $this->redirect('/');
        }
    }
    
    public function create() {
        $this->view('cupons/form', [
            'title' => 'Novo Cupom - ' . APP_NAME,
            'cupom' => null
        ]);
    }
    
    public function store() {
        echo json_encode(['success' => true, 'message' => 'Cupom criado']);
    }
}
?>
