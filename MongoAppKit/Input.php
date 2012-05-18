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
        $this->_sRequestMethod = $_SERVER['REQUEST_METHOD'];
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
            parse_str(file_get_contents('php://input'), $aPutValues);

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
        $data = strip_tags($data);
        $data = htmlspecialchars($data);

        return $data;
    }

    public function getGetData($sName) {
        return (isset($_GET[$sName])) ? $this->sanitize($_GET[$sName]) : null;
    }

    public function getPostData($sName) {
        return (isset($_POST[$sName])) ? $this->sanitize($_POST[$sName]) : null;
    }

    public function getPutData($sName) {
        return (isset($this->_aPutValues[$sName])) ? $this->sanitize($this->_aPutValues[$sName]) : null;
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