<?php
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-box me-3"></i>Gestão de Produtos</h1>
        <p class="text-muted">Gerencie seu catálogo de produtos</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="/produto/criar" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Novo Produto
        </a>
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
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Status</th>
                            <th width="200">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($produto['nome']) ?></strong>
                                    <?php if (!empty($produto['descricao'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars(substr($produto['descricao'], 0, 60)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <strong><?= formatMoney($produto['preco']) ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $estoque = $produto['total_estoque'] ?? 0;
                                    $badgeClass = $estoque <= 5 ? 'bg-warning' : ($estoque > 0 ? 'bg-success' : 'bg-danger');
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $estoque ?></span>
                                </td>
                                <td>
                                    <?php if ($produto['ativo']): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="/produto/detalhes?id=<?= $produto['id'] ?>" class="btn btn-sm btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/produto/editar?id=<?= $produto['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($estoque > 0): ?>
                                            <button class="btn btn-sm btn-outline-success" onclick="adicionarAoCarrinho(<?= $produto['id'] ?>)" title="Comprar">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
