<?php


//------------------------------------------------------------------
import('php2go.xml.XmlDocument');
import('Form60.bll.bllwines');
import('Form60.dal.dalwine_allocates');
import('php2go.form.field.EditField');
import('Form60.base.F60DbUtil');


//------------------------------------------------------------------


define('TLT_Allocated','Total allocateions including sold');
define('TLT_Samples','Samples / Special event');
define('TLT_Buffer','Buffer');
define('TLT_Corked','Breakage / Corked');
define('TLT_Other','Other');
define('TLT_Total','Available for allocations(Total+Sold-Allocations)');
define('TLT_Availiable','Total wine not sold');
define('TLT_Sold','Sold');

define('CTL_NUMBERS',6);

class F60AllocateGroup extends PHP2Go
{
    var $wine_name;
    var $vintage;
    var $color;
    var $wine_id;
    var $totalBottles;

    var $edtXmlnode;
    var $lblXmlnode;

    var $edtUnallocate;
    var $hiddenWineName;
    var $hiddenWineID;
    var $hiddenAlcted;
    var $hiddenSample;
    var $hiddenBuffer;
    var  $hiddenTotal;

    var $hiddenStb;

    var $htmlCode;

    var $tltNames = array(TLT_Allocated,TLT_Sold,TLT_Samples,TLT_Buffer,TLT_Corked,TLT_Total,TLT_Availiable);
    var $edtNames = array("edt_allocate_0","edt_sold","edt_allocate_1","edt_allocate_2","edt_allocate_3","edt_total","edt_available");
    var $fieldNames = array("unallocated","sold","sample","buffer","breakage_corked","other","ava");
    var $index;
    var $boxSize =7;
    var $editMode; //0: editable, 1: allcaote to customer

    var $edtFirst;
  //  var $edtOldDates;

    var $oldBreakage;
    var $oldSample;
    var $oldBuffer;
    var $isCm = true;


    function F60AllocateGroup($wine_id, $index, &$form, $isCm)
    {
      // print here;
        $this->wine_id = $wine_id;
        $this->index = $index;
        $this->isCm = $isCm;

        //  print $this->wine_id;

        $wines = & new bllwines();
        $wine = $wines->getByPrimaryKey($wine_id);
        $this->wine_name = $wine->get_data("wine_name");
        $this->wine_name_title = $wine->get_data("wine_name").' '.$wine->get_data("cspc_code");
        $this->wine_name = substr($this->wine_name,0,12);
        //if(wine_id == 9)
         //   print here;
        //else
         
         $this->color = $this->getColor($wine->get_data("lkup_wine_color_type_id"));
        
        $this->vintage = $wine->get_data("vintage");
        $this->totalBottles = $wine->get_data("total_bottles");
        $allcoated_tag =0;
        $samples =0;
        $buffers =0 ;
        $breakages =0 ;
        $solds =0;

        $sql="select * from wine_allocations where wine_id = ".$wine_id;
        $result = & F60DbUtil::runSQL($sql);
        $isFirst =0; //first
        $unallocate =0;
        if (!$result->EOF)
        {
           //if ($wine_id == 7)
           // print wine7;
           $row1 = & $result->FetchRow();
           $samples =$row1["sample"];
           $breakages =$row1["breakage_corked"];
           $buffers =$row1["buffer"];
           $unallocate =$row1["unallocated"];
           //print $unallocate;

           $isFirst = 1; //update
         //  print here;
        }
        
            $solds = 0;
      //   $sql = 'select distinct sum(ordered_quantity) sold from order_items odit, orders od  where wine_id ='.$wine_id.' and od.deleted=0 and odit.deleted=0 group by od.order_id ';//' and odit.order_id = od.order_id and od.customer_id ='.$cm_id;
          
     //       $sql = 'select distinct sum(ordered_quantity) sold from order_items odit  where wine_id ='.$wine_id.' and odit.deleted=0 group by odit.order_id ';//' and odit.order_id = od.order_id and od.customer_id ='.$cm_id;

			$sql ="select distinct sum(ordered_quantity) sold from order_items odit where wine_id =$wine_id and odit.deleted=0";
    		$result = & F60DbUtil::runSQL($sql);
    		if(!$result->EOF)
            {
                $row = & $result->FetchRow();
                $solds=$row['sold'];
                if ($solds==NULL)
                    $solds = 0;
            }
        $notAval =$samples+$breakages+$buffers+($unallocate-$solds);

          
          for ($i=0;$i<=CTL_NUMBERS;$i++)
          {
                $edtName = $this->edtNames[$i]."_".$this->wine_id;
                $tltName=$this->tltNames[$i];

                $this->edtUnallocate[$i] = & new EditField($form,false);

                $this->edtUnallocate[$i]->setLength(25);
                $this->edtUnallocate[$i]->setName($edtName);

                $this->edtUnallocate[$i]->setSize ($this->boxSize);
                $this->edtUnallocate[$i]->setLabel($tltName);
                $this->edtUnallocate[$i]->setMask ("INTEGER");
                $this->edtUnallocate[$i]->setStyle ("listInput");
                $avalibs = 0;

                if ($isFirst==1)
                {
                    $this->edtUnallocate[$i]->setValue($row1[$this->fieldNames[$i]]);


                    for ($j=0;$j<=(CTL_NUMBERS-2);$j++)
                    {
                        if(!$result->EOF)
                           $avalibs = $avalibs + $row[$this->fieldNames[$j]];
                        else
                            $avalibs = $avalibs;
                    }
               }

             if ($i == 0)
              {
                $this->edtUnallocate[$i]->setReadonly(true);
                    $this->edtUnallocate[$i]->setStyle("txtLable");
                //$this->edtUnallocate[$i]->setDisabled(true);
                //if ($isCm)
                $this->edtUnallocate[$i]->setValue($unallocate);
              }

                if ($i == 1) 
              {
                $this->edtUnallocate[$i]->setReadonly(true);
                    $this->edtUnallocate[$i]->setStyle("txtLable");
                //$this->edtUnallocate[$i]->setDisabled(true);
                //if ($isCm)
                if ($solds == 0 )
                   $this->edtUnallocate[$i]->setValue("0");
                else
                   $this->edtUnallocate[$i]->setValue($solds);
              }

                $hidTotals =$this->totalBottles - $avalibs;
               if ($i == (CTL_NUMBERS-1))
               {
                    $this->edtUnallocate[$i]->setReadonly(true);
                    $this->edtUnallocate[$i]->setValue($this->totalBottles - $notAval);
                    $this->edtUnallocate[$i]->setStyle("listInputTol");
  
               }

           
              if($i == (CTL_NUMBERS))
              {
              //  $this->edtUnallocate[$i]->setReadonly(true);
               $this->edtUnallocate[$i]->setReadonly(true);
                 $this->edtUnallocate[$i]->setValue($this->totalBottles);
                 $this->edtUnallocate[$i]->setStyle("txtLable");
              }
              if ($i>1 and $i<5)
              {
                //print $i;
                $this->edtUnallocate[$i]->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onblur', sprintf("setTotals('%s','%s',0)", $this->wine_id,$i-1 )));

              }

             //  if (!$editMode)
               //  $this->edtUnallocate[$i]->setReadonly(true);
          }

            $edtName = 'wine_id_'.$this->index;
            $this->hiddenWineID = & new HiddenField($form,false);
            $this->hiddenWineID->setName($edtName);
            $this->hiddenWineID->setValue($this->wine_id);

            $edtName = 'allocated_'.$this->wine_id;
            $this->hiddenAlcted = & new HiddenField($form,false);
            $this->hiddenAlcted->setName($edtName);
            $this->hiddenAlcted->setValue($unallocate);
/*

*/
            $edtName = 'hid_avals_'.$this->wine_id;
            $this->hiddenAvals = & new HiddenField($form,false);
            $this->hiddenAvals->setName($edtName);
            $this->hiddenAvals->setValue($this->totalBottles);

            $edtName = 'hid_alc_1_'.$this->wine_id;
            $this->hiddenSample = & new HiddenField($form,false);
            $this->hiddenSample->setName($edtName);
            $this->hiddenSample->setValue($samples);


            $edtName = 'hid_alc_2_'.$this->wine_id;
            $this->hiddenBuffer = & new HiddenField($form,false);
            $this->hiddenBuffer->setName($edtName);
            $this->hiddenBuffer->setValue($buffers);

           $edtName = 'hid_alc_3_'.$this->wine_id;
            $this->hiddenBreakage = & new HiddenField($form,false);
            $this->hiddenBreakage->setName($edtName);
            $this->hiddenBreakage->setValue($breakages);


          $edtName = 'hid_old_1_'.$this->wine_id;
            $this->oldSample = & new HiddenField($form,false);
            $this->oldSample->setName($edtName);
            $this->oldSample->setValue($samples);


            $edtName = 'hid_old_2_'.$this->wine_id;
            $this->oldBuffer = & new HiddenField($form,false);
            $this->oldBuffer->setName($edtName);
            $this->oldBuffer->setValue($buffers);

           $edtName = 'hid_old_3_'.$this->wine_id;
            $this->oldBreakage = & new HiddenField($form,false);
            $this->oldBreakage->setName($edtName);
            $this->oldBreakage->setValue($breakages);

            $edtName = 'is_first_'.$this->wine_id;
            $this->edtFirst = & new HiddenField($form,false);
            $this->edtFirst->setName($edtName);
            $this->edtFirst->setValue($isFirst);


           $edtName = 'hid_alc_stb_'.$this->wine_id;
            $this->hiddenStb = & new HiddenField($form,false);
            $this->hiddenStb->setName($edtName);
            $this->hiddenStb->setValue(0);
 }



		function getContent()
        {

            $htmlStr ="<td class=\"listContent\" nowrap width=\"100\" style=\"padding-left:5px;padding-right:5px;background: #F2F5FA;\" title=\"%s\">%s</td><td class=\"listContent\" align=\"center\" style=\"background: #F2F5FA;\" title=\"%s\">%s</td><td width=\"40\"class=\"listContent\" align=\"center\" style=\"background: #F2F5FA;\" title=\"%s\">%s</td>%s";
            $hidstr = "<td style=\"display:block\" class=\"hideTd\">%s</td>";
            $hidstrs ="";
            for ($i=0;$i<11;$i++)
            {
                $hidstrs = $hidstrs.$hidstr;
            }
            $htmlStr = $htmlStr.$hidstrs;

            $htmlTbl ="<td width=\"80\" align=\"right\"  style=\"background: #F2F5FA;padding-top:1px;padding-bottom:1px\" class=\"listContent\">%s</td>";

            $htmlTbls = "";
            for ($i=0;$i<=CTL_NUMBERS;$i++)
            {

                if ($i==CTL_NUMBERS)
                {
                    $htmlTbl ="<td  style=\"background: #F2F5FA;\" class=\"listContentRight\" >%s</td>";
                }
                else if ($i==CTL_NUMBERS-1)
                {
                    $htmlTbl ="<td width=\"80\" align=\"center\"  style=\"background: #F2F5FA;padding-top:1px;padding-bottom:1px\" class=\"listContent\">%s</td>";
                }
                else if ($i==0 ||$i==1)
                {
$htmlTbl ="<td width=\"80\" align=\"center\"  style=\"background: #F2F5FA;padding-top:1px;padding-bottom:1px;padding-right:2px\" class=\"listContent\">%s</td>";

                }
                else
                {
$htmlTbl ="<td width=\"80\" align=\"right\"  style=\"background: #F2F5FA;padding-top:1px;padding-bottom:1px;padding-right:2px\" class=\"listContent\">%s </td>";
                }
                

                $htmla = sprintf( $htmlTbl, $this->edtUnallocate[$i]->getCode() );
                $htmlTbls = $htmlTbls . $htmla;
            }

     	        $this->htmlCode = sprintf($htmlStr,
    			$this->wine_name_title,
    			$this->wine_name,
                $this->vintage,
                $this->vintage,
                $this->color,
                $this->color,
    			$htmlTbls,
                $this->hiddenWineID->getCode(),
                $this->hiddenAlcted->getCode(),
                $this->hiddenAvals->getCode(),
                $this->hiddenSample->getCode(),
                $this->hiddenBuffer->getCode(),
                $this->hiddenBreakage->getCode(),
                  $this->hiddenStb->getCode(),
               $this->oldSample->getCode(),
                $this->oldBuffer->getCode(),
                $this->oldBreakage->getCode(),
               $this->edtFirst->getCode()
              );


         //  echo $this->htmlCode;
    		return $this->htmlCode;

	}


      	function runSQL($sql)

	{

		$dbc = & Db::getInstance();

		$result = $dbc->query($sql);

		if (!$result)

		{

			PHP2Go::raiseError('SQL error:' . $sql, E_USER_ERROR, __FILE__, __LINE__);

			exit;

		}

		return $result;

	}

    function getColor($colorid)
    {
        $color='Red';
        if ($colorid == 1)
            $color='Red';
        else if ($colorid == 2)
            $color='White';
        else if ($colorid == 3)
            $color='Rose';
            
        return $color;

    }
}

?>
