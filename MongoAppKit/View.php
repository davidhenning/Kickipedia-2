<?php

namespace MongoAppKit;

abstract class View {
    
    protected $_sTemplateName = null;
    protected $_aTemplateData = array();
    protected $_sId = null;

    public function __construct($id = null) {
        if($id !== null) {
            $this->setId($id);
        }
    }

    public function getId() {
        return $this->_sId;
    }

    public function setId($id) {
        $this->_sId = $id;
    }

    public function render() {
        $loader = new \Twig_Loader_Filesystem(getBasePath() .'\Kickipedia2\Templates');
        $twig = new \Twig_Environment($loader, array(
          'cache' => getBasePath() .'/tmp',
          'auto_reload' => true,
          'debug' => true
        ));

        echo $twig->render($this->_sTemplateName, $this->_aTemplateData);
    }
}