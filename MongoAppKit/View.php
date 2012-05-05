<?php

namespace MongoAppKit;

abstract class View extends Base {
    
    protected $_sTemplateName = null;
    protected $_aTemplateData = array();
    protected $_sId = null;

    public function __construct($sId = null) {
        if($sId !== null) {
            $this->setId($sId);
        }
    }

    public function getId() {
        return $this->_sId;
    }

    public function setId($sId) {
        $this->_sId = $sId;
    }

    public function render() {
        $loader = new \Twig_Loader_Filesystem(getBasePath() .'\Kickipedia2\Templates');
        $twig = new \Twig_Environment($loader, array(
          'cache' => getBasePath() .'/tmp',
          'auto_reload' => $this->getConfig()->getProperty('TemplateDebugMode')
        ));

        echo $twig->render($this->_sTemplateName, $this->_aTemplateData);
    }
}