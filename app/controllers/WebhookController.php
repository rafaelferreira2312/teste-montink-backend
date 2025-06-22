<?php
class WebhookController extends BaseController {
    
    public function atualizarPedido() {
        echo json_encode(['success' => true, 'message' => 'Pedido atualizado']);
    }
    
    public function notificarEstoqueBaixo() {
        echo json_encode(['success' => true, 'message' => 'Notificação enviada']);
    }
    
    public function teste() {
        echo json_encode(['message' => 'Webhook funcionando', 'timestamp' => date('c')]);
    }
}
?>
