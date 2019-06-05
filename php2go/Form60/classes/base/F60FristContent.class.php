<?php


//------------------------------------------------------------------
import('php2go.xml.XmlDocument');
//------------------------------------------------------------------
define('LBL_WineName','Wine name');
define('LBL_Vintage','Vintage');
define('LBL_Color','Color');
define('LBL_Allocated','Allocations');
define('LBL_Samples','Samples');
define('LBL_Buffer','Buffer');
define('LBL_Corked','Breakage');
define('LBL_Other','Other');
define('LBL_Total','Available');
define('LBL_Sold','Sold');
define('LBL_Availiable','Total');

class F60FristContent extends PHP2Go
{
    var $wine_names;
    var $wine_ids;



    var $htmlCode;

    var $lblNames = array(LBL_Vintage,LBL_Color,LBL_Allocated,LBL_Sold,LBL_Samples,LBL_Buffer,LBL_Corked,LBL_Total,LBL_Availiable);
  //  var $edtNames = array("edt_unallocate","edt_allocted","edt_samples","edt_buffer","edt_corked","edt_other","edt_total","edt_available");

    var $nWineNumbers;
    var $ctlGroups;
    var $form;
    var $isCm;
    function F60FristContent($wine_ids, &$form, $isCm)
    {
     //  print $wine_ids[0];
        $this->form =$form;
        $this->nWineNumbers =sizeof($wine_ids);
      //  print $this->nWineNumbers;
        $this->wine_ids = $wine_ids;
        $this->createGroups($wine_ids,$form,$isCm);
        
    }

    function createGroups($wine_ids,$form,$isCm)
    {
        $index =sizeof($wine_ids);
        for ($i=0;$i<=$this->nWineNumbers-1;$i++)
        {
          //  print $wine_ids[$i];
            $this->ctlGroups[$i] = new F60AllocateGroup($wine_ids[$i],$i, $form,$isCm);

         }
    }

	function getContent()
    {
        $htmlStr= '';
        $htmlStr=$htmlStr.'<table  calss ="gridTable" cellpadding="0" border="0"  cellspacing="0" >
                            <tr><td  align="center"  height="20" class="listHeader"><b>Wine name</td>%s</tr>%s</table>';
   
        $htmlTbs = "";
        $htmlCaps ="";
        for ($i=0;$i<=8;$i++)
        {
           // print $i;
            if($i==8)
            {
                $htmlCap =sprintf("<td align=\"center\" class=\"listHeaderRight\"><B>%s</td>", $this->lblNames[$i]);
            }
            elseif($i==0)
                   $htmlCap =sprintf("<td align=\"center\" class=\"listHeaderVintage\"><B>%s</td>", $this->lblNames[$i]);
           // elseif($i==1)
           // {
              //  if(!$this->isCm)
              //      $htmlCap =sprintf("<td style=\"display:none\" class=\"hideTd\">%s</td>", $this->lblNames[$i]);
           // }
            else
             $htmlCap =sprintf("<td align=\"center\" class=\"listHeader\"><B>%s</td>", $this->lblNames[$i]);
            $htmlCaps = $htmlCaps.$htmlCap;

        }
        //print $htmlCaps;
        for ($i=0;$i<=$this->nWineNumbers-1;$i++)
        {

            $htmlTb =sprintf("<tr>%s</tr>", $this->ctlGroups[$i]->getContent());
            $htmlTbs = $htmlTbs.$htmlTb;

        }


       $this->htmlCode = sprintf($htmlStr,$htmlCaps,$htmlTbs);

        return $this->htmlCode;

	}
}

?>
