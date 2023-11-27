<?php

namespace Config;

use Exception;

class BarcodeSpiderAPI
{
    private $apiKey;
    private $apiUrl;
    private $apiHost;

    public function __construct()
    {
        $this->apiUrl = "https://real-time-product-search.p.rapidapi.com/search?q=";
        $this->apiKey = "60a0094883msh270133864c90e91p165634jsnbcfcf20b081c";
        $this->apiHost = "real-time-product-search.p.rapidapi.com";
    }
    public function getBarcodeDetails($barcode)
    {
        $url = $this->apiUrl . urlencode($barcode) . "&country=us&language=en";

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: " . $this->apiHost,
                "X-RapidAPI-Key: " . $this->apiKey
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error: " . $err;
        } else {
            return $response;
        }
    }
}
