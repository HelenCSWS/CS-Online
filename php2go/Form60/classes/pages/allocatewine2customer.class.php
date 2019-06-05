 <?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.bll.bllallocate2customer');
import('Form60.base.F60ControlsContent');
import('Form60.base.F60ControlUnit');
import('Form60.base.F60FristContent');
import('Form60.base.F60AllocateGroup');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Common');

class allocatewine2customer extends F60FormBase
{

	var $wine_ids;
	var $ctlWines;
	var $ctlCMs;
	var $wineNos;
	var $isCm = 2;//1: allocate wine only 2 cm ;  2:alt 2cm but no cm;  3:update breakage
	
	function allocatewine2customer()
	{
		if ($_REQUEST["isCurrentSave"]==1)
		{
			$this->isCm = 1;
			$this->saveAlt2Cm();
		}
		
		$isWine=0;
		if ($_REQUEST["pageid"]==18) //alt to cm
		{
			 $this->isCm = 1;
			 $isWine=1;
		}
		if ($this->isCm ==1 )
			$title = "Allocate wine";
		else
		{
			$title = "Update samples and breakage";		
		}
		
		//print $_REQUEST["pageid"];
		F60FormBase::F60FormBase('allocatewine2customer', $title, 'allocatewine2customer.xml', 'allocatewine2customer.tpl', 'btnAdd');
		$this->addScript('resources/js/javascript.wineAllocate.js');
		
		$form = & $this->getForm();
		$form->setFormAction('main.php?page_name=allocatewine2customer');
		// $this->form->setInputStyle('listInput');
		$this->form->setButtonStyle('btnOK');
		$this->form->setLabelStyle('label');
		if ($this->isCm !=1 )
		{
			$edtCm =& $form->getField("isNoCm");
			$edtCm ->setValue(3);
		}

         $wine_ids = $_REQUEST["wine_ids"];

         $edtallids = & $this->form->getField("wine_ids");
         $edtallids->setValue($wine_ids);

         $this->wine_ids =split("[|]",$wine_ids);
         $this->ctlWines = new F60FristContent($this->wine_ids, $form, false);

         $sql="select e.estate_id from estates e, wines w where w.estate_id =e.estate_id and w.wine_id=".$this->wine_ids[0];
         $result = & F60DbUtil::runSQL($sql);
         if(!$result->EOF)
         {
            $row = & $result->FetchRow();
            $estate_id =$row['estate_id'];
         }
         $edtestateid =& $form->getField("estate_id_order");
         $edtestateid ->setValue($estate_id);

         $this->ctlWines->editMode =0;
         if ($_REQUEST["customer_id"]!="")
         {
             $this->ctlCMs = new F60ControlsContent($wine_ids, $form,"","",$_REQUEST["customer_id"]);

             $edtTotalpages =& $this->form->getField("total_pages");
             $edtTotalpages -> setValue("1");
             $edtcms = & $form->getField("customers");
             $edtcms->setValue(1);
         }
         else
         {
             $this->prepairData($form,$wine_ids);
         }

         $edtpageid =& $form->getField("pageid");
         $edtpageid ->setValue($_REQUEST["pageid"]);


         $sUrl ='main.php';
         $this->registerActionhandler(array("btnSave", array($this, processForm), "URL", $sUrl));
         $this->registerActionhandler(array("saveCurrent", array($this, processForm), "SELF",NULL));
         $this->registerActionhandler(array("order", array($this, gotoCustomer), "LASTPAGE", null));

         $funName ='setTotalVals('.$isWine.');';

		 $this->attachBodyEvent('onLoad', $funName);
    }

	function gotoCustomer()
	{
		$this->processForm();
		
		$sURL ='main.php?page_name=customerAdd&id='.$_REQUEST['customer_id'].'&estate_id_order='.$_REQUEST['estate_id_order'];
		
		HtmlUtils::redirect($sURL);
		return false;
	}

	function prepairData($form,$wine_ids)
	{
	    if ($this->isCm==1)
	    {
			$sqlSelect="";
			$sqlWhere="";
			if ($_REQUEST['sql_select']=="") //not first time open page
			{
				$sqlArrays = $this->getSQL();
				
				$edtControl =& $form->getField("sql_select");
				$edtControl ->setValue($sqlArrays[0]);
				
				$edtControl =& $form->getField("sql_where");
				$edtControl ->setValue($sqlArrays[1]);
				
				$sqlSelect =$sqlArrays[0];
				$sqlWhere =$sqlArrays[1];
			}
			else
			{
			
				$sqlSelect =$_REQUEST['sql_select'];
				$sqlWhere =$_REQUEST['sql_where'];
			}
	
	     	$sql = 'select count(*) cnt from '.$sqlWhere;
	     	$result = & F60DbUtil::runSQL($sql);
		    $row = & $result->FetchRow();
	
			if ($row['cnt']!=0)
			{
				$this->ctlCMs = new F60ControlsContent($wine_ids, $form,$sqlSelect,$sqlWhere,"");
				if ($this->ctlCMs->isEmpty)
				{
					$this->isCm = 2;
					$edtCm =& $form->getField("isNoCm");
					$edtCm ->setValue(1);
				}				
			}
			else
			{
				$this->isCm = 2;
				$edtCm =& $form->getField("isNoCm");
				$edtCm ->setValue(1);
			}//if ($row['cnt']!=0)
		}//if ($this->isCm==1)
	}
	
	function display()
	{
		if (!$this->handlePost())
			$this->displayForm();			
	}

	function displayForm()
	{
		 $form = & $this->getForm();
		
		 $edtNumbers = & $form->getField("wine_numbers");
		 $edtNumbers ->setValue(sizeof($this->wine_ids));
				
		 if ($this->isCm ==1)
		 {
		     $form->Template->assign("wines_customers", $this->ctlCMs->getContent());
		 }
		 $form->Template->assign("content_wine", $this->ctlWines->getContent());
				
		 if ($_REQUEST['customer_id']!="")
		 {
		     $action = array( "Create order" => "javascript:callSubmit('allocatewine2customer','order');");
		     $this->setActions($action);
		 }
		
		 F60FormBase::display();
	}

     function getSQL()
     {
         $sql = "";
         $search_id =$_REQUEST["search_id"];
         $search_key =$_REQUEST["search_key"];
         $search_key=str_replace("'","\'",$search_key);
         $is_start=$_REQUEST["is_start"];

         $sql = "select cm.customer_id,cm.customer_name from ";
         $sfrom = "customers cm ";

         $sWhere = " Where ";
         $isContact ="";

         if ($_REQUEST["customer_id"]!="")
         {
				$sqlArrays[0]="select customer_id,customer_name from";
				$sql =" customers where customer_id = ".$_REQUEST["customer_id"];
				
				$sql = ' and cm.deleted = 0 and cm.status <>2  order by customer_name ';
				$sqlArrays[1]=$sql;
				$edtcms = & $form->getField("customers");
				$edtcms->setValue(1);
         }
         else
         {
            if ( $search_id != 3)
            {
                if($search_id == 0)
                {   if ($is_start==1)
                    {
                        $sWhere = $sWhere . "customer_name like '" .$search_key."%'";
                    }
                    else
                        $sWhere = $sWhere . "customer_name like '%" .$search_key."%'";

                }
                elseif($search_id == 1)//contact
                {
                    $fieldname = "first_name";
                    if ($_REQUEST["contact_key"]==2)
                        $fieldname = "last_name";
                        
                    $sfrom =$sfrom.",contacts c,customers_contacts cmc ";
                    $sWhere =$sWhere." cm.customer_id = cmc.customer_id
                            And c.contact_id = cmc.contact_id";
                    if ($is_start==1)
                    {
                      $sWhere =$sWhere . " And " . $fieldname . " like '" .$_REQUEST["search_key"]."%'";
                    }
                    else
                        $sWhere =$sWhere . " And " . $fieldname . " like '%" .$_REQUEST["search_key"]."%'";

                    $isContact ='cm.';

                }
                elseif($search_id == 2)
                {   if ($is_start==1)
                        $sWhere = $sWhere . "licensee_number like '" .$search_key."%'";
                    else
                        $sWhere = $sWhere . "licensee_number like '%" .$search_key."%'";
                }
            }

            if($search_id == 3)
            {
                $keyName ="";
                $addtions=array("","","","","","");
                $keyVal="";
                $fieldnames=array("billing_address_state","billing_address_city","billing_address_street",
									"billing_address_postalcode","po_box","lkup_store_type_id");
                $sWhere = $sWhere." billing_address_state = 'BC' ";
                for ($i=1;$i<5;$i++)
                {
                    $keyName = "key".$i;
                    $field ="field".$i;
                    if ($_REQUEST[$keyName]==1)
                    {
                        $vals =split("[|]",$_REQUEST[$field]);
                        if (sizeof($vals)>1)
                        {
                            $adts=array();
                            $ncnt = 0;
                            for ( $j=0;$j<sizeof($vals);$j++)
                            {

                               if(trim($vals[$j])!="")
                               {
                                    $adts[$ncnt] =$vals[$j];
                                    $ncnt++;
                               }
                            }
                            $strSql="";
                            for ($k=0;$k<$ncnt;$k++)
                            {
                                if($k==0)
                                {
                                    $strSql =" and (".$fieldnames[$i]." like '%".$adts[$k]."%'";
                                }
                                else
                                    $strSql =$strSql." or ".$fieldnames[$i]." like '%".$adts[$k]."%'";

                            }
                            $addtions[$i] =$strSql.")";
                        }
                        else
                        {
                            $addtions[$i] = " and ".$fieldnames[$i]." like '%".$_REQUEST[$field]."%'";
                        }
                    }
                    if ($addtions[$i]!="")
                    {
                       $sWhere = $sWhere.$addtions[$i];
                    }
                }
                $i++;
            }

            if ($_REQUEST["s_type"]!=0)
                $sWhere = $sWhere." and lkup_store_type_id = ".$_REQUEST["s_type"];
            
            if ($_REQUEST["s_user"]!=0)
            {
                $sfrom = $sfrom.",users_customers ucm";
                $sWhere = $sWhere." and cm.customer_id =ucm.customer_id and  cm.customer_id= ".$_REQUEST["s_user"];
            }

            $sqlArrays=array("","");
            $sqlArrays[0]=$sql;

			$sql = $sfrom .$sWhere;
			
			$sql = $sql.' and '.$isContact.'cm.deleted = 0 and cm.status <>2 and cm.lkup_store_type_id<>8 order by customer_name ';
			$sqlArrays[1]=$sql;
         }

		return $sqlArrays;

	}//end function
		
	function processForm()
	{
		return $this->saveAlt2Cm();
	}

	function saveAlt2Cm()
	{
		$form = & $this->getForm();
		
		$allocates = & new bllallocate2customer();
		$allocates->getDataFromForm($form,$this->isCm);
		
		if ($_REQUEST["customer_id"]!="")
		{
			$cm_id =$_REQUEST["customer_id"];
			return $allocates->save2DB($this->isCm,$cm_id);
		}
		else			
			return $allocates->save2DB($this->isCm);
	}
}

?>
