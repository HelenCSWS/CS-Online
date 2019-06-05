<?php
class F60PageStack extends Php2Go 
{
    
    function F60PageStack() 
    {		
    }
           
       
    function addtoPageStack($bForce= false)
    { 
    /*
        $currentUser = & User::getInstance();
        if ($currentUser->isAuthenticated() && ($_SERVER["REQUEST_METHOD"] == "GET" || $bForce))
        {
            $pageStack = $currentUser->getPropertyValue("page_stack");
            $thisPage = $_SERVER["REQUEST_URI"];
            if (empty($pageStack))
                $pageStack = array();

            $lastPage = "";
            if (count($pageStack) >0)
                $lastPage = $pageStack[count($pageStack) -1];

            if ($lastPage != $thisPage)
            {
                array_push($pageStack, $thisPage);
                $currentUser->setPropertyValue("page_stack", $pageStack);
                $currentUser->update();
            }
        }
    */
    }
    
    function addPagetoStack($page, $bForce= false)
    {
        $currentUser = & User::getInstance();
        if ($currentUser->isAuthenticated() && ($_SERVER["REQUEST_METHOD"] == "GET" || $bForce))
        {
            $pageStack = $currentUser->getPropertyValue("page_stack");
            if (empty($pageStack))
                $pageStack = array();

            array_push($pageStack, $page);
            $currentUser->setPropertyValue("page_stack", $pageStack);
            $currentUser->update();
        }
    }
    
    function fetchLastPage($action) //0 -just get the page, 1 - pop the page, 2 - goto page
    {
        $currentUser = & User::getInstance();
        if ($currentUser->isAuthenticated())
        {
            $pageStack = $currentUser->getPropertyValue("page_stack");
            $lastPage = array_pop($pageStack);
            $thisPage = $_SERVER["REQUEST_URI"];
            
          //  print_r($_SERVER);
          //  print $_SERVER["QUERY_STRING"];
              
            while ($thisPage == $lastPage)
            {
                $lastPage = array_pop($pageStack);
                if($lastPage == NULL)
                	break;
            }
            
            if (empty($lastPage) || $lastPage == "")
                $lastPage = "middle.htm";

            if ($action == 1 || $action == 2) //pop the page
            {
                $currentUser->setPropertyValue("page_stack", $pageStack);
                //$currentUser->update();
            }

            /*$output = '';
            foreach ($pageStack as $value) 
            {
                    $output .= "$value\n";
            }
            traceLog("Pop1: " . $output);*/

            if ($action == 2) //go to the page
            {
                import('php2go.util.HtmlUtils');
                import('php2go.net.Url');
                //traceLog("going to: " . $lastPage);
                $page = & new Url($lastPage);
                HtmlUtils::redirect($page->getUrl());
            }
            return $lastPage;
        }
    }
    
    function getLastPage()
    {
        return F60PageStack::fetchLastPage(0);
    }
    
    function popLastPage()
    {
        return F60PageStack::fetchLastPage(1);
    }
    
    function gotoLastPage()
    {
        return F60PageStack::fetchLastPage(2);
    }

    function Clear()
    {
        $currentUser = & User::getInstance();
        if ($currentUser->isAuthenticated())
        {
            $pageStack = array();
            $currentUser->setPropertyValue("page_stack", $pageStack);
            $currentUser->update();
        }
    }
    
}

?>