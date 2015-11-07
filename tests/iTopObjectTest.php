<?php
class iTopObjectTest extends PHPUnit_Framework_TestCase
{



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

        $iTopClient = new iTopApi\iTopClient('https://demo.combodo.com/simple/','admin','admin');
        $webserver = $iTopClient->getNewObject('WebServer');
        $myNewName = 'TestingForCreate'.time();
        $webserver->name = $myNewName;
        $webserver->system_id = 1;
        $webserver->org_id = 1;
        $webserver->save();
        $webserver = $this->getOneWebserverFromDemo(array('name'=>$myNewName));
        $this->assertEquals($myNewName,$webserver->name);

    }

    public function testDelete(){
        $iTopClient = new iTopApi\iTopClient('https://demo.combodo.com/simple/','admin','admin');
        $webserver = $iTopClient->getNewObject('WebServer');
        $myNewName = 'TestingForCreate'.time();
        $webserver->name = $myNewName;
        $webserver->system_id = 1;
        $webserver->org_id = 1;
        $webserver->save();
        $webserver = $this->getOneWebserverFromDemo(array('name'=>$myNewName));
        $this->assertEquals($myNewName,$webserver->name);
        $webserver->delete();
        $result = $iTopClient->coreGet('WebServer',array('name'=>$myNewName));
        $this->assertArrayHasKey('objects',$result);
        $this->assertNull($result['objects']);
    }

    public function getWebserversFromDemo($query=null) {
        $iTopClient = new iTopApi\iTopClient('https://demo.combodo.com/simple/','admin','admin');
        return $iTopClient->getObjects('WebServer',$query);
    }

    public function getOneWebserverFromDemo($query=null) {
        $webservers = $this->getWebserversFromDemo($query);
        return array_pop($webservers);
    }
}