<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60DbUtil');
import('Form60.base.F60DALBase');
import('Form60.bll.bllcommission_levels');

class commLevels extends F60FormBase
{

	function commLevels()
	{
		if (F60FormBase::getCached()) exit(0);
		
		$title = "Commission levels";
		
		
		F60FormBase::F60FormBase('commLevels', $title, 'comm_levels.xml', 'comm_levels.tpl','btnAdd');
		
		$this->addScript('resources/js/javascript.CommissionLevels.js');
		
		$form = & $this->getForm();
		$form->setFormAction($_SERVER["REQUEST_URI"]);	
		
		
		import('Form60.base.F60PageStack');
		F60PageStack::addtoPageStack();
		
		$this->registerActionhandler(array("btnAdd", array($this, processForm), "LASTPAGE", null));		
		
		$form->setButtonStyle('btnOK');
		$form->setInputStyle('input');
		$form->setLabelStyle('label');
		
		$this->attachBodyEvent('onLoad', 'loadCommlevels();');
     
   }

	function display()
	{
		if (!$this->handlePost())
			$this->displayForm();
	}
	
	function displayForm()
	{
		$this->loadData();
		F60FormBase::display();
	}
	
	function loadData()
	{	
		$edtsave = & $this->form->getField("is_save");
		
		if($edtsave->getValue()==0)
		{
			$this->loadLicenseeData();
			$this->loadBCLDBData();	
			$this->loadLicenseeDataByProvince(2);
		}
	}
       
	function loadLicenseeData()
	{
		$form = & $this->getForm();
		$comm_levels = & new bllcommission_levels();
		
		$sql="select * from commission_levels where lkup_store_type_id=-1 order by level_id";
		$result = & F60DbUtil::runSQL($sql);
		$i=1;
		while(!$result->EOF)
		{
			$row = & $result->FetchRow();
			$leve_id = $row['level_id'];
			$comm_level = $comm_levels->getByPrimaryKey($leve_id);
			if($comm_level!=null)
				$comm_level->loadDataToForm($form,$i);
			$i++;
		}
		$edtLevels = & $form->getField("levels");
		$edtLevels->setValue($i-2);
	
	}
	
	
	function loadLicenseeDataByProvince($pro_id)
	{
		$form = & $this->getForm();
		$comm_levels = & new bllcommission_levels();
		
		if($pro_id==2)	
			$sql="select * from commission_levels where lkup_store_type_id=8 order by level_id";
			
		$result = & F60DbUtil::runSQL($sql);
		$i=1;
		$suffix="_pro_2";
		
		while(!$result->EOF)
		{
			$row = & $result->FetchRow();
			$leve_id = $row['level_id'];
			
			$salesName="sales_$i".$suffix;
			$bonusName="bonus_$i".$suffix;
			$ctlSales = & $form->getField($salesName);
			$ctlbonus = & $form->getField($bonusName);
			
			$ctlSales->setValue($row["target_price"]);
			$ctlbonus->setValue($row["bonus"]);
			
			$i++;
		}
		$edtLevels = & $form->getField("pro2_levels");
		
		if($i>1)		
			$edtLevels->setValue($i-1);
		else
			$edtLevels->setValue(0);		
	}
	
	function loadBCLDBData()
	{
		$form = & $this->getForm();
		$comm_levels = & new bllcommission_levels();
		
		$sql="select * from commission_levels where lkup_store_type_id=6 order by level_id";
		$result = & F60DbUtil::runSQL($sql);
		$i=1;
		while(!$result->EOF)
		{
			$row = & $result->FetchRow();
			$leve_id = $row['level_id'];
			
			$salesName="sales_$i";
			$bonusName="bonus_$i";
			$ctlSales = & $form->getField($salesName);
			$ctlbonus = & $form->getField($bonusName);
			
			$ctlSales->setValue($row["target_price"]);
			$ctlbonus->setValue($row["bonus"]);
			
			$i++;
		}
		$edtLevels = & $form->getField("bcldb_levels");
	
		$edtLevels->setValue($i-1);
	}
	   
	function checkBonus(&$form)
	{
	
       $bonus = $_POST["bonus"];

       if($_POST["min_intl_cases"]==""||$_POST["min_intl_cases"]==0)
       {
           $form->addErrors("Please input International wine cases.");
           return FALSE;
       }

       if($_POST["min_canadian_cases"]==""||$_POST["min_canadian_cases"]==0)
       {
           $form->addErrors("Please input Canadian wine cases.");
           return FALSE;
       }
	  
       $levels=$_POST["levels"];
       
       for($i=1;$i<=$levels;$i++)
       {
         
			$edtBeginNm ="min_cases".($i);
			$edtEndNm="max_cases".($i);
			$edtCommRateNm="comm".$i;
			
			if( $_POST[$edtEndNm]==""||$_POST[$edtEndNm]==0)
			{
				$error="Level".$i."'s max cases can't be empty or 0.";
				$form->addErrors($error);				
				return false;
				break;
			}
			else if ( $_POST[$edtCommRateNm]==""||$_POST[$edtCommRateNm]==0)
			{
				$error="Level".$i."'s commission rate can't be empty or 0.";
				$form->addErrors($error);			
				return false;
				break;
			}
			else if ( $_POST[$edtBeginNm]>=$_POST[$edtEndNm])
			{
				$error="Level".$i."'s max cases must bigger than min cases.";
				$form->addErrors($error);
				return false;
				break;
			}
           
		}
	       
		if($this->bcldb_checkBonus(&$form))
	   {	      	
	      if($this->pro_checkBonus(&$form,2))
	      	return true;
	      else
	         return false;
	   }
	   else
	      	return false;
	}
        
	function bcldb_checkBonus(&$form )
	{
		
		$levels=$_POST["bcldb_levels"];
		for($i=1;$i<=$levels;$i++)
		{
		
			$edtSalesNm ="sales_".($i);
			$edtBonusm="bonus_".($i);
			
			if( $_POST[$edtSalesNm]==""||$_POST[$edtSalesNm]==0)
			{
				$error="Level".$i."'s salses can't be empty or 0.";
				$form->addErrors($error);
				
				return false;
				break;		
			}
			else if ( $_POST[$edtBonusm]==""||$_POST[$edtBonusm]==0)
			{
				$error="Level".$i."'s commission can't be empty or 0.";
				$form->addErrors($error);
				
				return false;
				break;
			}//end if( $_POST[$edtSalesNm]==""||$_POST[$edtSalesNm]==0)
			
			if($i==1)
			{
				$pre_sales = $_POST[$edtSalesNm];	
			}
			else
			{
				if ($_POST[$edtSalesNm]<=$pre_sales )
				{
					$j=$i-1;
					$error="Level".$j."'s sales can't be biger than level $i.";
					$form->addErrors($error);
					
					return false;
					break;
				}
				else
				{
					$pre_sales = $_POST[$edtSalesNm];
				}
			}//end if($i==1)
		}//end for
		return true;
	}
	
	function pro_checkBonus(&$form,$proid )
	{
		$levels=$_POST["pro2_levels"];
		
		$prefix="pro2_";
		$suffix="_pro_2";
		
	
		for($i=1;$i<=$levels;$i++)
		{		
			$edtSalesNm ="sales_".($i).$suffix;
			$edtBonusm="bonus_".($i).$suffix;

			if( $_POST[$edtSalesNm]==""||$_POST[$edtSalesNm]==0)
			{
				$error="Level".$i."'s cases can't be empty or 0.";
				$form->addErrors($error);
				
				return false;
				break;		
			}
			else if ( $_POST[$edtBonusm]==""||$_POST[$edtBonusm]==0)
			{
				$error="Level".$i."'s commission can't be empty or 0.";
				$form->addErrors($error);
				
				return false;
				break;
			}//end if( $_POST[$edtSalesNm]==""||$_POST[$edtSalesNm]==0)
			
			if($i==1)
			{
				$pre_sales = $_POST[$edtSalesNm];	
			}
			else
			{
				if ($_POST[$edtSalesNm]<=$pre_sales )
				{
					$j=$i-1;
					$error="Level".$j."'s cases can't be biger than level $i.";
					$form->addErrors($error);
					
					return false;
					break;
				}
				else
				{
					$pre_sales = $_POST[$edtSalesNm];
				}
			}//end if($i==1)
		}//end for
		
		return true;
	}

	function saveBcldbComm()
	{
		$form = & $this->getForm();
		
		$bcldb_commlevels = & new  bllcommission_levels();
		
		$edtLevels = & $form->getField("bcldb_levels");
		$levels =$edtLevels->getValue();
		for($i=1;$i<=$levels;$i++)
		{
			$bcldb_commlevel = $bcldb_commlevels->add_new();
			$bcldb_commlevel->saveBCLDB($form,$i);		
		}
		
		return true;
 
	}
	
	function saveProvinceComm($proid)
	{
		$form = & $this->getForm();
		$pro_commlevels = & new  bllcommission_levels();
		$edtLevels = & $form->getField("pro2_levels");
		$levels =$edtLevels->getValue();

		for($i=1;$i<=$levels;$i++)
		{
			$pro_commlevel = $pro_commlevels->add_new();
			$pro_commlevel->saveProvinceComm($form,$i,2);
		}
		
		return true;
	}
	
	
    function processForm()
    {

		$form = & $this->getForm();
		
		$edtsave = & $form->getField("is_save");
		$edtsave->setValue("1");

      if($this->checkBonus(&$form))
      {
			$commlevels = & new  bllcommission_levels();
			$bcldb_commlevel = & new  bllcommission_levels();
			
			$edtLevels = & $form->getField("levels");
			$levels =$edtLevels->getValue();
			
			$levels=$levels+1;
			
			for($i=1;$i<=$levels;$i++)
			{
				$commlevel = $commlevels->add_new();
				if($i==1)
				$commlevel->delete();
				
				$commlevel->saveLicensee($form,$i);
			}
			if ($this->saveBcldbComm())
				return $this->saveProvinceComm(2);
      }
      else
      	return false;
    }

}


?>
