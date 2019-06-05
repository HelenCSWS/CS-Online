<?php


//------------------------------------------------------------------

import('Form60.base.F60FormBase');
import('Form60.base.F60DALBase');
import('Form60.base.F60ControlUnit');
//import('Form60.util.F60Common');

//------------------------------------------------------------------
/*define('LBL_WineName','Wine name');
define('LBL_Vintage','Vintage');
define('LBL_Allocated','Customers');
define('LBL_Samples','Samples');
define('LBL_Buffer','Buffer');
define('LBL_Corked','Breakage');
define('LBL_Other','Other');
define('LBL_Total','Total');
define('LBL_Availiable','Available');

define*/

class F60ControlsContent extends PHP2Go
{
    var $wine_names;
    var $wine_ids;

    var $htmlCode;

    var $nWineNumbers;
    var $ctlWines;
    var $ctlCustomers;
    var $ctlTotals;
    var $form;
    var $customer_names;
    var $customer_ids;
    var $rows =10;
   // var $nwines;
    
    var $tn =0;
    var $isEmpty = true;
    //var $editMode = 0; //0: editable, 1: allocate to customer
    function F60ControlsContent($wine_ids, &$form,$sqlSelect,$sqlWhere,$cmid)
    {
      
        $this->form = $form;
        $wine_ids =split("[|]",$wine_ids);
        $this->nWineNumbers =sizeof($wine_ids);
        $this->wine_ids = $wine_ids;
        if ($cmid!="")
            $this->createGroups4cm($wine_ids,$form,$cmid);
        else
        {
           
            $this->createGroups($wine_ids,$form,$sqlSelect,$sqlWhere);
        }
    }

    function createGroups4cm($wine_ids,$form,$cmid)
    {
        $sql = "select customer_name from customers where customer_id = ".$cmid;
        $result = $this->runSQL($sql);
   	    $row = & $result->FetchRow();
   	    $customer_name =$row["customer_name"];

        $this->rows = 1;
   	    
   	    //for ($j=0;$j<$this->rows;$j++)
        //        {
        $this->ctlCustomers[0]= new F60ControlUnit($cmid,"",0,$form, 0,0,$customer_name);
        $this->ctlWines[0] = new wineGroup($this->nWineNumbers,$wine_ids,$cmid,0,$form);
//        }
        //total controls
        for ($i=0;$i<=$this->nWineNumbers-1;$i++)
        {

            $this->ctlTotals[$i] = new F60ControlUnit($wine_ids[$i],"",0,$form,$i,2,"");
        }

    }
    function createGroups($wine_ids,$form,$sqlSelect,$sqlWhere)
    {
        $records = $_REQUEST['record_counts'];
        $status = $_REQUEST['status'];
        $currentpage = $_REQUEST['current_page'];
       // print $currentpage;
        
        if ( $records==0)
        {
           $pages = 1;
            $this->isEmpty = false;
            $sql = 'select count(*) cnt from '.$sqlWhere;

           	$result = $this->runSQL($sql);
       	    $row = & $result->FetchRow();
           	if ($row['cnt']!=0)
           	{
                $pages = ceil($row['cnt']/$this->rows);
                $records =$row['cnt'];
                $edtPages =& $form->getField("total_pages");
                $edtPages->setValue($pages);
                $edtRecords =& $form->getField("record_counts");
                $edtRecords->setValue($records);
            }
        }
        
        $displayrecords=$records;

       	if ($records!=0)
       	{
          $this->isEmpty =false;
          $beginRec = "";

           if($records>$this->rows)
           {
             switch ($status):
                case 0: //first
                    $sql = $sqlSelect.$sqlWhere." limit ".$this->rows;
                    break;
                case 1: //next
                    $beginRec = ($currentpage-1)*$this->rows;
                    $endRec = $this->rows;
                    $sql =$sqlSelect.$sqlWhere." limit ".$beginRec.",".$endRec;
                    break;
                case 2: //preivours
                    $beginRec = ($currentpage-1)*$this->rows;
                    $endRec = $this->rows;
                    $sql =$sqlSelect.$sqlWhere." limit ".$beginRec.",".$endRec;
                    break;
                case 3: //last
                   $beginRec = ($currentpage-1)*$this->rows;
                    $endRec = $records;
                    $sql =$sqlSelect.$sqlWhere." limit ".$beginRec.",".$endRec;
                     break;
                endswitch;

               
              //  if  (($records -$beginRec)>0)
              //          $this->rows = $records -$beginRec;
           }
           else
           {
                $this->rows =$records;
               $sql = $sqlSelect.$sqlWhere;
               $pages =1;
           }
           $numbers = & $form->getField("customer_numbers");
           $numbers->setValue($displayrecords);
       	    $result = $this->runSQL($sql);
         	
           
        	
           if (!$result->EOF)
           {
                
                $this->rows=0;
               
                while($row = & $result->FetchRow())
                {

                    $customer_id = $row['customer_id'];
                    $customer_name =$row['customer_name'];
                     $this->customer_names[$this->rows]=$customer_name;
                     $this->customer_ids[$this->rows]=$customer_id;
                     $this->rows ++;
                }
                  $edtcms = & $form->getField("customers");
                $edtcms->setValue($this->rows);

                for ($j=0;$j<$this->rows;$j++)
                {
                     $this->ctlCustomers[$j]= new F60ControlUnit($this->customer_ids[$j],"",$j,$form, 0,0,$this->customer_names[$j]);
                   $this->ctlWines[$j] = new wineGroup($this->nWineNumbers,$wine_ids,$this->customer_ids[$j],$j,$form);
                }
                //total controls
                for ($i=0;$i<=$this->nWineNumbers-1;$i++)
                {
                    $this->ctlTotals[$i] = new F60ControlUnit($wine_ids[$i],"",0,$form,$i,2,"");
                }
            }
        }
        else
            $this->isEmpty =true;
            
        

    }

	function getContent()
    {
       
        $htmlStr='<table  name="cmtbl" calss ="" cellpadding="3" border="0"  cellspacing="0" >%s%s%s%s</table>';
        
        $htmTbs ="";
        $htmCaps ="";
        $htmTotalTbs="";
        $wine_td_width='128';
        //print $this->nWineNumbers;
       for ($i=0;$i<$this->nWineNumbers;$i++)
        {
             $sql = "select * from wines where wine_id = ".$this->wine_ids[$i];
            $result = $this->runSQL($sql);
            $row = & $result->FetchRow();
            $wine_name1 = $row['wine_name'];
           // print $row['lkup_wine_color_type_id'];
            $fullname =$row['wine_name'].'. '.$row['vintage'].'. '.$this->getColor(1);//$row['lkup_wine_color_type_id']);

            $wine_name_title= str_replace(' ','&nbsp;',$fullname);
            $wine_name = substr($wine_name1,0,12);
            if($i == 0)
            {
                $htmTbl =sprintf("<td width=\"%s\" style=\" background: #F2F5FA;
                border-bottom: #333333 0px solid;
                                            border-left: #a3b3c0 0px solid;
                                            border-right: #a3b3c0 1px solid;
                                            border-top: #a3b3c0 1px solid;\"
                                            colspan=\"2\" align=\"center\" title=%s class=\"tdCap\"><b>%s</td>",$wine_td_width,$wine_name_title,$wine_name);

          
          }
     //     elseif($i == $this->nWineNumbers)
     //     {

     //     }
          else
          {
            $htmTbl =sprintf("<td width=\"%s\" style=\" background: #F2F5FA;border-bottom: #a3b3c0 0px solid;
                                            border-left: #a3b3c0 0px solid;
                                            border-right: #a3b3c0 1px solid;
                                            border-top: #a3b3c0 1px solid;\"
                                            colspan=\"2\" align=\"center\"  title=%s class=\"tdCap\"><b>%s</td>",$wine_td_width,$wine_name_title,$wine_name);

        }
        if($i == 0)
        {
            $htmCap ="<td align=\"center\" style=\" background: #e0f0ff;border-bottom: #333333 1px solid;
                                                border-left: #a3b3c0 0px solid;
                                                border-right: #a3b3c0 1px solid;
                                                border-top: #a3b3c0 1px solid;\"
                                                class=\"tdCap\" style=\"padding-left:5px\" ><b>Sold</td>
                                                <td style=\" background: #e0f0ff;border-bottom: #333333 1px solid;
                                                border-left: #a3b3c0 0px solid;
                                                border-right: #a3b3c0 1px solid;
                                                border-top: #a3b3c0 1px solid;\" align=\"center\" class=\"tdCap\" style=\"padding-left:5px\"><b>Allo/Aval</td>";
        }
        else if ($i==1)
        {
              $htmCap ="<td align=\"center\" style=\" background: #e0f0ff;border-bottom: #333333 1px solid;
                                                border-left: #a3b3c0 0px solid;
                                                border-right: #a3b3c0 1px solid;
                                                border-top: #a3b3c0 1px solid;\"
                                                class=\"tdCap\" style=\"padding-left:5px\" ><b>Sold</td>
                                                <td style=\" background: #e0f0ff;border-bottom: #333333 1px solid;
                                                border-left: #a3b3c0 0px solid;
                                                border-right: #a3b3c0 1px solid;
                                                border-top: #a3b3c0 1px solid;\" align=\"center\" class=\"tdCap\" style=\"padding-left:5px\"><b>Allo/Aval</td>";

        }
                                               $htmTbs = $htmTbs.$htmTbl;
            //allocate&sold
          /*  $htmCap ="<td align=\"center\" style=\" background: #e0f0ff;border-bottom: #333333 1px solid;
                                            border-left: #a3b3c0 1px solid;
                                            border-right: #a3b3c0 0px solid;
                                            border-top: #a3b3c0 1px solid;\"
                                            class=\"tdCap\" style=\"padding-left:7px\" ><b>Allocate</td>
                                            <td style=\" background: #e0f0ff;border-bottom: #333333 1px solid;
                                            border-left: #a3b3c0 1px solid;
                                            border-right: #a3b3c0 0px solid;
                                            border-top: #a3b3c0 1px solid;\" align=\"center\" class=\"tdCap\" style=\"padding-left:7px\"><b>Sold</td>";*/
            $htmCaps = $htmCaps.$htmCap;
            
            
            //total
            $htmTotal = $this->ctlTotals[$i]->getContent();
            $htmTotalTbs=$htmTotalTbs.$htmTotal;
        }

        $htmNames=sprintf("<tr><td style=\" background: #F2F5FA;border-bottom: #a3b3c0 0px solid;
                                            border-left: #a3b3c0 1px solid;
                                            border-right: #a3b3c0 1px solid;
                                            border-top: #a3b3c0 1px solid;\">&nbsp;</td>%s</tr>",$htmTbs);
     
        $htmCapts =sprintf("<tr><td align=\"center\"style=\" background: #e0f0ff;border-bottom: #333333 1px solid;
                                            border-left: #a3b3c0 1px solid;
                                            border-right: #a3b3c0 1px solid;
                                            border-top: #a3b3c0 1px solid;\"
                                            class=\"tdCap\"><b>Customers</td>%s</tr>",$htmCaps);
        $htmTotals="";//sprintf("<tr><td class=\"tdCap\" align=\"right\"><b>Total</td>%s</tr>",$htmTotalTbs);

        $htmTbs="";
        $htmCMTrs="";
        for($j=0;$j<$this->rows;$j++)
        {
           // $j=0;
            $htmCM = $this->ctlCustomers[$j]->getContent();

            $htmWines =$this->ctlWines[$j]->getControlStr($this->nWineNumbers,$j);
           // $htmTb = $htmWines;
           // $htmTbs =$htmTbs.$htmWines;
           /* for ($i=0;$i<$this->nWineNumbers;$i++) //just show the wine which has been allocated;
            {
               // print $j;
                $htmTbl=$this->ctlWines[$j]->getContent();
                $htmTbs =$htmTbs.$htmTbl;
            }*/
           // $this->writeLog($htmWines,"ctlog.txt");

            
            $htmRow =$htmCM.$htmWines;
   
            $htmTr =sprintf("<tr><td align=\"right\" style=\" background: #F2F5FA;border-bottom: #a3b3c0 1px solid;
                                            border-left: #a3b3c0 1px solid;
                                            border-right: #a3b3c0 1px solid;
                                            border-top: #a3b3c0 0px solid;\" >%s</td></tr>",$htmRow);

            $htmCMTrs = $htmCMTrs.$htmTr;
        }

    //    $this->writeLog($htmCMTrs,"ctlog.txt");
        $this->htmlCode = sprintf($htmlStr,$htmNames,$htmCapts,$htmCMTrs,$htmTotals);
  
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
	
	
    function writeLog($txt,$filename)
    {
        $fname ="logs/".$filename;
    	$fp = fopen($fname,"a");

    	fputs($fp, $txt."\n");

    //		fputs($fp, memory_get_usage() . "\n");

    	fclose($fp);

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

class wineGroup extends PHP2Go
{
    var $ctlWines;
    
    function wineGroup($nWineNumbers,$wine_ids,$customer_id,$j,$form)
    {
        for ($i=0;$i<=$nWineNumbers-1;$i++)
            {
              //  $this->nwines =$this->nwines+1;
                   // print $wine_ids[$i];
                $this->ctlWines[$i] = new F60ControlUnit($wine_ids[$i],$customer_id,$j,$form,$i,1,"");
            }
    }
    function getControlStr($nWineNumbers,$xx)
    {
            $htmTbs ="";
          for ($i=0;$i<$nWineNumbers;$i++) //just show the wine which has been allocated;
            {
               // print $j;
                $htmTbl=$this->ctlWines[$i]->getContent();
                $htmTbs =$htmTbs.$htmTbl;
            }

            if($xx !=2)
            {
                //$this->writeLog($htmTbs,"log.txt");
            }
            return $htmTbs;

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
