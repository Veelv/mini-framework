<?php

namespace Config;

use Config\Language;
use DateTime;
use DateTimeZone;
use Predis\Client;

class DateFormat
{
    private static $config = [
        'cache_enabled' => true,
        'default_cache_ttl' => 3600,
        'timezone' => 'America/Sao_Paulo',
        'language' => 'pt_BR',
    ];

    private static $cache = [];

    public static function setConfig($config)
    {
        self::$config = array_merge(self::$config, $config);
    }

    public static function v_date($format = 'datetime', $addHours = 0, $addMinutes = 0, $addSeconds = 0)
    {
        // Check if the execution time exceeds 5 seconds and return default values
        $startTime = microtime(true);

        $language = new Language();

        $translation = function ($key) use ($language) {
            return $language->translation($key);
        };

        try {
            $now = new DateTime('now', new DateTimeZone('UTC'));
            $timeZone = self::$config['timezone'];
            $now->setTimezone(new DateTimeZone($timeZone));

            // Adiciona ou remove horas, minutos e segundos da data atual
            $now->modify(($addHours >= 0 ? "+" . $addHours : $addHours) . " hours");
            $now->modify(($addMinutes >= 0 ? "+" . $addMinutes : $addMinutes) . " minutes");
            $now->modify(($addSeconds >= 0 ? "+" . $addSeconds : $addSeconds) . " seconds");

            // Calculate the elapsed time
            $elapsedTime = microtime(true) - $startTime;
            if ($elapsedTime >= 5) {
                // Execution time exceeded 5 seconds, return default values
                return self::getDefaultFormattedDate($format);
            }

            // Format the date based on the provided format
            $formattedDate = self::formatDate($now, $format);

            // Define as traduções para os dias da semana e meses no idioma correspondente
            $daysOfWeek = array(
                "Sunday" => $translation('Sunday'),
                "Monday" => $translation('Monday'),
                "Tuesday" => $translation('Tuesday'),
                "Wednesday" => $translation('Wednesday'),
                "Thursday" => $translation('Thursday'),
                "Friday" => $translation('Friday'),
                "Saturday" => $translation('Saturday')
            );

            $months = array(
                "January" => $translation('January'),
                "February" => $translation('February'),
                "March" => $translation('March'),
                "April" => $translation('April'),
                "May" => $translation('May'),
                "June" => $translation('June'),
                "July" => $translation('July'),
                "August" => $translation('August'),
                "September" => $translation('September'),
                "October" => $translation('October'),
                "November" => $translation('November'),
                "December" => $translation('December')
            );

            $formattedDate = strtr($formattedDate, $daysOfWeek);
            $formattedDate = strtr($formattedDate, $months);

            return $formattedDate;
        } catch (\Exception $e) {
            // Tratar qualquer erro que ocorra durante o processamento da data
            return 'Error: ' . $e->getMessage();
        }
    }

    private static function getDefaultFormattedDate($format)
    {
        switch ($format) {
            case 'date':
                return date('d \d\e F \d\e Y');
            case 'today':
            case 'tomorrow':
            case 'time':
            case 'datetime':
            case 'datetime2':
            case 'timestemp':
            case 'currentTime':
                return date('l \à\s H:i');
            case 'custom':
                return date('d/m/Y');
            default:
                return date('d \d\e F \d\e Y \à\s H:i');
        }
    }

    private static function formatDate(DateTime $dateTime, $format)
    {
        switch ($format) {
            case 'date':
                return $dateTime->format('d \d\e F \d\e Y');
            case 'today':
                return $dateTime->format('l \à\s H:i');
            case 'tomorrow':
                return $dateTime->format('l \a\t H:i');
            case 'time':
                return $dateTime->format('l \à\s H:i');
            case 'custom':
                return $dateTime->format('d/m/Y');
            case 'datetime':
                return $dateTime->format('d \d\e F \d\e Y \à\s H:i');
            case 'datetime2':
                return $dateTime->format('Y-m-d H:i:s');
            case 'timestemp':
                return $dateTime->getTimestamp();
            case 'currentTime':
                return $dateTime->format('H:i');
            default:
                return $dateTime->format('d \d\e F \d\e Y \à\s H:i');
        }
    }

    public static function clearCache()
    {
        $redis = new Client();
        $redis->connect();

        // Limpa o cache padrão
        $defaultCacheKey = 'default_cache_data';
        $redis->del($defaultCacheKey);
    }
}
