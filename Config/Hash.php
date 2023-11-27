<?php

namespace Config;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Guid\Guid;

class Hash
{

    function generateFormattedCode()
    {
        $uuid = Guid::uuid4();
        $uuidString = strtoupper($uuid->toString());

        $uuidString = str_replace('-', '', $uuidString);

        // Formatar o UUID no padrão BOJ1-1N8W-QURZ-ZALE-Z7NH
        $formattedCode = substr($uuidString, 0, 4) . '-' .
            substr($uuidString, 4, 4) . '-' .
            substr($uuidString, 8, 4) . '-' .
            substr($uuidString, 12, 4) . '-' .
            substr($uuidString, 16, 4);

        return $formattedCode;
    }

    // public function generateNumericCode($length = 4, $segments = 5)
    // {
    //     $characters = '0123456789';
    //     $code = '';

    //     for ($i = 0; $i < $segments; $i++) {
    //         for ($j = 0; $j < $length; $j++) {
    //             $code .= $characters[random_int(0, strlen($characters) - 1)];
    //         }

    //         if ($i < $segments - 1) {
    //             $code .= '-';
    //         }
    //     }

    //     return $code;
    // }

    public function generateLotteryCode($length = 8)
    {
        $uuid = Uuid::uuid4();
        $uuidString = strtoupper($uuid->toString());
        $uuidString = str_replace('-', '', $uuidString);
        $formattedCode = substr($uuidString, 0, 5) .
            substr($uuidString, 2, 5);

        return $formattedCode;
    }

    public function order()
    {
        $uuid = Uuid::uuid4();
        $uuidString = strtoupper($uuid->toString());
        $uuidString = str_replace('-', '', $uuidString);
        $formattedCode = substr($uuidString, 0, 6);
        return $formattedCode;
    }

    public function token($length = 8)
    {
        $uuid = Uuid::uuid4();
        $uuidString = strtoupper($uuid->toString());
        $uuidString = str_replace('-', '', $uuidString);
        $formattedCode = substr($uuidString, 0, 17);
        return $formattedCode;
    }
}
