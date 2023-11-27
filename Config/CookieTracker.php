<?php
namespace Config;
class CookieTracker {
    /**
     * Define um cookie com o nome, valor e tempo de expiração especificados.
     *
     * @param string $name Nome do cookie
     * @param string $value Valor do cookie
     * @param int $expiration Tempo de expiração em segundos (opcional)
     */
    public static function setCookie($name, $value, $expiration = 0) {
        if ($expiration !== 0) {
            $expiration = time() + $expiration;
        }
        setcookie($name, $value, $expiration, '/');
    }

    /**
     * Obtém o valor de um cookie específico.
     *
     * @param string $name Nome do cookie
     * @return string|null Valor do cookie ou null se não existir
     */
    public static function getCookie($name) {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return null;
    }

    /**
     * Exclui um cookie específico.
     *
     * @param string $name Nome do cookie
     */
    public static function deleteCookie($name) {
        if (isset($_COOKIE[$name])) {
            self::setCookie($name, '', time() - 3600);
            unset($_COOKIE[$name]);
        }
    }

    /**
     * Realiza o rastreamento das informações do usuário.
     * Neste exemplo, estamos rastreando apenas a última visita do usuário.
     */
    public static function trackUser() {
        $lastVisit = self::getCookie('last_visit');

        if ($lastVisit) {
            echo 'Bem-vindo de volta! Sua última visita foi em: ' . $lastVisit;
        } else {
            echo 'Bem-vindo! É a sua primeira visita!';
        }

        $currentDate = date('d/m/Y H:i:s');
        self::setCookie('last_visit', $currentDate);
    }
}