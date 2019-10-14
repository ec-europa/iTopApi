<?php
class iTopClientTest extends PHPUnit_Framework_TestCase
{

    static $iTopInstance;

    static function getItopInstance() {
        if(is_null(self::$iTopInstance)) {
            self::$iTopInstance =
                new iTopApi\ITopClient('http://itop:80','admin','admin');
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
