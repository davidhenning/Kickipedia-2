<?php

namespace Kickipedia2\Views;

use MongoAppKit\View,
    Kickipedia2\Models\EntryDocumentList;

class EntryListView extends BaseView {
    
    protected $_appName = 'Kickipedia2';
    protected $_templateName = 'entry_list.twig';
    protected $_baseUrl = '/entry/list';
    protected $_documents = null;
    protected $_searchTerm = null;
    protected $_listType = 'list';
    protected $_customSorting = array();
    protected $_customSortOrder = null;
    protected $_possibleParams = array(
        array(
            'property' => '_iSkippedDocuments',
            'param' => 'skip'
        ),
        array(
            'property' => '_iDocumentLimit',
            'param' => 'limit'
        ),
        array(
            'property' => '_iDocumentType',
            'param' => 'type'
        ),
        array(
            'property' => '_sSeachTerm',
            'param' => 'term'
        ),
        array(
            'property' => '_sCustomSortField',
            'param' => 'sort'
        ),
        array(
            'property' => '_sCustomSortOrder',
            'param' => 'direction'
        )
    );

    public function setDocumentType($type) {
        $this->_documentType = $type;
    }

    public function setListType($type) {
        $this->_listType = $type;
    }

    public function getSearchTerm() {
        return $this->_searchTerm;
    }

    public function setSearchTerm($term) {
        $this->_templateName = 'entry_search.twig';
        $this->_searchTerm = $term;
    }

    public function getTotalDocuments() {
        return $this->_totalDocuments;
    }

    public function getFoundDocuments() {
        return $this->_foundDocuments;
    }

    public function setCustomSorting($field, $order) {
        $this->_customSortField = $field;
        $this->_customSortOrder = $order;
        $this->addAdditionalUrlParameter('sort', $field);
        $this->addAdditionalUrlParameter('direction', $order);
    }

    public function getDocuments() {
        if($this->_documents === null) {
            $entryDocumentList = new EntryDocumentList($this->_app);
            $documentLimit = $this->getDocumentLimit();

            if(!empty($this->_customSortField)) {
                $entryDocumentList->setCustomSorting($this->_customSortField, $this->_customSortOrder);
            }

            if($this->_listType === 'search') {
                $this->addAdditionalUrlParameter('term', $this->_searchTerm);
                $entryDocumentList->findByTerm($this->_searchTerm, $documentLimit, $this->_skippedDocuments);

            } elseif($this->_listType === 'list') {
                if($this->_documentType === null && $this->_outputFormat === 'html') {
                    $this->_documentType = 1;
                }

                if($this->_documentType !== null) {
                    $this->addAdditionalUrlParameter('type', $this->_documentType);
                    $entryDocumentList->findByType($this->_documentType, $documentLimit, $this->_skippedDocuments);
                } else {
                    $entryDocumentList->find($documentLimit, $this->_skippedDocuments);
                }

            }

            $this->_documents = $entryDocumentList;
        }

        return $this->_documents;
    }

    public function render($app) {
        $this->_foundDocuments = $this->getDocuments()->getFoundDocuments();
        $this->_totalDocuments = $this->getDocuments()->getTotalDocuments();
        $this->_templateData['view'] = $this;

        return parent::render($app);
    }

    protected function _renderJSON($app) {
        $output = array(
            'status' => 200,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => 'GET',
                'url' => $app['request']->getPathInfo()
            ),
            'response' => array(        
                'total' => $this->_totalDocuments,
                'found' => $this->_foundDocuments
            )
        );

        foreach($this->_possibleParams as $param) {
            $value = (isset($this->{$param['property']})) ? $this->{$param['property']} : null;
            
            if(!empty($value)) {
                $output['request']['params'][$param['param']] = $value;
            }
        }

        $output['response']['documents'] = array();

        if(count($this->getDocuments()) > 0) {
            foreach($this->getDocuments() as $document) {
                $output['response']['documents'][] = $document->getProperties();
            }
        }

        return $app->json($output);
    }

    protected function _renderXML($app) {
        $kickipedia = new \SimpleXMLElement('<kickipedia></kickipedia>');
        
        $kickipedia->addChild('status', 200);
        $kickipedia->addChild('date', date('Y-m-d H:i:s'));
        
        $request = $kickipedia->addChild('request');
        $request->addChild('method', 'GET');
        $request->addChild('url', $app['request']->getPathInfo());

        foreach($this->_possibleParams as $param) {
            $value = (isset($this->{$param['property']})) ? $this->{$param['property']} : null;
            
            if(!empty($value)) {
                $request->addChild($param['param'], $value);
            }
        }

        $response = $kickipedia->addChild('response');
        $response->addChild('total', $this->_totalDocuments);
        $response->addChild('found', $this->_foundDocuments);

        $documents = $response->addChild('documents');

        if(count($this->getDocuments()) > 0) {
            foreach($this->getDocuments() as $documentData) {
                $oDocument = $documents->addChild('document');
                foreach($documentData as $key => $value) {
                    $oDocument->addChild($key, $documentData->getProperty($key));
                }
            }
        }

        echo $kickipedia->asXML();
    }
}