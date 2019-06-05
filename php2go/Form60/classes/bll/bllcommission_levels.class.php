<?php

import('Form60.dal.dalcommission_levels');
import('Form60.base.F60DbUtil');

class bllcommission_level extends dalcommission_levels
{
	function bllcommission_level()
	{
		parent::dalcommission_levels();
	}

    function loadByPrimaryKey($keyValues)
    {

        if (parent::loadByPrimaryKey($keyValues))
        {
            return true;
        }

        return false;
    }

    function getFromDAL($dal)
    {
        if (TypeUtils::isObject($dal) && TypeUtils::isInstanceOf($dal, "dalcommission_levels"))
            return $this->loadByPrimaryKey($dal->get_data("level_id")); //extra DB trip here
        else
            return false;
    }

    function saveBCLDB($form, $i)
    {
		
       $this->bcldb_getDataFromForm(&$form,$i);
      
        return parent::save();   
    }
   
	 function saveProvinceComm($form, $i,$proID)
    {

       $this->pro_getDataFromForm(&$form,$i,$proID);
       
      
      
        return parent::save();   
    }
    
    
    function saveLicensee($form, $i)
    {
		$this->getDataFromForm(&$form,$i);
          return parent::save();
	}
    function delete()
    {
        $sql ="Truncate commission_levels";
        $restult= & F60DbUtil::runSQL($sql);
    }
    
    
    function bcldb_getDataFromForm(&$form,$i)
    {
       
        $edtSalesNm ="sales_".($i);
        $edtBonusNm="bonus_".($i);
      
        $edtSalse=& $form->getField($edtSalesNm);
        $edtBonus=& $form->getField($edtBonusNm);
        
     
        if($i!=0)
        {
  			$this->set_data("lkup_store_type_id","6");
  			
  			
 		   	$this->set_data("target_price",$edtSalse->getValue());   
				$bonus=$edtBonus->getValue();        
            $this->set_data("commission_rate","0");
            $this->set_data("bonus",$bonus);
				        $caption = "level".$i;            
        }

        $this->set_data("caption",$caption);
        $this->set_data("min_intl_cases","0");
        $this->set_data("min_canadian_cases","0");
            
            
			$this->set_data("min_cases","0");
			
			$this->set_data("max_cases","0");
			
			$this->set_data("is_float",1);
       	//	$this->set_data("bonus","0");
     }

	 function pro_getDataFromForm(&$form,$i,$proid)
    {
       $suffix="_pro_2";
        $edtSalesNm ="sales_".($i).$suffix;
        $edtBonusNm="bonus_".($i).$suffix;
      
        $edtSalse=& $form->getField($edtSalesNm);
        $edtBonus=& $form->getField($edtBonusNm);
        
     
        if($i!=0)
        {
  			$this->set_data("lkup_store_type_id","8");
  			
  			
 		   	$this->set_data("target_price",$edtSalse->getValue());   
				$bonus=$edtBonus->getValue();        
            $this->set_data("commission_rate","0");
            $this->set_data("bonus",$bonus);
				        $caption = "level".$i;            
        }

        $this->set_data("caption",$caption);
        	$this->set_data("min_intl_cases","0");
            $this->set_data("min_canadian_cases","0");
            
            
            $this->set_data("min_cases","0");
          
            $this->set_data("max_cases","0");
             
            $this->set_data("is_float",1);
            
          
       	//	$this->set_data("bonus","0");
     }
     
     
    function getDataFromForm(&$form,$i)
    {
       
		$i--;
		$edtBeginNm ="min_cases".($i);
		$edtEndNm="max_cases".($i);
		$edtCommRateNm="comm".$i;
		
		$edtBegin=& $form->getField($edtBeginNm);
		$edtEnd=& $form->getField($edtEndNm);
		$edtCommRate=& $form->getField($edtCommRateNm);
		
		
		$edtIntlCases=& $form->getField("min_intl_cases");
		$edtCaCases=& $form->getField("min_canadian_cases");
		$edtBouns=& $form->getField("bonus");
		
		$this->set_data("min_intl_cases",$edtIntlCases->getValue());
		$this->set_data("min_canadian_cases",$edtCaCases->getValue());
		$this->set_data("bonus",$edtBouns->getValue());
		if($i==0)
		{
			$this->set_data("min_cases","0");
			$edtBegin=& $form->getField("min_cases1");
			$this->set_data("max_cases",($edtBegin->getValue()-1));
			$this->set_data("commission_rate","0");
			$this->set_data("level_id",$i+1);
			$this->set_data("lkup_store_type_id","-1");
		}
		else
		{
			$edtBeginNm ="min_cases".($i);
			$edtEndNm="max_cases".($i);
			$edtCommRateNm="comm".$i;
			
			$edtBegin=& $form->getField($edtBeginNm);
			$edtEnd=& $form->getField($edtEndNm);
			$edtCommRate=& $form->getField($edtCommRateNm);
			
			$this->set_data("min_cases",$edtBegin->getValue());
			$this->set_data("max_cases",$edtEnd->getValue());
			$this->set_data("commission_rate",$edtCommRate->getValue());
			
			$this->set_data("level_id",$i+1);
			$this->set_data("lkup_store_type_id","-1");
			
			$this->set_data("min_intl_cases","75");
			$this->set_data("min_canadian_cases","50");
		}
		$caption = "level".$i;            
		$this->set_data("caption",$caption);
		$this->set_data("is_float","0");
		$this->set_data("target_price","0");
    }


    function loadDataToForm(&$form,$i)
    {
         if($i==1)//minimum cases
            {
               $edtIntlCases=& $form->getField("min_intl_cases");
               $edtCaCases=& $form->getField("min_canadian_cases");
               $edtBouns=& $form->getField("bonus");
               $edtBouns_d=& $form->getField("bonus_d");


               $edtIntlCases->setValue($this->get_data("min_intl_cases"));
               $edtCaCases->setValue($this->get_data("min_canadian_cases"));
             // $this->get_data("bonus")
              $edtBouns->setValue($this->get_data("bonus"));
              $bouns="$".$this->get_data("bonus");
              $edtBouns_d->setValue($bouns);

            }
            else
             {
              	$i--;
//                $chklevelNm="chklevel".$i;
                $edtBeginNm ="min_cases".($i);
                $edtEndNm="max_cases".($i);
                $edtCommRateNm="comm".$i;

                 $edtBegin=& $form->getField($edtBeginNm);
                $edtEnd=& $form->getField($edtEndNm);
                $edtCommRate=& $form->getField($edtCommRateNm);
   //             $chklevel=& $form->getField($chklevelNm);

             // $chklevel->setChecked(true);
                $edtBegin->setValue($this->get_data("min_cases"));
                $edtEnd->setValue($this->get_data("max_cases"));
                $edtCommRate->setValue($this->get_data("commission_rate"));
            }




    }//end fucntion
}

class bllcommission_levels extends dalcommission_levelsCollection
{
	function bllcommission_levels()
	{
		parent::dalcommission_levelsCollection();
	}


    function &getByPrimaryKey($keyValues)
    {
        $dal = parent::getByPrimaryKey($keyValues);
        if ($dal)
        {
            $bll = & new bllcommission_level();
            if ($bll->getFromDAL($dal))
                return $bll;
        }
        return null;
    }

    function add_new()
    {
        //override collection add_new, return bll class
        $bll = & new bllcommission_level();
        return $bll;
    }
}

?>
