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
}