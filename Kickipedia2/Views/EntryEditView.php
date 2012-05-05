<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryRecord;

class EntryEditView extends View {
    
    protected $_sTemplateName = 'entry_edit.twig';
    protected $_sId = null;

    public function render() {
        $oEntry = new EntryRecord();
        $oEntry->load($this->getId());  
        $this->_aTemplateData['entry'] = $oEntry;
        $this->_aTemplateData['formAction'] = url_for('entry', $this->getId(), 'update');

        parent::render();
    }

    public function update($aData) {
        $oEntry = new EntryRecord();
        $oEntry->load($this->getId()); 
        $oEntry->updateProperties($aData);
        $oEntry->save();     
    }
}