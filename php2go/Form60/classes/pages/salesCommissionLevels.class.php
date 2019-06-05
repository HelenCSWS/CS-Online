<?php

/**

 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60DbUtil');
import('Form60.base.F60DALBase');
import('Form60.bll.bllSalesCommission');

class salesCommissionLevels extends F60FormBase
{
 
 	var $user_id;
 	var $lkup_sales_commission_type_id;
	var $bllCommData;
	
	var $levelData;
	var $levelDataDetails=array();
	
	var $is_new =false;
	
	
	function salesCommissionLevels()
	{
		if (F60FormBase::getCached()) exit(0);
		
		$title = "Commission levels";
		
		
		$this->user_id = $_REQUEST["user_id"];
		
		$user_name = &F60DbUtil::getUserNameById($this->user_id);
	
		$title = "Commission levels for $user_name";
	
		
		$this->lkup_sales_commission_type_id = $_REQUEST["lkup_sales_commission_type_id"];
		
		
		$xmlFileName = "comm_levels_type$this->lkup_sales_commission_type_id.xml";
		$tplFileName = "comm_levels_type_$this->lkup_sales_commission_type_id.tpl";
		
	
		F60FormBase::F60FormBase('salesCommission', $title, $xmlFileName, $tplFileName,'btnAdd');
		
		$this->addScript('resources/js/javascript.salesCommissionLevels.js');
		
		$form = & $this->getForm();
		$form->setFormAction($_SERVER["REQUEST_URI"]);	
		
		import('Form60.base.F60PageStack');
		F60PageStack::addtoPageStack();
		
		
		$this->registerActionhandler(array("btnAdd", array($this, processForm), "LASTPAGE", null));		
		
		$URL ="main.php?page_name=selectCommissionType";         
        $this->registerActionhandler(array("btnAddNext", array($this, processForm), "URL", $URL));
        
		$form->setButtonStyle('btnOK');
		$form->setInputStyle('input');
		$form->setLabelStyle('label');
		
		$this->setCtlValue("lkup_sales_commission_type_id",$this->lkup_sales_commission_type_id);
	
		$functionStr ="loadCommlevels($this->lkup_sales_commission_type_id);";
		
		$this->bllCommData = new salesCommissionData();
		
		$this->attachBodyEvent('onLoad',$functionStr );     
		
		$this->createLevelDataArray();		
    }        


	function createLevelDataArray()
	{
	 	$levelData = array();
	 	
	 	$levelData["sales_commission_id"]="";
	 	$levelData["lkup_sales_commission_type_id"]="";
	 	$levelData["target_cases_ca"]="0";
	 	$levelData["target_cases_intl"]="0";
	 	$levelData["lkup_commission_sales_sum_type_id"]="0";
	 	$levelData["user_id"]="";
	 	
	 	$levelData["level_start_cases"]="0";
	 	$levelData["level_end_cases"]="0";
	 	$levelData["level_commission_rate"]="0";
	 	$levelData["level_target_sales"]="0";
	 	$levelData["level_caption"]="";
	 	$levelData["level_number"]="0";
	 	
	 	$levelData["when_entered"]="";
	 	$levelData["when_modified"]="";
	 	$levelData["created_user_id"]="";
	 	$levelData["modified_user_id"]="";
	 	
	 	return $levelData;
	}
	
	function display()
	{
		if (!$this->handlePost())
			$this->displayForm();
	}
	
	function displayForm()
	{
		$this->loadData($this->lkup_sales_commission_type_id);
		F60FormBase::display();
	}
	
	function loadData($lkup_sales_commission_type_id)
	{		

		$levelsData = $this->bllCommData->getCommissionLevelsDetail($this->user_id,$this->lkup_sales_commission_type_id);
		
	
		$this->setCtlValue("user_id",$this->user_id);
		$this->setCtlValue("province_id",$_REQUEST["province_id"]);;
		$this->setCtlValue("lkup_sales_commission_type_id",$this->lkup_sales_commission_type_id);
		if($levelsData!=0)
		{
			$this->setCtlValue("sales_commission_level_id",$levelsData[0]["sales_commission_level_id"]);
			$this->setCtlValue("lkup_commission_sales_sum_type_id",$levelsData[0]["lkup_commission_sales_sum_type_id"]);
			$this->loadLevelsData($levelsData,$lkup_sales_commission_type_id);
		}	
		

		
	}
	
	function loadLevelsData($levelsData, $commission_type_id)
	{
		$form = & $this->getForm();
	
		$i=0;
		foreach($levelsData as $levelDataDetail)
		{
		 	if($commission_type_id==1)
		 	{
			 	if($i==0)
			 	{
					$this->setCtlValue("min_intl_cases",$levelsData[$i]["target_cases_intl"]);			
					$this->setCtlValue("min_canadian_cases",$levelsData[$i]["target_cases_ca"]);			
				}
				else
				{
					$levelStartCaseCtl="min_cases$i";
					$levelEndCaseCtl="max_cases$i";
					$levelCommCtl="comm$i";
						
					$this->setCtlValue($levelStartCaseCtl,$levelsData[$i]["level_start_cases"]);							
				
					$this->setCtlValue($levelEndCaseCtl,$levelsData[$i]["level_end_cases"]);			
				
					$this->setCtlValue($levelCommCtl,$levelsData[$i]["level_commission_rate"]);			
		
				}
			}
			else if($commission_type_id>1)
			{
				if($i!=0)
				{
					$levelStartCaseCtl="sales_$i";
					$levelCommCtl="bonus_$i";
				
					$this->setCtlValue($levelCommCtl,$levelsData[$i]["level_commission_bonus"]);								
					
					if($commission_type_id<4)
						$this->setCtlValue($levelStartCaseCtl,$levelsData[$i]["level_target_sales"]);						
					else if($commission_type_id==4)
						$this->setCtlValue($levelStartCaseCtl,$levelsData[$i]["level_end_cases"]);						
				}
				else
				{
					if($commission_type_id==3)
					{
						$this->setCtlValue("min_canadian_cases",$levelsData[$i]["target_cases_ca"]);				
					}
				}
			}			
			 $i++;
					
		}

	
		$this->setCtlValue("levels",$i-1);
			
	}
	
	
	
	
	
	function checkBonus($commission_type_id)
	{
		$form = & $this->getForm();
		
       	$levels=$_POST["levels"];
       	
		if($commission_type_id==1)
		{

			if($_POST["min_intl_cases"]==""||$_POST["min_intl_cases"]==0)
			{
			   $form->addErrors("Please input International wine cases.");
			   return FALSE;
			}
			
			if($_POST["min_canadian_cases"]==""||$_POST["min_canadian_cases"]==0)
			{
			 /* Updated by Helen, remove Canadian cases Validation. No Ca case (0)) for commission level start from October 2018; code date Nov 2018*/
			  // $form->addErrors("Please input Canadian wine cases.");
			 //  return FALSE;
			}
			
			
			
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
		}
		else if($commission_type_id>1)
		{
		 	if($commission_type_id==3)
		 	{
		 		if($_POST["min_canadian_cases"]==""||$_POST["min_canadian_cases"]==0)
				{
				 /* Updated by Helen, remove Canadian cases Validation. No Ca case (0)) for commission level start from October 2018; code date Nov 2018*/
				  // $form->addErrors("Please input Canadian wine cases.");
				  // return FALSE;
				}
			}
			for($i=1;$i<=$levels;$i++)
			{
		
				$edtSalesNm ="sales_".($i);
				$edtBonusm="bonus_".($i);
				
				if( $_POST[$edtSalesNm]==""||$_POST[$edtSalesNm]==0)
				{
				 	if($commission_type_id <4)
						$error="Level".$i."'s salses can't be empty or 0.";
					else if($commission_type_id==4)
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
						if($commission_type_id<4)
							$error="Level".$j."'s sales can't be biger than level $i.";
						else if($commission_type_id==4)
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
		}
		
		
	    return true;
	
	}
        
/*	function checkBonus_type_2()
	{
		$form = & $this->getForm();
		
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
	
	function checkBonus_type_4()
	{
	 	$form = & $this->getForm();
	 	
		$levels=$_POST["levels"];

		for($i=1;$i<=$levels;$i++)
		{		
			$edtSalesNm ="sales_".($i);
			$edtBonusm="bonus_".($i);

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
*/

	

	
	function getCtlValue($ctlName)
	{

	 	$form = & $this->getForm();
		$ctl = & $form->getField($ctlName);

		return $ctl->getValue();
		
	}
	
	function setCtlValue($ctlName, $value)
	{

	 	$form = & $this->getForm();
		$ctl = & $form->getField($ctlName);
		
	
		return $ctl->setValue($value);
		
	}
	
	function getLevelDetailData($commission_type_id)
	{
		$form = & $this->getForm();	

		$levels = $this->getCtlValue("levels");
		
		$levelData = array();
		
		for($i=0;$i<=$levels;$i++)
		{		 	
		 	if($commission_type_id==1)		 	
		 	{
				$levelStartCaseCtl="min_cases$i";
				$levelEndCaseCtl="max_cases$i";
				$levelCommCtl="comm$i";
				$levelName="Level $i";
				$levelNumer=$i;						
			
				$levelDetailAry = $this->createLevelDataArray();
							
				$levelDetailAry["lkup_sales_commission_type_id"]=$this->getCtlValue("lkup_sales_commission_type_id");
				$levelDetailAry["lkup_commission_sales_sum_type_id"]=$this->getCtlValue("lkup_commission_sales_sum_type_id");
				$levelDetailAry["user_id"]=$this->getCtlValue("user_id");
				$levelDetailAry["level_caption"]="'$levelName'";
				
				
				if($i==0)						
				{
					$levelDetailAry["target_cases_intl"]=$this->getCtlValue("min_intl_cases");
					$levelDetailAry["target_cases_ca"]=$this->getCtlValue("min_canadian_cases");				
			
				}
				else
				{
					$levelDetailAry["level_start_cases"]=$this->getCtlValue($levelStartCaseCtl);
					$levelDetailAry["level_end_cases"]=$this->getCtlValue($levelEndCaseCtl);
					
					$levelDetailAry["level_commission_rate"]=$this->getCtlValue($levelCommCtl);
					
					$levelDetailAry["level_number"]=$levelNumer;
					$levelDetailAry["user_id"]=$this->getCtlValue("user_id");
				}			
			}
			else if($commission_type_id>1)
			{				
				$levelStartCaseCtl="sales_$i";
			
				$levelCommCtl="bonus_$i";
				$levelName="Level $i";
				$levelNumer=$i;
							
				$levelDetailAry = $this->createLevelDataArray();
				$levelDetailAry["lkup_sales_commission_type_id"]=$this->getCtlValue("lkup_sales_commission_type_id");
				$levelDetailAry["lkup_commission_sales_sum_type_id"]=$this->getCtlValue("lkup_commission_sales_sum_type_id");
				$levelDetailAry["user_id"]=$this->getCtlValue("user_id");
				$levelDetailAry["level_caption"]="'$levelName'";
	
				if($i!=0)						
				{ 				 		
					$levelDetailAry["level_commission_bonus"]=$this->getCtlValue($levelCommCtl);				
					$levelDetailAry["level_number"]=$levelNumer;
					$levelDetailAry["user_id"]=$this->getCtlValue("user_id");
					
					if($commission_type_id<4)
						$levelDetailAry["level_target_sales"]=$this->getCtlValue($levelStartCaseCtl);
				 	else if($commission_type_id ==4)
						$levelDetailAry["level_end_cases"]=$this->getCtlValue($levelStartCaseCtl);
					
				}		
				else
				{
					if($commission_type_id==3)
					{
						if($i==0)						
						{
							$levelDetailAry["target_cases_ca"]=$this->getCtlValue("min_canadian_cases");				
					
						}
					}
				}
					
			}

			array_push($levelData,$levelDetailAry);							
									
		}	
		return 	$levelData;
	}

	function processForm()
	{
		if($this->checkBonus($this->lkup_sales_commission_type_id))
		{		
			$levelData = $this->getLevelDetailData($this->lkup_sales_commission_type_id);	
			$bllCommData = new salesCommissionData();
			$bllCommData->saveCommissionData($levelData);
			return true;
		}
		else
			return false;
	}

}


?>
