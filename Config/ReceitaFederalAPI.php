<?php
namespace Config;
use Config\Language;
use Config\Router\Response;

class ReceitaFederalAPI {
    private $apiUrl = 'https://www.receitaws.com.br/v1/cnpj/';

    private $response;
    private $language;

    public function __construct()
    {
        $this->response = new Response();
        $this->language = new Language();
    }
    
    public function consultCNPJ($cnpj) {
        // Remover caracteres não numéricos do CNPJ
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        // Verificar se o CNPJ é válido
        if (!$this->validateCNPJ($cnpj)) {
            $this->response->notFound(['message' => $this->language->translation('invalid_CNPJ')]);
        }
        
        // Consultar a API da Receita Federal
        $apiUrl = $this->apiUrl . $cnpj;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Verificar a resposta da API
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] === 'ERROR') {
            $this->response->notFound(['message' => $this->language->translation('CNPJ_Not_found_in_the_federal_revenue')]);
        }
        
        // Retornar os dados da API
        // return $this->response->success($data);
        return $data;
    }
    
    private function validateCNPJ($cnpj) {
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        $soma = 0;
        $multiplicador = 2;
        
        for ($i = 11; $i >= 0; $i--) {
            $soma += $multiplicador * (int)$cnpj[$i];
            $multiplicador = ($multiplicador + 1) % 10;
            if ($multiplicador === 0) {
                $multiplicador = 2;
            }
        }
        
        $digito = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
        
        return (int)$cnpj[12] === $digito;
    }
}
