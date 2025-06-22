<?php
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-receipt me-3"></i>Gestão de Pedidos</h1>
        <p class="text-muted">Acompanhe e gerencie todos os pedidos</p>
    </div>
</div>

<?php if (empty($pedidos)): ?>
    <div class="text-center py-5">
        <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
        <h4>Nenhum pedido encontrado</h4>
        <p class="text-muted">Os pedidos aparecerão aqui quando realizados</p>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td>#<?= $pedido['numero_pedido'] ?></td>
                                <td><?= htmlspecialchars($pedido['cliente_nome']) ?></td>
                                <td><?= formatMoney($pedido['total']) ?></td>
                                <td><span class="badge bg-warning"><?= $pedido['status'] ?></span></td>
                                <td><?= date('d/m/Y', strtotime($pedido['created_at'])) ?></td>
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
