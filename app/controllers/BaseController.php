<?php
class BaseController {
    
    /**
     * Renderizar view
     */
    protected function view($view, $data = []) {
        // Extrair dados para variáveis
        extract($data);
        
        // Incluir a view
        $viewFile = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View não encontrada: {$view}");
        }
    }
    
    /**
     * Redirecionar
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Resposta JSON
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Validar campos obrigatórios
     */
    protected function validateRequired($fields, $data) {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[] = "O campo {$field} é obrigatório";
            }
        }
        return $errors;
    }
    
    /**
     * Sanitizar dados de entrada
     */
    protected function sanitize($data) {
        return clean($data);
    }
    
    /**
     * Verificar se é requisição POST
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Verificar se é requisição GET
     */
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    /**
     * Obter dados POST sanitizados
     */
    protected function getPostData() {
        return $this->sanitize($_POST);
    }
    
    /**
     * Obter dados GET sanitizados
     */
    protected function getGetData() {
        return $this->sanitize($_GET);
    }
    
    /**
     * Definir mensagem de sucesso
     */
    protected function setSuccess($message) {
        $_SESSION['success'] = $message;
    }
    
    /**
     * Definir mensagem de erro
     */
    protected function setError($message) {
        $_SESSION['error'] = $message;
    }
    
    /**
     * Definir mensagens de erro (array)
     */
    protected function setErrors($errors) {
        $_SESSION['errors'] = $errors;
    }
    
    /**
     * Definir dados antigos para reexibir no formulário
     */
    protected function setOldData($data) {
        $_SESSION['old_data'] = $data;
    }
    
    /**
     * Validar email
     */
    protected function validateEmail($email) {
        return isValidEmail($email);
    }
    
    /**
     * Validar CEP
     */
    protected function validateCEP($cep) {
        return isValidCEP($cep);
    }
    
    /**
     * Log de ação
     */
    protected function log($message, $type = 'INFO') {
        writeLog($message, $type);
    }
}
?>
