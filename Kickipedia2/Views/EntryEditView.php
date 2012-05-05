<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryRecord;

class EntryEditView extends View {
    
    protected $_sTemplateName = 'entry_edit.twig';
    protected $_sId = null;

    public function render() {
        $entry = new EntryRecord();
        $entry->load($this->getId());  
        $this->_aTemplateData['entry'] = $entry;
        $this->_aTemplateData['formAction'] = url_for('entry', $this->getId(), 'update');

        parent::render();
    }

    public function update($aData) {
        $entry = new EntryRecord();
        $entry->load($this->getId()); 
        $entry->updateProperties($aData);
        $entry->save();     
    }
}