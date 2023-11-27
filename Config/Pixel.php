<?php

namespace Config;

class Pixel
{
    private $facebook;
    private $google;

    public function __construct()
    {
        $this->facebook = "SEU_FACEBOOK_PIXEL_ID";
        $this->google = "SEU_GOOGLE_PIXEL_ID";
    }

    public function trackEventFacebook($eventName, $eventData = array())
    {
        if (!empty($this->facebook)) {
            $fbq = <<<FBQ
            <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window,document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{$this->facebook}');
            fbq('track', '{$eventName}', {$eventData});
            </script>
            FBQ;

            // Renderize o código do Facebook Pixel
            echo $fbq;
        }
    }

    public function trackEventGoogle($eventName, $eventData = array())
    {
        if (!empty($this->google)) {
            $url = 'https://www.google-analytics.com/collect';
            $data = array(
                'v' => '1',
                'tid' => $this->google,
                'cid' => '555', // ID do cliente (pode ser um valor único para cada usuário)
                't' => 'event',
                'ec' => 'conversion',
                'ea' => $eventName
            );

            if (!empty($eventData)) {
                $dataString = http_build_query($eventData);
                $url .= '?' . $dataString;
            }

            // Envie os dados para o Google Analytics
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
    }
}