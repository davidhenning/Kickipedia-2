<?php

namespace MongoAppKit;

abstract class View extends Base {
    
    protected $_sTemplateName = null;
    protected $_aTemplateData = array();
    protected $_sId = null;
    protected $_iPerPage = 50;
    protected $_iPage = 1;
    protected $_sPaginationBaseUrl = '';

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

    public function setPerPage($iPerPage) {
        $this->_iPerPage = $iPerPage;
    }

    public function setPage($iPage) {
        $this->_iPage = $iPage;
    }

    protected function _getPagination($iTotalRecords) {
        $aPages = array();

        $iPages = ceil($iTotalRecords / $this->_iPerPage);

        if($iPages > 0) {
            for($i = 1; $i <= $iPages; $i++) {


                $aPage = array(
                    'nr' => $i,
                    'url' => $this->_createPageUrl($i)
                );

                if($i === $this->_iPage) {
                    $aPage['active'] = true;
                }

                $aPages[] = $aPage;
            }
        }

        return $aPages;
    }

    protected function _createPageUrl($iPage) {
        $sUrl = "{$this->_sPaginationBaseUrl}/";
        
        if(!empty($this->_sPaginationAddiotionalUrl)) {
            $sUrl .= "{$this->_sPaginationAddiotionalUrl}/";
        }

        return $sUrl . $iPage;
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