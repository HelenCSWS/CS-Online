<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60DALBase');
import('Form60.base.SupplierSalesList');
import('Form60.base.F60DbUtil');
import('Form60.bll.bllsupplierData');

/*
    search_id = 1 : search Estate

*/
class supplierSales extends F60FormBase
{
	var $search_id ;
	var $estate_name;
	var $sURL;

	var $province_id;
	
	var $isWine=false;
	
	var $spSales;
	var $estate_id;
	
	var $form;
	var $is_international=0;
	
	var $logid =1;
	

	function supplierSales()
	{
		$login_user_id = & F60DALBase::get_current_user_id();
		if($login_user_id ==3500)
		{
			$login_user_id = 33;
			$this->logid =2;	
		}
	
		
		$this->spSales = new suppliersData();
		
		$estates= $this->spSales->getEstate($login_user_id);
		
	//	print_r($estates);
		$this->estate_id =$estates[0]["estate_id"];
	//	 echo $this->estate_id;
		 
		 
	
		
		if($this->estate_id == 96)
		{
			$title ="Enotecca winery";
		}
		else
			$title = $estates[0]["estate_name"];
	            
		$this->province_id=$_COOKIE["F60_PROVINCE_ID"];
			
			
		F60FormBase::F60FormBase('supplierSales', $title, 'supplierSales.xml', 'supplierSales.tpl');
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/cookie.js");
		$this->addScript('resources/js/javascript.suppliersales.js');
		Registry::set('current_province_id', $this->province_id);	
		Registry::set('current_user_province_id', $this->province_id);	
		
		$form = & $this->getForm();
		$form->setFormAction($_SERVER["REQUEST_URI"]);
		
		$this->registerActionhandler(array("btnClose", array($this, processForm), "LASTPAGE",  null));
		$this->form->setButtonStyle('btnOK');
		$this->form->setInputStyle('input');
		$this->form->setLabelStyle('label');

		if(!F60DbUtil::checkIsBCByEstate($this->estate_id))
		{
			$edtIsBCEstate = & $form->getField("isBCEstate");
			$edtIsBCEstate->setValue("1");
		}
		
		$edtEstate = & $form->getField("estate");
		if($this->estate_id == 96)
		{
			$this->estate_id =-1;            	
		}
		$edtEstate ->setValue($this->estate_id);
		
		
		$edtUser = & $form->getField("user_id");			
		$edtUser ->setValue($login_user_id);
					
		$cntl = & $this->form->getField("quarter_desc");
		$cntl->setStyle("text");
			
		if($_REQUEST["isWine"]==1)
		{
			 $cntl = & $this->form->getField("isWine");
			 $cntl->setValue("1");
			 $this->isWine=true;
		}
		
		$this->setProvinces($this->estate_id);
		$this->form = & $this->getForm();
		
		$this->attachBodyEvent('onLoad', "initSpForm();");	
	}

	function setProvinces($estate_id)
	{
		$form = & $this->getForm();
		
		$pros = $this->spSales->getProvinces($estate_id);
		$cmbProvince=& $form->getField("province_id");
			
		if(count($pros)!=0)
		{
			for ($i=0;$i<count($pros); $i++)
			{				
				$cmbProvince->addOption($pros[$i]['province_id'],$pros[$i]['short_name'],$i);
			}
		}	
			
	}
	function display()
	{    	
		if (!$this->handlePost())
		{             	
			$this->displayForm();
			if(F60DbUtil::checkIsBCByEstate($this->estate_id))
			{
			 
			 	if($this->logid==2)				
				{
					$action = array(
								"Export "=>"javascript:exportReport();",
								"Export CC List"=>"javascript:exportCCReport();",
							
								"Export DSWR "=>"javascript:exportDSWReport();"
								);   
								
							
				}	
				else
				{
				$action = array(
								"Export "=>"javascript:exportReport();",
								"Export CC List"=>"javascript:exportCCReport();",
								"Export DSWR "=>"javascript:exportDSWReport();",
								"Search "=>"javascript:searchInvoices($this->estate_id);"
								
								);    
				}		
							
			
			}
			else
			{
				$action = array("Export "=>"javascript:exportReport();");
			}
			$this->setActions($action);
			F60FormBase::display();
		}
	}

     function displayForm()
     {
        $form = & $this->getForm();

		$cmdStoreType = & $form->getField("lkup_store_type_id");
		$cmdStoreType->setFirstOption('All',-1);
		
		$cmdUser = & $form->getField("user_id");
		$cmdUser->setFirstOption('All',-1);	
		
		$cmdestate = & $form->getField("estate_id");
		$cmdestate->setFirstOption('All',-1);
		
		$this->setWines();
				
		$month = date(m);
		$year = date(y);//

		$day=date(d);			
	
		$last_day_of_month = date("d", mktime(0, 0, 0, $month+1, 0, $year ));
		
		$year = date(Y);
	
		if (strlen($month)==1)
		{
		   $month = '0'.$month;
		}
		$last_date_of_month = $month.'/'.$last_day_of_month.'/'.$year;
		$first_date_of_month = $month.'/01'.'/'.$year;
		
		$sqlFirstDate= $year.$month.'01';
		
		$currentDate = date("m/d/Y");
		$edtDate =& $this->form->getField("from_1");
		$edtDate->setValue($first_date_of_month);

		if($this->province_id ==1 && F60DbUtil::checkIsBCByEstate($this->estate_id))// bc supplier sales in bc
		{
		 	$date1= $sqlFirstDate;			 
		 	$date2 = date("Ymd");
		 
		 	$dataType = 0;
			$orderBy="delivery_date";
			$sqlSort="a";
		}
		else
		{
			$date1= date(Y);// current year
						
			$edtMonth =& $this->form->getField("sales_month");
			
		 	$date2 = "-1";//current month
		 	
		 	$sales = new suppliersData();

		 	$provinces = $sales ->checkProvinces($this->estate_id,$date1);
		 	
		 	$this->province_id =1;
		 	if(count($provinces)==1)
		 		$this->province_id = $provinces[0]['province_id'];
		 	
		 	$sales_months = $sales ->getSaleMonths($this->province_id,$this->estate_id,$date1);
		 

		 	$last_index = sizeof($sales_months)-1;
		 
		 	$date2 =$sales_months[$last_index]["sale_month"];			 	
		 	
		 	if($date2=='')
		 		$date2=1;
		 		
		 	$dataType = 2;
			$orderBy ="customer_name";
			$sqlSort="a";
		}
	//($Document,$estate_id, $date1, $date2, $order_by,$order_type, $dateType=2, $store_type_id=-1, $user_id=-1,$province_id=1,$wine_id="", $reportType=1, $page = 1, $isSearch=false,$isFirst=false)
	
		$salesList = & new SupplierSalesList(&$this,$this->estate_id, $date1,$date2 , $orderBy,$sqlSort,$dataType, -1, -1,$this->province_id,-1, 1, 1);		
		$this->form->Template->assign("info_list", $salesList->getContent()); 
    }

	function getWineInfos( $province_id,$estate_id)
	{
	 	$spSales = new suppliersData();
		$wines = $spSales->getWines($province_id,$estate_id);
		

		$i=0;
		
	/*	$arrayWinesTest =array();
		$arrayTest =array("vintage"=>2010,"info"=>'2010|987');
		
		array_push($arrayWinesTest,$arrayTest);
		
		$arrayTest =array("vintage"=>2009,"info"=>'2009|987');
		
		array_push($arrayWinesTest,$arrayTest);
		
		$arrayTest =array("vintage"=>2008,"info"=>'2008|987');
		
		array_push($arrayWinesTest,$arrayTest);
		
		arsort($arrayWinesTest);
		
		print_r($arrayWinesTest);*/
		
		
		
		$arrayWines =array();
		$arrayIndex =array();
		
		$arrayWineInfors=array();
		$arrayWineInfor=array();
		
		
		$arrayWineInforsT=array();
		$arrayWineInforT=array();
		
	
		if(!F60DbUtil::checkIsBCByEstate($estate_id))
		{
		
			if(count($wines)!=0)
			{
				for ($i=0;$i<count($wines); $i++)
				{
				 	 $cspc_codeTxt =str_pad($wines[$i]['cspc_code'], 6, "0", STR_PAD_LEFT); 
					 $wine_info = $cspc_codeTxt.' - '.$wines[$i]['wine_name'];
					
					 $arrayWineInfoT=array("wine_infos"=>$wine_info, "indexes"=>$cspc_codeTxt);
				 	
					 array_push($arrayWineInforsT,$arrayWineInfoT);
		
				}		
			}		
		}
		else
		{
		
			for ($i=0;$i<count($wines); $i++)
			{
			 
			 	$cspc_code = $wines[$i]['cspc_code'];
			 	$vintages = $spSales->getVintages($cspc_code);
			 	
			 	if(count($vintages)==1)
			 	
			 	{
			 	
				 		//replace -vic and -okan		
						 $wine_name = 	$wines[$i]['wine_name'];
						$wine_name=str_replace('- okan','',$wine_name);
						$wine_name=str_replace('-okan','',$wine_name);
						$wine_name=str_replace('- vic','',$wine_name);
						$wine_name=str_replace('-vic','',$wine_name);
						$wine_name=str_replace(' - Vic','',$wine_name);
						
			
					$cspc_codeTxt =str_pad($wines[$i]['cspc_code'], 6, "0", STR_PAD_LEFT); 
						
				  	$wine_info = $cspc_codeTxt.' - '.$wines[$i]['vintage'].' - '.$wine_name;
			 	 	
			 	 	$index = $wines[$i]['cspc_code'].'|'.$wines[$i]['vintage'];
			 		
					$vintage = $wines[$i]['vintage'];
				
			 	 	
					array_push($arrayWines,$wine_info);
					array_push($arrayIndex,$index);
				
					$arrayWineInfoT=array("vintage"=>$vintage, "wine_infos"=>$wine_info, "indexes"=>$index);
					array_push($arrayWineInforsT,$arrayWineInfoT);
				}
				else
				{
				
				//	print_r($vintages);
					$j=0;
					{
						for ($j=0;$j<count($vintages); $j++)
						{
							$vintage = $vintages[$j]['vintage'];
							$cspc_codeTxt =str_pad($wines[$i]['cspc_code'], 6, "0", STR_PAD_LEFT); 
							
							//replace -vic and -okan		
						 $wine_name = 	$wines[$i]['wine_name'];
						$wine_name=str_replace('- okan','',$wine_name);
						$wine_name=str_replace('-okan','',$wine_name);
						$wine_name=str_replace('- vic','',$wine_name);
						$wine_name=str_replace('-vic','',$wine_name);
						
							$wine_info = $cspc_codeTxt.' - '.$vintage.' - '.$wine_name;
							$index = $wines[$i]['cspc_code'].'|'.$vintage;
							
							array_push($arrayWines,$wine_info);
							array_push($arrayIndex,$index);
						
							$arrayWineInfoT=array("vintage"=>$vintage, "wine_infos"=>$wine_info, "indexes"=>$index);
							array_push($arrayWineInforsT,$arrayWineInfoT);
						
						}		
					
					}
			 	
			 	}
			}
		 
		}	
		rsort($arrayWineInforsT);
		 	
		return $arrayWineInforsT;
			
		
	}
	function setWines()
	{	 	
	
	//Array ( [116] => Array ( [vintage] => 2012 [wine_infos] => 882019 - 2012 - Vivace Pinot Grigio [indexes] => 882019|2012 ) 
	
		$infos = $this->getWineInfos( $this->province_id,$this->estate_id);
		
		$arrayWines = $infos["wine_infos"];
		 $arrayIndex = $infos["indexes"];
		$i=0;
		$comWineid =& $this->form->getField("wine_id");
	 	$comWineid->addOption("-1","All",$i);
		
		foreach ($infos as $arrayWines )	
		{
			$wine_info = $arrayWines["wine_infos"];
			$index = $arrayWines["indexes"];
			$comWineid->addOption($index,$wine_info,$i+1);
		}
		$comWineid->setValue(-1);
	}


	function getWinesCtl4SelectScript( $province_id,$estateid)
	{		
		$infos = supplierSales::getWineInfos( $province_id,$estateid);
		
		$i=0;
		
		$strSelect = "var c = document.getElementById(\"wine_id\");";
		$strSelect .= "c.options.length=0;";
	 	$strSelect .= 'c.options[0]=new Option("All", "-1", true, false);';		
		
		foreach ($infos as $arrayWines )	
		{
		 	
		 	$wine_info = $arrayWines["wine_infos"];
			$index = $arrayWines["indexes"];
			$nindex= $i+1;
			$strSelect .= 'c.options['.$nindex.']=new Option("'.$wine_info.'", "'.$index.'", false, false);';	
			$i++;
		}
					
		return $strSelect;
	}
	
	function getVintageCtl4SelectScript( $SKU)
	{
		$sales = new suppliersData();
		$vintages= $sales->getVintages( $SKU);
		$i=0;        
		
		$strSelect = "var c = document.getElementById(\"vintage\");";
		$strSelect .= "c.options.length=0;";
		if(count($vintages)!=0)
		{
			$strSelect .= 'c.options[0]=new Option("All", "-1", true, false);';
			for ($i=0;$i<count($vintages); $i++)
			{
		 		$cspc_code = $vintages[$i]["vintage"];
		 		$nindex= $i+1;
		 		$vintage_info = $vintages[$i]['vintage'];
		    	$strSelect .= 'c.options['.$nindex.']=new Option("'.$vintage_info.'", "'.$vintage_info.'", false, false);';			
			}
		}
		
		
		return $strSelect;
	}
	
	
		
	function getUSersCtl4SelectScript($province_id,$estate_id)
	{
		$sales = new suppliersData();
		$results= $sales->getUsers( $province_id, $estate_id);
		$i=0;        
		
		$strSelect = "var c = document.getElementById(\"user_id\");";
		$strSelect .= "c.options.length=0;";
		
		if(count($results)!=0)
		{
			$strSelect .= 'c.options[0]=new Option("All", "-1", true, false);';
			for ($i=0;$i<count($results); $i++)
			{
		 		$user_id = $results[$i]["user_id"];
		 		$user_name = $results[$i]["user_name"];
		 		$nindex= $i+1;
		    	$strSelect .= 'c.options['.$nindex.']=new Option("'.$user_name.'", "'.$user_id.'", false, false);';				
			}
		}
		return $strSelect;
	}
		
	function getYearsCtl4SelectScript($province_id,$estate_id)
	{
		$sales = new suppliersData();
		$results= $sales->getSaleYears( $province_id,$estate_id);
		$i=0;
      			
		$strSelect = "var c = document.getElementById(\"sales_year\");";
		$strSelect .= "c.options.length=0;";
	
		if(count($results)!=0)
		{	
			for ($i=0;$i<count($results); $i++)
			{
		 		$sale_year = $results[$i]["sale_year"];
				$strSelect .= 'c.options['.$i.']=new Option("'.$sale_year.'", "'.$sale_year.'", false, false);';
			}
		}	
		return $strSelect;
	}
		
	function getMonthsCtl4SelectScript($province_id,$estate_id,$sales_year)
	{
		$sales = new suppliersData();
		$results= $sales->getSaleMonths( $province_id,$estate_id,$sales_year);
		$i=0;
      			
		$strSelect = "var c = document.getElementById(\"sales_month\");";
		$strSelect .= "c.options.length=0;";
		if(count($results)!=0)
		{	
			for ($i=0;$i<count($results); $i++)
			{
		 		$sale_month = $results[$i]["sale_month"];
		 		$txtMonth=F60Date::getMonthTxt($sale_month);
		 		
		 		if($i==count($results)-1)
		 		{
					$strSelect .= 'c.options['.$i.']=new Option("'.$txtMonth.'", "'.$sale_month.'", false, true);';
				}
				else
				{
					$strSelect .= 'c.options['.$i.']=new Option("'.$txtMonth.'", "'.$sale_month.'", false, false);';
				}				
			}
		}
		return $strSelect;
	}
	
		
	function getStoreTypesCtl4SelectScript( $province_id,$estate_id)
	{
		$sales = new suppliersData();
		$results= $sales->getStoreTypes( $province_id,$estate_id);
		$i=0;
     			
		$strSelect = "var c = document.getElementById(\"lkup_store_type_id\");";
		$strSelect .= "c.options.length=0;";
		
		if(count($results)!=0)
		{
		 	if($province_id ==1)
				$strSelect .= 'c.options[0]=new Option("All", "-1", true, false);';

			for ($i=0;$i<count($results); $i++)
			{
		 		$storetype = $results[$i]["license_name"];
		 		$storetypeid = $results[$i]["lkup_store_type_id"];
		 				 		
				if($province_id ==1)
				{
				 	$nindex= $i+1;
				    $strSelect .= 'c.options['.$nindex.']=new Option("'.$storetype.'", "'.$storetypeid.'", false, false);';
				}
				else
				{
					$strSelect .= 'c.options[0]=new Option("'.$storetype.'", "'.$storetypeid.'", true, false);';
				}								
			}
		}

		return $strSelect;
	}
 
    function processForm()
    {
		return true;
    }
}

?>
