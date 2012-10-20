<?php

namespace Kickipedia2\Views;

use MongoAppKit\View,
	Kickipedia2\Models\EntryDocument;

class EntryView extends BaseView {
    
    protected $_appName = 'Kickipedia2';
    protected $_templateName = 'entry.twig';

    public function render($app) {
        $entry = new EntryDocument($app);
        $entry->load($this->getId());
        $this->_aTemplateData['entry'] = $entry;

        return parent::render($app);
    }

    protected function _renderJSON($app) {
        $entry = new EntryDocument($app);
        $entry->load($this->getId());

        $output = array(
            'status' => 200,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => 'GET',
                'url' => $app['request']->getPathInfo()
            ),
            'response' => array(        
                'total' => 1,
                'found' => 1                    
            )
        );

        $output['response']['documents'] = array();
		$output['response']['documents'][] = $entry->getProperties();

        return $app->json($output);
    }

    protected function _renderXML($app) {
        $entry = new EntryDocument($app);
        $entry->load($this->getId());
        $kickipedia = new \SimpleXMLElement('<kickipedia></kickipedia>');
        
        $kickipedia->addChild('status', 200);
        $kickipedia->addChild('date', date('Y-m-d H:i:s'));
        
        $request = $kickipedia->addChild('request');
        $request->addChild('method', 'GET');
        $request->addChild('url', $app['request']->getPathInfo());

        $response = $kickipedia->addChild('response');
        $response->addChild('total', 1);
        $response->addChild('found', 1);

        $documents = $response->addChild('documents');


        $document = $documents->addChild('document');
        foreach($entry->getProperties() as $key => $value) {
            $document->addChild($key, $entry->getProperty($key));
        }

        echo $kickipedia->asXML();
    }
}