<?php

namespace Config\Router;

class Request
{
    public function getParsedBody()
    {
        try {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if (stripos($contentType, 'application/json') !== false) {
                // Obter dados do corpo da solicitação JSON
                $body = file_get_contents('php://input');
                $parsedBody = json_decode($body, true);
            } elseif (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
                // Obter dados do corpo da solicitação de formulário urlencode
                $parsedBody = $_POST;
            } elseif (stripos($contentType, 'multipart/form-data') !== false) {
                // Obter dados do corpo da solicitação multipart/form-data
                $parsedBody = $_POST;

                // Verificar se há arquivo enviado
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    // O arquivo foi enviado com sucesso
                    $parsedBody['image'] = $_FILES['image'];
                }
            } else {
                $parsedBody = [];
            }

            return $parsedBody;
        } catch (\Throwable $e) {
            // Tratamento de erro personalizado
            throw new \Exception('Erro ao obter o corpo da solicitação: ' . $e->getMessage());
        }
    }
}
