<?php

use Config\Mysql\MysqlDatabase;

if (!function_exists('slugify')) {
    function slugify($string)
    {
        // Remove caracteres especiais e deixa apenas letras, números e espaços
        $string = preg_replace('/[^\p{L}0-9\s]/u', '', $string);

        // Substitui acentos e caracteres com diacríticos por suas versões sem acento
        $string = preg_replace('/[\p{M}]/u', '', Normalizer::normalize($string, Normalizer::FORM_KD));

        // Transforma espaços em hífens
        $string = str_replace(' ', '-', $string);

        // Transforma letras maiúsculas em minúsculas
        $string = strtolower($string);

        // Remove hífens duplicados
        $string = preg_replace('/-+/', '-', $string);

        // Remove hífens do começo e do final da string
        $string = trim($string, '-');

        return $string;
    }

    function sanitize($value)
    {
        if ($value === null) {
            // Tratar o valor nulo como desejado, como retornar um valor padrão ou lançar uma exceção
            throw new InvalidArgumentException("O valor fornecido não pode ser nulo.");
        }

        if (is_array($value)) {
            return array_map('sanitize', $value);
        }

        $sanitizedValue = trim($value);
        $sanitizedValue = htmlspecialchars($sanitizedValue, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $sanitizedValue = filter_var($sanitizedValue, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        return $sanitizedValue;
    }

    function brl2decimal($brl, $casasDecimais = 2)
    {
        // Se já estiver no formato USD, retorna como float e formatado
        if (preg_match('/^\d+\.{1}\d+$/', $brl))
            return (float) number_format($brl, $casasDecimais, '.', '');
        // Tira tudo que não for número, ponto ou vírgula
        $brl = preg_replace('/[^\d\.\,]+/', '', $brl);
        // Tira o ponto
        $decimal = str_replace('.', '', $brl);
        // Troca a vírgula por ponto
        $decimal = str_replace(',', '.', $decimal);
        return (float) number_format($decimal, $casasDecimais, '.', '');
    }

    function decimal2brl($decimal, $casasDecimais = 2)
    {
        return 'R$ ' . number_format($decimal, $casasDecimais, ',', '.');
    }
}
