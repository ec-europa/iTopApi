<?php
namespace iTopApi {

    /**
     * Class ITopClient
     * @package iTopApi
     */
    class ITopClient
    {
        /**
         * @var string Endpoint for iTopApi
         */
        private $endpoint;
        /**
         * @var string Username
         */
        private $user;
        /**
         * @var string Password
         */
        private $password;
        /**
         * @var bool Perform ssl checks
         */
        private $certificateCheck = true;
        /**
         * @var array Array of custom curl options
         */
        private $curlOptions = array();

        /**
         * @var bool use environement proxy or not
         */
        private $proxyEnv = array();

        /**
         * @param string $endpoint
         * @param string $user
         * @param string $password
         * @param string $version  (Default:1.0)
         */
        public function __construct($endpoint, $user, $password, $version = '1.0', $proxyEnv = false)
        {

            $this->endpoint = $endpoint;
            $this->user = $user;
            $this->password = $password;
            $this->version = $version;
            $this->proxyEnv = $proxyEnv;
        }

        /**
         * @param int   $option One of the CURLOPT_* constant
         * @param mixed $value  Setting
         * @return $this
         */
        public function setCurlOption($option, $value)
        {
            $this->curlOptions[$option] = $value;
            return $this;
        }

        /**
         * @param $bool Check for SSL issues
         * @return $this
         */
        public function setCertificateCheck($bool)
        {
            $this->certificateCheck = $bool;
            return $this;
        }

        /**
         * Send a JSON request to the API
         * @param array $data
         * @return mixed
         * @throws \Exception
         */
        public function sendRequest(array $data)
        {
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
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, count($params));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

            if ($this->proxyEnv == false) {
                curl_setopt($curl, CURLOPT_PROXY, null);
            }

            if ($this->certificateCheck) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
            } else {
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            }

            foreach ($this->curlOptions as $option => $value) {
                curl_setopt($curl, $option, $value);
            }

            $jsonResponse = curl_exec($curl);
            
            if ($errno = curl_errno($curl)) {
                $error_message = curl_strerror($errno);
                throw new \Exception("cURL error ({$errno}):\n {$error_message}");
            }
            
            $response = json_decode($jsonResponse, true);
            
            curl_close($curl);

            if (!is_array($response)) {
                throw new \Exception('Invalid response from server : '.$jsonResponse);
            }

            if ($response['code'] != 0) {
                throw new \Exception('iTop Exception : '.$response['message']);
            }

            return $response;
        }

        /**
         * Execute an API operation
         * @param string $operation
         * @param array  $data
         * @return mixed
         * @throws \Exception
         */
        public function operation($operation, array $data = array())
        {
            $data['operation'] = $operation;
            return $this->sendRequest($data);
        }

        /**
         * Execute an API core/get
         * @param string $class Class to look for
         * @param mixed  $query Param to be passed as key
         * @return mixed
         */
        public function coreGet($class, $query = null)
        {
            if (is_null($query)) {
                $query = 'SELECT '.$class;
            }
            return $this->operation(
                'core/get',
                array(
                'class' => $class,
                'key' => $query
                )
            );
        }

        /**
         * Get relations
         * @param string $class    Class to look for
         * @param mixed  $key      Param to be passed as key
         * @param string $relation Related class to look for
         * @param int    $depth    Depth
         * @return mixed
         */
        public function coreGetRelated($class, $key, $relation, $depth = 1)
        {
            return $this->operation(
                'core/get_related',
                array(
                'class' => $class,
                'key' => $key,
                'relation' => $relation,
                'depth' => $depth
                )
            );
        }

        /**
         * Delete one/multiple objects
         * @param string $class   Class to delete
         * @param mixed  $query   Param to be passed as key
         * @param string $comment Comment to record in iTop
         * @return mixed
         */
        public function coreDelete($class, $query, $comment = null)
        {
            if (is_null($comment)) {
                $comment = 'iTopAPI library delete '.$class.' from '.$this->user;
            }
            return $this->operation(
                'core/delete',
                array(
                'class' => $class,
                'key' => $query,
                'comment' => $comment
                )
            );
        }

        // soon to be deprecated :
        public function coreGetCustomSelect($class, $query)
        {
            trigger_error("Deprecated function called: iTopApi::coreGetCustomSelect", E_USER_DEPRECATED);
            return $this->coreGet($class, $query);
        }

        /**
         * Update one/multiple objects in iTop
         * @param string $class   Class to update
         * @param mixed  $query   Param to be passed as key
         * @param array  $data    Data to update
         * @param string $comment Comment to record in iTop
         * @return mixed
         */
        public function coreUpdate($class, $query, $data, $comment = null)
        {
            if (is_null($comment)) {
                $comment = 'iTopAPI library update '.$class.' from '.$this->user;
            }

            return $this->operation(
                'core/update',
                array(
                'class' => $class,
                'key' => $query,
                'fields' => $data,
                'comment' => $comment
                )
            );
        }

        /**
         * Creates an object in iTop
         * @param string $class   Class to create
         * @param array  $data    Data for the objects
         * @param string $comment Record a comment in iTop
         * @return mixed
         */
        public function coreCreate($class, $data, $comment = null)
        {
            if (is_null($comment)) {
                $comment = 'iTopAPI library create '.$class.' from '.$this->user;
            }

            return $this->operation(
                'core/create',
                array(
                'class' => $class,
                'fields' => $data,
                'comment' => $comment
                )
            );
        }

        /**
         * Dispense a new ITopObject
         * @param string $class Class to get a new object for
         * @return ITopObject
         */
        public function getNewObject($class)
        {
            return new ITopObject($class, null, array(), $this);
        }

        /**
         * Get objects from iTopApi
         * @param string $class Class to get objects for
         * @param mixed  $query Param to be passed as key
         * @return array|ITopObject
         */
        public function getObjects($class, $query = null)
        {
            $objects = array();
            $results = $this->coreGet($class, $query);
            if (count($results['objects']) < 1) {
                return array();
            }


            foreach ($results['objects'] as $key => $object) {
                list($class,$key) = explode('::', $key);
                $objects[] = new ITopObject($class, $key, $object['fields'], $this);
            }
            return $objects;
        }
    }
}
