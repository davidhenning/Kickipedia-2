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
}