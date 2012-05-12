<?php

/**
 * Class DocumentList
 *
 * Collects a list of documents
 * 
 * @author David Henning <madcat.me@gmail.com>
 * 
 * @package MongoAppKit
 */

namespace MongoAppKit;

abstract class DocumentList extends IterateableList {

    /**
     * MongoDB object
     * @var MongoDB
     */

    protected $_oDatabase = null;

    /**
     * Collection name
     * @var string
     */

    protected $_sCollectionName = null;

    /**
     * MongoCollection object
     * @var MongoCollection
     */

    protected $_oCollection = null;

    /**
     * Name of the document class with the complete namespace
     * @var string
     */

    protected $_sDocumentClass = null;

    /**
     * Count of selected documents
     * @var integer
     */

    protected $_iDocuments = 0;

    /**
     * Total count of documents of the selected MongoDB collection
     * @var integer
     */

    protected $_iTotalDocuments = 0;

    /**
     * Document field for custom sorting
     * @var string
     */

    protected $_sCustomSortField = null;

    /**
     * Direction of custom sorting (asc or desc)
     * @var string
     */

    protected $_sCustomSortOrder = null;

    /**
     * Gets MongoDB object and selects MongoDB collection
     */

    public function __construct() {
        $this->_oDatabase = $this->getStorage()->getDatabase();
        
        if($this->_sCollectionName !== null) {
            $this->_oCollection = $this->_oDatabase->selectCollection($this->_sCollectionName);
        }
    }

    /**
     * Loads all documents of selected MongoDB collection
     */

    public function findAll() {       
        $oCursor = $this->_getDefaultCursor();
        $this->_setPropertiesFromCursor($oCursor);
    }

    /**
     * Loads documents of selected MongoDB collection by given page
     *
     * @param integer $iPage
     * @param integer $iPerPage
     * @param MongoCursor $oCursorOverride
     */

    public function findByPage($iPage = 1, $iPerPage = 50, $oCursorOverride = null) {
        // set default cursor if no override is available
        $oCursor = ($oCursorOverride !== null && $oCursorOverride instanceof \MongoCursor) ? $oCursorOverride : $this->_getDefaultCursor();
        // set limit for page
        $oCursor->limit($iPerPage);

        $iSkip = ($iPage - 1) * $iPerPage;

        if($iSkip > 0) {
            $oCursor->skip($iSkip);
        }

        $this->_setDocumentsFromCursor($oCursor);       
    }

    /**
     * Returns total count of documents of selected MongoDB collection
     *
     * @return integer
     */

    public function getTotalDocuments() {
        return $this->_iTotalDocuments;
    }

    /**
     * Returns MongoCursor object with given fields for given where clause
     *
     * @param array $aWhere
     * @param arary $aFields
     * @return MongoCursor
     */

    protected function _getDefaultCursor($aWhere = null, $aFields = null) {
        // no where clause if none given
        if($aWhere === null) {
            $aWhere = array();
        }

        // select all fields if none given
        if($aFields === null) {
            $aFields = array();
        }

        // get documents
        $oCursor = $this->_oCollection->find($aWhere, $aFields);
        $aSorting = array();

        if($this->_sCustomSortField !== null) {
            $iSortOrder = 1;

            // set sorting direction
            if($this->_sCustomSortField !== null) {
                if($this->_sCustomSortField === 'asc') {
                    $iSortOrder = 1;
                } elseif($this->_sCustomSortField === 'asc') {
                    $iSortOrder = -1;
                } else {
                    $iSortOrder = 1;
                }
            }   

            // order documents by custom sorting field
            $aSorting = array($this->_sCustomSortField => $iSortOrder);
        } else {
            // default sorting by creation date
            $aSorting = array('createdOn' => -1);
        }

        // sort
        $oCursor->sort($aSorting);

        return $oCursor;     
    }

    /**
     * Creates instances of the given document class for each document in the given MongoCursor object 
     *
     * @param MongoCursor $oCursor
     */

    protected function _setDocumentsFromCursor(\MongoCursor $oCursor) {
        $aData = array();
        
        // check for valid cursor
        if($oCursor === null) {
            throw new \Exception("No cursor object set");
        }

        // iterate cursor
        foreach($oCursor as $oLine) {
            // create new document object with given class and store the current document
            $oDocument = new $this->_sDocumentClass();
            $oDocument->updateProperties($oLine);
            $aData[] = $oDocument;
        }

        $this->_iDocuments = $oCursor->count(true);
        $this->_iTotalDocuments = $oCursor->count();
        $this->_aProperties = $aData;
    }
}