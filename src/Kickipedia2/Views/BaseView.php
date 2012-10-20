<?php

namespace Kickipedia2\Views;

use MongoAppKit\Config,
    MongoAppKit\View;

use Symfony\Component\HttpFoundation\Request;

use Silex\Application;

class BaseView extends View {
    protected $_types = null;
    protected $_documentType = null;
    protected $_navigation = null;

    public function __construct(Application $app, $id = null) {
        parent::__construct($app, $id);

        $this->setAppName($this->_config->getProperty('AppName'));
    }

    public function redirect($app, $url, $params = null) {
        if(!empty($params) && is_array($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $app->redirect($url);
    }

    public function getTypes() {
        if($this->_types === null) {
            $rawTypes = $this->_config->getProperty('EntryTypes');
            $types = array();

            foreach($rawTypes as $id => $name) {
                $type = array(
                    'id' => $id,
                    'name' => $name,
                    'url' => $this->_createUrl(array('type' => $id))
                );

                if($this->_documentType == $id) {
                    $type['active'] = true;
                } else {
                    $type['active'] = false;
                }

                $types[] = $type;
            }

            $this->_types = $types;
        }

        return $this->_types;
    }
    
    public function getNavigation() {
        if($this->_navigation === null) {
            $rawNav = $this->_config->getProperty('NavItems');
            $nav = array();

            foreach($rawNav as $item) {
                $newItem = $item;

                if($this->_request->getPathInfo() == $newItem['route']) {
                    $newItem['active'] = true;
                } else {
                    $newItem['active'] = false;
                }

                $nav[] = $newItem;
            }

            $this->_navigation = $nav;
        }

        return $this->_navigation;
    }

    public function getSession() {
        return $this->_app['session'];
    }
}