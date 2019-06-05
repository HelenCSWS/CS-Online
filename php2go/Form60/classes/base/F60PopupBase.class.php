<?php

/**
 * Perform the necessary imports
 */
import('Form60.base.F60FormBase');

class F60PopupBase extends F60FormBase 
{

    function F60PopupBase($formName, $formTitle, $formXML, $fromTemplateName, $saveButton = '') 
    {		
    
        F60DocBase::F60DocBase($formName, 'popup_layout.tpl');
        Document::addScript('resources/js/javascript.forms.js');
        
        $this->setTitle($formTitle);
        $this->addOnloadCode("formOnLoad('" . $this->getLastPage() . "','" . $saveButton . "');");
        
        $this->templateName = TEMPLATE_PATH . $fromTemplateName;  
        $this->formXML = XML_PATH . $formXML;
        
        $this->_buildForm();
    }
            
   

    function display() 
    {
        $this->elements['main'] = $this->form->getContent();
        F60DocBase::display();   
    }  
    
}


?>