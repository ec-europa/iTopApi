<?php
class iTopObjectTest extends PHPUnit_Framework_TestCase
{
    static $iTopInstance;

    static function getItopInstance() {
        if(is_null(self::$iTopInstance)) {
            self::$iTopInstance =
                new iTopApi\iTopClient('https://demo.combodo.com/simple/','admin','admin');
            // switch SSLv3 if available ... ( broken curl )
            if(defined('CURL_SSLVERSION_SSLv3'))
                self::$iTopInstance->setCertificateCheck(false)
                ->setCurlOption(CURLOPT_SSLVERSION,CURL_SSLVERSION_SSLv3);
        }
        return self::$iTopInstance;
    }

    public function testSetGet(){
        //get Webservers from demo :
        $webserver = $this->getOneWebserverFromDemo();
        $name = $webserver->name;
        $this->assertEquals($name,$webserver->name);
        $newName = 'TestingNameForAWebserver';
        $webserver->name = $newName;
        $this->assertEquals($newName,$webserver->name);
    }

    public function testDirtyBehavior(){
        //get Webservers from demo :
        $webserver = $this->getOneWebserverFromDemo();
        $this->assertFalse($webserver->isDirty());
        $webserver->name = 'TestingWebserver';
        $this->assertTrue($webserver->isDirty());

    }

    public function testSave(){
        //get Webservers from demo :
        $myNewName = 'TestingForSave'.time();
        $webserver = $this->getOneWebserverFromDemo();
        $webserver->name = $myNewName;
        $webserver->save();
        $webserver = $this->getOneWebserverFromDemo(array('name'=>$myNewName));
        $this->assertEquals($myNewName,$webserver->name);

    }

    public function testCreate(){

        $webserver = self::getItopInstance()->getNewObject('WebServer');
        $myNewName = 'TestingForCreate'.time();
        $webserver->name = $myNewName;
        $webserver->system_id = 1;
        $webserver->org_id = 1;
        $webserver->save();
        $webserver = $this->getOneWebserverFromDemo(array('name'=>$myNewName));
        $this->assertEquals($myNewName,$webserver->name);

    }

    public function testDelete(){

        $webserver = self::getItopInstance()->getNewObject('WebServer');
        $myNewName = 'TestingForCreate'.time();
        $webserver->name = $myNewName;
        $webserver->system_id = 1;
        $webserver->org_id = 1;
        $webserver->save();
        $webserver = $this->getOneWebserverFromDemo(array('name'=>$myNewName));
        $this->assertEquals($myNewName,$webserver->name);
        $webserver->delete();
        $result = self::getItopInstance()->coreGet('WebServer',array('name'=>$myNewName));
        $this->assertArrayHasKey('objects',$result);
        $this->assertNull($result['objects']);
    }

    public function getWebserversFromDemo($query=null) {
       return self::getItopInstance()->getObjects('WebServer',$query);
    }

    public function getOneWebserverFromDemo($query=null) {
        $webservers = $this->getWebserversFromDemo($query);
        return array_pop($webservers);
    }
}
