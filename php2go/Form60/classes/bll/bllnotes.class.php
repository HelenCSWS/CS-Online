<?php

import('Form60.dal.dalnotes');
import('Form60.dal.dalestates_notes');
import('Form60.dal.dalcustomers_notes');

class bllNote extends dalNotes
{

    var $owner_id;
    var $owner_type;
    
    function bllNote()
    {
        parent::dalNotes();
    }
    
    //Only need to override save to add link to owner
    function save()
    {
        $is_new= $this->is_new;
        if (parent::save())
        {
            //need to add link only if it's a new note
            if ($is_new == 1)
            {
                $retVal = false;
                $note_id = $this->get_data("note_id");
                
                $owner_type = "";
                $owner_id = "";
                
                switch ($this->owner_type)
                {
                    case "estate":
                        $dalNoteLink = "dalestates_notes";
                        $owner_id_name = "estate_id";
                        break;
                            
                    case "customer":
                        $dalNoteLink = "dalcustomers_notes";
                        $owner_id_name = "customer_id";
                        break;
                    
                    case "contact":
                        $dalNoteLink = "dalcontacts_notes";
                        $owner_id_name = "contact_id";
                        break;
                    
                    default:
                        $dalNoteLink = "";
                        $owner_id_name = "";
                        return false;
                
                }
                
                if ($dalNoteLink != "" && $this->owner_id != "")
                {
                    $dal = & new $dalNoteLink;
                    $dal->set_data($owner_id_name,  $this->owner_id);
                    $dal->set_data("note_id",  $note_id);
                    $retVal = $dal->save();
                }
                return $retVal;
            }
        }
        return false;
    }
    
    function getDataFromForm($form)
    {
        parent::getDataFromForm($form);
        $field= & $form->getField("owner_type");
        $this->owner_type =  $field->getValue();
        
        $field= & $form->getField("owner_id");
        $this->owner_id =  $field->getValue();
    }
}

class bllnotes extends dalnotescollection
{
    function bllnotes()
    {
            parent::dalnotescollection();
    }
	
    function add_new()
    {
        //override collection add_new, return bll class
        $bll = & new bllNote();
        return $bll;
    }
	
}
?>