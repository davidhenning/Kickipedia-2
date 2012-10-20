<?php

namespace Kickipedia2\Models;

use MongoAppKit\Config,
    MongoAppKit\Documents\DocumentList;

use Silex\Application;

class EntryDocumentList extends DocumentList {
    protected $_collectionName = 'entry';

    public function __construct(Application $app) {
        parent::__construct($app);
        $this->setDocumentBaseObject(new EntryDocument($app));
    }

    public function findByType($type, $limit = 100, $skip = 0) {
        $cursor = $this->_getDefaultCursor(array('type' => $type));
        $this->find($limit, $skip, $cursor);
    }

    public function findByTerm($term, $limit = 100, $skip = 0) {
        $where = array('$or' => array(
            array('name' => new \MongoRegex("/{$term}/i")),
            array('reason' => new \MongoRegex("/{$term}/i")),
            array('comment' => new \MongoRegex("/{$term}/i"))
        ));

        $cursor = $this->_getDefaultCursor($where);
        $this->find($limit, $skip, $cursor);
    }
}