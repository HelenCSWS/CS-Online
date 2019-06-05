<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60PopupBase');
import('Form60.util.F60Date');
import('Form60.bll.bllnotes');

class noteAdd extends F60PopupBase
{
    var $note_id;
    var $owner_type;
    var $owner_id;

    function noteAdd()
    {

        $this->note_id = $this->getRecordID();

        if ($this->editMode())
        {
            $title = "Edit note";
        }
        else
        {
            $title = "Add note";
        }

        F60PopupBase::F60PopupBase('noteAdd', $title, 'noteAdd.xml', 'noteAdd.tpl', 'btnOK');
        Document::addScript('resources/js/javascript.notes.js');

        $form = & $this->getForm();
        $form->setFormAction('main.php?page_name=noteAdd');
        
        $form->setErrorDisplayOptions('error', FORM_CLIENT_ERROR_DHTML, 'form_client_errors');
        
        $this->registerActionhandler(array("btnOK", array($this, processForm), "LASTPAGE", null));

        $this->form->setButtonStyle('btnOK');
        $this->form->setInputStyle('input');
        $this->form->setLabelStyle('label');
        
    }

    function display()
    {
            if (!$this->handlePost())
                $this->displayForm();
    }

    function displayForm()
    {
        $this->showTimer = false;
        
        $form = & $this->getForm();

        if ($this->editMode())
        {
            $this->loadData(&$form, $this->note_id);
        }

       $this->setFocus('notAdd','note_text');
       F60PopupBase::display();
    }

    function loadData(&$form, $note_id)
    {
        $notes = & new bllnotes();
        $note = $notes->getByPrimaryKey($note_id);
        $note->loadDataToForm($form);
    }

    function processForm()
    {
      $form = & $this->getForm();
      
      $note_id = $_POST['note_id'];
        if (strlen($note_id)>0)
            $edit = TRUE;
        else
        {
            $edit = False;
            $note_id = null;
        }
        
        
        $notes = & new bllnotes();
        
        
        if ($edit)
            $note = $notes->getByPrimaryKey($note_id);
        else
            $note = $notes->add_new(); 
        
        $note->getDataFromForm($form);
        
        $note->save();
        
        return false;
    }
}

?>