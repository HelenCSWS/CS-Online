<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60LayoutBase');
import('Form60.base.F60PageStack');

class F60FormBase extends F60LayoutBase 
{
    var $formXML;
    var $form;
    var $actionHandlers = array();
    var $lastpage = '';
    
    function F60FormBase($formName, $formTitle, $formXML, $fromTemplateName = '', $saveButton = '') 
    {		

        F60LayoutBase::F60LayoutBase($formName, $formTitle, $fromTemplateName);
        Document::addScript('resources/js/javascript.forms.js');

        $this->lastpage = F60PageStack::getLastpage();
        //traceLog("last page: " . $_SERVER["REQUEST_URI"] . " " . $this->lastpage); 
        $this->addOnloadCode("formOnLoad('" . $this->lastpage . "','" . $saveButton . "');");
        
        $this->formXML = XML_PATH . $formXML;
        
        $this->_buildForm();
    }
            
    function _buildForm()
    {
        if (trim($this->templateName) != '')
        {
            import('Form60.base.F60FormTemplate');
            $this->form =& new F60FormTemplate($this->formXML, $this->templateName, $this->pageName, $this);
        }
        else
        {
            import('php2go.form.FormBasic');
            $this->form =& new FormBasic($this->formXML, $this->pageName, $this);
            $this->form->setFormMethod('POST');
            $this->form->setFormAlign('center');
            $this->form->setInputStyle('input');
            $this->form->setLabelStyle('label');
            $this->form->setButtonStyle('button');
        }
 //       $errheader = "<B>Please correct the following errors and try again.</B>";
        $errheader = "";
        $this->form->setErrorStyle('error_style', FORM_ERROR_BULLET_LIST, $errheader);
        $this->form->setErrorDisplayOptions('error', FORM_CLIENT_ERROR_ALERT, 'form_client_errors');
    }
    
    function &getForm()
    {
        return $this->form;
    }
    
    function editMode()
    {
        return isset($_GET["id"]);
    }
    
    function getRecordID()
    {
        return (isset($_GET["id"])? $_GET["id"] : null);
    }

    function getPagePara($recordname)
    {
        return (isset($_REQUEST[$recordname])? $_REQUEST[$recordname] : null);
    }

    function display() 
    {
        //cache if enabled
        if (PHP2Go::getConfigVal('TEMPLATE_CACHE', FALSE) && !($this->editMode()))
        {
            require_once('cachelite/Output.php');
    
             $options = getCacheOptions();
                 
             $cache = new Cache_Lite_Output($options);
             $cacheID = md5($_SERVER[REQUEST_URI]);
             if (!($cache->start($cacheID)))
             {
                $this->_display();
                $cache->end();
             }
        }
        else
            $this->_display();
        
    }  
    
    function _display()
    {
        $this->elements['main'] = $this->form->getContent();
        F60LayoutBase::display();   
    }
    
    function registerActionhandler($actionHandler)
    {
        import('php2go.util.TypeUtils');
        if (TypeUtils::isInstanceOf($actionHandler, "F60ActionHandler"))
            $this->actionHandlers[$actionHandler->actionName] = $actionHandler;
        elseif (TypeUtils::isArray($actionHandler))
        {
            $handler = & new F60ActionHandler($actionHandler[0], $actionHandler[1], $actionHandler[2], 
                $actionHandler[3], $this->lastpage);
            $this->actionHandlers[$handler->actionName] = & $handler;
        }
        else
            PHP2Go::raiseError("Parameter must be an array or a F60ActionHandler.", E_USER_ERROR, __FILE__, __LINE__);
    }
    
    function handlePost()
    {
        if ($this->form->isPosted() && isset($_POST["action_name"]))
        {
            $actionName = $_POST["action_name"];
            if (array_key_exists($actionName, $this->actionHandlers))
            {
                //F60PageStack::popLastpage();
                return $this->actionHandlers[$actionName]->execute();
            }
        }
        
        return false;
    }
    
    function getCached()
    {
        if (PHP2Go::getConfigVal('TEMPLATE_CACHE', FALSE) && !($this->editMode()))
        {
            require_once('cachelite/Output.php');
    
            $cnt = new TimeCounter();

            $options = getCacheOptions();
                 
            $cache = new Cache_Lite_Output($options);
            $cacheID = md5($_SERVER[REQUEST_URI]);
            $data = $cache->get($cacheID);

            if ($data) 
            {
                $cnt->stop();
                $data = preg_replace("/Generation time:/", 
                    "Generation time : " . round($cnt->getInterval(),3)
                    . " seconds", $data);

               
                return true;
            }
        }
        return false;
    }
}

class F60ActionHandler extends PHP2Go
{
    var $actionName;
    var $handler;
    var $returnURL;
    var $returnType;
    
    function F60ActionHandler($actionName, $handler, $returnTo, $returnURL = null, $lastPage = null) 
    {
        import('php2go.net.Url');
        $this->actionName = $actionName;
        $this->handler = & new Callback($handler);
        $this->returnType = $returnTo;
        
        switch ($returnTo)
        {
            case "SELF":
                $this->returnURL = & new Url($_SERVER["REQUEST_URI"]);
                break;
                
            case "LASTPAGE":
                $this->returnURL = & new Url($lastPage);
                break;
                
            case "URL":
                if (!returnURL)
                    PHP2Go::raiseError("Must specify a return URL for action handler.", E_USER_ERROR, __FILE__, __LINE__);
                else
                    $this->returnURL = & new Url($returnURL);
        }
    }

    function execute()
    {
        import('php2go.util.HtmlUtils');
        if (isset($this->handler))
            if ($this->handler->invoke())
            {
                if ($this->returnType == "LASTPAGE")
                    F60PageStack::popLastpage();
                HtmlUtils::redirect($this->returnURL->getUrl());
            }
    }
}


function getCacheOptions()
{
    return array(
                 'cacheDir' => PHP2Go::getConfigVal('FORM60_CACHE_PATH'),
                 'lifeTime' => PHP2Go::getConfigVal('CACHE_LIFETIME'),
                 'fileLocking' => true,
                 'writeControl' => false,
                 'readControl' => false,
                 'hashedDirectoryLevel' => PHP2Go::getConfigVal('CACHE_HASHDIR_LEVEL')
             );
}












?>