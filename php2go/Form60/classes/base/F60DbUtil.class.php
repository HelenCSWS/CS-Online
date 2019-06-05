<?php
import('php2go.base.Php2Go');

import('Form60.util.F60Date');
import('Form60.bll.bllorders');
import('Form60.util.F60Common');

//import('php2go.base.Php2Go');
//import('php2go.util.TypeUtils');


//!-----------------------------------------------------------------
// @class		F60DbUtil
// @desc			A common classe province globle functions 
// @package		Form60.F60Date
// @extends		PHP2Go-Date
// @author		Helen
// @version		
//!-----------------------------------------------------------------
class F60DbUtil extends Php2Go
{
    var $db;
	function F60DbUtil()
   	{     
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
   	}

	function getEstateName($estate_id)
    {
			$db = & Db::getInstance();
        	$db->setFetchMode(ADODB_FETCH_ASSOC);
        
			$sql ="select estate_name, e.estate_id from  estates e where e.estate_id=$estate_id";
			$rows = $db->getAll($sql);
			
			$estate_name = $rows[0]["estate_name"];
			return $estate_name;
	}
	
    function getEstateCountry($estate_id)
    {
			$db = & Db::getInstance();
        	$db->setFetchMode(ADODB_FETCH_ASSOC);
        
			$sql ="select billing_address_country country from  estates e where e.estate_id=$estate_id";
			$rows = $db->getAll($sql);
			
			$country = $rows[0]["country"];
			return $country;
	}
    
    
    function updateCC()
    {
        /*
        
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql= "SELECT distinct u.email1, u.user_id FROM `users` u, estates e Where u.user_id =e.user_id and e.deleted=0 and u.deleted=0";
        $rows = $db->getAll($sql);
        
		return $rows;	        
        */
			$db = & Db::getInstance();
        	$db->setFetchMode(ADODB_FETCH_ASSOC);
        
			$sql ="select * from customers where lkup_payment_type_id>2 and province_id =1 and (cc_number!='' or cc_number!=NULL) and deleted =0";
			$rows = $db->getAll($sql);
			
        
            $i=0;
			if (count($rows) != 0)
            {
                foreach ($rows as $key => $cmInfo)
                {
                    $cc_number = F60Common::enCodeString($cmInfo['cc_number']);
                    $cc_dig =  F60Common::enCodeString($cmInfo['cc_digit_code']);                    
                    $customer_id = $cmInfo['customer_id'];
                    
                    $sqlUpdate ="update customers 
                                set cc_number ='$cc_number',
                                cc_digit_code ='$cc_dig'
                                where customer_id =$customer_id";
                    
                    $this->db->execute($sqlUpdate);	
                  //  $i++;                 
                }
            }
            
            echo $i;
		
	}
	
    
	function getEstatePayMentInfo($estate_id)
    {
			$db = & Db::getInstance();
        	$db->setFetchMode(ADODB_FETCH_ASSOC);
        
			$sql ="select payment_info from  estates e where e.estate_id=$estate_id";
			$rows = $db->getAll($sql);
			
			$payment_info = $rows[0]["payment_info"];
			return $payment_info;
	}
	
	function getABRank($customer_id)
    {
			$db = & Db::getInstance();
        	$db->setFetchMode(ADODB_FETCH_ASSOC);
        
			$sql ="select ar.rank from  ab_customer_rank ar,customers c where c.customer_id=$customer_id and ar.licensee_no=c.licensee_number";
			$rows = $db->getAll($sql);
			
			if(count($rows)!=0)
				$rank = $rows[0]["rank"];
			else
				$rank =0;
				
			return $rank;
	}
	
	function phone_number($sPhone){ 
	    $sPhone = ereg_replace("[^0-9]",'',$sPhone); 
	    if(strlen($sPhone) != 10) return(False); 
	    $sArea = substr($sPhone,0,3); 
	    $sPrefix = substr($sPhone,3,3); 
	    $sNumber = substr($sPhone,6,4); 
	    $sPhone = $sArea.".".$sPrefix.".".$sNumber; 
	    return($sPhone); 
	} 

	function getEstateContactNumber($estate_id)
    {
			$db = & Db::getInstance();
        	$db->setFetchMode(ADODB_FETCH_ASSOC);
        
			$sql ="select phone_office1 from  estates e where e.estate_id=$estate_id";
			$rows = $db->getAll($sql);
			
			$contactNumber =& F60DbUtil::phone_number($rows[0]["phone_office1"]);
			return $contactNumber;
	}
	
	function geWineNameByWineId($wine_id)
    {
			$db = & Db::getInstance();
        	$db->setFetchMode(ADODB_FETCH_ASSOC);
        
			$sql ="select wine_name from wines where wine_id=$wine_id";
			$rows = $db->getAll($sql);
			
			$wine_name = $rows[0]["wine_name"];
			return $wine_name;
	}
	
	function getUserNameById($user_id)
    {
			$db = & Db::getInstance();
        	$db->setFetchMode(ADODB_FETCH_ASSOC);
        
			$sql ="select concat(first_name,' ',last_name) user_name from users where user_id=$user_id";
			$rows = $db->getAll($sql);
			
			$user_name = $rows[0]["user_name"];
			return $user_name;
	}
		
	function getAllEstatesManagerEmails($isBC=true)
    {
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql= "SELECT distinct u.email1, u.user_id FROM `users` u, estates e Where u.user_id =e.user_id and e.deleted=0 and u.deleted=0";
        $rows = $db->getAll($sql);
        
		return $rows;	        
	}
	
	function checkIsBCByEstate($estate_id)
	{
	 	if($estate_id ==-1)//all enotecca
	 		$estate_id =96;
	 		
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql= "SELECT billing_address_country FROM estates where estate_id =$estate_id";
      
        $rows = $db->getAll($sql);
      
        if(strtoupper($rows[0]["billing_address_country"])=="CANADA")
			return true;	    
		else
			return false;
	}
	
	function getManagerEmailByEstate_id($estate_id)
    {
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql= "SELECT distinct u.email1, u.user_id FROM `users` u, estates e Where u.user_id =e.user_id and e.estate_id =$estate_id";
        $rows = $db->getAll($sql);
        
    	return $rows;	    
	}
	
	
	function getUserEmailAddress($user_id)
    {
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql= "Select email1 from users where user_id =$user_id";
        $rows = $this->db->getAll($sql);
        
		return $rows[0]["email1"];	    
        
	}
	
	function getAccountUserNameByCustomer($customer_id)
    {
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        
        $sql= "select concat(u.first_name,' ',u.last_name) user_name from users u, users_customers uc
			   where u.user_id =uc.user_id
			   and uc.customer_id =$customer_id
			   and uc.deleted=0";
        $rows = $db->getAll($sql);
    
    	
	    if(count($rows)>0)
        {
			return $rows[0]["user_name"];	    	
		}
		else
		{
			return "0";
		} 
	}
	
	
	function updateProfit()
	{
       $sql= "select o.lkup_store_type_id, o.estate_id, ot.price_winery, lkbt.size_value, ot.order_item_id, ot.wine_id, ot.ordered_quantity, ot.profit
 			  from order_items ot, orders o, wines w, lkup_bottle_sizes lkbt
					where ot.deleted =0
					and o.deleted=0
					and o.order_id = ot.order_id
					and ot.wine_id = w.wine_id
					and w.lkup_bottle_size_id = lkbt.lkup_bottle_size_id
					and ( ot.profit =null 
					or ot.profit=0)
					limit 1;
					";
			
			
		  $orderItems = $this->db->getAll($sql);

		  $sql ="";
		  
		  if (count($orderItems) == 0)
		  {
			
			}
			else
			{
			 	$nrow =0;
			  for ($i=0; $i<count($orderItems); $i++)
			  {
				
					$estate_id = $orderItems[$i]["estate_id"];
					$store_type_id = $orderItems[$i]["lkup_store_type_id"];
					$unit_price = $orderItems[$i]["price_winery"];
					$order_units = $orderItems[$i]["ordered_quantity"];
					$bottle_size = $orderItems[$i]["size_value"];
					
					$order_item_id = $orderItems[$i]["order_item_id"];
					
					$profit = $order_units * (F60DbUtil::getProfits($estate_id,$store_type_id,$unit_price,$bottle_size));
					
					
					$sqlUpdate = "update order_items set profit = $profit where order_item_id = $order_item_id";
					
					$this->db->execute($sqlUpdate);	
					
					$nrow++;
				}
			
				
			}						
	}
	
	function isBCEstate($cspc_code)
	{	
	 	$recVal = 0;

		$sql="select distinct estate_id from wines w where w.cspc_code = $cspc_code and (w.estate_id <3 or w.estate_id =97 or w.estate_id =96)";
		
		$id =  $this->db->getAll($sql);
		
		if($id == null)
		{
			$recVal =1;
		}
		else
		{
			$recVal =0;
		}
	 	
		return $recVal;
	}
	
	function getBCWineProfits4BCSales($estate_id,$store_type_id,$wine_id) //affact on 04-01-2015 
	{
	
		if($store_type_id==6)
			$store_type_id =1;
			
		$profitPerUnit =0;
		
	
		$sql= "select bottles_per_case, price_winery, price_per_unit, lk_size.size_value,lk_size.litter_deposit from wines w, lkup_bottle_sizes lk_size where wine_id=$wine_id
				 and w.lkup_bottle_size_id =lk_size.lkup_bottle_size_id";
				 
		$result = $this->db->getAll($sql);
       	$bottle_per_cases = $result[0]["bottles_per_case"];
       	$deposit = $result[0]["litter_deposit"];
       	$price = $result[0]["price_winery"];
       	
 
      	if($store_type_id==3)
			$price =  $result[0]["price_per_unit"];
			
	
	
      // 	$shipping_deduct 58/$bottle_per_cases;  //add by Helen Mar 11,2009  Deduct $0.42 per bottle for shipping, according to Heather's email.       
      
       	$shipping_deduct =8/$bottle_per_cases;  //update by Helen April 01,2015  Deduct $8 per case for shipping, according to Garry's email.       
		   
		$sql= "select commission/100 cmrate, discount_rate, profit_formula from estates_commissions where estate_id=$estate_id and lkup_commission_types_id=$store_type_id";
		
		$result = $this->db->getAll($sql);
		
		$commission_rate = $result[0]["cmrate"];
		
	
			
		/*		 $fp = fopen("logs/price_1.log","a");
		fputs($fp,  "price=".$price);
		fclose($fp);
		*/
		   		 
	//	$total = $price*0.05+$price+$deposit;
		
		$commission = $price*$commission_rate;
		
		
		 $sql= "select profit_percentage from lkup_profit_cost_percentage where province_id=1 order by effective_date desc Limit 1";
       
        $arry_profit_percentage =  $this->db->getAll($sql);
        
        $profit_percentage = 0.22;		//update by Helen April 01,2015  decrease the shipping percentage       
		   
        
	
		
		$profitPerUnit =(floatval($commission) - $shipping_deduct)*$profit_percentage;
		if($estate_id==175) ////no deduction for C.C. Jentsch Cellars  2015 Dec 10th
			$profitPerUnit =$profitPerUnit*0.45;
				 
				 
	
	
        return $profitPerUnit;
	 
	}
	 
	 
	
	function getProfits($estate_id,$store_type_id,$unit_price,$bottle_size,$wine_id) // before 04-01-2015 
	{

	
		$profitPerUnit = 0.0;
                
        $sql= "select commission, discount_rate, profit_formula from estates_commissions where estate_id=$estate_id and lkup_commission_types_id=$store_type_id";
               
        $comms = $this->db->getAll($sql);
        
        $sql= "select profit_percentage from lkup_profit_cost_percentage where province_id=1 order by effective_date desc Limit 1";
       
        $arry_profit_percentage =  $this->db->getAll($sql);
        
        $profit_percentage = $arry_profit_percentage[0]["profit_percentage"];		

		
		$sql= "select bottles_per_case, lk_size.size_value from wines w, lkup_bottle_sizes lk_size where wine_id=$wine_id
				 and w.lkup_bottle_size_id =lk_size.lkup_bottle_size_id";
       	$result = $this->db->getAll($sql);
       	$bottle_per_cases = $result[0]["bottles_per_case"];
       	$bottle_size = $result[0]["size_value"];
       	$shipping_deduct =5/$bottle_per_cases;  //add by Helen Mar 11,2009  Deduct $0.42 per bottle for shipping, according to Heather's email.       
        
		
		$profit=0;
	//	$estate_name =$this->getEstateName($estate_id);
		if (count($comms) > 0)  
        {		  
         	if($estate_id <3) //hillside and paradise
         	{
		
			
				
	            $discount_rate=$comms[0]["discount_rate"];
	            $commission_rate = $comms[0]["commission"]/100;
	            $profit_formula = "return " . $comms[0]["profit_formula"] .";";
	              
	            $sql="select excise_tax from lkup_excise_tax where province = 'BC' order by effective_date desc Limit 1";
	            $tax = $this->db->getAll($sql);
	            $excise_tax = $tax[0]["excise_tax"];
	          
	            
				$profitPerUnit = eval($profit_formula);
				
				$profitPerUnit =(floatval($profitPerUnit) - $shipping_deduct)*$profit_percentage;
				
			}
			else if($estate_id==96 || $estate_id ==97)//enotecca and arrowleaf
			{ 
				
				$wine_name =& F60DbUtil::geWineNameByWineId($wine_id);
				$wine_type =& bllorder::getWineTypeByWineID($wine_id);
				
				if($wine_type==10)//ovlier oil. 13% 
				{
					$profitPerUnit =$unit_price*0.13;
				}
				else
				{
					if(strpos(strtolower($wine_name),strtolower("feenie"))===false&&strpos(strtolower($wine_name),strtolower("Cuvee 900 Seymour"))===false)
					{
					 		
					 	$profit_formula = "return " . $comms[0]["profit_formula"] .";";
						$profitPerUnit = eval($profit_formula); 	
						$profitPerUnit =floatval($profitPerUnit) - $shipping_deduct;
					}
					else
					{
						$profitPerUnit =floatval(9/$bottle_per_cases)*$profit_percentage;; // add by Helen, May 30th,2011, According Sarah's email, for all Feenies wine, we get $9 per case
					}
				}
				
			}
//			else if($estate_id ==110||$estate_id ==118||$estate_id ==119||$estate_id ==120||$estate_id ==126||$estate_id ==150)
			else if($estate_id ==150)
			{
	            $profit_formula = "return " . $comms[0]["profit_formula"] .";";       
				$profitPerUnit = eval($profit_formula)*$profit_percentage;
			
			
			//	if($estate_id != 120 && $estate_id!=110)	//no deduction for arrow leaf and BlackHill, July 18
				if($estate_id != 175)	//no deduction for C.C. Jentsch Cellars  2015 Dec 10th
				{
					$profitPerUnit =(floatval($profitPerUnit) - $shipping_deduct)*$profit_percentage; //add by Helen Mar 11,2009  Deduct $0.42 per bottle for shipping, according to Heather's email.
					
				}
			
			}
		 }
        else 
            $profitPerUnit = 0.0;
    
    
	
        return $profitPerUnit;
	 
	 }
	function isCustomerUpdateDone()
	{
        $reval = false;
        $sql = 'select Max(session_id) id from uploaded_customers_sessions' ;
        $result = & F60DbUtil::runSQL($sql);

        if ($result->EOF )
        {$row = & $result->FetchRow();
           //print $row;

            $id =$row["id"];

            $sql = "select upload_date,step_id from uploaded_customers_sessions where session_id = ".$id ;
            $result=& F60DbUtil::runSQL($sql);
            $row = & $result->FetchRow();
            $date =$row["upload_date"];
            if ($row["step_id"]!=5)
            {
               $dates = split(" ",$date);//2005-10-26 14:31:14
               $mdates = split("-",$dates[0]);
             //  print $mdates[0];
               $mtimes =split(":",$dates[1]);

               if( (time()-strtotime($date))/60 > 30)
                    $reval = true;
            }
            else
                $reval = true;
        }
        else
             $reval = true;

        return $reval;
    }
    
 
  //!-----------------------------------------------------------------
	// @function	runSQL
	// @desc			running select, add, update, delete ... query
	// @access		public
	// @param		$sql: standard sql query
	//!-----------------------------------------------------------------
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
	//!-----------------------------------------------------------------
	// @function	runSQL
	// @desc			running select query
	// @access		public
	// @param		$sql: standard sql query
	//!-----------------------------------------------------------------
 	function returnSQL($sql)
	{
		 $this->db = & Db::getInstance();
       $this->db->setFetchMode(ADODB_FETCH_ASSOC);
       return $this->db->getAll($sql);
       
	}
	
	function getStoreType($id)
	{
	 	$type = "";
		if($id ==1)
		{
			$type="L.R.S";
		}
		else if($id ==2)
		{
			$type="Licensee";
		}
		else if($id ==3)
		{
			$type="Agency";
		}
		else if($id ==6)
		{
			$type="BCLDB";
		}
		else if($id ==8)
		{
			$type="AB licensee";
		}
		
		return $type;
	}
	//!-----------------------------------------------------------------
	// @function	get_user_name
	// @desc			get user name by user_id
	// @access		public
	// @param		$user_id
	//!-----------------------------------------------------------------
    function get_user_name($user_id)
    {
        $sql = "SELECT concat(first_name,' ',last_name) sname FROM users WHERE deleted=0 and user_id = ".$user_id;
        $result=& F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        $sname =$row["sname"];
        return $sname;
    }
    
    //!-----------------------------------------------------------------
	// @function	get_user_name
	// @desc			get user name by user_id
	// @access		public
	// @param		$user_id
	//!-----------------------------------------------------------------
    function get_user_level($user_id)
    {
        $sql = "SELECT user_level_id FROM users WHERE deleted=0 and user_id = ".$user_id;
        $result=& F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        $user_level_id =$row["user_level_id"];
        return $user_level_id;
    }
    
    function getProvinceId4LoginUser($user_id)
    {
			$db = & Db::getInstance();
        	$db->setFetchMode(ADODB_FETCH_ASSOC);
        
			$sql ="select province_id From users where user_id =$user_id ";
			$rows = $db->getAll($sql);
			
			$province_id = $rows[0]["province_id"];
			return $province_id;
	}
  
    
   //!-----------------------------------------------------------------
	// @function	getWineTyepByTypeId
	// @desc			get wine's type ( color etc ...) by lkup_color_type_id
	// @access		public
	// @param		$typeId
	//!-----------------------------------------------------------------
    function getBeerTyepByTypeId($typeId)
    {
        $sql="select caption from lkup_beer_types where lkup_beer_type_id=".$typeId;
        $result=& F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        $sname =$row["caption"];
        return $sname;
        
    }
    
    
  
    function getWineTyepByTypeId($typeId)
    {
        $sql="select caption from lkup_wine_color_types where lkup_wine_color_type_id=".$typeId;
        $result=& F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        $sname =$row["caption"];
        return $sname;
        
    }
    
	function getWineTyepByIds($typeId, $productid)
    {
     	if($productid==1)
	        $sql="select caption from lkup_wine_color_types where lkup_wine_color_type_id=".$typeId;
	    else
	    { 
			if($productid==2)
				$sql="select caption from lkup_beer_types where lkup_beer_type_id=".$typeId;
				
			else if($productid==3)
				$sql="select caption from lkup_beer_types where lkup_beer_type_id==100";
				
			else if($productid==4)
				$sql="select caption from lkup_beer_types where lkup_beer_type_id>100";
		}
        $result=& F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        $sname =$row["caption"];
        return $sname;
        
    }
    
    function getProductTypsByProductId($product_id)
    {
     	if($product_id==1)
     	{
	        $sql="select caption, lkup_wine_color_type_id product_type_id from lkup_wine_color_types";
	    }
	    else if($product_id ==2)// beer only
	    {
	     	
			$sql="select caption, lkup_beer_type_id product_type_id from lkup_beer_types where lkup_beer_type_id<200";	
				
		}
		else if($product_id ==3)// sake only
	    {
	     	
			$sql="select caption, lkup_beer_type_id product_type_id from lkup_beer_types where lkup_beer_type_id>200 and lkup_beer_type_id < 300";	
			
			
		}
		else if($product_id ==4)// spirits only
	    {
	     	
			$sql="select caption, lkup_beer_type_id product_type_id from lkup_beer_types where lkup_beer_type_id>200";				
			
		}
		
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);        
        $rows = $db->getAll($sql);
        return $rows;
        
    }
    
    function getProvinces()
    {
     	$sql="select * from lkup_provinces where province_id>0";				
		
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);        
        $rows = $db->getAll($sql);
        return $rows;
        
    }
    
    
    function getProvinceInit($province_id)
    {
     	$sql="select short_name  from lkup_provinces where province_id=$province_id";				
		
        $result=& F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        return $row["short_name"];
        
    }
    
   //!-----------------------------------------------------------------
	// @function	format_phone
	// @desc			format phone number as "xxx.xxx.xxxx"
	// @access		public
	// @param		$typeId
	//!-----------------------------------------------------------------
    function format_phone($phone)
    {
        return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1.$2.$3", $phone);
    }

	//!-----------------------------------------------------------------
	// @function	deleteAllocations
	// @desc			delete allocations by wine id
	// @access		public
	// @param		$wine_id
	//!-----------------------------------------------------------------
	function deleteAllocations($wine_id)
	{
	    $sql ="update wine_allocations set deleted=1 where wine_id=".$wine_id;
	    $result=& F60DbUtil::runSQL($sql);
	    return true;
	}

		//!-----------------------------------------------------------------
	// @function	deleterOrderItemsbyWineid
	// @desc			delete order_items by wine_id
	// @access		public
	// @param		$wine_id
	//!-----------------------------------------------------------------
	function deleterOrderItemsbyWineid($wine_id)
	{
	  	$sql="update order_items ,orders set order_items.deleted=1 where order_items.order_id =orders.order_id and orders.lkup_order_status_id =1 and order_items.wine_id =".$wine_id;
	    $result=& F60DbUtil::runSQL($sql);
	    return true;
	
	}

	//!-----------------------------------------------------------------
	// @function	caltWine4DelCm
	// @desc			when delete a customer, check if any wine allocate to this customer and not sold yet, put the allocation back to wines total bottles
	// @access		public
	// @param		$customer_id
	//!-----------------------------------------------------------------
	function caltWine4DelCm($customer_id)
	{
        $aclt_cm_bottoles =0;
        $aclt_cm_sold =0;
        $aclt_bottoles =0;
        $aclt_sold =0;
        $wine_id ="";
        $avilable = 0;
        
        $order_sold =0;
        $order_not_sold=0;
        
        $totales = 0;
        
        $isHold = false;
      //get allocation and sold number from

        $sql = "select allocated, sold ,wine_id from customer_wine_allocations where customer_id=".$customer_id;
        $result=& F60DbUtil::runSQL($sql);

        while (!$result->EOF )
        {
            $isHold = true;
            $row = & $result->FetchRow();
            $aclt_cm_bottoles =$row["allocated"];
            $aclt_cm_sold =$row["sold"];
            $wine_id =$row["wine_id"];
 
            
        //get bottles in order which not  actrually sold( not sold - pending - lkup_status_id = 1 )
            $sql = "select sum(ordered_quantity) pendings from order_items odit,orders od where odit.wine_id=".$wine_id.
                   " and odit.order_id = od.order_id and od.customer_id =".$customer_id." and od.lkup_order_status_id = 1";
                   
            $result1=& F60DbUtil::runSQL($sql);
            if (!$result->EOF )
            {
                $row = & $result->FetchRow();
                $order_not_sold =$row["pendings"]; 
            }
            
           // $sold;
            $order_sold =$aclt_cm_sold - $order_not_sold;


            //allocate to cm which not sold
            $aclt_cm_bottoles =$aclt_cm_bottoles -$order_sold;
            //update wines table : add wines to total_bottles
            
            //1. select totals from wines
            $sql = "select total_bottles from wines where wine_id = ".$wine_id;
            $result3=& F60DbUtil::runSQL($sql);
            if (!$result3->EOF )
            {
                $row = & $result3->FetchRow();
                $totales =$row["total_bottles"];
            }
            $totales =$order_not_sold +$totales;
            
            //2 update with new numbers
            $sql = "update wines set total_bottles = ".$totales." where wine_id=".$wine_id;
            $result3=& F60DbUtil::runSQL($sql);
            
            //select bottles from wine_allocations
            $sql= "select unallocated from wine_allocations where wine_id=".$wine_id;
            $result3=& F60DbUtil::runSQL($sql);
            if (!$result3->EOF )
            {
                $row = & $result3->FetchRow();
                $aclt_bottoles=$row["unallocated"];
            }

            $aclt_bottoles = $aclt_bottoles-$aclt_cm_bottoles;
         
            //update wine_allocations
            $sql = "update wine_allocations set unallocated = ".$aclt_bottoles." where wine_id=".$wine_id;
            $result3=& F60DbUtil::runSQL($sql);
          
            //delete customer_wine_allocations

            $sql = "update customer_wine_allocations set deleted=1 where wine_id=".$wine_id." and customer_id = ".$customer_id;
            $result3=& F60DbUtil::runSQL($sql);

            $sql ="select odit.order_item_id from order_items odit, orders od where odit.wine_id =".$wine_id.
                    " and odit.order_id =od.order_id and od.customer_id =".$customer_id." and od.lkup_order_status_id=1";
            $result_odit=& F60DbUtil::runSQL($sql);

            while (!$result_odit->EOF )
            {
                $result_odit ->FetchRow();

                //delete order_items
                $sql = " update order_items set deleted = 1 where order_items.order_item_id =".$result_odit['order_item_id'];

                $result3=& F60DbUtil::runSQL($sql);
            }
            
         
             if ($isHold )
             {
	             //delete orders
                 $sql = " update orders set deleted = 1 where orders.customer_id =".$customer_id;
                 $result2=& F60DbUtil::runSQL($sql);
             }

        }
       
       return true;
    }

	function debugOut2file($filename,$outputData)    
	{
    	$fp = fopen("logs/$filename","a");
		fputs($fp, $outputData."\n");
		fclose($fp);
	}	

    
    function getAmountOwned()
    {
 
        $amountOwn="  if(o.lkup_store_type_id=3, sum(oi.price_per_unit * oi.ordered_quantity) 
		+sum(oi.litter_deposit * oi.ordered_quantity) 
		+sum(oi.price_per_unit * oi.ordered_quantity)*0.05,
      sum(oi.price_winery * oi.ordered_quantity) 
	  +sum(oi.litter_deposit * oi.ordered_quantity) 
	  +sum(oi.price_winery * oi.ordered_quantity)*0.05)";

        return $amountOwn;
    }
    
    function beginTran()
    {
	//	execute
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $db->execute("BEGIN");
	}
	
	function commTran()
    {
	//	execute
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $db->execute("COMMIT");
	}
	
	function rollbkTran()
    {
	//	execute
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $db->execute("ROLLBACK");
	}
}
?>
