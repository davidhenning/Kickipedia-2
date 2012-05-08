<?php

namespace MongoAppKit;

abstract class View extends Base {
    
    protected $_sAppName = '';
    protected $_sTemplateName = null;
    protected $_aTemplateData = array();
    protected $_sId = null;
    protected $_iPerPage = 50;
    protected $_iPage = 1;
    protected $_sPaginationBaseUrl = '';
    protected $_sPaginationAdditionalUrl = '';

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
        $iPages = ceil($iTotalRecords / $this->_iPerPage);
        $aPages = array(
            'pages' => array(),
            'currentPage' => $this->_iPage,
            'totalPages' => $iPages  
        );

        if($this->_iPage > 1) {
            $aPages['prevPageUrl'] = $this->_createPageUrl($this->_iPage - 1);
            $aPages['firstPageUrl'] = $this->_createPageUrl(1);
        }

        if($this->_iPage < $iPages) {
            $aPages['nextPageUrl'] = $this->_createPageUrl($this->_iPage + 1);
            $aPages['lastPageUrl'] = $this->_createPageUrl($iPages);
        }

        if($iPages > 0) {
            for($i = 1; $i <= $iPages; $i++) {
                $aPage = array(
                    'nr' => $i,
                    'url' => $this->_createPageUrl($i)
                );

                if($i === $this->_iPage) {
                    $aPage['active'] = true;
                }

                $aPages['pages'][] = $aPage;
            }
        }

        return $aPages;
    }

    protected function _createPageUrl($iPage) {
        $sUrl = "{$this->_sPaginationBaseUrl}/";
        
        if(!empty($this->_sPaginationAdditionalUrl)) {
            $sUrl .= "{$this->_sPaginationAdditionalUrl}/";
        }

        return $sUrl . $iPage;
    }

    public function render() {
        $loader = new \Twig_Loader_Filesystem(getBasePath() ."\\{$this->_sAppName}\Templates");
        $twig = new \Twig_Environment($loader, array(
          'cache' => getBasePath() .'/tmp',
          'auto_reload' => $this->getConfig()->getProperty('TemplateDebugMode')
        ));

        echo $twig->render($this->_sTemplateName, $this->_aTemplateData);
    }
}