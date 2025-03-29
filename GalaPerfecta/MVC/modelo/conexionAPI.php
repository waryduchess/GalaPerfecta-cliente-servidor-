<?php

class APIConnection {
    const API_BASE_URL = "http://localhost:3306";  
       private static $instancia = null;
    private function __construct() {
    }

    private function __clone() { }
    public static function getInstance() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }
    public function get($endpoint, $headers = []) {
        $url = self::API_BASE_URL . $endpoint;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception("Error en la petición: " . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    public function post($endpoint, $data, $headers = []) {
        $url = self::API_BASE_URL . $endpoint;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
            ["Content-Type: application/json"],
            $headers
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception("Error en la petición: " . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }
}

?>