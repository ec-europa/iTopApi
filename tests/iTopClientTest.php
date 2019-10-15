<?php
class iTopClientTest extends PHPUnit_Framework_TestCase
{

    static $iTopInstance;

    static function getItopInstance() {
        if(is_null(self::$iTopInstance)) {
            $itopURL = (getenv ("ITOP_URL") !== FALSE )? getenv ("ITOP_URL") : "http://127.0.0.1:80";
            self::$iTopInstance =
                new iTopApi\ITopClient($itopURL,'admin','admin');
            // switch SSLv3 if available ... ( broken curl )
            if(defined('CURL_SSLVERSION_SSLv3'))
                self::$iTopInstance->setCertificateCheck(false)
                    ->setCurlOption(CURLOPT_SSLVERSION,CURL_SSLVERSION_SSLv3);
        }
        return self::$iTopInstance;
    }


    public function testCanListOperationsFromDemo() {

        $response = self::getItopInstance()->sendRequest(array('operation' =>'list_operations'));
        $this->assertArrayHasKey('operations',$response,'Operation list was unsucessful !');
    }

    public function testGetWebserverFromDemo() {
        $response = self::getItopInstance()->coreGet('WebServer');
        $this->assertArrayHasKey('objects',$response,'Response missing objects');
    }


}
