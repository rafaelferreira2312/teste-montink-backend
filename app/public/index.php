<?php
require_once '../config/config.php';

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);
$path = strtok($path, '?');
$path = rtrim($path, '/');
if (empty($path)) $path = '/';

try {
    switch ($path) {
        case '/':
            $controller = new HomeController();
            $controller->index();
            break;
        case '/produtos':
            $controller = new ProdutoController();
            $controller->index();
            break;
        case '/produto/criar':
            $controller = new ProdutoController();
            $controller->create();
            break;
        case '/produto/salvar':
            $controller = new ProdutoController();
            $controller->store();
            break;
        case '/produto/editar':
            $controller = new ProdutoController();
            $controller->edit();
            break;
        case '/produto/atualizar':
            $controller = new ProdutoController();
            $controller->update();
            break;
        case '/produto/detalhes':
            $controller = new ProdutoController();
            $controller->show();
            break;
        case '/carrinho':
            $controller = new CarrinhoController();
            $controller->index();
            break;
        case '/carrinho/adicionar':
            $controller = new CarrinhoController();
            $controller->adicionar();
            break;
        case '/carrinho/remover':
            $controller = new CarrinhoController();
            $controller->remover();
            break;
        case '/carrinho/atualizar':
            $controller = new CarrinhoController();
            $controller->atualizar();
            break;
        case '/carrinho/limpar':
            $controller = new CarrinhoController();
            $controller->limpar();
            break;
        case '/carrinho/aplicar-cupom':
            $controller = new CarrinhoController();
            $controller->aplicarCupom();
            break;
        case '/pedidos':
            $controller = new PedidoController();
            $controller->index();
            break;
        case '/pedido/finalizar':
            $controller = new PedidoController();
            $controller->finalizar();
            break;
        case '/pedido/detalhes':
            $controller = new PedidoController();
            $controller->detalhes();
            break;
        case '/cupons':
            $controller = new CupomController();
            $controller->index();
            break;
        case '/cupom/criar':
            $controller = new CupomController();
            $controller->create();
            break;
        case '/cupom/salvar':
            $controller = new CupomController();
            $controller->store();
            break;
        case '/api/cep':
            $controller = new ApiController();
            $controller->consultarCep();
            break;
        case '/api/produtos':
            $controller = new ApiController();
            $controller->buscarProdutos();
            break;
        case '/api/estoque':
            $controller = new ApiController();
            $controller->verificarEstoque();
            break;
        case '/api/frete':
            $controller = new ApiController();
            $controller->calcularFrete();
            break;
        case '/api/status':
            $controller = new ApiController();
            $controller->status();
            break;
        case '/webhook/pedido':
            $controller = new WebhookController();
            $controller->atualizarPedido();
            break;
        case '/webhook/estoque':
            $controller = new WebhookController();
            $controller->notificarEstoqueBaixo();
            break;
        case '/webhook/teste':
            $controller = new WebhookController();
            $controller->teste();
            break;
        default:
            http_response_code(404);
            include '../views/404.php';
            break;
    }
} catch (Exception $e) {
    writeLog("Erro: " . $e->getMessage(), 'ERROR');
    echo "<h1>Erro</h1><p>" . $e->getMessage() . "</p>";
}
?>
