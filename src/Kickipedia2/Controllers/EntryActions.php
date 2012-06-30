<?php

namespace Kickipedia2\Controllers;

use Kickipedia2\Views\EntryListView,
    Kickipedia2\Views\EntryView,
    Kickipedia2\Views\EntryEditView,
    Kickipedia2\Views\EntryNewView;

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;

class EntryActions extends BaseActions {

    protected function _initRoutes(Application $oApp) {
        $oActions = $this;

        /* GET actions */

        $oApp->get('/entry/list.{format}', function($format) use($oApp, $oActions) {
            return $oActions->showList($oApp, $format);
        })->bind('list_get');

        $oApp->get('/entry/{id}.{format}', function($id, $format) use($oApp, $oActions) {
            return $oActions->showEntry($oApp, $id, $format);
        })->bind('view_get');

        $oApp->get('/entry/new', function() use ($oApp, $oActions) {
            return $oActions->newEntry($oApp);
        })->bind('new_get');

        $oApp->get('/entry/{id}/edit', function($id) use ($oApp, $oActions) {
            return $oActions->editEntry($oApp, $id);
        })->bind('edit_get');

        /* PUT actions */

        $oApp->put('/entry', function() use ($oApp, $oActions) {
            return $oActions->updateEntry($oApp, null);
        })->bind('insert_put');

        $oApp->put('/entry/{id}', function($id) use ($oApp, $oActions) {
            return $oActions->updateEntry($oApp, $id);
        })->bind('update_put');

        /* DELETE actions */

        $oApp->delete('/entry/{id}', function($id) use ($oApp, $oActions) {
            return $oActions->deleteEntry($oApp, $id);
        })->bind('delete_delete');

        /* POST actions */

        $oApp->post('/entry/insert', function() use ($oApp, $oActions) {
            return $oActions->updateEntry($oApp);
        })->bind('insert_post');        

        $oApp->post('/entry/{id}/update', function($id) use ($oApp, $oActions) {
            return $oActions->updateEntry($oApp, $id);
        })->bind('update_post');

        $oApp->post('/entry/{id}/delete', function($id) use ($oApp, $oActions) {
            return $oActions->deleteEntry($oApp, $id);
        })->bind('delete_post');
    }

    public function showList(Application $oApp, $sFormat) {
        $oConfig = $oApp['config'];
        $oRequest = $oApp['request'];
        $iSkip = (int) $oConfig->sanitize($oRequest->query->get('skip'));
        $iLimit = (int) $oConfig->sanitize($oRequest->query->get('limit'));
        $iType = (int) $oConfig->sanitize($oRequest->query->get('type'));
        $sTerm = $oConfig->sanitize($oRequest->query->get('term'));
        $sSort = $oConfig->sanitize($oRequest->query->get('sort'));
        $sDirection = $oConfig->sanitize($oRequest->query->get('direction'));
        $sFormat = $oConfig->sanitize($sFormat);

        $oView = new EntryListView($oApp);
        
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

        return $oView->render($oApp);    
    }

    public function showEntry(Application $oApp, $sId, $sFormat) {
        $oConfig = $oApp['config'];
        $sId = $oConfig->sanitize($sId);
        $sFormat = $oConfig->sanitize($sFormat);
        
        $oView = new EntryView($oApp, $sId);

        if(!empty($sFormat)) {
            $oView->setOutputFormat($sFormat);
        }

        return $oView->render($oApp);
    }

    public function newEntry(Application $oApp) {
        $oView = new EntryNewView($oApp);
        
        return $oView->render($oApp); 
    }

    public function editEntry(Application $oApp, $sId) {
        $oConfig = $oApp['config'];
        $sId = $oConfig->sanitize($sId);

        $oView = new EntryEditView($oApp, $sId);
        return $oView->render($oApp);        
    }

    public function updateEntry(Application $oApp, $sId) {
        $oConfig = $oApp['config'];
        $oRequest = $oApp['request'];
        $sId = $oConfig->sanitize($sId);       
        $aEntryData = $oConfig->sanitize($oRequest->request->get('data'));

        if(empty($aEntryData)) {
            throw new \InvalidArgumentException("The attribute 'data' is missing or it is empty. Please check your request data.", 400);
        }
        
        $oView = new EntryEditView($oApp, $sId);
        $oDocument = $oView->getDocument();
        $oDocument->updateProperties($aEntryData);
        $oDocument->save();

        try {
            $iTypeId = $oView->getTypeId();
        } catch(\Exception $e) {
            $iTypeId = null;
        }

        if($oRequest->headers->get('content_type') === 'application/json' || $oRequest->getMethod() === 'PUT') {
            return $oView->renderJsonUpdateResponse($oApp);
        } else {
            return $oView->redirect($oApp, '/entry/list.html', array('type' => $iTypeId));
        }          
    }

    public function deleteEntry(Application $oApp, $sId) {
        $oConfig = $oApp['config'];
        $oRequest = $oApp['request'];
        $sId =  $oConfig->sanitize($sId);
        
        $oView = new EntryEditView($oApp, $sId);
        $oView->getDocument()->delete();

        if($oRequest->headers->get('content_type') === 'application/json' || $oRequest->getMethod() === 'DELETE') {
            return $oView->renderJsonDeleteResponse($oApp);
        } else {            
            return $oView->redirect($oApp, '/entry/list.html', array('type' => $iTypeId));
        }
    }
}