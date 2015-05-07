<?php
namespace iTopApi {
    class iTopClient {
        var $endpoint;
        var $user;
        var $password;
        var $debug = false;
        public function __construct($endpoint,$user,$password,$version='1.0') {

            $this->endpoint = $endpoint;
            $this->user = $user;
            $this->password = $password;
            $this->version = $version;
        }

        public function sendRequest($data) {
            $url = $this->endpoint . '/webservices/rest.php';
            $data['auth_user'] = $this->user;
            $data['auth_pwd'] = $this->password;
            $payload = json_encode($data);
            $query = array(
                'version' => $this->version,
                'auth_user' => $this->user,
                'auth_pwd' => $this->password,
                'json_data' => $payload
            );

            $params = http_build_query($query);

            $curl = curl_init();
            curl_setopt($curl,CURLOPT_URL,$url);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, count($params));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            $jsonResponse = curl_exec($curl);
            $response = json_decode($jsonResponse,true);
            curl_close($curl);

            if(!$response)
                throw new \Exception('Invalid response from server : '.$jsonResponse);

            return $response;
        }

        public function operation($operation) {
            return $this->sendRequest(array('operation' => $operation));
        }

    }
}