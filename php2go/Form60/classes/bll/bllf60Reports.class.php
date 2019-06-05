<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Common');

class F60ReportsData extends Php2Go 
{
    var $db;
    var $cfg;
    var $data_cfg;
    var $logFile;
    var $errorMessage;
   
    function F60ReportsData()
    {
     	include('config/emailoutconfig.php');
     	include('config/dataconfig.php');
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $this->cfg = $EMAIL_CFG;
        $this->data_cfg = $DATA_CFG;
    }
    
    function getFormatDate($sDate) //yyyymmdd    return: mm/dd/yyyy YYYYKL SUB STING ($ISDate,))
    {
        $syear=substr($sDate,0,4);
        $smonth=substr($sDate,4,2);
        $sday=substr($sDate,6,2);

        return $smonth.'/'.$sday.'/'.$syear;
    }
    function getEstate($estate_id)
	{
        $sql = "select estate_name from estates where estate_id = ".$estate_id;
        $result = & F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        return  $row['estate_name'];
    }
    
    function getBCEstate($bc_estate_id)
	{
        $sql = "select estate_name from bc_estates where bc_estate_id = ".$bc_estate_id;
        $result = & F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        return  $row['estate_name'];

    }
    
    
     
    function getF60RepTitle($search_id,$estateid,$from,$to)
	{
	  
        $sfrom=$this->getFormatDate($from);
        $sto=$this->getFormatDate($to);
        $estatename=$this->getEstate($estateid);
	    switch ($search_id)
	    {
			case 1:	
	           $sfrom=$this->getFormatDate($from);
	           $sto=$this->getFormatDate($to);
	           $estatename=$this->getEstate($estateid);
	           $titleText = 'All invoice for '.$this->getEstate($estateid).' from '.$this->getFormatDate($from).' to '.$this->getFormatDate($to);
	           break;
       		case 2:
	           $isPaid =" not paid";
	           if ($_REQUEST["searchAdt"]==2)
	               $isPaid=" paid";
	
	           $titleText="Who has ".$isPaid.' for '.$this->getEstate($_REQUEST["estateid"]).' from '.$this->getFormatDate($_REQUEST["from"]).' to '.$this->getFormatDate($_REQUEST["to"]);
	           break;
			case 3:
	           $isrecieved =" not received";
	           if ($_REQUEST["searchAdt"]==2)
	              $isrecieved=" received";
	
	           $titleText="Who has ".$isrecieved.' for '.$this->getEstate($_REQUEST["estateid"]).' from '.$this->getFormatDate($_REQUEST["from"]).' to '.$this->getFormatDate($_REQUEST["to"]);
	           break;
	         case 4:
	             $titleText="Complete inventory for ".$this->getEstate($_REQUEST["estateid"]);
	           break;
	         case 5:
	             $titleText="Allocation summary for ".$this->getEstate($_REQUEST["estateid"]).' by '.$this->getWine($_REQUEST["wine_id"]);
	           break;
	      	case 6:
				$titleText="Sales report for ".$this->getEstate($_REQUEST["estateid"]).' from '.$this->getFormatDate($_REQUEST["from"]).' to '.$this->getFormatDate($_REQUEST["to"]) ;
			case 14:
				$titleText="Sales report for ".$this->getEstate($_REQUEST["estateid"]).' from '.$this->getFormatDate($_REQUEST["from"]).' to '.$this->getFormatDate($_REQUEST["to"]) ;
	
	           break;
	    }          
     	return $titleText;
	}//FUNCTON END

    function getInvoicesData($search_id, $user_id, $estate_id, $from, $to, $store_type_id="", $wine_id ="",$searchAdt="")
    {
     
		if ($search_id==1||$search_id==6||$search_id==14) // all invoice
		{                          
        	if($search_id ==6)
			{
				$sql = "Select cm.customer_id id,
						date_format(o.delivery_date ,'%m-%d-%Y') delivery_date,
						o.invoice_number invoice_number,
						stype.license_name store_type,
						cm.licensee_number,
						cm.customer_name,
						concat(IFNULL(cm.billing_address_unit,'') ,
						IFNULL(cm.billing_address_street_number,''), ' ',
						IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address,
						
						concat('$',format(".F60DbUtil::getAmountOwned().",2))  as amount_owned,
						sum(oi.ordered_quantity/w.bottles_per_case) total_cs,
						if( o.lkup_payment_status_id =2, 'Paid','Not paid')  isPaid,
						if( o.lkup_order_status_id =2, 'Delivered','Pending')  isRecieved,
						concat(IFNULL(u.first_name,''),' ',IFNULL(u.last_name,'')) user_name"; 
						
					
			}
			else if($search_id == 1||$search_id == 14)
			{
				$sql = "Select cm.customer_id id,
						date_format(o.delivery_date ,'%m-%d-%Y') delivery_date,
						o.invoice_number invoice_number,
						stype.license_name store_type,
						cm.licensee_number,
						cm.customer_name,
						concat(IFNULL(cm.billing_address_unit,'') ,
						IFNULL(cm.billing_address_street_number,''), ' ',
						IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address,
						
						oi.cspc_code sku,
						concat(oi.wine_name,' ',oi.wine_vintage) wine_name,
						oi.ordered_quantity orqt,
						oi.ordered_quantity/w.bottles_per_case total_cs,
						oi.price_winery csws_price,
						oi.price_per_unit market_price,
						
						if( o.lkup_payment_status_id =2, 'Paid','Not paid')  isPaid,
						if( o.lkup_order_status_id =2, 'Delivered','Pending')  isRecieved,
						concat(IFNULL(u.first_name,''),' ',IFNULL(u.last_name,'')) user_name,
						cm.billing_address_city city";
			}
         
                             
             $sql .=  " From customers cm
						left join customers_contacts cmc on cm.customer_id = cmc.customer_id and cmc.is_primary=1,	 
						orders o, estates e,order_items oi, wines w,lkup_store_types stype , users_customers uc, users u";
						
						
            if($user_id!="")
            {
                $sql = $sql." ,users_customers ucm ";
            }
				                
				                
           $storetypeidAdt = "";
           if ( $search_id==6 )
           {
            	$storetypeidAdt = " and cm.lkup_store_type_id = " .$store_type_id." and w.wine_id = ".$wine_id;
           }

   		   $cityAdt="";
           if ( $search_id==14 )
           {            
            	if(Intval($store_type_id)!==0)
	            	$storetypeidAdt = " and cm.lkup_store_type_id = " .$store_type_id;
	           
	            if(trim($searchAdt)!="")
	            {
	             	if(strstr($searchAdt,";")!="")
						$cities=explode (";",$searchAdt);
					else
						$cities[0]=$searchAdt;
						
					for($i=0;$i<count($cities); $i++)
					{					 	
					 	if(trim($cities[$i])!="")
					 	{
							$cityName = str_replace("'","\'",$cities[$i]);
							if($i==0)
							{								
								$cityAdt = " and (cm.billing_address_city like '%$cityName%'";
							}
							else
								$cityAdt = $cityAdt." or cm.billing_address_city like '%$cityName%'";
						}	
					}
				
				}
				
           }
           if($cityAdt!="")
           		$cityAdt =$cityAdt.")";

			
        	$userAdt="";
            if ($user_id!="")
            {
                $userAdt =" and ucm.deleted=0 and cm.customer_id=ucm.customer_id and ucm.user_id=".$user_id;
            }
            
            $groupBy="";
            if($search_id ==6)
            {
				$groupBy=" group by cm.customer_id, o.order_id ";
			}
			
            $sql .= " where cm.deleted=0
                      and stype.lkup_store_type_id=cm.lkup_store_type_id
                      and o.deleted=0
                      and oi.deleted=0
							 and o.customer_id = cm.customer_id
							 and o.estate_id = e.estate_id
							 and cm.customer_id=uc.customer_id
							 and uc.user_id=u.user_id
							 and e.estate_id =".$estate_id.$storetypeidAdt.$userAdt.$cityAdt.
                   // "and (when_entered > ".$from." and when_entered < ".$to.
                    " and oi.order_id = o.order_id
                      and oi.wine_id = w.wine_id
                      and (cmc.is_primary=1 or cmc.is_primary is NULL)
                      and date_format(o.delivery_date,'%Y%m%d')>='".$from.
                     "' and date_format(o.delivery_date,'%Y%m%d') <='".$to."'
				 			$groupBy ";            
           
            
            
            if($search_id==14)
            	$sql .= " order by cm.billing_address_city, o.delivery_date";
            else
            	$sql .= " order by o.delivery_date";

       }
       
		else if ($search_id==2 ||$search_id==3)
		{
  

	       if ( ($search_id ==2&&$searchAdt==2)||($search_id ==3&&$searchAdt==2)) // paid or recived
	       {
	         $sql ="Select cm.customer_id id,cm.customer_name,
	         			cm.licensee_number,
	         			lks.license_name store_type,
							 concat(IFNULL(c.first_name,''),' ',IFNULL(c.last_name,'')) contact_name,
							 CONCAT_WS('.', SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end)
, 1, 3), SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end)  , 4, 3), SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end) , 7))  contact_number,							 concat(IFNULL(cm.billing_address_unit,'') ,
							 IFNULL(cm.billing_address_street_number,''), ' ',
							 IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address,
							 o.invoice_number invoice_number, o.delivery_date delivery_date,
							 concat('$',format(".F60DbUtil::getAmountOwned().",2))  as amount_owned,
							  sum(oi.ordered_quantity/w.bottles_per_case) total_cs,
							  if( o.lkup_payment_status_id =2, 'Paid','Not paid')  isPaid,
							  if( o.lkup_order_status_id =2, 'Delivered','Pending')  isRecieved,
							  concat(IFNULL(u.first_name,''),' ',IFNULL(u.last_name,'')) user_name";

         $sql .=" From customers cm
							 left join customers_contacts cmc on cm.customer_id = cmc.customer_id and cmc.is_primary=1
							 left join contacts c on c.contact_id = cmc.contact_id  and c.deleted=0,
							 orders o, estates e,order_items oi, wines w, lkup_store_types lks";
							 
                    $sql =$sql." ,users_customers ucm, users u";

               if ($search_id ==2)
                {
                    $adtCause = " and o.lkup_payment_status_id = ".$searchAdt ;
                }
                else
                {
                    $adtCause = " and o.lkup_order_status_id = ".$searchAdt ;
                }

                if($user_id!="")
                {
                    $adtCause =$adtCause." and ucm.user_id=0 and cm.customer_id=ucm.customer_id and ucm.user_id=".$user_id;
                }
                $sql .= " Where cm.deleted=0
									and o.deleted=0
									and oi.deleted=0
									and o.customer_id = cm.customer_id
									and o.estate_id = e.estate_id
									and oi.wine_id=w.wine_id
									and cm.customer_id=ucm.customer_id
									and cm.lkup_store_type_id = lks.lkup_store_type_id
									and ucm.user_id=u.user_id
									and e.estate_id =".$estate_id.$adtCause.
									" and oi.order_id = o.order_id
									and (cmc.is_primary=1 or cmc.is_primary is NULL)
									and date_format(o.delivery_date,'%Y%m%d')>='".$from.
									"' and date_format(o.delivery_date,'%Y%m%d') <='".$to."'
									group by cm.customer_id,oi.order_id
									order by o.delivery_date";
							 
							 
							 
	      }
	      elseif ( ($search_id ==2&&$searchAdt==1)||($search_id ==3&&$searchAdt==1)) // not paid or not recived
	      {	
																
																
 					$sql  = "Select o.delivery_date delivery_date,
                        o.invoice_number invoice_number,
                        cm.customer_id id,cm.customer_name,
                        cm.licensee_number licensee_number,
                        stype.license_name store_type,
                        concat(IFNULL(cm.billing_address_unit,'') ,
							 IFNULL(cm.billing_address_street_number,''), ' ',
							 IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address,
                     
							concat('$',format(".F60DbUtil::getAmountOwned().",2))  as amount_owned,
                                                

                                                 sum(oi.ordered_quantity/w.bottles_per_case) total_cs,
																if( o.lkup_payment_status_id =2, 'Paid','Not paid')  isPaid,
																if( o.lkup_order_status_id =2, 'Delivered','Pending')  isRecieved,
							  concat(IFNULL(u.first_name,''),' ',IFNULL(u.last_name,'')) user_name";


                if ($search_id ==2)
                {
                    $adtCause = " and o.lkup_payment_status_id = ".$searchAdt ;
                }
                else
                {

				
                    $adtCause = " and o.lkup_order_status_id =1 " ;
                }
                
                 if($user_id!="")
                {
                    $adtCause =$adtCause." and cm.customer_id=ucm.customer_id and ucm.user_id=".$user_id;
                }
					$sql .=" From customers cm
								LEFT JOIN customers_contacts cmc
								ON cm.customer_id = cmc.customer_id AND cmc.is_primary=1
                                                LEFT JOIN contacts c ON c.contact_id = cmc.contact_id
                                                                                        AND c.deleted=0,
                                                orders o, estates e,order_items oi ,wines w , estates_commissions ec, lkup_bottle_sizes lkbtsz,lkup_store_types stype";
               $sql =$sql." ,users_customers ucm, users u";
                    
               $sql .=" Where cm.deleted=0
                            and o.deleted=0
                             and oi.deleted=0
                             and cm.lkup_store_type_id =stype.lkup_store_type_id
                    AND o.customer_id = cm.customer_id
                    AND o.estate_id = e.estate_id
                    AND e.estate_id =".$estate_id.$adtCause.
                   // " and o.lkup_payment_type_id = " .$_REQUEST["searchAdt"].
                    " AND oi.order_id = o.order_id
                    and w.wine_id = oi.wine_id
                    and e.estate_id = ec.estate_id
                    and cm.customer_id=ucm.customer_id
							 and ucm.user_id=u.user_id
                    and (cmc.is_primary=1 or cmc.is_primary is NULL)
                    and ec.lkup_commission_types_id =cm.lkup_store_type_id
                              and date_format(o.delivery_date,'%Y%m%d')>='".$from.
                              "' and date_format(o.delivery_date,'%Y%m%d') <='".$to."'
                    and w.lkup_bottle_size_id=lkbtsz.lkup_bottle_size_id
                    GROUP BY cm.customer_id,oi.order_id
						  order by o.delivery_date";

        		}

	
         
       }//if search id =2 or 3
       if ($search_id==4) 
       {
	      

           $sql= " Select distinct w.wine_id id,
                                             concat(wine_name,' ', vintage) wine,
                                             w.cspc_code,
                                         	   lkcl.display_name color,
                                             lkbs.display_name size,
                                             concat('$',w.price_per_unit) price,
                                             unallocated,
                                             sample,
                                             breakage_corked,
                                             floor(w.total_bottles/w.bottles_per_case) cases,
                                             mod(w.total_bottles,w.bottles_per_case) btls,
                                              (w.total_bottles - ifnull(sample-breakage_corked,0)) bottles
                                            ";

          $sql .= " From wines w left outer join
                                            wine_allocations wal on wal.wine_id = w.wine_id,
                                            estates e,
                                            lkup_wine_color_types lkcl,
                                            lkup_bottle_sizes lkbs ";
          
       $sql .= " Where w.lkup_wine_color_type_id = lkcl.lkup_wine_color_type_id
AND w.lkup_bottle_size_id =lkbs.lkup_bottle_size_id
and w.price_per_unit!=0
and w.is_international<>1
and w.deleted=0
AND w.estate_id = e.estate_id and e.estate_id = $estate_id order by wine_name";
          

       }
  	if ($search_id==5 ) 
        {
           
							 
            $sql = " Select distinct cm.customer_id id,   cm.customer_name,
             lkstype.license_name store_type,
       		 concat(IFNULL(c.first_name,''),' ',IFNULL(c.last_name,'')) contact_name,
             CONCAT_WS('.', SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end), 1, 3), SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end)  , 4, 3), SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end) , 7))  contact_number,
                                               
 							 concat(IFNULL(cm.billing_address_unit,'') ,
							 IFNULL(cm.billing_address_street_number,''), ' ',
							 IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address,
							 alw2c.allocated allocated, alw2c.sold sold ";
							 
				$sql .= " From customers cm
							 left join customers_contacts cmc on cm.customer_id = cmc.customer_id and cmc.is_primary =1
							 left join contacts c on c.contact_id = cmc.contact_id  and c.deleted=0,
							 customer_wine_allocations alw2c, estates e,wines w,lkup_wine_color_types lkcl, lkup_store_types lkstype ";
           
            
            $sql .= " Where cm.deleted=0
							 and alw2c.customer_id = cm.customer_id
							 and w.estate_id = e.estate_id
							 and e.estate_id =".$estate_id.
							 " and w.wine_id=".$wine_id.
                             " and alw2c.wine_id = w.wine_id
                             and w.is_international<>1
                              and w.lkup_wine_color_type_id = lkcl.lkup_wine_color_type_id
                              and lkstype.lkup_store_type_id=cm.lkup_store_type_id and w.deleted=0
										and w.price_per_unit!=0
										order by customer_name";

       }
       
       //start to pageset
       
       	$pagedRS = & PagedDataSet::getInstance("db");
        
			$pagedRS->setPageSize(100000);
			$pagedRS->setCurrentPage(1);
			
			
			
			if (!$pagedRS->load($sql))
			{
			   $this->file_format_error .= "Error: unable to get report data.";
			   $bRet = false;
			   return false;
			}
			
			$rs["invoicData"] = $this->db->getAll($sql);
			
			
			if($search_id==14)
			{
				if(count($rs["invoicData"])==0)
					return 0;
			}
			return $rs;
	 	  
	}
	
	 function getTotalAmountByInvoiceNo($invoiceNo)
	{
	
        $sql = "Select 			 			 
			 sum(round( if(o.lkup_store_type_id=3, (oi.price_per_unit * oi.ordered_quantity) 
			  +(oi.litter_deposit * oi.ordered_quantity) +(oi.price_per_unit * oi.ordered_quantity)*0.05,
				(oi.price_winery * oi.ordered_quantity) +(oi.litter_deposit * oi.ordered_quantity)
			    +(oi.price_winery * oi.ordered_quantity)*0.05),2)) amount		 
			 From 
			 orders o,order_items oi
			 where invoice_number=$invoiceNo and o.order_id=oi.order_id
			 group by invoice_number
			 ";
			 
        $result = & F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        
     
        return  $row['amount'];
    }
    
	function getCustomInvoicesData( $estate_id, $sale_month, $sale_year)
	{	
		$sql = "Select cm.customer_id id,cm.lkup_store_type_id,
			 date_format(o.delivery_date ,'%m-%d-%Y') order_date,
			 o.invoice_number invoice_number, 
			 stype.license_name store_type, cm.licensee_number, cm.customer_name, 
			 concat(IFNULL(cm.billing_address_unit,'') , IFNULL(cm.billing_address_street_number,''), ' ',
			 IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address, 			 
			 oi.cspc_code sku, concat(oi.wine_name,' ',oi.wine_vintage) wine_name, 			 
			 oi.ordered_quantity orqt,			 
			 round(oi.ordered_quantity/w.bottles_per_case,2) total_cs,			 
			 oi.price_winery csws_price, oi.price_per_unit market_price, 			 			 
			 round( if(o.lkup_store_type_id=3, (oi.price_per_unit * oi.ordered_quantity) 
			  +(oi.litter_deposit * oi.ordered_quantity) +(oi.price_per_unit * oi.ordered_quantity)*0.05,
            (oi.price_winery * oi.ordered_quantity) +(oi.litter_deposit * oi.ordered_quantity)
            +(oi.price_winery * oi.ordered_quantity)*0.05),2) amount,			
			 if( o.lkup_payment_status_id =2, 'Paid','Not paid') isPaid						 
			 From customers cm left join customers_contacts cmc on cm.customer_id = cmc.customer_id and cmc.is_primary=1,
			 orders o, estates e,order_items oi, wines w,lkup_store_types stype 
			 where cm.deleted=0 
			 and o.customer_id = cm.customer_id and o.estate_id = e.estate_id  
			 and (cmc.is_primary=1 or cmc.is_primary is NULL) 
			 and stype.lkup_store_type_id=cm.lkup_store_type_id 
			 and o.deleted=0 and oi.deleted=0 
			 and oi.order_id = o.order_id and oi.wine_id = w.wine_id			 
			 and e.estate_id =$estate_id 
			 and year(o.delivery_date)=$sale_year
			 and month(o.delivery_date)=$sale_month
			 order by store_type,invoice_number 
			";
   
       //start to pageset
       
       		$pagedRS = & PagedDataSet::getInstance("db");
        
			$pagedRS->setPageSize(100);
			$pagedRS->setCurrentPage(1);
			
			if (!$pagedRS->load($sql))
			{
			   $this->file_format_error .= "Error: unable to get report data.";
			   $bRet = false;
			   return false;
			}
			
			$rs["invoicData"] = $this->db->getAll($sql);
		
			if(count($rs["invoicData"])==0)
				return 0;
		
			return $rs;
	 	  
	}
	
	function getWine($wine_id)
	{
        $sql = "select concat(wine_name,' ',vintage) wine_name from wines where wine_id = ".$wine_id;
        $result = & F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();

        return  $row['wine_name'];

     }
     
	function getOverDueQueryStr($overdue_type)
	{
		switch ($overdue_type)
	 	{
			case 0:
			
				$overdue_filter = " and (TO_DAYS(CURDATE()) - TO_DAYS(o.delivery_date) )>=31 ";
				break;
			case 1:
				$overdue_filter = " and ((TO_DAYS(CURDATE()) - TO_DAYS(o.delivery_date) )>=31 
										 and (TO_DAYS(CURDATE()) - TO_DAYS(o.delivery_date) <=60))";
				break;
			case 2:
				$overdue_filter = " and ((TO_DAYS(CURDATE()) - TO_DAYS(o.delivery_date) )>=60 
										 and (TO_DAYS(CURDATE()) - TO_DAYS(o.delivery_date) <=90))";
				break;
				
			/*case 3:
				$overdue_filter = " and ((TO_DAYS(CURDATE()) - TO_DAYS(o.delivery_date) )>=90 
										 and (TO_DAYS(CURDATE()) - TO_DAYS(o.delivery_date) <=120))"	;
				break;*/
			case 3:
				$overdue_filter = " and (TO_DAYS(CURDATE()) - TO_DAYS(o.delivery_date)) >=90 "	;
				break;
		}
		
		return $overdue_filter;
	}
     
 	function getOverDueInvoices($order_by, $order_type,$page_size,$page,$store_type_id,$overdue_type,$estate_id, $user_id)
	{
	 	$store_type_filter=($store_type_id == 0)?"":" and c.lkup_store_type_id = $store_type_id";
	 	$estate_filter=($estate_id == 0)?"":" and o.estate_id = $estate_id";
	 	
	 	
	 	$factor=($store_type_id==5)?"0":"o.agency_LRS_factor";
	 	$user_filter=($user_id == 0)?"":" and u.user_id = $user_id";
	 	
	 	
	 	$order_type =($order_type == "d")?"DESC":"ASC";
	 	
	 
		
	 	$overdue_filter =$this->getOverDueQueryStr($overdue_type) ;
	 	
	 	$order_by = $order_by." ".$order_type." , c.licensee_number ASC";
    
	    $sql = "SELECT TO_DAYS(CURDATE()) - TO_DAYS(o.delivery_date) overdays,e.estate_name,
				o.order_id,o.delivery_date, lktp.license_name, c.licensee_number, c.customer_name,
				concat_ws(' ', concat_ws('-', c.billing_address_unit, c.billing_address_street_number), c.billing_address_street,' - ',c.billing_address_city) as address, 
				o.delivery_date as order_date, 
				invoice_number, 
				sum(oi.ordered_quantity) btl_sold, 
				sum(oi.ordered_quantity)/w.bottles_per_case cases_sold, 
				
				sum(oi.price_per_unit * oi.ordered_quantity)+ if(o.deposit>0, o.deposit, sum(oi.litter_deposit * oi.ordered_quantity)) -( IFNULL(o.agency_LRS_factor,0)* sum(oi.price_per_unit * oi.ordered_quantity))- IFNULL(o.adjustment_1, 0.0) - IFNULL(o.adjustment_2, 0.0)as total_amount, 
				
				concat(u.first_name,' ', u.last_name) user_name 

				FROM estates e, orders o inner join customers c on c.customer_id = o.customer_id 
				inner join users_customers uc on uc.customer_id = c.customer_id 
				inner join users u on u.user_id =uc.user_id 
				inner join lkup_store_types lktp on c.lkup_store_type_id = lktp.lkup_store_type_id 
				inner join lkup_order_statuses os on o.lkup_order_status_id= os.lkup_order_status_id 
				inner join order_items oi on o.order_id = oi.order_id inner join wines w on w.wine_id = oi.wine_id 
				
				WHERE o.deleted = 0 
				and oi.ordered_quantity !=0
				and o.estate_id = e.estate_id
				and o.lkup_payment_status_id =1
				and c.licensee_number != 0				
				
				$store_type_filter
				$estate_filter
				$user_filter
				$overdue_filter
				group by o.order_id
				order by $order_by ";

		$pagedRS = & PagedDataSet::getInstance("db");
		
		
		$pagedRS->setPageSize($page_size);
		$pagedRS->setCurrentPage($page);
			
		if (!$pagedRS->load($sql))
		{
		   $this->file_format_error .= "Error: unable to get report data.";
		   $bRet = false;
		   return false;
		}
			
        
        $rs["invoicData"] = $pagedRS;
	 	$rs["total_records"]=$pagedRS->getTotalRecordCount();
	        
	       // $rs["totalSales"] = $this->db->getAll($sqlTotal);
			
			
			return $rs;

     }    
     
  	function getBCTotalInfoByStoreType($estate_id,$year,$month,$store_type)
    {
     $store_type_filter="";
     if($store_type!=-1)
     	$store_type_filter="and o.lkup_store_type_id=$store_type";
     	
    		$sql ="Select  
			 sum(oi.ordered_quantity) orqt,
           			 
			 round(sum(oi.ordered_quantity/w.bottles_per_case),2) total_cs,

		 	 round( sum(if(o.lkup_store_type_id=3, (oi.price_per_unit * oi.ordered_quantity) 
			  +(oi.litter_deposit * oi.ordered_quantity) +(oi.price_per_unit * oi.ordered_quantity)*0.05,
				(oi.price_winery * oi.ordered_quantity) +(oi.litter_deposit * oi.ordered_quantity)
			    +(oi.price_winery * oi.ordered_quantity)*0.05)),2) amount
			
             From 	orders o,order_items oi, wines w
			 where o.deleted=0 and oi.deleted=0 
			 and oi.order_id = o.order_id 	
             and w.wine_id=oi.wine_id
			 and o.estate_id =$estate_id
			 and year(o.delivery_date)=$year
			 and month(o.delivery_date)=$month
             $store_type_filter
			";
			  
			$rows = $this->db->getAll($sql);
			
		//	print_r($rows);
			
			return $rows;
	 }	     
	 

    function getForm60Users()
    {
    		$sql ="SELECT distinct u.user_id,concat(first_name,' ',last_name) user_name FROM users u, users_customers uc, orders o
					   WHERE o.deleted=0 
					   AND u.deleted =0
					   And u.user_id = uc.user_id
					   and uc.customer_id =o.customer_id
					   and o.lkup_payment_status_id =1
					   ORDER BY first_name";
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }	 
	 
	function getCaseValueList($province_id)
	{
		$sql ="SELECT 1 product, w.wine_name product_name, w.vintage, 
				bottles_per_case,lktype.display_name type,lksize.display_name size, 
				wf.cspc_code,wf.case_value,0 case_sold
				From wines w, wines_info wf , lkup_wine_color_types lktype, lkup_bottle_sizes lksize
				where w.wine_id =wf.wine_id and wf.province_id =$province_id 
				and w.lkup_wine_color_type_id =lktype.lkup_wine_color_type_id 
				and w.lkup_bottle_size_id =lksize.lkup_bottle_size_id
				and wf.case_value!=1
				and wf.deleted=0
				and w.is_available =0
				and wf.is_available=0
				UNION				
				SELECT 2 product, w.beer_name product_name, 0 vintage,bottles_per_case,
				lktype.display_name type,lksize.display_name size, 
				wf.cspc_code,wf.case_value,wf.case_sold
				From beers w, beers_info wf , lkup_beer_types lktype, lkup_beer_sizes lksize
				where w.beer_id =wf.beer_id and wf.province_id =$province_id 
				and w.lkup_beer_type_id =lktype.lkup_beer_type_id 
				and w.lkup_beer_size_id =lksize.lkup_beer_size_id
				and (wf.case_value/wf.case_sold)!=1
				and w.is_available =0
				and wf.is_available=0
				order by product asc, bottles_per_case asc
			 ";
		$rows = $this->db->getAll($sql);
		
		return $rows;
	}	 
	
	 
	function getOverdueUsers($user_id,$overdue_type, $estate_id, $store_type_id)
    {
 		$overdue_filter =$this->getOverDueQueryStr($overdue_type);
		
 		$store_type_filter=($store_type_id == 0)?"":" and o.lkup_store_type_id = $store_type_id";
	 	$estate_filter=($estate_id == 0)?"":" and o.estate_id = $estate_id";
	 	
	 
	 	$factor=($store_type_id==5)?"0":"o.agency_LRS_factor";
     	
     		if( $user_id ==0)
     		{
				$sql ="SELECT distinct u.user_id,concat(first_name,' ',last_name) user_name FROM users u, users_customers uc, orders o
					   WHERE o.deleted=0 
					   AND u.deleted =0
					   And u.user_id = uc.user_id
					   and uc.customer_id =o.customer_id
					   and o.lkup_payment_status_id =1
					   $store_type_filter 
					   $estate_filter
					   $overdue_filter
					   ORDER BY first_name";
			}
			else
			{
				$sql ="SELECT user_id,concat(first_name,' ',last_name) user_name FROM users 
				 	   WHERE user_id=$user_id ORDER BY first_name"; //temperary code by stupid disition	
				
			}
			
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }	 
	 
     
     /*
	 	If isExpiry = true, get expiry CC info
	 */
	 function getCCInfo($isExpiry=false,$user_id=null,$estate_id=0,$expirInDays =0)
	 {
//	 print $estate_id;;

			
	  		$estateFilter=" ";
			if($estate_id!=0)	
			{
				if($estate_id==-1)//enotecca
				{
					$estateFilter = " and (o.estate_id=96 or o.estate_id=97) ";
				}
				else
				{
					$estateFilter = " and o.estate_id=$estate_id ";	
				}
			}
			
			$store_type_filter="";
			if($estate_id==1)
			{
			//	$store_type_filter =" and c.lkup_store_type_id !=1 ";
			}
	
			if($user_id!=null)
			{
				$sql=$this->getExpiryCCQueryString($user_id,$expirInDays);
			}
			else
			{
				$sql="SELECT distinct c.customer_name customer_name, 
						c.licensee_number license_number, cc_number card_number, 
						cc_exp_month expiry_month, cc_exp_year expiry_year ,
						lkct.caption card_type
						
	 					FROM customers c, order_items od, orders o, lkup_payment_types lkct
	
						Where o.customer_id =c.customer_id and o.order_id=od.order_id 
						$estateFilter
						and cc_number<>''
						and c.lkup_payment_type_id!=1
						and c.lkup_payment_type_id!=2 
						and c.lkup_payment_type_id=lkct.lkup_payment_type_id
						and concat(cc_exp_year,'-', cc_exp_month) >= DATE_FORMAT( CURDATE(),'%Y-%m') 
						and c.deleted =0
						$store_type_filter
						order by c.customer_name ";
			}
					
			$pagedRS = & PagedDataSet::getInstance("db");
			$pagedRS->setPageSize(10000);
        	$pagedRS->setCurrentPage(1);
        
			if (!$pagedRS->load($sql))
			{
			    $this->file_format_error .= "Error: Unable to get Card information.";
			    $bRet = false;
			    return false;
			}
			
			return $pagedRS;
	 }
	 
	 function getExpiryCCQueryString($user_id,$estate_id=0,$expiryInDays=0)
	 {
//	 print $estate_id;;
	  		$estateFilter=" ";
			if($estate_id!=0)	
			{
				if($estate_id==-1)//enotecca
				{
					$estateFilter = " and (o.estate_id=96 or o.estate_id=97) ";
				}
				else
				{
					$estateFilter = " and o.estate_id=$estate_id ";	
				}
			}
			
//			$expiryDate="DATE_FORMAT( CURDATE(),'%Y-%m')";
		//	if($$expiryInDays!=0)
		//	{
		 		$expiryInDays =30;
				$expiryDate = date("Y-m ", time()+((60*60)*24*$expiryInDays));
		//	}		
			$user_filter=($user_id == 0)?"":" and u.user_id = $user_id";
		
			$sql="	SELECT cc_number card_number,lkct.caption card_type,
						cc_exp_month expiry_month,cc_exp_year expiry_year,
						cm.customer_name,lksty.license_name,cm.licensee_number license_number, 
						concat(IFNULL(c.first_name,''),' ',IFNULL(c.last_name,'')) contact_name, 
						CASE length(CASE WHEN cm.lkup_phone_type_id =1 THEN cm.phone_office1 WHEN cm.lkup_phone_type_id =2 THEN cm.phone_other1 ELSE cm.phone_fax END) WHEN 0 THEN '' WHEN 7 THEN CONCAT_WS('.', SUBSTRING((CASE WHEN cm.lkup_phone_type_id =1 THEN cm.phone_office1 WHEN cm.lkup_phone_type_id =2 THEN cm.phone_other1 ELSE cm.phone_fax END), 1, 3), SUBSTRING( (CASE WHEN cm.lkup_phone_type_id =1 THEN cm.phone_office1 WHEN cm.lkup_phone_type_id =2 THEN cm.phone_other1 ELSE cm.phone_fax END) , 4)) ELSE CONCAT_WS('.', SUBSTRING((CASE WHEN cm.lkup_phone_type_id =1 THEN cm.phone_office1 WHEN cm.lkup_phone_type_id =2 THEN cm.phone_other1 ELSE cm.phone_fax END), 1, 3), SUBSTRING( (CASE WHEN cm.lkup_phone_type_id =1 THEN cm.phone_office1 WHEN cm.lkup_phone_type_id =2 THEN cm.phone_other1 ELSE cm.phone_fax END) , 4, 3), SUBSTRING( (CASE WHEN cm.lkup_phone_type_id =1 THEN cm.phone_office1 WHEN cm.lkup_phone_type_id =2 THEN cm.phone_other1 ELSE cm.phone_fax END) , 7)) END contact_number, 
						concat(IFNULL(billing_address_unit,'') , IFNULL(billing_address_street_number,''), ' ', IFNULL(billing_address_street,''),' ',IFNULL(billing_address_city,''))address, 
						concat(IFNULL(u.first_name,'Not'),' ',IFNULL(u.last_name,'Assgined')) user_name 					
					FROM lkup_payment_types lkct, lkup_store_types lksty,orders o,
						customers cm LEFT JOIN customers_contacts cmc ON cm.customer_id = cmc.customer_id 
						AND cmc.is_primary=1 LEFT JOIN contacts c ON c.contact_id = cmc.contact_id 
						AND c.deleted=0 
						LEFT OUTER JOIN users_customers ucm ON ucm.customer_id = cm.customer_id 
						LEFT OUTER JOIN users u ON u.user_id = ucm.user_id 
					
					WHERE cm.lkup_store_type_id=lksty.lkup_store_type_id 
						AND lksty.province_id = 1 
						AND lksty.lkup_store_type_id =cm.lkup_store_type_id 
						AND cm.deleted=0 AND cm.status<>2
						and	cc_number<>''
						and cm.lkup_payment_type_id!=1
						and cm.lkup_payment_type_id!=2 
						and cm.lkup_payment_type_id=lkct.lkup_payment_type_id
						and cm.lkup_store_type_id < 8
						and concat(cc_exp_year,'-', cc_exp_month) <=  '$expiryDate'
						and o.customer_id=cm.customer_id
                        and o.delivery_date>= DATE_SUB(CURDATE(), INTERVAL 365 DAY) 
                       
					$user_filter
						
									
					GROUP BY cm.customer_id 
					order by cc_exp_year asc, cc_exp_month asc ";
					
			//estate_id =110
			return $sql;
	 }
	 
	 /*
	 	Get lastest not paid but delivered order information for the customer who has the expiry credit card and purchased BC wine in one year back from current day
	 
	 */
	function getLatestOrderByExpiryCC($license_no)
	{
		$sql="SELECT invoice_number, date_format(delivery_date,' %m-%d-%Y') delivery_date, e.estate_name
			 FROM orders o, estates e 
			 Where licensee_number =$license_no 
			 And lkup_order_status_id =2 
			 And lkup_payment_status_id =1
 			 And delivery_date>= DATE_SUB(CURDATE(), INTERVAL 365 DAY) 
 			 And o.estate_id =e.estate_id
             And e.deleted =0
             
			 Order by delivery_date desc
			 Limit 1";
			 
		$rows = $this->db->getAll($sql);
			
		return $rows;
		
	}
	 
	function getConfigVal($config)
    {     	
        return $this->cfg[$config];
    }
    
	function sendCCEmail($isCC=0)
	{       
	 
						
			// office BCC list and Estate BCC list
			$bccAddress = $this->getConfigVal("BCC_EMAIL_RECEPIENTS");
			
		//
			//$bccAddress ="";
			$toAddress=$this->getConfigVal("EMAIL_SALES_RECEPIENTS");
			//$toAddress ="helen@christopherstewart.com;";
								
			// Email from address
			$fromAddress=$this->getConfigVal("EMAIL_FROM_ADDRESS");
						
			$currentDate = date("F dS, Y");
		
		
			if($isCC==0)
			{
			//Email subject
				$emailSubject = $this->getConfigVal("EMAIL_EXPIRY_CC_CARD_SUBJECT").$currentDate;
			
				
				// Email content	
				$emailContent = $this->getConfigVal("EMAIL_EXPIRY_CC_CARD_CONTENT");		
			
				$from_name = $this->getConfigVal("EMAIL_FROM_CSWS_TITLE");
				
				 $arrayAttachFileNames =array();
		        //total alberta sales report
		
				import('Form60.pages.excelCCReport');
				
				$excelReport = new excelCCReport(true);
				$fileName = $excelReport->generateExpiryCCSpreadsheet(true);
				array_push($arrayAttachFileNames,$fileName);
				
			}
			else
			{
			 	$emailSubject = $this->getConfigVal("EMAIL_OVERDUE_SUBJECT").$currentDate;;
							
				// Email content	
				$emailContent = $this->getConfigVal("EMAIL_OVERDUE_CONTENT");		
			
				$from_name = $this->getConfigVal("EMAIL_FROM_CSWS_TITLE");
				 
				import('Form60.exportreports.excelOverdueReport');
				
				
				$excelReport = new excelOverdueReport(1,1,0,0,true);
			
				$estateIDs=array(1,126); //Bench 1775, Bench 1775 Paradise Ranch
				$arrayAttachFileNames =array();
				
				foreach ($estateIDs as $estateid)
				{
				
					
					$fileName = $excelReport->generateSpreadsheet(true,$estateid,0,0,0);
				
					array_push($arrayAttachFileNames,$fileName);	
				
				}				
			}
			
			$toAddress = $this->getConfigVal("EMAIL_SALES_RECEPIENTS");
			$bccAddress = $this->getConfigVal("EMAIL_BCC_INFO_ESTATE_RECEPIENTS_CC_CARD").$this->getConfigVal("EMAIL_BCC_INFO_CSWS_RECEPIENTS");
		
	
			if (file_exists($fileName))
			{    
				if(F60Common::_sendEmail($toAddress,$bccAddress,$fromAddress,$emailSubject,$emailContent,$from_name,$arrayAttachFileNames))
				 return true;				
			}
			else
			{
				$toAddress="helen@christopherstewart.com"; 
			 	$bccAddress ="";
			 	$emailContent ="Email sent failed."; 
			 	
				if(F60Common::_sendEmail($toAddress,$bccAddress,$fromAddress,$emailSubject,$emailContent,$from_name))
					return true;	
			}
	}

	
//BC estate sales report

		function getSkusById($estate_id, $province_id=1, $sMonth="", $sYear="") 
	{
		$sql="select distinct cspc_code+0 cspc_code, bottles_per_case from wines where estate_id=$estate_id and deleted=0 and cspc_code!=''";
		
	//	$sYear =date(Y);

	//	echo $province_id;
		if($province_id ==2)
		{
			$sql="SELECT distinct ab.skua cspc_code, w.bottles_per_case bottles_per_case
					FROM `ab_sales_bc` ab , wines w, wines_info wf
					
					where year(sale_date)=$sYear
					and  month(sale_date)=$sMonth
						
					and w.wine_id = wf.wine_id
					
					and w.estate_id =$estate_id
					
					and ab.skua = wf.cspc_code";
		}
		
		$rows = $this->db->getAll($sql);			
		
	
		
		return $rows;
	}
	
	function getSubEstates($bc_estate_id) 
	{
		$sql="select * from bc_estates where bc_estate_id=$estate_id";
		$rows = $this->db->getAll($sql);			
		return $rows;
	}
	
	function getSingleWineInfoBySku($cspc_code, $province_id =1, $month, $year)
	{
		$sql="select concat_ws(' ',wine_name,vintage) wine_name from wines where cspc_code=$cspc_code and deleted=0 order by wine_name asc, vintage desc";
		
		if($province_id ==2)
		{
			$sql="select  concat(product_name) wine_name from ab_sales_bc where skua=$cspc_code and year(sale_date)=$year and month(sale_date)=$month order by wine_name asc";
		}
		
		$rows = $this->db->getAll($sql);
		
		if(count($rows)==0)
			$wine_name="";
		else
			$wine_name = $rows[0]["wine_name"]." - $cspc_code";
		
		return $wine_name;
	}
	
	/*
		$location_id: 0 =All ; 2 = okanagan; 1= v island; 3 = whistler
	*/
	function getForm60Details($estate_id,$sYear,$sMonth,$location_id=0,$province_id =1 )
	{
	 //	$sYear = date("Y");
	 	
	 //	$store_type_filter=($store_type_id == 0)?"":" and c.lkup_store_type_id = $store_type_id";
	 	$estate_filter=($estate_id == 0)?"":" and o.estate_id = $estate_id";
	 	
	 	
	 	$date_filter=" and year(o.delivery_date)=$sYear 
		 			   and month(o.delivery_date)=$sMonth ";
		 			   
		$location_filter ="";
		
		$okanagan_user_id=$this->data_cfg["OKANAGAN_USER"];
		$victoria_User_id=$this->data_cfg["VAN_ISLAND_USER"];
		

		
		switch ($location_id) // of 1 or 2 not whistler, the customer will be Liesly: okanagan or Jill: van island ( not good code here)
		{
		 	case 0: // vancouver
				$location_filter = " and u.user_id!=$victoria_User_id and u.user_id!=$okanagan_user_id and (c.billing_address_city != 'whistler' 
									and c.billing_address_city != 'squamish' 
									and c.billing_address_city != 'pemberton')";
				break;
		 	case 1: // vic island user_id =99
				$location_filter = " and u.user_id=$victoria_User_id";
				break;
			case 2: //okan, user id =102
				$location_filter = " and u.user_id=$okanagan_user_id";
				break;
			case 3: // whistler: customer city = whistler, 
				$location_filter = " and (c.billing_address_city like '%whistler%' 
									or c.billing_address_city like '%squamish%' 
									or c.billing_address_city like '%pemberton%')";
				break;
			
		}	
	//sum(oi.ordered_quantity) btl_sold, 				
				//sum(oi.price_per_unit * oi.ordered_quantity)+ if(o.deposit>0, o.deposit, sum(oi.litter_deposit * oi.ordered_quantity)) -( IFNULL(o.agency_LRS_factor,0)* sum(oi.price_per_unit * oi.ordered_quantity))- IFNULL(o.adjustment_1, 0.0) - IFNULL(o.adjustment_2, 0.0)as total_amount, 
		
		$newrule = true;// start from 2015-04-01 new calculation 
		
		if($sYear<2015)
			$newrule = false;
		if($sYear==2015 && $sMonth<4)
			$newrule = false;
		
		if($province_id ==1)
		{
		 	if($newrule) // start from 2015-04-01 new calculation 
		 	{
				 $sql = "Select o.order_id,
					date_format(o.delivery_date,' %m-%d-%Y') as order_date, 
					lktp.license_name, 
					o.lkup_store_type_id,
					c.licensee_number, c.customer_name,
					c.billing_address_city as city, 
					
					o.invoice_number, 
					oi.ordered_quantity btl_sold, 				
					oi.cspc_code,
					if(o.lkup_store_type_id =3, oi.price_winery,oi.price_per_unit) price,
					      if(o.lkup_store_type_id =3,
					      	(oi.price_per_unit * oi.ordered_quantity) + if(o.deposit > 0,  o.deposit, (oi.litter_deposit * oi.ordered_quantity)) +  (oi.price_per_unit * oi.ordered_quantity) *0.05,			      
					      (oi.price_winery * oi.ordered_quantity) + if(o.deposit > 0,  o.deposit, (oi.litter_deposit * oi.ordered_quantity)) +  (oi.price_winery * oi.ordered_quantity) *0.05
 ) as total_amount,
 					u.user_id
					
					FROM estates e, orders o inner join customers c on c.customer_id = o.customer_id 
					inner join users_customers uc on uc.customer_id = c.customer_id 
					inner join users u on u.user_id =uc.user_id 
					inner join lkup_store_types lktp on c.lkup_store_type_id = lktp.lkup_store_type_id 
					inner join lkup_order_statuses os on o.lkup_order_status_id= os.lkup_order_status_id 
					inner join order_items oi on o.order_id = oi.order_id inner join wines w on w.wine_id = oi.wine_id 
					
					WHERE o.deleted = 0 
					and oi.ordered_quantity !=0
					and o.estate_id = e.estate_id
					and c.licensee_number != 0		
					$estate_filter	
					$date_filter
					$location_filter
					
					order by lktp.license_name asc,o.delivery_date,customer_name asc,o.invoice_number asc
			
					";
			}
			else
			{
			  if($location_id ==3)
				  $sql = "Select o.order_id,
						date_format(o.delivery_date,' %m-%d-%Y') as order_date, 
						lktp.license_name, 
						o.lkup_store_type_id,
						c.licensee_number, c.customer_name,
						c.billing_address_city as city, 
						
						o.invoice_number, 
						oi.ordered_quantity btl_sold, 				
						oi.cspc_code,
						oi.price_per_unit price,
				
						(oi.price_per_unit * oi.ordered_quantity)+ if(o.deposit>0, o.deposit, (oi.litter_deposit * oi.ordered_quantity)) -( IFNULL(o.agency_LRS_factor,0)* (oi.price_per_unit * oi.ordered_quantity))- IFNULL(o.adjustment_1, 0.0) - IFNULL(o.adjustment_2, 0.0)as total_amount,
						u.user_id
						
						FROM estates e, orders o inner join customers c on c.customer_id = o.customer_id 
						inner join users_customers uc on uc.customer_id = c.customer_id 
						inner join users u on u.user_id =uc.user_id 
						inner join lkup_store_types lktp on c.lkup_store_type_id = lktp.lkup_store_type_id 
						inner join lkup_order_statuses os on o.lkup_order_status_id= os.lkup_order_status_id 
						inner join order_items oi on o.order_id = oi.order_id inner join wines w on w.wine_id = oi.wine_id 
						
						WHERE o.deleted = 0 
						and oi.ordered_quantity !=0
						and o.estate_id = e.estate_id
						and c.licensee_number != 0		
						$estate_filter	
						$date_filter
						$location_filter
						
						order by lktp.license_name asc,o.delivery_date,customer_name asc,o.invoice_number asc
				
						";
			  else
			  {
				 $sql = "Select o.order_id,
					date_format(o.delivery_date,' %m-%d-%Y') as order_date, 
					lktp.license_name, 
					o.lkup_store_type_id,
					c.licensee_number, c.customer_name,
					c.billing_address_city as city, 
					
					o.invoice_number, 
					oi.ordered_quantity btl_sold, 				
					oi.cspc_code,
					oi.price_per_unit price,
			
					(oi.price_per_unit * oi.ordered_quantity)+ if(o.deposit>0, o.deposit, (oi.litter_deposit * oi.ordered_quantity)) -( IFNULL(o.agency_LRS_factor,0)* (oi.price_per_unit * oi.ordered_quantity))- IFNULL(o.adjustment_1, 0.0) - IFNULL(o.adjustment_2, 0.0)as total_amount,
					u.user_id
					
					FROM estates e, orders o inner join customers c on c.customer_id = o.customer_id 
					inner join users_customers uc on uc.customer_id = c.customer_id 
					inner join users u on u.user_id =uc.user_id 
					inner join lkup_store_types lktp on c.lkup_store_type_id = lktp.lkup_store_type_id 
					inner join lkup_order_statuses os on o.lkup_order_status_id= os.lkup_order_status_id 
					inner join order_items oi on o.order_id = oi.order_id inner join wines w on w.wine_id = oi.wine_id 
					
					WHERE o.deleted = 0 
					and oi.ordered_quantity !=0
					and o.estate_id = e.estate_id
					and c.licensee_number != 0		
					$estate_filter	
					$date_filter
					$location_filter
					
					order by lktp.license_name asc,o.delivery_date,customer_name asc,o.invoice_number asc
			
					";
					}
			}
		   
		}
		else
		{
			 	//
			 	
				$sql="select distinct 
					'order_date', 'Alberta licensee' license_name,c.licensee_number,
					 c.customer_name,c.billing_address_city as city, 
					 'N/A' invoice_number, ab.unit_sales btl_sold, ab.skua cspc_code, ab.price_unit,
					ab.price_case*ab.total_cs total_amount, '100' user_id,
					
					ab.*  from ab_sales_bc ab , wines_info wf, wines w , customers c
					
					where ab.skua=wf.cspc_code
								
					and wf.wine_id =w.wine_id
										
					and w.estate_id =$estate_id
					and month(ab.sale_date)= $sMonth
					and year(ab.sale_date)= $sYear
					
					and ab.licensee_no =c.licensee_number
					
			

					order by licensee_number asc, cspc_code
				
					";
			}
/*if($province_id==2)
{
			$fp = fopen("logs/report.log","a");
		fputs($fp, $sql."\n");
		fclose($fp);}
*/		
		
			$results =$this->db->getAll($sql);
		
		//o.invoice_number=103743 or 
	
			
			return $results;
		
  
	
     }    
     
     
   
     
	

}
?>