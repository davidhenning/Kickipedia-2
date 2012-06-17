<?php

namespace Kickipedia2\Views;

use MongoAppKit\View,
	Kickipedia2\Models\EntryDocument;

class EntryView extends BaseView {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry.twig';

    public function render() {
        $oEntry = new EntryDocument();
        $oEntry->load($this->getId());  
        $this->_aTemplateData['entry'] = $oEntry;

        parent::render();
    }

    protected function _renderJSON() {
        $oEntry = new EntryDocument();
        $oEntry->load($this->getId());

        $aOutput = array(
            'status' => 200,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => 'GET',
                'url' => request_uri()
            ),
            'response' => array(        
                'total' => 1,
                'found' => 1                    
            )
        );

        $aOutput['response']['documents'] = array();
		$aOutput['response']['documents'][] = $oEntry->getProperties();

        echo json_encode($aOutput);
    }

    protected function _renderXML() {
        $oEntry = new EntryDocument();
        $oEntry->load($this->getId());
        $oKickipedia = new \SimpleXMLElement('<kickipedia></kickipedia>');
        
        $oKickipedia->addChild('status', 200);
        $oKickipedia->addChild('date', date('Y-m-d H:i:s'));
        
        $oRequest = $oKickipedia->addChild('request');
        $oRequest->addChild('method', 'GET');
        $oRequest->addChild('url', request_uri());

        $oResponse = $oKickipedia->addChild('response');
        $oResponse->addChild('total', 1);
        $oResponse->addChild('found', 1);

        $oDocuments = $oResponse->addChild('documents');


        $oDocument = $oDocuments->addChild('document');
        foreach($oEntry->getProperties() as $key => $value) {
            $oDocument->addChild($key, $oEntry->getProperty($key));
        }

        echo $oKickipedia->asXML();
    }
}