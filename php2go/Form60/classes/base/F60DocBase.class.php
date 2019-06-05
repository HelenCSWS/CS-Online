<?php


//------------------------------------------------------------------
import('php2go.base.Document');
import('php2go.datetime.TimeCounter');
import('Form60.base.F60Auth');
//------------------------------------------------------------------

define('DEFAULT_CSS', 'Form60app.css');
define('DEFAULT_TEMPLATE', 'page_layout.tpl');

class F60DocBase extends Document
{
    var $cssName;			
    var $templateName;			
    var $sessionKeyName;	
    var $pageName;        
    var $pageTimerOn;
    var $timeCounter;
    var $displayActionMenu = true; // flag for display action menu or not
    
    function F60DocBase($pageName, $templateName = DEFAULT_TEMPLATE, $cssName = DEFAULT_CSS) 
    {
        	
     	
        $auth =& Auth::getInstance();
        if (!$auth->isActiveSession())
        {
            $this->onInvalidSession();
        }
                
        $this->pageTimerOn = $this->getConfigVal('PAGE_TIMER');
        if ($this->pageTimerOn)
                $this->timeCounter = new TimeCounter();
        if ($this->isA('F60DocBase', FALSE)) {
                PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'F60DocBase'), E_USER_ERROR, __FILE__, __LINE__);
        }
        $this->pageName = $pageName;
        $this->cssName = CSS_PATH . $cssName;
        $this->templateName = TEMPLATE_PATH . $templateName;
        
        //from php2go document constructor
        $docLayout = $this->templateName;
        PHP2Go::PHP2Go();
        $this->docLayout = $docLayout;
        $this->docLanguage = PHP2Go::getConfigVal('LOCALE', FALSE);
        $this->docTitle = PHP2Go::getConfigVal('TITLE', FALSE);
        $this->Template =& new Template($docLayout);
        
        if (PHP2Go::getConfigVal('TEMPLATE_CACHE', FALSE))
            $this->Template->setCacheProperties(PHP2Go::getConfigVal('FORM60_CACHE_PATH'), 86400);
            
        $this->Template->parse();
        $this->TimeCounter =& new TimeCounter();
        $this->_initMetaTags();
        $this->_initDeclaredElements();
        $this->_addSystemElements();
        parent::registerDestructor(&$this, '_Document');
        //-------------------------------------------------
             
        Document::Document($this->templateName);
        Document::setCache(FALSE);
        Document::setCompression($this->getConfigVal('DOC_COMPRESSION', FALSE));
        Document::addScript('resources/js/javascript.common.js');
 
     	Document::addScript('https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js');  // add jquery
        Document::addStyle($this->cssName);
       
        $icons="<LINK REL=\"icon\" type=\"image/png\" href=\"Address-bar-icon.png\" />";
	   Document::appendHeaderContent($icons);
         
        
                
    }
	function setDisplayAction($isDisplay)
	{
		$this->displayActionMenu = $isDisplay;
	}
	
    function onInvalidSession() 
    {
        import('php2go.util.HtmlUtils');

        //Handles the "not logged" state, redirecting the user to the login page
        HtmlUtils::redirect('login.php');
        exit;
    }
        
    function checkPageAcess()
    {
        $currentUser = & User::getInstance();
        $restrictedPages = $currentUser->getPropertyValue('restricted_pages');
        

        import('php2go.text.StringUtils');
        
        $arrayRestrictedPages=explode(",",$restrictedPages);
        
     
        for($i=0; $i<count($arrayRestrictedPages);$i++)
        {
			if($arrayRestrictedPages[$i]==$this->pageName)
			{return false;}
		}
		
		return true;
		
    	/*if (StringUtils::match($restrictedPages, $this->pageName, FALSE))
        {
  //       	echo why;
    		return FALSE;
        }
    	else
        {
    		return TRUE;
        }*/
    }
	
    function getCurrentUserID()
    {
        import('php2go.auth.User');
        $currentUser = & User::getInstance();
        return $currentUser->getPropertyValue('user_id');
    }
    
    function display() 
    {
        //check page access here
       if ($this->checkPageAcess() == FALSE)
        {
            $this->elements['error'] = '<font color=red>You don\'t have access to this page. Contact your administrator for access.</font><br>';
            $this->elements['main'] = '';
        }
        if ($this->pageTimerOn)
        {
           $this->timeCounter->stop();
            $this->Template->globalAssign('pagetimer', "Generation time: " . round($this->timeCounter->getInterval(),3)
               . " seconds");
        }
        $this->Template->globalAssign('request_uri', $_SERVER[REQUEST_URI]);
        
        if($this->displayActionMenu)
        {
			
        	$this->Template->globalAssign('display_action', "block");	
        }
        else
        {
        	$this->Template->globalAssign('display_action', "none");	
        }
        	
        Document::display();
    }
    
}
?>