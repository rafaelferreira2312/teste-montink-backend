<?php
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1>
            <i class="fas fa-eye me-3 gradient-text"></i>
            Detalhes do Produto
        </h1>
        <p class="text-muted">Visualização completa das informações do produto</p>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group">
            <a href="/produtos" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
            <a href="/produto/editar?id=<?= $produto['id'] ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Editar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informações Principais -->
    <div class="col-lg-8">
        <div class="card shadow-custom-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <?= htmlspecialchars($produto['nome']) ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Preço Base</h6>
                        <h3 class="text-primary mb-3">
                            <?= formatMoney($produto['preco']) ?>
                        </h3>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Status</h6>
                        <div class="mb-3">
                            <?php if ($produto['ativo']): ?>
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle me-1"></i>Produto Ativo
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary fs-6">
                                    <i class="fas fa-pause-circle me-1"></i>Produto Inativo
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($produto['descricao'])): ?>
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Descrição</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Data de Cadastro</h6>
                        <p class="mb-0">
                            <i class="fas fa-calendar me-1"></i>
                            <?= date('d/m/Y', strtotime($produto['created_at'])) ?>
                            <small class="text-muted">às <?= date('H:i', strtotime($produto['created_at'])) ?></small>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Última Atualização</h6>
                        <p class="mb-0">
                            <i class="fas fa-clock me-1"></i>
                            <?= date('d/m/Y', strtotime($produto['updated_at'])) ?>
                            <small class="text-muted">às <?= date('H:i', strtotime($produto['updated_at'])) ?></small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Variações (se existirem) -->
        <?php if (!empty($produto['variacoes'])): ?>
            <div class="card shadow-custom-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        Variações Disponíveis
                        <span class="badge bg-info ms-2"><?= count($produto['variacoes']) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($produto['variacoes'] as $variacao): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">
                                                <?= htmlspecialchars($variacao['nome']) ?>
                                            </h6>
                                            <span class="badge <?= ($variacao['quantidade_estoque'] ?? 0) <= 5 ? 'bg-warning' : 'bg-success' ?>">
                                                <?= $variacao['quantidade_estoque'] ?? 0 ?> un.
                                            </span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <?php if ($variacao['valor_adicional'] > 0): ?>
                                                    <small class="text-success">
                                                        <i class="fas fa-plus me-1"></i>
                                                        <?= formatMoney($variacao['valor_adicional']) ?>
                                                    </small>
                                                    <br>
                                                    <strong class="text-primary">
                                                        <?= formatMoney($produto['preco'] + $variacao['valor_adicional']) ?>
                                                    </strong>
                                                <?php else: ?>
                                                    <strong class="text-primary">
                                                        <?= formatMoney($produto['preco']) ?>
                                                    </strong>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (($variacao['quantidade_estoque'] ?? 0) > 0): ?>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                       onclick="adicionarAoCarrinho(<?= $produto['id'] ?>, <?= $variacao['id'] ?>)">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar: Estoque e Ações -->
    <div class="col-lg-4">
        <div class="card shadow-custom-sm mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-warehouse me-2"></i>
                    Informações de Estoque
                </h6>
            </div>
            <div class="card-body text-center">
                <?php 
                $estoque_total = 0;
                if (!empty($produto['variacoes'])) {
                    foreach ($produto['variacoes'] as $variacao) {
                        $estoque_total += $variacao['quantidade_estoque'] ?? 0;
                    }
                } else {
                    $estoqueModel = new Estoque();
                    $estoque_total = $estoqueModel->verificarEstoque($produto['id']);
                }
                ?>
                
                <div class="mb-4">
                    <h1 class="display-4 <?= $estoque_total <= 5 ? 'text-warning' : 'text-success' ?>">
                        <?= $estoque_total ?>
                    </h1>
                    <p class="text-muted mb-0">unidades disponíveis</p>
                    
                    <?php if ($estoque_total <= 5 && $estoque_total > 0): ?>
                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Estoque baixo - reabastecer
                        </small>
                    <?php elseif ($estoque_total == 0): ?>
                        <small class="text-danger">
                            <i class="fas fa-times me-1"></i>
                            Produto esgotado
                        </small>
                    <?php else: ?>
                        <small class="text-success">
                            <i class="fas fa-check me-1"></i>
                            Estoque adequado
                        </small>
                    <?php endif; ?>
                </div>
                
                <?php if ($estoque_total > 0): ?>
                    <button class="btn btn-success btn-lg w-100 mb-3" 
                            onclick="adicionarAoCarrinho(<?= $produto['id'] ?>)">
                        <i class="fas fa-cart-plus me-2"></i>
                        Adicionar ao Carrinho
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg w-100 mb-3" disabled>
                        <i class="fas fa-ban me-2"></i>
                        Produto Esgotado
                    </button>
                <?php endif; ?>
                
                <div class="d-grid gap-2">
                    <a href="/produto/editar?id=<?= $produto['id'] ?>" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Editar Produto
                    </a>
                    <button class="btn btn-outline-secondary" onclick="copyProductLink()">
                        <i class="fas fa-share me-2"></i>Compartilhar
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Informações Técnicas -->
        <div class="card shadow-custom-sm">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Informações Técnicas
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-2 small">
                    <div class="col-6">
                        <strong>ID:</strong>
                    </div>
                    <div class="col-6">
                        #<?= $produto['id'] ?>
                    </div>
                    
                    <div class="col-6">
                        <strong>Variações:</strong>
                    </div>
                    <div class="col-6">
                        <?= count($produto['variacoes'] ?? []) ?>
                    </div>
                    
                    <div class="col-6">
                        <strong>Status:</strong>
                    </div>
                    <div class="col-6">
                        <?= $produto['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </div>
                    
                    <div class="col-6">
                        <strong>Criado em:</strong>
                    </div>
                    <div class="col-6">
                        <?= date('d/m/Y', strtotime($produto['created_at'])) ?>
                    </div>
                    
                    <div class="col-6">
                        <strong>Atualizado:</strong>
                    </div>
                    <div class="col-6">
                        <?= date('d/m/Y', strtotime($produto['updated_at'])) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyProductLink() {
    const url = window.location.href;
    MiniERPUtils.copyToClipboard(url);
}

// Atalhos de teclado
$(document).keydown(function(e) {
    // E para editar
    if (e.key === 'e' || e.key === 'E') {
        window.location.href = '/produto/editar?id=<?= $produto['id'] ?>';
    }
    
    // B para voltar
    if (e.key === 'b' || e.key === 'B') {
        window.location.href = '/produtos';
    }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
