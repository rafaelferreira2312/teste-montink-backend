<?php
ob_start();
?>

<div class="text-center py-5">
    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-4"></i>
    <h1 class="display-1 fw-bold">404</h1>
    <h2 class="mb-4">Página não encontrada</h2>
    <p class="lead mb-4">A página que você está procurando não existe.</p>
    <a href="/" class="btn btn-primary">
        <i class="fas fa-home me-2"></i>Voltar ao Início
    </a>
</div>

<?php
$content = ob_get_clean();
$title = 'Página não encontrada - ' . APP_NAME;
include 'layout.php';
?>
