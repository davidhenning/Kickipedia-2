<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryRecord;

class EntryView extends View {
    
    protected $_sTemplateName = 'entry.twig';
    protected $_sId = null;

    public function render() {
        $oEntry = new EntryRecord();
        $oEntry->load($this->getId());  
        $this->_aTemplateData['entry'] = $oEntry;

        parent::render();
    }
}