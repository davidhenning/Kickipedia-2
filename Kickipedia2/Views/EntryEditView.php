<?php

namespace Kickipedia2\Views;

use MongoAppKit\View;
use Kickipedia2\Models\EntryDocument;

class EntryEditView extends View {
    
    protected $_sAppName = 'Kickipedia2';
    protected $_sTemplateName = 'entry_edit.twig';
    protected $_sId = null;
    protected $_oEntry = null;

    public function render() {
        $this->_oEntry = new EntryDocument();
        $this->_oEntry->load($this->getId());  
        $this->_aTemplateData['entry'] = $this->_oEntry;
        $this->_aTemplateData['formAction'] = url_for('entry', $this->getId(), 'update');

        parent::render();
    }

    public function update($aData) {
        $this->_oEntry = new EntryDocument();
        $sId = $this->getId();

        if(!empty($sId)) {
            $this->_oEntry->load($sId);
        }
            
        $this->_oEntry->updateProperties($aData);
        $this->_oEntry->save();  
    }

    public function delete() {
        $this->_oEntry = new EntryDocument();
        $this->_oEntry->load($this->getId()); 
        $this->_oEntry->delete();     
    }

    public function getTypeId() {
        return $this->_oEntry->getProperty('type');
    }  
}