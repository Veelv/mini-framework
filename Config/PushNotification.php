<?php

namespace Config;

class PushNotification {
    private $firebaseApiKey;
    private $oneSignalApiKey;

    public function __construct($firebaseApiKey, $oneSignalApiKey) {
        $this->firebaseApiKey = $firebaseApiKey;
        $this->oneSignalApiKey = $oneSignalApiKey;
    }

    public function sendFirebaseNotification($deviceToken, $title, $body) {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = [
            'Authorization: key=' . $this->firebaseApiKey,
            'Content-Type: application/json'
        ];

        $data = [
            'to' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'click_action' => 'OPEN_ACTIVITY'
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function sendOneSignalNotification($deviceTokens, $title, $body) {
        $url = 'https://onesignal.com/api/v1/notifications';

        $headers = [
            'Authorization: Basic ' . $this->oneSignalApiKey,
            'Content-Type: application/json'
        ];

        $data = [
            'app_id' => 'YOUR_ONESIGNAL_APP_ID',
            'headings' => ['en' => $title],
            'contents' => ['en' => $body],
            'include_player_ids' => $deviceTokens
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}