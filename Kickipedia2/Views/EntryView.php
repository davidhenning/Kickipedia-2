<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryDocument;

class EntryView extends View {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry.twig';
    protected $_sId = null;

    public function render() {
        $oEntry = new EntryDocument();
        $oEntry->load($this->getId());  
        $this->_aTemplateData['entry'] = $oEntry;

        parent::render();
    }
}