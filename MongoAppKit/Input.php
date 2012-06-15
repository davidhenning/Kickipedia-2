<?php

namespace MongoAppKit;

class Input {

    /**
     * Config object
     * @var Config
     */

    private static $_oInstance = null;

    protected $_sRequestMethod = 'GET';
    protected $_aPutValues = null;
    protected $_sInputStream = null;

    /**
     * Return instance of class Config
     *
     * @return Config
     */

    public static function getInstance() {
        if(self::$_oInstance === null) {
            self::$_oInstance = new Input();
        }

        return self::$_oInstance;
    }

    public function __construct() {
        $this->_sInputStream = file_get_contents("php://input");
        $this->_sRequestMethod = (isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : 'CONSOLE';
    }

    /**
     * Prohibit cloning of the class object (Singleton pattern)
     */

    public function __clone() {
        return null;
    }

    protected function _getPutValues() {
        if($this->_aPutValues === null) {
            $aPutValues = array();
            
            if($_SERVER['CONTENT_TYPE'] === 'application/json') {
                $aPutValues = json_decode($this->_sInputStream, true);

                if(!array_key_exists('data', $aPutValues)) {
                    throw new \UnexpectedValueException("Invalid JSON: can not find attribute 'data'.", 400);
                }

                if(empty($aPutValues['data'])) {
                    throw new \UnexpectedValueException("Invalid JSON: attribute 'data' is empty.", 400);
                }

            } else {
                parse_str($this->_sInputStream, $aPutValues);

                if(!array_key_exists('data', $aPutValues)) {
                    throw new \UnexpectedValueException("Invalid POST request: can not find key 'data'.", 400);
                }

                if(empty($aPutValues['data'])) {
                    throw new \UnexpectedValueException("Invalid POST request: array 'data' is empty.", 400);
                }
            }

            $this->_aPutValues = $aPutValues;
        }

        return $this->_aPutValues;
    }

    public function getRequestMethod() {
        return $this->_sRequestMethod;
    }

    public function sanitize($data) {
        if(is_array($data)) {
            $aSanitizedData = array();
            foreach($data as $key => $value) {
                $aSanitizedData[$key] = $this->sanitize($value);
            }

            return $aSanitizedData;
        }

        $data = trim($data);
        $data = rawurldecode($data);     
        $data = htmlspecialchars($data);
        $data = strip_tags($data);

        return $data;
    }

    public function getGetData($sName) {
        return (isset($_GET[$sName])) ? $this->sanitize($_GET[$sName]) : null;
    }

    public function getPostData($sName) {
        return (isset($_POST[$sName])) ? $this->sanitize($_POST[$sName]) : null;
    }

    public function getPutData($sName) {
        $aPutValues = $this->_getPutValues();
        return (isset($aPutValues[$sName])) ? $this->sanitize($aPutValues[$sName]) : null;
    }

    public function getData($sName) {
        if($this->_sRequestMethod === 'POST') {
            return $this->getPostData($sName);
        }

        if($this->_sRequestMethod === 'PUT') {
            return $this->getPutData($sName);
        }
    }
}