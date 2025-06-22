<?php
ob_start();
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['old_data']);
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="fas fa-<?= $produto ? 'edit' : 'plus' ?> me-3 gradient-text"></i>
            <?= $produto ? 'Editar Produto' : 'Novo Produto' ?>
        </h1>
        <p class="text-muted">
            <?= $produto ? 'Atualize as informações do produto e gerencie o estoque' : 'Preencha os dados para cadastrar um novo produto no catálogo' ?>
        </p>
    </div>
    <div class="col-md-4 text-end">
        <a href="/produtos" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar à Lista
        </a>
    </div>
</div>

<form method="POST" action="<?= $produto ? '/produto/atualizar' : '/produto/salvar' ?>" enctype="multipart/form-data">
    <?php if ($produto): ?>
        <input type="hidden" name="id" value="<?= $produto['id'] ?>">
    <?php endif; ?>
    
    <div class="row">
        <!-- Informações Principais -->
        <div class="col-lg-8">
            <div class="card shadow-custom-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Principais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nome" class="form-label">
                                Nome do Produto *
                                <small class="text-muted">(Será exibido para os clientes)</small>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nome" 
                                   name="nome" 
                                   value="<?= htmlspecialchars($old_data['nome'] ?? $produto['nome'] ?? '') ?>" 
                                   required 
                                   placeholder="Ex: Smartphone Galaxy S23"
                                   data-char-counter
                                   maxlength="100">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">
                            Descrição Detalhada
                            <small class="text-muted">(Opcional)</small>
                        </label>
                        <textarea class="form-control" 
                                  id="descricao" 
                                  name="descricao" 
                                  rows="4" 
                                  placeholder="Descreva as características, benefícios e especificações do produto..."
                    </div>
                    
                    <div class="mb-3">
                        <label for="imagem" class="form-label">
                            URL da Imagem
                            <small class="text-muted">(Opcional)</small>
                        </label>
                        <input type="url" 
                               class="form-control" 
                               id="imagem" 
                               name="imagem" 
                               value="<?= htmlspecialchars($old_data['imagem'] ?? $produto['imagem'] ?? '') ?>" 
                               placeholder="https://exemplo.com/imagem.jpg">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Cole a URL de uma imagem para exibir no produto
                        </div>
                                  data-auto-resize
                                  data-char-counter
                                  maxlength="500"><?= htmlspecialchars($old_data['descricao'] ?? $produto['descricao'] ?? '') ?></textarea>
                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Uma boa descrição ajuda os clientes a entenderem melhor o produto
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Variações do Produto -->
            <div class="card shadow-custom-sm">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-layer-group me-2"></i>
                            Variações do Produto
                        </h5>
                        <span class="badge bg-info">Opcional</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Dica:</strong> Use variações para oferecer diferentes opções do mesmo produto 
                        (tamanhos, cores, modelos) com preços e estoques específicos.
                    </div>
                    
                    <div id="variacoes-container">
                        <?php if (!empty($variacoes)): ?>
                            <?php foreach ($variacoes as $index => $variacao): ?>
                                <div class="row mb-3 variacao-item p-3 border rounded bg-light">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Nome da Variação</label>
                                        <input type="text" 
                                               class="form-control form-control-sm" 
                                               name="variacoes[<?= $index ?>][nome]" 
                                               placeholder="Ex: Tamanho M, Cor Azul, 128GB" 
                                               value="<?= htmlspecialchars($variacao['nome']) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Valor Adicional</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">+R$</span>
                                            <input type="number" 
                                                   class="form-control" 
                                                   name="variacoes[<?= $index ?>][valor_adicional]" 
                                                   placeholder="0,00" 
                                                   step="0.01" 
                                                   min="0"
                                                   value="<?= $variacao['valor_adicional'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Estoque Inicial</label>
                                        <input type="number" 
                                               class="form-control form-control-sm" 
                                               name="variacoes[<?= $index ?>][estoque]" 
                                               placeholder="0" 
                                               min="0"
                                               value="<?= $variacao['quantidade_estoque'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm remove-variacao w-100"
                                                data-bs-toggle="tooltip" 
                                                title="Remover esta variação">
                                            <i class="fas fa-trash me-1"></i>Remover
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary" id="add-variacao">
                        <i class="fas fa-plus me-2"></i>Adicionar Nova Variação
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sidebar: Preço e Configurações -->
        <div class="col-lg-4">
            <div class="card shadow-custom-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>
                        Preço e Estoque
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço Base *</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <input type="number" 
                                   class="form-control" 
                                   id="preco" 
                                   name="preco" 
                                   step="0.01" 
                                   min="0" 
                                   value="<?= $old_data['preco'] ?? $produto['preco'] ?? '' ?>" 
                                   required
                                   placeholder="0,00">
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Preço base do produto (sem variações)
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estoque" class="form-label">
                            Quantidade em Estoque
                            <?php if (isset($produto['estoque_atual'])): ?>
                                <small class="text-primary">(Atual: <?= $produto['estoque_atual'] ?>)</small>
                            <?php endif; ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-cubes"></i>
                            </span>
                            <input type="number" 
                                   class="form-control" 
                                   id="estoque" 
                                   name="estoque" 
                                   min="0" 
                                   value="<?= $old_data['estoque'] ?? ($produto['estoque_atual'] ?? '') ?>"
                                   placeholder="Quantidade disponível">
                        </div>
                        <div class="form-text">
                            <?= $produto ? 'Deixe em branco para manter o estoque atual' : 'Quantidade inicial em estoque' ?>
                        </div>
                    </div>
                    
                    <?php if ($produto): ?>
                        <div class="mb-3">
                            <label class="form-label">Status do Produto</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="ativo" 
                                       name="ativo" 
                                       value="1" 
                                       <?= ($produto['ativo'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ativo">
                                    <strong>Produto ativo na loja</strong>
                                </label>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-eye-slash me-1"></i>
                                Produtos inativos não aparecem para os clientes
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Preview do Produto -->
            <div class="card shadow-custom-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Preview do Produto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="bg-light rounded p-4 mb-3">
                            <i class="fas fa-box fa-3x text-muted"></i>
                        </div>
                        <h6 id="preview-nome" class="text-muted">Nome do produto...</h6>
                        <h5 id="preview-preco" class="text-primary">R$ 0,00</h5>
                        <small id="preview-descricao" class="text-muted">Descrição do produto...</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botões de Ação -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-custom-sm">
                <div class="card-body">
                    <div class="d-flex gap-3 justify-content-end">
                        <a href="/produtos" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>
                            <?= $produto ? 'Atualizar Produto' : 'Salvar Produto' ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let variacaoIndex = <?= !empty($variacoes) ? count($variacoes) : 0 ?>;

$(document).ready(function() {
    // Preview em tempo real
    updatePreview();
    
    $('#nome, #preco, #descricao').on('input', function() {
        updatePreview();
    });
});

// Adicionar nova variação
$('#add-variacao').click(function() {
    const html = `
        <div class="row mb-3 variacao-item p-3 border rounded bg-light" style="animation: fadeInUp 0.3s ease-out;">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Nome da Variação</label>
                <input type="text" 
                       class="form-control form-control-sm" 
                       name="variacoes[${variacaoIndex}][nome]" 
                       placeholder="Ex: Tamanho M, Cor Azul, 128GB">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Valor Adicional</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">+R$</span>
                    <input type="number" 
                           class="form-control" 
                           name="variacoes[${variacaoIndex}][valor_adicional]" 
                           placeholder="0,00" 
                           step="0.01" 
                           min="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Estoque Inicial</label>
                <input type="number" 
                       class="form-control form-control-sm" 
                       name="variacoes[${variacaoIndex}][estoque]" 
                       placeholder="0" 
                       min="0">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" 
                        class="btn btn-outline-danger btn-sm remove-variacao w-100"
                        data-bs-toggle="tooltip" 
                        title="Remover esta variação">
                    <i class="fas fa-trash me-1"></i>Remover
                </button>
            </div>
        </div>
    `;
    
    $('#variacoes-container').append(html);
    variacaoIndex++;
    
    // Reativar tooltips nos novos elementos
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    showToast('Nova variação adicionada', 'success');
});

// Remover variação
$(document).on('click', '.remove-variacao', function() {
    const $item = $(this).closest('.variacao-item');
    $item.fadeOut(300, function() {
        $(this).remove();
        showToast('Variação removida', 'info');
    });
});

// Atualizar preview
function updatePreview() {
    const nome = $('#nome').val() || 'Nome do produto...';
    const preco = $('#preco').val();
    const descricao = $('#descricao').val() || 'Descrição do produto...';
    
    $('#preview-nome').text(nome);
    $('#preview-preco').text(preco ? formatMoney(parseFloat(preco)) : 'R$ 0,00');
    $('#preview-descricao').text(descricao.substring(0, 50) + (descricao.length > 50 ? '...' : ''));
}

// Validações do formulário
$('form').on('submit', function(e) {
    const preco = parseFloat($('#preco').val());
    
    if (preco <= 0) {
        e.preventDefault();
        showToast('O preço deve ser maior que zero', 'error');
        $('#preco').focus();
        return false;
    }
    
    // Validar nome único (simulação)
    const nome = $('#nome').val().trim();
    if (nome.length < 3) {
        e.preventDefault();
        showToast('O nome deve ter pelo menos 3 caracteres', 'error');
        $('#nome').focus();
        return false;
    }
});

// Atalhos de teclado
$(document).keydown(function(e) {
    // Ctrl + S para salvar
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        $('form').submit();
    }
    
    // Esc para cancelar
    if (e.key === 'Escape') {
        if (confirm('Deseja cancelar e voltar à lista de produtos?')) {
            window.location.href = '/produtos';
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
