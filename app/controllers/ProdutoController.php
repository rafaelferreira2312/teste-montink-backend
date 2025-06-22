<?php
class ProdutoController extends BaseController {
    private $produtoModel;
    private $estoqueModel;
    
    public function __construct() {
        $this->produtoModel = new Produto();
        $this->estoqueModel = new Estoque();
    }
    
    /**
     * Listar produtos
     */
    public function index() {
        try {
            $produtos = $this->produtoModel->getWithEstoque();
            
            $this->view('produtos/index', [
                'title' => 'Produtos - ' . APP_NAME,
                'produtos' => $produtos
            ]);
        } catch (Exception $e) {
            $this->log("Erro ao listar produtos: " . $e->getMessage(), 'ERROR');
            $this->setError('Erro ao carregar produtos');
            $this->redirect('/');
        }
    }
    
    /**
     * Exibir formulário de criação
     */
    public function create() {
        $this->view('produtos/form', [
            'title' => 'Novo Produto - ' . APP_NAME,
            'produto' => null,
            'variacoes' => []
        ]);
    }
    
    /**
     * Salvar novo produto
     */
    public function store() {
        if (!$this->isPost()) {
            $this->redirect('/produtos');
        }
        
        $data = $this->getPostData();
        $errors = $this->validateRequired(['nome', 'preco'], $data);
        
        // Validações específicas
        if (!is_numeric($data['preco']) || $data['preco'] <= 0) {
            $errors[] = 'Preço deve ser um valor válido maior que zero';
        }
        
        if (!empty($errors)) {
            $this->setErrors($errors);
            $this->setOldData($data);
            $this->redirect('/produto/criar');
        }
        
        try {
            $this->produtoModel->beginTransaction();
            
            $produto_id = $this->produtoModel->create($data);
            
            if ($produto_id) {
                // Processar estoque inicial se informado
                if (!empty($data['estoque']) && is_numeric($data['estoque'])) {
                    $this->estoqueModel->atualizarEstoque($produto_id, null, (int)$data['estoque']);
                }
                
                // Processar variações se existirem
                if (!empty($data['variacoes']) && is_array($data['variacoes'])) {
                    foreach ($data['variacoes'] as $variacao) {
                        if (!empty($variacao['nome'])) {
                            $this->produtoModel->createVariacao($produto_id, $variacao);
                        }
                    }
                }
                
                $this->produtoModel->commit();
                $this->setSuccess('Produto criado com sucesso!');
                $this->redirect('/produtos');
            } else {
                throw new Exception('Erro ao criar produto');
            }
        } catch (Exception $e) {
            $this->produtoModel->rollback();
            $this->log("Erro ao criar produto: " . $e->getMessage(), 'ERROR');
            $this->setError('Erro ao criar produto: ' . $e->getMessage());
            $this->setOldData($data);
            $this->redirect('/produto/criar');
        }
    }
    
    /**
     * Exibir formulário de edição
     */
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            $this->setError('Produto não encontrado');
            $this->redirect('/produtos');
        }
        
        try {
            $produto = $this->produtoModel->findById($id);
            $variacoes = $this->produtoModel->getVariacoes($id);
            
            if (!$produto) {
                $this->setError('Produto não encontrado');
                $this->redirect('/produtos');
            }
            
            // Buscar estoque atual
            $estoque_atual = $this->estoqueModel->verificarEstoque($id);
            $produto['estoque_atual'] = $estoque_atual;
            
            $this->view('produtos/form', [
                'title' => 'Editar Produto - ' . APP_NAME,
                'produto' => $produto,
                'variacoes' => $variacoes
            ]);
        } catch (Exception $e) {
            $this->log("Erro ao buscar produto: " . $e->getMessage(), 'ERROR');
            $this->setError('Erro ao carregar produto');
            $this->redirect('/produtos');
        }
    }
    
    /**
     * Atualizar produto
     */
    public function update() {
        if (!$this->isPost()) {
            $this->redirect('/produtos');
        }
        
        $data = $this->getPostData();
        $id = $data['id'] ?? null;
        
        if (!$id || !is_numeric($id)) {
            $this->setError('Produto não encontrado');
            $this->redirect('/produtos');
        }
        
        $errors = $this->validateRequired(['nome', 'preco'], $data);
        
        if (!is_numeric($data['preco']) || $data['preco'] <= 0) {
            $errors[] = 'Preço deve ser um valor válido maior que zero';
        }
        
        if (!empty($errors)) {
            $this->setErrors($errors);
            $this->redirect("/produto/editar?id={$id}");
        }
        
        try {
            $this->produtoModel->update($id, $data);
            
            // Atualizar estoque se fornecido
            if (isset($data['estoque']) && is_numeric($data['estoque'])) {
                $this->estoqueModel->atualizarEstoque($id, null, (int)$data['estoque']);
            }
            
            $this->setSuccess('Produto atualizado com sucesso!');
            $this->redirect('/produtos');
        } catch (Exception $e) {
            $this->log("Erro ao atualizar produto: " . $e->getMessage(), 'ERROR');
            $this->setError('Erro ao atualizar produto: ' . $e->getMessage());
            $this->redirect("/produto/editar?id={$id}");
        }
    }
    
    /**
     * Ver detalhes do produto
     */
    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            $this->redirect('/produtos');
        }
        
        try {
            $produto = $this->produtoModel->getCompleto($id);
            
            if (!$produto) {
                $this->setError('Produto não encontrado');
                $this->redirect('/produtos');
            }
            
            $this->view('produtos/show', [
                'title' => $produto['nome'] . ' - ' . APP_NAME,
                'produto' => $produto
            ]);
        } catch (Exception $e) {
            $this->log("Erro ao visualizar produto: " . $e->getMessage(), 'ERROR');
            $this->setError('Erro ao carregar produto');
            $this->redirect('/produtos');
        }
    }
}
?>
