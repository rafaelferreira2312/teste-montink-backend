<?php
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-ticket-alt me-3"></i>Gestão de Cupons</h1>
        <p class="text-muted">Gerencie cupons de desconto</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="/cupom/criar" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Novo Cupom
        </a>
    </div>
</div>

<?php if (empty($cupons)): ?>
    <div class="text-center py-5">
        <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
        <h4>Nenhum cupom cadastrado</h4>
        <p class="text-muted">Crie cupons para oferecer descontos aos clientes</p>
        <a href="/cupom/criar" class="btn btn-primary">Criar Primeiro Cupom</a>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Válido até</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cupons as $cupom): ?>
                            <tr>
                                <td><strong><?= $cupom['codigo'] ?></strong></td>
                                <td><?= $cupom['tipo'] ?></td>
                                <td><?= $cupom['tipo'] == 'percentual' ? $cupom['valor'] . '%' : formatMoney($cupom['valor']) ?></td>
                                <td><?= date('d/m/Y', strtotime($cupom['data_fim'])) ?></td>
                                <td><span class="badge bg-success">Ativo</span></td>
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
