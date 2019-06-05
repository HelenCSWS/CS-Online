<?php

import('Form60.base.F60DocBase');
import('Form60.base.F60PageStack');

class F60LayoutBase extends F60DocBase 
{
    var $actions;
    var $templateName;
    var $pageTitle;
    var $contents;
    var $showPrint = false;
    var $showPDF = false;
    var $showTimer = true;
    
    function F60LayoutBase($pageName, $pageTitle, $templateName) 
    {		
        F60DocBase::F60DocBase($pageName);
        $this->pageTitle = $pageTitle;
        $this->templateName = TEMPLATE_PATH . $templateName;  
        if (isset($_GET["pop"])) F60PageStack::popLastPage();
    }
           
    function setActions($actions)
    {
        $this->actions = $actions;
    }
    
    function _createActionBlock()
    {
        //set actions
        $tpl = & $this->Template;
        $tpl->createBlock('action_table_start');
        foreach($this->actions as $title => $url)
        {
            $tpl->createBlock('action_block');
            $tpl->assign('action_title', $title);
            $tpl->assign('action_url', $url);
            $tpl->assign('action_caption', $title);
        } 
        $tpl->createBlock('action_table_end');   
        $tpl->setCurrentBlock( "_ROOT" );
    }
    
    function _createPageExportBlock()
    {
        if (($this->pageTimerOn && $this->showTimer) || $this->showPrint || $this->showPDF)
        {
            $tpl = & $this->Template;
            $tpl->createBlock('action_page_export_start');
            if ($this->showPrint)
                $tpl->createBlock('action_page_print');
            if ($this->showPDF)
                $tpl->createBlock('action_page_pdf');
            if ($this->pageTimerOn && $this->showTimer)
                $tpl->createBlock('action_page_timer');
            $tpl->createBlock('action_page_export_end');        
            $tpl->setCurrentBlock( "_ROOT" );
        }
    }
    
    function display() 
    {
        if (isset($_GET['print']))
            $this->displayPrint();
        else if (isset($_GET['pdf']))
            $this->displayPDF(); 
        else 
        {
            $this->elements['title'] = $this->pageTitle;
            if (isset($this->actions))
                $this->_createActionBlock();
            $this->_createPageExportBlock();
            F60DocBase::display(); 
        }            
    }
    
    function displayPrint()
    {
        $doc = new Document('resources/template/print_layout.tpl');
        $doc->addStyle($this->cssName);
        $doc->setTitle($this->elements['title']);
        $doc->elements['contents'] = $this->elements['main'];
        $doc->display();
    }
    
   function displayPDF()
    {
        $doc = new Document('resources/template/pdf_layout.tpl');
        $doc->addStyle($this->cssName);
        $doc->setTitle($this->elements['title']);
        $doc->elements['contents'] = $this->elements['main'];
        ob_start();
        $doc->display();
        // Output-Buffer in variable:
        $html=ob_get_contents();
        // delete Output-Buffer
        ob_end_clean();
        
        require_once(PHP2GO_ROOT . "modules/html2pdf/html2fpdf.php");
        $pdf = new HTML2FPDF();
        $pdf->DisplayPreferences('FitWindow');
        $pdf->UseCSS(TRUE);
        $pdf->UsePRE(TRUE);
        $pdf->UseTableHeader(TRUE);
        $pdf->AddPage();
        $pdf->WriteHTML($html);
        $pdf->Output($this->pageTitle  . ".pdf", I);
    }
}

?>