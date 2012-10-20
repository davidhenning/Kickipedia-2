<?php

namespace Kickipedia2\Models;

use MongoAppKit\Config,
    MongoAppKit\Documents\DocumentList;

use Silex\Application;

class UserDocumentList extends DocumentList {
    protected $_collectionName = 'user';

    public function __construct(Application $app) {
        parent::__construct($app);
        $this->setDocumentBaseObject(new UserDocument($app));
    }

    public function findByName($name) {
        $cursor = $this->_getDefaultCursor(array('name' => $name));
        $this->_setDocumentsFromCursor($cursor);
    }

    protected function _isValidUser($name) {
        $this->findByName($name);

        if($this->_foundDocuments === 1) {
            return true;
        }

        return false;
    }

    public function getUser($name) {
        if($this->_isValidUser($name) === true) {
            return $this->_properties[0];
        }

        throw new \InvalidArgumentException("Could not find user: {$name}");
    }
}