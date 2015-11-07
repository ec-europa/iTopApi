<?php
namespace iTopApi {
    /**
     * Util class to acces iTop object oriented
     * Class iTopObject
     * @package iTopApi
     */
    class iTopObject
    {
        /**
         * @var Was the oject updated or not
         */
        private $dirty_ = false;

        /**
         * @var This is not an ssh one
         */
        private $key_;

        /**
         * @var Current class
         */
        private $class_;

        /**
         * @var iTopClient Current iTopClient
         */
        private $iTopClient_;

        /**
         * @var Data about the current object
         */
        private $data_ = array();

        /**
         * Instantiate a new iTopObject
         * @param $class
         * @param $data
         */
        function __construct($class,$key,$data,iTopClient $iTopClient) {
            $this->class_ = $class;
            $this->data_ = $data;
            $this->key_ = $key;
            $this->iTopClient_ = $iTopClient;
        }

        /**
         * @param null $comment
         * @return mixed
         */
        function save($comment=null) {
            $this->dirty_ = false;
            if(!is_null($this->key_))
                return $this->iTopClient_->coreUpdate($this->class_,$this->key_,$this->data_,$comment);
            return $this->iTopClient_->coreCreate($this->class_,$this->data_,$comment);
        }

        /**
         * @param null $comment
         * @return mixed
         */
        function delete($comment=null) {
            if(is_null($this->key_))
                throw new \Exception('Trying to delete a new object');
            return $this->iTopClient_->coreDelete($this->class_,$this->key_,$comment);
        }

        /**
         * @return Is dirty or not
         */
        function isDirty() {
            return $this->dirty_;
        }

        /**
         * @param $variable
         * @return mixed
         */
        function __get($variable) {
            return $this->data_[$variable];
        }

        /**
         * @param $variable
         * @param $value
         */
        function __set($variable,$value) {
            $this->dirty_ = true;
            $this->data_[$variable] = $value;
        }
    }
}