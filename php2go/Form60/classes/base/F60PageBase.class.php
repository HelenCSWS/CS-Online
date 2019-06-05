<?php

/**
 * Perform the necessary imports
 */
import('Form60.base.F60LayoutBase');

class F60PageBase extends F60LayoutBase 
{
    var $contents;
    
    function F60PageBase($pageName, $pageTitle, $templateName) 
    {		
    
        F60LayoutBase::F60LayoutBase($pageName, $pageTitle, $templateName);
        $this->_prepareContents();
    }
      
    function _prepareContents()
    {
        $this->contents =& new DocumentElement();
        $this->contents->put($this->templateName, T_BYFILE);
        $this->contents->parse();    
    }
    
    function &getContents()
    {
        return $this->contents;
    }
    
    function display() 
    {
        $this->elements['main'] = & $this->contents;
        F60LayoutBase::display();          
    }

}

?>