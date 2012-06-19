<?php

namespace Kickipedia2\Controllers;

use MongoAppKit\Base,
    MongoAppKit\Input;

use Kickipedia2\Views\EntryListView,
    Kickipedia2\Views\EntryView,
    Kickipedia2\Views\EntryEditView,
    Kickipedia2\Views\EntryNewView;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

class EntryActions extends Base {

    protected $_oApp = null;

    public function __construct($oApp) {
        $this->_oApp = $oApp;
        $actions = $this;
        
        /* GET actions */

        $oApp->get('/entry/list.{format}', function(Request $oRequest, $format) use($oApp, $actions) {
            return $actions->showList($format);
        });

        $oApp->get('/entry/{id}.{format}', function(Request $oRequest, $id, $format) use($oApp, $actions) {
            return $actions->showEntry($id, $format);
        });

        $oApp->get('/entry/new', function(Request $oRequest) use ($oApp, $actions) {
            return $actions->newEntry();
        });

        $oApp->get('/entry/{id}/edit', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->editEntry($id);
        });

        /* PUT actions */

        $oApp->put('/entry', function(Request $oRequest) use ($oApp, $actions) {
            return $actions->updateEntry();
        });

        $oApp->put('/entry/{id}', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->updateEntry($id);
        });

        /* DELETE actions */

        $oApp->delete('/entry/{id}', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->deleteEntry($id);
        });


        /* POST actions */

        $oApp->post('/entry/insert', function(Request $oRequest) use ($oApp, $actions) {
            return $actions->updateEntry();
        });        

        $oApp->post('/entry/{id}/update', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->updateEntry($id);
        });

        $oApp->post('/entry/{id}/delete', function(Request $oRequest, $id) use ($oApp, $actions) {
            return $actions->deleteEntry($id);
        });
    }

    public function showList($sFormat) {
        $oView = new EntryListView();
        $oInput = Input::getInstance();

        $iSkip = (int)$oInput->getGetData('skip');
        $iLimit = (int)$oInput->getGetData('limit');
        $iType = (int)$oInput->getGetData('type');
        $sTerm = $oInput->getGetData('term');
        $sSort = $oInput->getGetData('sort');
        $sDirection = $oInput->getGetData('direction');
        $sFormat = $oInput->sanitize($sFormat);
        
        if(!empty($sTerm)) {
            $oView->setListType('search');
            $oView->setSearchTerm($sTerm);
        }

        if($iSkip > 0) {
            $oView->setSkippedDocuments($iSkip);
        }

        if($iLimit > 0) {
            $oView->setDocumentLimit($iLimit);
        }

        if($iType > 0) {
            $oView->setDocumentType($iType);
        }

        if(!empty($sSort)) {
            $sDirection = (!empty($sDirection)) ? $sDirection : null;
            $oView->setCustomSorting($sSort, $sDirection);
        }

        if(!empty($sFormat)) {
            $oView->setOutputFormat($sFormat);
        }

        return $oView->render($this->_oApp);    
    }

    public function showEntry($id, $format) {
        $oInput = Input::getInstance();
        
        $sId = $oInput->sanitize($id);
        $sFormat = $oInput->sanitize($format);

        $oView = new EntryView($sId);

        if(!empty($sFormat)) {
            $oView->setOutputFormat($sFormat);
        }

        return $oView->render($this->_oApp);
    }

    public function newEntry() {
        $oView = new EntryNewView();
        
        return $oView->render($this->_oApp); 
    }

    public function editEntry($id) {
        $sId = Input::getInstance()->sanitize($id);

        $oView = new EntryEditView($sId);
        return $oView->render($this->_oApp);        
    }

    public function updateEntry($id) {
        $sId = $id;
        $entryData = Input::getInstance()->getData('data');

        $oView = new EntryEditView($sId);
        $oDocument = $oView->getDocument();
        $oDocument->updateProperties($entryData);
        $oDocument->save();

        try {
            $iTypeId = $oView->getTypeId();
        } catch(\Exception $e) {
            $iTypeId = null;
        }

        if((isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json')
            || Input::getInstance()->getRequestMethod() === 'PUT') {
            return $oView->renderJsonUpdateResponse($this->_oApp);
        } else {
            $oView->redirect('/entry/list.html', array('type' => $iTypeId));
        }          
    }

    public function deleteEntry($id) {
        $sId = $id;
        $oView = new EntryEditView($sId);
        $oView->getDocument()->delete();

        if((isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json')
            || Input::getInstance()->getRequestMethod() === 'DELETE') {
            return $oView->renderJsonDeleteResponse($this->_oApp);
        } else {            
            $oView->redirect('/entry/list.html', array('type' => $iTypeId));
        }
    }
}