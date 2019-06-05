<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('Form60.util.F60Common');


class F60NotesList extends PagedDataSet
{
    var $_Document;
    var $ownerType;
    var $ownerID;
    var $orderBy = "when_created";
    var $orderType = "d";
    var $page = 1;
    var $sqlCode;
    
    var $Template = NULL;
    var $templateFile = 'notecontrol.tpl';
    var $templateFileNoResize = 'notecontrolnoresize.tpl';
    var $sortSymbols; 
    
    var $noteYear="";


    function F60NotesList($Document, $owner, $ID,  $noResize=false,$noteYear="")
    {
       PagedDataSet::PagedDataSet('db');
       PagedDataSet::setPageSize(20);
       
       if ($Document)
       {
            $Document->addScript('resources/js/javascript.notes.js');
            $this->_Document = $Document;
       }
       $this->ownerType = $owner;
       $this->ownerID = $ID;
       
       if ($noResize)
            $this->templateFile = TEMPLATE_PATH. $this->templateFileNoResize;
       else
            $this->templateFile = TEMPLATE_PATH. $this->templateFile;
       $this->Template =& new Template($this->templateFile);
       if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
            $this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));
        
       $this->Template->parse();
       
       $this->sortSymbols = & F60Common::sortSymbols();
          

		$this->noteYear=$noteYear;
		
    }

	
    function _loadDataset()
    {
     	
        $sqlTemplate = "SELECT n.note_id, n.when_created, n.note_text, CONCAT_WS(' ', u.first_name, u.last_name) as user_name 
                        FROM notes n, users u, %s en
                        where n.deleted = 0
                        and n.created_user_id = u.user_id
                        and n.note_id = en.note_id
                        and en.%s = %s
                        and year(n.when_created)=%s
                        order by %s %s";
                        
        if ($this->ownerType == "estate")
        {
            $ownerTable 	= "estates";
            $joinTable 		= "estates_notes";
            $idFiledName 	= "estate_id";
        }
        else
        {
            $ownerTable 	= "customers";
            $joinTable		= "customers_notes";
            $idFiledName 	= "customer_id";
        }
        
        	        
        $this->sqlCode = sprintf($sqlTemplate, $joinTable, $idFiledName,$this->ownerID, $this->noteYear,$this->orderBy,
                        ($this->orderType == "d")?"DESC":"ASC");
        PagedDataSet::setCurrentPage($this->page);
        PagedDataSet::load($this->sqlCode);
        
        //adjust for deletes
        $pageCount = PagedDataSet::getPageCount();
        if ($pageCount<$this->page)
        {
            while ($pageCount<$this->page)
            {
                --$this->page;
            }
            PagedDataSet::setCurrentPage($this->page);
            PagedDataSet::load($this->sqlCode);
        }
                        
    }
    
    function _buildContent() 
    {
     	$yearsSet = $this->getNoteYears();
     	
     		 
     		//Note content
     /*	 $fp = fopen("logs/log.log","a");
		fputs($fp, 'noteyear1: '.$this->noteYear."\n");
		fclose($fp);*/
        $this->_loadDataset();
     	//Note year
     	if($yearsSet!=false)
     	{
	     
	     	foreach ($yearsSet as $noteYear)
	     	{
			
				
				$nYear =$noteYear["note_year"];
				
				
				$this->Template->createBlock('year_loop_line');
				$this->Template->assign("note_year", $nYear);
				if($nYear==$this->noteYear)
						$this->Template->assign("selected", "selected");
				
			}
		}
     	
     	
     	//Note content
     
        $aRow = 0;	
					
        while ($lineData = PagedDataSet::fetch()) 
        {
            $aRow++;
            $this->Template->createBlock('loop_line');
            $this->Template->assign("row_style", ($aRow % 2)?"gridrowOdd":"gridrowEven");
            $this->Template->assign("note_id", $lineData["note_id"]);
            $this->Template->assign("when_created", date("m/d/Y",strtotime($lineData["when_created"])));
            $this->Template->assign("user_name", $lineData["user_name"]);
            $this->Template->assign("note_text", $lineData["note_text"]);
        }
        
        $this->Template->globalAssign("total",  PagedDataSet::getTotalRecordCount());
        $this->Template->globalAssign("page",  (PagedDataSet::getPageCount()==0)?0:PagedDataSet::getCurrentPage());
        $this->Template->globalAssign("total_page",  PagedDataSet::getPageCount());
        
        if (PagedDataSet::getPreviousPage())
        {
            $this->Template->createBlock('prev_page_link');
            $this->Template->assign("prev_page", PagedDataSet::getPreviousPage());
        }
        if (PagedDataSet::getPageCount()>1 &&  PagedDataSet::getCurrentPage() <PagedDataSet::getPageCount())
        {
            $this->Template->createBlock('next_page_link');
            $this->Template->assign("next_page", PagedDataSet::getNextPage());
        }
        
        $this->Template->globalAssign($this->orderBy . "_sort", $this->sortSymbols[$this->orderType]);
        $this->Template->globalAssign("owner_type", $this->ownerType);
        $this->Template->globalAssign("owner_id", $this->ownerID);
        $this->Template->globalAssign("order_by", $this->orderBy);
        $this->Template->globalAssign("order_type", $this->orderType);
        
    }
    
    function getNoteYears()
    {
		 $sqlTemplate = "SELECT distinct year(n.when_created) note_year
                        FROM notes n, users u, %s en
                        where n.deleted = 0
                        and n.created_user_id = u.user_id
                        and n.note_id = en.note_id
                        and en.%s = %s
                        order by note_year desc";
                        
        if ($this->ownerType == "estate")
        {
            $ownerTable = "estates";
            $joinTable = "estates_notes";
            $idFiledName = "estate_id";
        }
        else
        {
            $ownerTable = "customers";
            $joinTable = "customers_notes";
            $idFiledName = "customer_id";
        }
        $sqlCode = sprintf($sqlTemplate, $joinTable, $idFiledName, $this->ownerID, $this->orderBy,
                        ($this->orderType == "d")?"DESC":"ASC");
                        
        $db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);

		
		
		$rows = $db->getAll($sqlCode);
		if(count($rows)!=0)
		{
			
		 	if($this->noteYear==""||is_object($this->noteYear))
				$this->noteYear= $rows[0]["note_year"];
				    
        
			return	$rows;
		}
		else
		{
	 		if($this->noteYear=="")
	 			$this->noteYear =2000;
	 			       
			return false;
		}
	
	}
    function getContent() 
    {		
        $this->_buildContent();
        //traceLog("Notes:" . $this->Template->getContent());
        return $this->Template->getContent();
    }
}


?>