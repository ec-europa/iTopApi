<?php
class iTopClientTest extends PHPUnit_Framework_TestCase
{
    public function testCanListOperationsFromDemo() {
        $iTopClient = new iTopApi\iTopClient('https://demo.combodo.com/simple/','admin','admin');
        $iTopClient->setCertificateCheck(false)->setCurlOption(CURLOPT_SSLVERSION,CURL_SSLVERSION_SSLv3);
        $response = $iTopClient->sendRequest(array('operation' =>'list_operations'));
        $this->assertArrayHasKey('operations',$response,'Operation list was unsucessful !');
    }

    public function testGetWebserverFromDemo() {
        $iTopClient = new iTopApi\iTopClient('https://demo.combodo.com/simple/','admin','admin');
        $iTopClient->setCertificateCheck(false);
        $response = $iTopClient->coreGet('WebServer');
        $this->assertArrayHasKey('objects',$response,'Response missing objects');
    }



}
