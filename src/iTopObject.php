<?php
namespace iTopApi {
    class iTopObject
    {
        var $class;
        var $data = array();
        var $toSave = array();
        var $iTopClient;


        function __construct(\iTopApi\iTopClient $iTopClient,$class,$data=null) {
            $this->class = $class;
            $this->iTopClient = $iTopClient;
            if(!is_array($data) || !array_key_exists('fields',$data))
                return;
            $this->fields = $data['fields'];
        }

        function getValue($key) {
            if (!array_key_exists($key,$this->fields))
                return null;
            return $this->fields[$key];
        }

        function setValue($key,$value) {
            $this->toSave[] = $key;
            $this->fields[$key] = $value;
        }

        function save() {
            $this->iTopClient->coreUpdate()
        }
    }
}