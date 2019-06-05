<?php

/**
 * Perform the necessary imports
 */
import('Form60.base.F60DocBase');

class F60DesktopDoc extends F60DocBase 
{
	
	function F60DesktopDoc() 
	{		
	
            F60DocBase::F60DocBase('Desktop', 'desktop_layout.tpl');
            Document::addScript('resources/js/javascript.popup.js');
            Document::addStyle('resources/css/popup.css');
	}
		
	
	function _buildMenu() 
	{
            import('Form60.base.DHTMLMenu');

            $mainMenu =& new DHTMLMenu($this);
            //exclude restricted pagses
            $currentUser = & User::getInstance();
            $restrictedPages = $currentUser->getPropertyValue('restricted_pages');
            
            $restrictedSubPages = $currentUser->getPropertyValue('restricted_sub_pages');
            
           
            
            $pages = explode(",", $restrictedPages);
            $restrictedPages  = "";
            
            $sub_pages = explode(",", $restrictedSubPages);
            $restrictedSubPages  = "";
            foreach($pages as $p)
            {
                $restrictedPages = $restrictedPages . ",'" . $p . "'";
                $restrictedSubPages = $restrictedSubPages . ",'" . $p . "'";
            }
            $restrictedPages = "(" . substr($restrictedPages,1) . ")";
            $restrictedSubPages = "(" . substr($restrictedSubPages,1) . ")";

            $db =& Db::getInstance();
            
            $user_level_id = $_COOKIE["F60_USER_LEVEL_ID"];
            
          
            if($user_level_id ==5)
            {
         
					$mainMenu->loadFromDatabase(
                    "select page_id as id_menu, menu_caption as caption, link, menu_sort_order from pages where parent_page_id is null and show_in_menu=1 and page_id =43", 
                    "select page_id as id_menu, menu_caption as caption, link, menu_sort_order from pages where parent_page_id = ~id_menu~ and show_in_menu=1 and page_id=43"
                );
            
				}
				else
					
           		 $mainMenu->loadFromDatabase(
                    "select page_id as id_menu, menu_caption as caption, link, menu_sort_order from pages where parent_page_id is null and page_id<>43 and show_in_menu=1 and name not in " . $restrictedPages . " order by menu_sort_order", 
                    "select page_id as id_menu, menu_caption as caption, link, menu_sort_order from pages where parent_page_id = ~id_menu~ and page_id<>43 and show_in_menu=1 and name not in " . $restrictedSubPages . " order by menu_sort_order"
                );
            
            return $mainMenu->getContent();
	}
	
	function display() 
	{
		$this->elements['menu'] = $this->_buildMenu();
		$this->elements['main'] = "middle.htm" ; 
		F60Docbase::display();
	}
}

?>