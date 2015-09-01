<?php
namespace iTopApi {
    class iTopClient {
        var $endpoint;
        var $user;
        var $password;
        var $debug = false;
        var $certificateCheck = true;

        public function __construct($endpoint,$user,$password,$version='1.0') {

            $this->endpoint = $endpoint;
            $this->user = $user;
            $this->password = $password;
            $this->version = $version;
        }

        function setCertificateCheck($bool) {
            $this->certificateCheck = $bool;
        }

        public function sendRequest(array $data) {
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
            curl_setopt($curl, CURLOPT_URL,$url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, count($params));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            if( ! $this->certificateCheck ) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            } else {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
            }
            $jsonResponse = curl_exec($curl);
            $response = json_decode($jsonResponse,true);
            curl_close($curl);

            if(!$response)
                throw new \Exception('Invalid response from server : '.$jsonResponse);

            return $response;
        }

        public function operation($operation, array $data=array()) {
            $data['operation'] = $operation;
            return $this->sendRequest($data);
        }

        public function coreGet($class,$type=null) {
            if(is_null($type))
                $type = $class;
            return $this->operation('core/get',array(
                'class' => $class,
                'key' => 'SELECT '.$type
            ));
        }

        public function coreGetCustomSelect($class,$query) {
            return $this->operation('core/get',array(
                'class' => $class,
                'key' => $query
            ));
        }

    }
}