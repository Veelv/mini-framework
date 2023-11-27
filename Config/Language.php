<?php

namespace Config;

class Language
{
    private $defaultLanguage = 'en_US';
    private $supportedLanguages = ['en_US', 'pt_BR'];
    private $translations = [];
    private $env;
    private $ambient;

    public function __construct()
    {
        $this->env = new EnvLoader(__DIR__ . '/../.env');
        $this->ambient = $this->env->get('APP_ENV');
    }

    public function getLanguage()
    {
        $cachedLanguage = $this->getCachedLanguage();
        if ($cachedLanguage) {
            return $cachedLanguage;
        }

        $userLanguage = $this->getUserLanguage();
        if ($userLanguage) {
            return $userLanguage;
        }

        return $this->defaultLanguage;
    }

    private function getCachedLanguage()
    {
        if (isset($_COOKIE['language'])) {
            $cachedLanguage = $_COOKIE['language'];
            if ($this->isLanguageSupported($cachedLanguage)) {
                return $cachedLanguage;
            }
        }

        return false;
    }

    private function getUserLanguage()
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $acceptedLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($acceptedLanguages as $language) {
                $language = explode(';', $language)[0];
                $language = trim($language);
                if ($this->isLanguageSupported($language)) {
                    return $language;
                }
            }
        }

        return null;
    }

    private function isLanguageSupported($language)
    {
        return in_array($language, $this->supportedLanguages);
    }

    private function loadTranslations($language)
    {
        $translationFile = __DIR__ . '/../App/Languages/' . $this->defaultLanguage . '.json';
        if (file_exists($translationFile)) {
            $this->translations[$language] = json_decode(file_get_contents($translationFile), true);
        } else {
            $this->translations[$language] = [];
        }
    }

    private function setTranslation($key, $value)
    {
        $this->translations[$this->defaultLanguage][$key] = $value;
    }

    private function getOriginalTranslation($key)
    {
        $defaultLanguageTranslationFile = __DIR__ . '/../App/Languages/' . $this->defaultLanguage . '.json';
        if (file_exists($defaultLanguageTranslationFile)) {
            $defaultLanguageTranslations = json_decode(file_get_contents($defaultLanguageTranslationFile), true);
            if (isset($defaultLanguageTranslations[$key])) {
                return $defaultLanguageTranslations[$key];
            }
        }

        return null;
    }

    public function translation($key)
    {
        $language = $this->getLanguage();
        if ($this->isLanguageSupported($language)) {
            if (!isset($this->translations[$language])) {
                $this->loadTranslations($language);
            }
            $translation = $this->getTranslation($key, $key);
            if ($translation) {
                return $translation;
            } else {
                $originalTranslation = $this->getOriginalTranslation($key);
                if ($originalTranslation) {
                    $this->setTranslation($key, $originalTranslation);
                    return $originalTranslation;
                }
            }
        }

        return $key;
    }

    private function getTranslation($key, $language)
    {
        if (isset($this->translations[$language][$key])) {
            return $this->translations[$language][$key];
        }

        return null;
    }
}