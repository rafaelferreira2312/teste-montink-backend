<?php
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-shopping-cart me-3"></i>Carrinho de Compras</h1>
        <p class="text-muted">Revise seus itens e finalize sua compra</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="/" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Continuar Comprando
        </a>
    </div>
</div>

<?php if (empty($carrinho)): ?>
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
        <h4>Seu carrinho está vazio</h4>
        <p class="text-muted">Adicione produtos ao seu carrinho</p>
        <a href="/" class="btn btn-primary">Ver Produtos</a>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>Itens no Carrinho</h5>
                </div>
                <div class="card-body">
                    <p>Itens do carrinho aparecerão aqui</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Resumo</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Total:</span>
                        <strong><?= formatMoney($totais['total']) ?></strong>
                    </div>
                    <button class="btn btn-success w-100 mt-3">Finalizar Compra</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
