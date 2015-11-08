<?php
namespace iTopApi {

    /**
     * Util class to acces iTop object oriented
     * Class ITopObject
     * @package iTopApi
     */
    class ITopObject
    {
        /**
         * @var bool Was the oject updated or not
         */
        private $dirty_ = false;

        /**
         * @var int This is not an ssh one
         */
        private $key_;

        /**
         * @var string Current class
         */
        private $class_;

        /**
         * @var iTopClient Current iTopClient
         */
        private $iTopClient_;

        /**
         * @var array Data about the current object
         */
        private $data_ = array();

        /**
         * Instantiate a new ITopObject
         * @param string $class Class for the current object
         * @param array  $data  Data for the current object (content of 'fields')
         */
        public function __construct($class, $key, $data, iTopClient $iTopClient)
        {
            $this->class_ = $class;
            $this->data_ = $data;
            $this->key_ = $key;
            $this->iTopClient_ = $iTopClient;
            // If new object we're dirty :
            if (is_null($this->key_)) {
                $this->dirty_ = true;
            }
        }

        /**
         * Update/Create the current object in iTop Database
         * @param string $comment Comment to record in iTop
         * @return mixed
         */
        public function save($comment = null)
        {
            $this->dirty_ = false;
            if (!is_null($this->key_)) {
                return $this->iTopClient_->coreUpdate($this->class_, $this->key_, $this->data_, $comment);
            }
            $response = $this->iTopClient_->coreCreate($this->class_, $this->data_, $comment);
            $object = array_pop($response['objects']);
            $this->key_ = $object['key'];
            return $response;
        }

        /**
         * Delete the current object from the database and mark is a new
         * @param string $comment Comment to save in iTop
         * @return mixed
         */
        public function delete($comment = null)
        {
            if (is_null($this->key_)) {
                throw new \Exception('Trying to delete a new object');
            }
            $key = $this->key_;
            $this->dirty_ = true;
            $this->key_ = null;
            return $this->iTopClient_->coreDelete($this->class_, $key, $comment);

        }

        /**
         * @return bool Is dirty or not
         */
        public function isDirty()
        {
            return $this->dirty_;
        }

        /**
         * @param string $variable
         * @return mixed
         */
        public function __get($variable)
        {
            return $this->data_[$variable];
        }

        /**
         * @param string $variable
         * @param mixed  $value
         */
        public function __set($variable, $value)
        {
            $this->dirty_ = true;
            $this->data_[$variable] = $value;
        }
    }
}
