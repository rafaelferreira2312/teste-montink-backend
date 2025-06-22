<?php
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-tachometer-alt me-3"></i>Dashboard</h1>
        <p class="text-muted">Bem-vindo ao <?= APP_NAME ?> - Gerencie seus produtos e vendas</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="/produto/criar" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Novo Produto
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-box fa-2x mb-2"></i>
                <h3><?= count($produtos ?? []) ?></h3>
                <p class="mb-0">Produtos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                <h3><?= isset($_SESSION['carrinho']) ? count($_SESSION['carrinho']) : 0 ?></h3>
                <p class="mb-0">Carrinho</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-warehouse fa-2x mb-2"></i>
                <h3><?= isset($produtos) ? array_sum(array_column($produtos, 'total_estoque')) : 0 ?></h3>
                <p class="mb-0">Estoque Total</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h3><?= isset($produtos) ? count(array_filter($produtos, function($p) { return ($p['total_estoque'] ?? 0) <= 5; })) : 0 ?></h3>
                <p class="mb-0">Estoque Baixo</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <h3><i class="fas fa-star me-2"></i>Produtos em Destaque</h3>
    </div>
</div>

<?php if (empty($produtos)): ?>
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
        <h4>Nenhum produto cadastrado</h4>
        <p class="text-muted">Comece adicionando produtos ao seu catálogo</p>
        <a href="/produto/criar" class="btn btn-primary btn-lg">
            <i class="fas fa-plus me-2"></i>Cadastrar Primeiro Produto
        </a>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach (array_slice($produtos, 0, 6) as $produto): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($produto['imagem'])): ?>
                        <img src="<?= htmlspecialchars($produto['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($produto['nome']) ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($produto['nome']) ?></h5>
                        <p class="card-text text-muted flex-grow-1"><?= htmlspecialchars(substr($produto['descricao'] ?? '', 0, 80)) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-primary mb-0"><?= formatMoney($produto['preco']) ?></h4>
                            <span class="badge bg-info"><?= $produto['total_estoque'] ?? 0 ?> un.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="/produto/editar?id=<?= $produto['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <?php if (($produto['total_estoque'] ?? 0) > 0): ?>
                                <button class="btn btn-sm btn-success flex-fill" onclick="adicionarAoCarrinho(<?= $produto['id'] ?>)">
                                    <i class="fas fa-cart-plus"></i> Comprar
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary flex-fill" disabled>
                                    <i class="fas fa-ban"></i> Esgotado
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (count($produtos) > 6): ?>
        <div class="text-center mt-4">
            <a href="/produtos" class="btn btn-outline-primary">
                Ver Todos os Produtos (<?= count($produtos) ?>)
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="row mt-5">
    <div class="col-12 mb-3">
        <h3><i class="fas fa-bolt me-2"></i>Ações Rápidas</h3>
    </div>
    <div class="col-md-3">
        <a href="/produto/criar" class="btn btn-outline-primary w-100 py-3">
            <i class="fas fa-plus fa-2x d-block mb-2"></i>
            Novo Produto
        </a>
    </div>
    <div class="col-md-3">
        <a href="/cupons" class="btn btn-outline-success w-100 py-3">
            <i class="fas fa-ticket-alt fa-2x d-block mb-2"></i>
            Cupons
        </a>
    </div>
    <div class="col-md-3">
        <a href="/pedidos" class="btn btn-outline-warning w-100 py-3">
            <i class="fas fa-receipt fa-2x d-block mb-2"></i>
            Pedidos
        </a>
    </div>
    <div class="col-md-3">
        <a href="/carrinho" class="btn btn-outline-danger w-100 py-3">
            <i class="fas fa-shopping-cart fa-2x d-block mb-2"></i>
            Carrinho
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
