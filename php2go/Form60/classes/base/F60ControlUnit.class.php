<?php


//------------------------------------------------------------------
import('php2go.xml.XmlDocument');
import('Form60.bll.bllwines');
import('Form60.dal.dal_alct_wines_customers');
import('php2go.form.field.EditField');
import('php2go.form.field.TextField');
import('Form60.base.F60DbUtil');

//------------------------------------------------------------------


class F60ControlUnit extends PHP2Go
{

    var $htmlCode;
    
    var $tltNames = array("Sold","Allocated");
    var $edtNames = array("sold","allocated");

    var $tltTotals = array("Total allocate","Total sold");
    var $edtTotals = array("tal_allocate","tal_sold");

   // var $fieldNames = array("unallocated","sample","buffer","breakage_corked","other","ava");
   var $index;
    var $boxSize =11;
    var $editMode = 0; //0: editable, 1: allcaote to customer
    
    var $controlTag =0;
    var $id;
    var $name;
    var $editBox;
    var $hiddenBox;
    var $editBoxs;
   var $iswineid;
   
   var $isHidden =0;
   var $rowIndex=0;
   var $editBox1;
     
   var $hidAlct;
   var $hidDbAlct;
   var $isFirst;
   var $hidIsFirst;
    /*
        $cnotrolTag: 0: customer, 1; wine, 2:total 
        $id: customer id or wine_id
        $isWinie: false:customer, true:wine
    */
//    function F60ControlUnit( $id,$cm_id="", $row_index, $col_index=0,$controlTag=0,$name="", &$form)
    function F60ControlUnit( $id,$cm_id, $row_index, &$form,$col_index=0,$controlTag=0,$name="" )
    {
       // print "++".$row_index;

        $this->controlTag = $controlTag;
        $this->id = $id;
        $this->name = $name;
      //  $this->rowIndex =$row_index;
     
         //customer name
        if ($controlTag == 0)
        {

           $edtName = $row_index."_".$id;;
           $tltName=$name;

           $this->editBox = & new EditField($form,false);
  
           $this->editBox->setLength(25);
           $this->editBox->setName($edtName);
          $this->editBox->setSize (40);
           $this->editBox->setLabel($tltName);

          //  print substr($name,0,80);
           $this->editBox->setValue(substr($name,0,65));
           $this->editBox->setReadonly(true);
           //$this->editBox->setDisabled(true);

           $this->editBox->setStyle ("txtLable");
 

            $edtName =$row_index."_customerid";
            $this->hiddenBox = & new HiddenField($form,false);

            $this->hiddenBox->setName($edtName);
            $this->hiddenBox->setValue($this->id);

            $this->hiddenBox->setValue($this->id);

       }
       elseif ($controlTag==1)//wine
       {

           $allocations = & new dal_alct_wines_customersCollection();
           $allocations -> add_filter("wine_id","=",$id);
           $allocations -> add_filter("and customer_id","=",$cm_id);
           $allocations->load();
            
             
            $sql = 'select * from customer_wine_allocations where wine_id ='.$id.' and customer_id = '.$cm_id;
    		$result = & F60DbUtil::runSQL($sql);

            $bottles =array("0","0");
            $cm_w_id ="";
            if(!$result->EOF)
            {
                $row = & $result->FetchRow();
                $bottles[1]=$row['allocated'];
            //    $bottles[1]=$row['sold'];
                $cm_w_id =$row['customer_wine_allocation_id'];
            }
            
            $sql = 'select IFNULL(sum(ordered_quantity),0) sold from order_items odit, orders od  where od.deleted=0 and wine_id ='.$id.' and odit.order_id = od.order_id and od.customer_id ='.$cm_id;
    		$result = & F60DbUtil::runSQL($sql);
    		if(!$result->EOF)
            {
                $row1 = & $result->FetchRow();
                //$bottles[0]=$row['allocated'];
               $bottles[0]=$row1['sold'];
            }

            $bottles[1] = $bottles[1]-$bottles[0];


            for ($i=0;$i<=1;$i++)
            {

                $edtName = $row_index."_".$id."_".$this->edtNames[$i];
                $tltName = $this->tltNames[$i];

                $this->editBoxs[$i] = & new EditField($form,false);

                $this->editBoxs[$i]->setLength(20);
                $this->editBoxs[$i]->setName($edtName);

               // $bsize =6;
               $bsize =3;
                
                if($i==1)
                {
                     $this->editBoxs[$i]->setSize(6);
                     $this->editBoxs[$i]->setAlign('center');
                }
                else if($i==0)
                {
                    $bsize=3;
                    $this->editBoxs[$i]->setSize($bsize);
                }
               
                     
                $this->editBoxs[$i]->setLabel($tltName);
                $this->editBoxs[$i]->setMask ("INTEGER");
                if ($i==1)
                    $this->editBoxs[$i]->setStyle ("Input");
                else
                 $this->editBoxs[$i]->setStyle ("txtLable");
                   // $this->editBoxs[$i]->setStyle ("txtLable");
                $this->editBoxs[$i]->setValue ($bottles[$i]);
                if ($i==1)
                {
   
                    $this->editBoxs[$i]->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onblur', sprintf("exc_allocate('%s','%s')", $id,$row_index )));

                }
                else
                    $this->editBoxs[$i]->setReadOnly(true);

            }
            if($row_index==0)
            {
                $this->isHidden =1;
                $edtName =$col_index."_wine_id";
                $this->hiddenBox = & new HiddenField($form,false);
                $this->hiddenBox->setName($edtName);
                $this->hiddenBox->setValue($this->id);
            }
          
            $edtName =$row_index."_".$id."_old_alct";
            $this->hidAlct = & new EditField($form,false);
            $this->hidAlct->setName($edtName);
            $this->hidAlct->setValue($bottles[1]);



            $edtName =$row_index."_".$id."_db_alct";
            $this->hidDbAlct = & new EditField($form,false);
            $this->hidDbAlct->setName($edtName);
            $this->hidDbAlct->setValue($bottles[1]);

            $edtName =$row_index."_".$id."_is_first";
            $this->hidIsFrist = & new EditField($form,false);
            $this->hidIsFrist->setName($edtName);
            $this->hidIsFrist->setValue($cm_w_id);
       }
       elseif ($controlTag == 2)
       {
              
          //  $this->editBox1[$row_index]=& new editGroup($form,$row_index,$id,$this->edtTotals,$this->tltTotals,2);
            for ($i=0;$i<=1;$i++)
            {
                $edtName = $id."_".$this->edtTotals[$i];
                $tltName = $this->tltTotals[$i];
                $this->editBoxs[$i] = & new EditField($form,false);
                $this->editBoxs[$i]->setLength(25);
                $this->editBoxs[$i]->setName($edtName);
               $bsize =6;
                if($i==1)
                    $bsize=3;
                 $this->editBoxs[$i]->setSize ($bsize);
                $this->editBoxs[$i]->setLabel($tltName);
                $this->editBoxs[$i]->setMask ("INTEGER");
                $this->editBoxs[$i]->setValue("0");
               // if($i==0)
                  //  $this->editBoxs[$i]->setStyle("inputTol");
               // else
                $this->editBoxs[$i]->setStyle("Input");
                $this->editBoxs[$i]->setDisabled(true);
            }
            
        //    $sql = $sql="select unallocated from wine_allocations where wine_id = ".$id;
    	//	$result = & F60DbUtil::runSQL($sql);
    	//	if(!$result->EOF)
        //    {
         //       $row = & $result->FetchRow();
                //$bottles[0]=$row['allocated'];
             // $this->editBoxs[0]->setValue($row['unallocated']);
         //   }

            $sql = 'select IFNULL(sum(ordered_quantity),0) sold from order_items odit, orders od  where od.deleted=0 and wine_id ='.$id;//.' and odit.order_id = od.order_id and od.customer_id ='.$cm_id;
    		$result = & F60DbUtil::runSQL($sql);
    		if(!$result->EOF)
            {
                $row = & $result->FetchRow();

             //  $this->editBoxs[1]->setValue($row['sold']);
            }
            
        }
       
       
    }


	
		function getContent()
        {
          // $row_index =$row_index+2;

            switch ($this->controlTag):
                case 0:
                    $htmlTbls ="<table cellpadding=\"0\" border=\"0\"  cellspacing=\"0\"  ><tr><td nowrap style=\"padding-left:0px;padding-right:0px\" align=\"left\">%s</td><td style=\"display:none\" class=\"hideTd\">%s</td></tr></table>";
                    $htmlStr=sprintf( $htmlTbls,$this->editBox->getCode(), $this->hiddenBox->getCode());
                    break;
                case 1:
                    $htmEdt = "";
                  //  $htmEdt=$this->editBox[$row_index]->getControl(1);
                    for ($i=0;$i<=1;$i++)
                    {
                      if($i==0)
                      {
 
                         $htmlTbl ="<td align=\"right\" style=\" background: #F2F5FA;border-bottom: #a3b3c0 1px solid;
                                            border-left: #a3b3c0 0px solid;
                                            border-right: #a3b3c0 1px solid;
                                            border-top: #a3b3c0 0px solid;\"
                                            nowrap style=\"padding-left:1px;padding-right:2px\">%s</td>";

                       }
                       else
                       {
                       $htmlTbl ="<td align=\"center\" style=\" background: #F2F5FA;border-bottom: #a3b3c0 1px solid;
                                            border-left: #a3b3c0 0px solid;
                                            border-right: #a3b3c0 1px solid;
                                            border-top: #a3b3c0 0px solid;\"
                                            nowrap style=\"padding-left:10px;padding-right:2px\">%s</td>";
                                            

                       }
                        
                        $htmla = sprintf( $htmlTbl, $this->editBoxs[$i]->getCode());
                        $htmEdt = $htmEdt . $htmla;
                    }
                    $htmHdAlct =sprintf("<td style=\"display:none\" class=\"hideTd\">%s</td>",$this->hidAlct->getCode());
                    $htmHdAlct =$htmHdAlct. sprintf("<td style=\"display:none\" class=\"hideTd\">%s</td>",$this->hidDbAlct->getCode());
                    $htmHdAlct =$htmHdAlct. sprintf("<td style=\"display:none\" class=\"hideTd\">%s</td>",$this->hidIsFrist->getCode());
                    $htmEdt = $htmEdt.$htmHdAlct;
                    if ($this->hiddenBox)
                    {
                        $htmlTbl = "<td style=\"display:none\" class=\"hideTd\">%s</td>";
                        $htmla = sprintf( $htmlTbl, $this->hiddenBox->getCode());
                        $htmlStr = $htmEdt . $htmla;
                    }
                    else
                        $htmlStr = $htmEdt;
                    break;
                case 2:
                $htmlStr = "";
                   //$htmlStr = $this->editBox1[$row_index]->getControl(2);
                 /*   for ($i=0;$i<=1;$i++)
                    {
                        $htmlTbl ="<td nowrap style=\"padding-left:2px;padding-right:2px\">%s</td>";
                        $htmla = sprintf( $htmlTbl, $this->editBoxs[$i]->getCode());
                        $htmlStr = $htmlStr . $htmla;
                    }*/


                    break;
                default:

            endswitch;

          

     	     $this->htmlCode = sprintf($htmlStr);
         //  echo $this->htmlCode;
    		return $this->htmlCode;

	}
	
	function writeLog($txt,$filename)
    {
        $fname ="logs/".$filename;
    	$fp = fopen($fname,"a");

    	fputs($fp, $txt."\n");

    //		fputs($fp, memory_get_usage() . "\n");

    	fclose($fp);

    }
    
 
}


?>
