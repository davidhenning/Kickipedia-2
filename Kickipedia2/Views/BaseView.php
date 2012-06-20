<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;

class BaseView extends View {
    protected $_aTypes = null;
    protected $_iDocumentType = null;
    protected $_aNavigation = null;

    public function __construct($sId = null) {
        parent::__construct($sId);

        $this->_setAppName($this->getConfig()->getProperty('AppName'));
    }

    public function redirect($sUrl, $aParams = null) {
        if(!empty($aParams) && is_array($aParams)) {
            $sUrl .= '?' . http_build_query($aParams);
        }

        send_header('Location: '.$sUrl, true, 302);
        exit();
    }

    public function getTypes() {
        if($this->_aTypes === null) {
            $aRawTypes = $this->getConfig()->getProperty('EntryTypes');
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
            $aRawNav = $this->getConfig()->getProperty('NavItems');
            $aNav = array();
            $oRequest = $this->getRequest();

            foreach($aRawNav as $aItem) {
                $aNewItem = $aItem;

                if($oRequest->getPathInfo() == $aNewItem['route']) {
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