<?php

namespace Kickipedia2\Views;

use MongoAppKit\Config,
    MongoAppKit\View;

use Symfony\Component\HttpFoundation\Request;

use Silex\Application;

class BaseView extends View {
    protected $_aTypes = null;
    protected $_iDocumentType = null;
    protected $_aNavigation = null;

    public function __construct(Application $oApp, $sId = null) {
        parent::__construct($oApp, $sId);

        $this->_setAppName($this->_oConfig->getProperty('AppName'));
    }

    public function redirect($oApp, $sUrl, $aParams = null) {
        if(!empty($aParams) && is_array($aParams)) {
            $sUrl .= '?' . http_build_query($aParams);
        }

        return $oApp->redirect($sUrl);
    }

    public function getTypes() {
        if($this->_aTypes === null) {
            $aRawTypes = $this->_oConfig->getProperty('EntryTypes');
            $aTypes = array();

            foreach($aRawTypes as $sId => $sName) {
                $aType = array(
                    'id' => $sId,
                    'name' => $sName,
                    'url' => $this->_createUrl(array('type' => $sId))
                );

                if($this->_iDocumentType == $sId) {
                    $aType['active'] = true;
                } else {
                    $aType['active'] = false;
                }

                $aTypes[] = $aType;
            }

            $this->_aTypes = $aTypes;          
        }

        return $this->_aTypes;
    }
    
    public function getNavigation() {
        if($this->_aNavigation === null) {
            $aRawNav = $this->_oConfig->getProperty('NavItems');
            $aNav = array();

            foreach($aRawNav as $aItem) {
                $aNewItem = $aItem;

                if($this->_oRequest->getPathInfo() == $aNewItem['route']) {
                    $aNewItem['active'] = true;
                } else {
                    $aNewItem['active'] = false;
                }

                $aNav[] = $aNewItem;
            }

            $this->_aNavigation = $aNav;
        }

        return $this->_aNavigation;        
    }

    public function getSession() {
        return $_SESSION;
    }
}