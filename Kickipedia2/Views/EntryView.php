<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryModel;

class EntryView extends View {
    
    protected $_sTemplateName = 'entry.twig';
    protected $_sId = null;

    public function render() {
        $entry = new EntryModel();
        $entry->load($this->getId());  
        $this->_aTemplateData['entry'] = $entry;

        parent::render();
    }
}