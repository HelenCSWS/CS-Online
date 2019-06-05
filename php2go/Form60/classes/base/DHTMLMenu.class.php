<?php
//Adopting from DHTML menu from http://www.BrainJar.com

//------------------------------------------------------------------
import('php2go.gui.Menu');
//------------------------------------------------------------------

class DHTMLMenu extends Menu
{
    var $menuCode = ''; 			

    function DHTMLMenu(&$Document) 
    {
        Menu::Menu($Document);
    }


    function display() 
    {
        Menu::buildMenu();
        $this->_buildCode();
        print $this->menuCode;
    }

    function getContent() 
    {
        Menu::buildMenu();
        $this->_buildCode();
        return $this->menuCode;
    }

    function _buildCode() 
    {
            
        $this->_Document->addScript('resources/js/javascript.dhtmlmenu.js');
        
        $this->menuCode .= '<div class="menuBar" >';		
        
        $topItemFormat = '<a class="menuButton"
                        href=\'\'
                        onclick="return buttonClick(event, \'%s\');"
                        onmouseover="buttonMouseover(event, \'%s\');">%s</a>';
        $topItemNoChildFormat = '<a class="menuButton"
                        href=\'%s\' onmouseover="buttonMouseover(event, \'%s\');">%s</a>';
                        
        $childItemFormat = '<a class="menuItem" href=\'%s\'
                            onclick="return true;">%s</a>';
                        
        for ($i = 0; $i < sizeOf($this->tree); $i++) 
        {
            if (!empty($this->tree[$i]['CHILDREN']))
                $this->menuCode .= sprintf($topItemFormat, 
                        'menu' . $i, 'menu' . $i,
                        $this->tree[$i]['CAPTION']);
            else
                 $this->menuCode .= sprintf($topItemNoChildFormat, 
                        $this->tree[$i]['LINK'], 'menu' . $i, $this->tree[$i]['CAPTION']);
        }
        
        $this->menuCode .= '</div>';
        
        for ($i = 0; $i < sizeOf($this->tree); $i++) 
        {
            /*if (!empty($this->tree[$i]['CHILDREN']))
            {
                $this->menuCode .= sprintf('<div id="%s" class="menu" 
                    onmouseover="menuMouseover(event)">', 'menu' . $i);
                $children = $this->tree[$i]['CHILDREN'];
                for ($j = 0; $j < sizeOf($children); $j++) 
                {
                    $this->menuCode .= sprintf($childItemFormat,  $children[$j]['LINK'], 
                        $children[$j]['CAPTION']);
                }
                $this->menuCode .= '</div>';
            }*/
            $this->menuCode .= $this->_buildSubMenus($this->tree[$i], 'menu' . $i);
        }
    }
    
    function _buildSubMenus($node, $nodeName)
    {
        $subMenus = "";
        $childMenus = "";
        $childItemFormat = '<a class="menuItem" href=\'%s\'
                            onclick="return true;">%s</a>';
        $subMenuFormat = '<a class="menuItem" href="" onclick="return false;"
                       onmouseover="menuItemMouseover(event, \'%s\');">
                       <span class="menuItemText">%s</span>
                       <span class="menuItemArrow">&#9654;</span></a>';
        if (!empty($node['CHILDREN']))
        {
            $subMenus .= sprintf('<div id="%s" class="menu" 
                onmouseover="menuMouseover(event)">', $nodeName);
            $children = $node['CHILDREN'];
            for ($j = 0; $j < sizeOf($children); $j++) 
            {
                if (!empty($children[$j]['CHILDREN']))
                {
                
                    $subMenus .= sprintf($subMenuFormat,
                       $nodeName ."_" . $j, $children[$j]['CAPTION']);
                    $childMenus .= $this->_buildSubMenus($children[$j], $nodeName ."_" . $j);
                }
                else
                {
                    $subMenus .= sprintf($childItemFormat,  
                        $children[$j]['LINK'], $children[$j]['CAPTION']);
                }
            }
            $subMenus .= '</div>' . $childMenus ;
        }
        return $subMenus;
    }
}
?>