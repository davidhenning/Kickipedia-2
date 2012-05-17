<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;

class BaseView extends View {
    protected $_sAppName = 'Kickipedia2';
    protected $_aTypes = null;
    protected $_iType = 1;
    protected $_aNavigation = null;

    public function getTypes() {
        if($this->_aTypes === null) {
            $aRawTypes = $this->getConfig()->getProperty('EntryTypes');
            $aTypes = array();

            foreach($aRawTypes as $sId => $sName) {
                $aType = array(
                    'id' => $sId,
                    'name' => $sName,
                    'url' => "{$this->_sPaginationBaseUrl}/type/{$sId}"
                );

                if($this->_iType == $sId) {
                    $aType['active'] = true;
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

            foreach($aRawNav as $aItem) {
                $aNewItem = $aItem;

                if(request_uri() == $aNewItem['route']) {
                    $aNewItem['active'] = true;
                }

                $aNav[] = $aNewItem;
            }

            $this->_aNavigation = $aNav;
        }

        return $this->_aNavigation;        
    }
}