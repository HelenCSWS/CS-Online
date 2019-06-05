<?php
/**
 * Perform the necessary imports
 */

import('Form60.base.F60PageBase');
import('php2go.data.Report');
import('Form60.base.F60ReportsBase');
import('php2go.util.HtmlUtils');
import('Form60.base.F60DbUtil');

//$F60_PAGE_ID['CUSTOMER'] = "4";
define('F60_PAGE_ID_CUSTOMER', '4');
define('F60_PAGE_ID_UPDATE_WINE', '25');
define('F60_PAGE_ID_ADD_WINE', '24');
define('F60_PAGE_ID_WINE_DELIVERY', '19');
define('F60_PAGE_ID_UPDATE_SAMPLE', '20');
define('F60_PAGE_ID_WINE_ALLOCATE', '18');
define('F60_PAGE_ID_USER', '14');
define('F60_PAGE_ID_ESTATE', '1');

class F60Reports extends F60PageBase
{
    var $pageid;
    
	function F60Reports()
	{
	   $search_id = $_REQUEST["searchType"];

            $title="No result";

             switch ($search_id)
             {
                case 1:

                    $estateid =$_REQUEST["estateid"];
                    $from =$_REQUEST["from"];
                    
                    $to =$_REQUEST["to"];
                    $user_id=$_REQUEST["user_id"];
                  

                    $sfrom=$this->getFormatDate($from);
                    $sto=$this->getFormatDate($to);
                    $estatename=$this->getEstate($estateid);
                    $title = 'Find all invoice for '.$this->getEstate($estateid).' from '.$this->getFormatDate($from).' to '.$this->getFormatDate($to);
                    
                    $totalcs =0;
                    $totalcs =$this->getBCTotalCases( $search_id,$user_id,$from,$to,$estateid);
                    
               
                    break;
                case 2:
                    $isPaid =" not paid";
                    if ($_REQUEST["searchAdt"]==2)
                        $isPaid=" paid";

                    $title="Who has ".$isPaid.' for '.$this->getEstate($_REQUEST["estateid"]).' from '.$this->getFormatDate($_REQUEST["from"]).' to '.$this->getFormatDate($_REQUEST["to"]);
                    break;
                case 3:
                    $isrecieved =" not recieved";
                    if ($_REQUEST["searchAdt"]==2)
                        $isPaid=" recieved";
                    $title="Who has ".$isrecieved.' for '.$this->getEstate($_REQUEST["estateid"]).' from '.$this->getFormatDate($_REQUEST["from"]).' to '.$this->getFormatDate($_REQUEST["to"]);
                    break;
                  case 4:
                      $title="Print complete inventory for ".$this->getEstate($_REQUEST["estateid"]);
                    break;
                  case 5:
                      $title="Allocation summary for ".$this->getEstate($_REQUEST["estateid"]).' by';
                    break;
               case 6:
                    $title="Print report by".$this->getStoreType($_REQUEST['store_type_id']).' for '.$this->getEstate($_REQUEST["estateid"]).' from '.$this->getFormatDate($_REQUEST["from"]).' to '.$this->getFormatDate($_REQUEST["to"]);
                    break;

	    	}

           
      F60PageBase::F60PageBase('F60Reprots',$title, 'F60SearchResult.tpl');
      
       
      $surl ='resources/js/javascript.reports.js';
      $this->addScript($surl);
      $surl ='resources/js/javascript.suppliersales.js';
      $this->addScript($surl);
      
      if($search_id==1)
      {
       		
       	   $funcName = "loadReports($totalcs);";
	      $this->attachBodyEvent('onLoad', $funcName);
     }
      
	}//FUNCTON END
	
    function getBCTotalCases( $search_id,$user_id,$from,$to,$estate_id)
    {
			$userFilter =" AND o.customer_id = uc.customer_id 
				 		AND uc.user_id=$user_id";
			
			$userFilteTable=", users_customers uc ";
			if($user_id =="")
			{
				
				$userFilter="";
				$userFilteTable="";
			}
			
	
			
			$estateFilter = " AND o.estate_id = $estate_id";
			if($estate_id ==0)
				$estateFilter ="";
		
			$sql="SELECT round(sum(oi.ordered_quantity/w.bottles_per_case),2) cases

				FROM  orders o, estates e,order_items oi, wines w $userFilteTable 
				
				WHERE 
				 o.deleted=0 AND oi.deleted=0 
				 $estateFilter
				 $userFilter
				 
				 
				 and e.estate_id =w.estate_id
				 AND oi.wine_id = w.wine_id 
				 AND oi.order_id = o.order_id 
				 
				 AND date_format(o.delivery_date,'%Y%m%d')>='$from' AND date_format(o.delivery_date,'%Y%m%d') <='$to' ";
				 
			  	$db = & Db::getInstance();
        		$db->setFetchMode(ADODB_FETCH_ASSOC);	
        		
        		 $bRet=$db->getAll($sql);
		 
		 	
				 return $bRet[0]['cases'];				 
				
	}
	
	function display()
	{
        $search_id="";
        $from="";
        $to="";
        $estateid="";
        $storetypeidAdt="";
        $searchAdt="";
        $wine_id="";
        $storetype_id= $_REQUEST["store_type_id"];
        
       
        $estateid ="";
        
        $this->Template->globalAssign("storetype_id", "storetype_id");
        
        $search_id = $_REQUEST["searchType"];
        
        $user_id="";
   
        $report =& new F60ReportsBase('resources/xml/F60SearchResult.xml', 'resources/template/F60SearchResultReport.tpl', $this);
        $report->setStyleMapping('links', 'input', 'input', 'title');	// set link, input, button and title styles

        $report->hasHeader = true;// enable column headers
        $report->setAlternateStyle('cellA', 'cellB');					// set alternate style (at least 2)
        $report -> setSearchPara($search_id,"");
  
  
  	
       
       
  
  
        if ($search_id==1||$search_id==6) // all invoice
        {
            $from =$_REQUEST["from"];
            $to =$_REQUEST["to"];
            $estateid =$_REQUEST["estateid"];
            
  
            
            $this->Template->globalAssign("storetype_id", $estateid);
            
            $user_id=$_REQUEST["user_id"];
            
            $requststr = "&searchType=".$search_id."&from=".$from."&to=".$to."&estateid=".$estateid."&user_id=".$user_id;
            if ($search_id==6)
            {
                $wine_id = $_REQUEST["wine_id"];
                $requststr = $requststr."&wine_id=".$wine_id;
            }
                
                $estateField="";
               // echo $search_id;
                
                if($search_id==1&&$estateid ==0)
                {
                
					$estateField ="e.estate_name,";
				}
        		$report->_dataSource['FIELDS'] .= "cm.customer_id id, o.order_id order_id,
																date_format(o.delivery_date ,'%m-%d-%Y') delivery_date,
																o.invoice_number invoice_number,
																stype.license_name store_type,
																cm.licensee_number,$estateField
																cm.customer_name,
																concat(IFNULL(cm.billing_address_unit,'') ,
																IFNULL(cm.billing_address_street_number,''), ' ',
																IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address,
																
																concat('$',format(".$this->getAmountOwned().",2))  as amount_owned,
																sum(oi.ordered_quantity/w.bottles_per_case) total_cs,
																if( o.lkup_payment_status_id =2, 'Paid','Not paid')  isPaid,
																if( o.lkup_order_status_id =2, 'Delivered','Pending')  isRecieved,
																concat(IFNULL(u.first_name,''),' ',IFNULL(u.last_name,'')) user_name";
                             
                             
           $storetypeidAdt = "";
           if ( $search_id==6 )
           {
                $storetypeidAdt = " and cm.lkup_store_type_id = " .$_REQUEST["store_type_id"]." and w.wine_id = ".$wine_id;
             //   $storeTypeID="$_REQUEST["store_type_id"]|$wine_id";
                $requststr = $requststr."&store_type_id=".$_REQUEST["store_type_id"];
           }

            $userAdt ="";
            if ($user_id!="")
            {
                $userAdt =" and cm.customer_id=ucm.customer_id and ucm.user_id=".$user_id;
            }
            
            $estateFilter="";
            
            if($estateid!=0)
            	$estateFilter="and e.estate_id =$estateid";
            	
            $cause = "cm.deleted=0
                      and stype.lkup_store_type_id=cm.lkup_store_type_id
                      and o.deleted=0
                      and oi.deleted=0
							 and o.customer_id = cm.customer_id
							 and o.estate_id = e.estate_id
							 and cm.customer_id=uc.customer_id
							 and uc.user_id=u.user_id
							 
							 $estateFilter 
							 $storetypeidAdt $userAdt 
                   
                     and oi.order_id = o.order_id
                      and oi.wine_id = w.wine_id
                      and (cmc.is_primary=1 or cmc.is_primary is NULL)
                      and date_format(o.delivery_date,'%Y%m%d')>='$from' 
					  and date_format(o.delivery_date,'%Y%m%d') <='$to'
				 group by cm.customer_id,oi.order_id";


             $report->_dataSource['CLAUSE']=$cause;

            $tables =  "customers cm
							 left join customers_contacts cmc on cm.customer_id = cmc.customer_id and cmc.is_primary=1,
							 
							 orders o, estates e,order_items oi, wines w,lkup_store_types stype , users_customers uc, users u";
            if($user_id!="")
            {
                $tables = $tables." ,users_customers ucm ";
            }
            $report->_dataSource['TABLES'] =$tables;
            $report->_dataSource['ORDERBY'] = "o.delivery_date";

            $report->setColumnAlias('customer_name', 'Customer');					// set column aliases
           // $report->setColumnAlias('contact_name', 'Contact name');
            $report->setColumnAlias('store_type', 'Store type');
            $report->setColumnAlias('address', 'Address');
           $report->setColumnAlias('invoice_number', 'Invoice#');
           $report->setColumnAlias('delivery_date', 'Delivery');
           $report->setColumnAlias('licensee_number', 'Licensee#');
           $report->setColumnAlias('amount_owned', 'Total');
           $report->setColumnAlias('total_cs', 'Cases');
           $report->setColumnAlias('isPaid', 'Paid');
           $report->setColumnAlias('isRecieved', 'Status');
           $report->setColumnAlias('user_name', 'Assinged to');
			$report->setColumnSizes(array(1,1,1,1,1,1,1,1,1,1,90));
			
			 if($search_id==1&&$estateid ==0)
			 {
			  	$report->setColumnAlias('estate_name', 'Estate');
				$report->setColumnSizes(array(1,1,1,1,1,1,1,1,1,1,1,89));
				
			}
            $report->setHidden("id");
            $report->setHidden("order_id");
            
      		$report->pageLink ="main.php?page_name=customerAdd";//$_SERVER["REQUEST_URI"];
  			$report->idName = "id";
  			$report->order_idName="order_id";
  			$report->setRequestString($requststr);

       }
 if ($search_id==2 ||$search_id==3)
{
            $from =$_REQUEST["from"];
            $to =$_REQUEST["to"];
            $estateid =$_REQUEST["estateid"];
             $user_id=$_REQUEST["user_id"];

            $searchAdt = $_REQUEST["searchAdt"];
 

             $requststr = "&searchType=".$search_id."&from=".$from."&to=".$to."&estateid=".$estateid."&searchAdt=".$searchAdt."&user_id=".$user_id;
      //  print $searchAdt;
       $report -> setRequestString($requststr);

       if ( ($search_id ==2&&$searchAdt==2)||($search_id ==3&&$searchAdt==2)) // paid or recived
        {
        $field ="cm.customer_id id,o.order_id, cm.customer_name,
							 concat(IFNULL(c.first_name,''),' ',IFNULL(c.last_name,'')) contact_name,
							 CONCAT_WS('.', SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end)
, 1, 3), SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end)  , 4, 3), SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end) , 7))  contact_number,							 concat(IFNULL(cm.billing_address_unit,'') ,
							 IFNULL(cm.billing_address_street_number,''), ' ',
							 IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address,
							 o.invoice_number invoice_number, o.delivery_date delivery_date,
							 concat('$',format(".$this->getAmountOwned().",2))  as amount_owned,
							  sum(oi.ordered_quantity/w.bottles_per_case) total_cs,
							  if( o.lkup_payment_status_id =2, 'Paid','Not paid')  isPaid,
							  concat(IFNULL(u.first_name,''),' ',IFNULL(u.last_name,'')) user_name";

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
                    $adtCause =$adtCause." and cm.customer_id=ucm.customer_id and ucm.user_id=".$user_id;
                }
                $cause = "cm.deleted=0
									and o.deleted=0
									and oi.deleted=0
									and o.customer_id = cm.customer_id
									and o.estate_id = e.estate_id
									and oi.wine_id=w.wine_id
									and cm.customer_id=ucm.customer_id
									and ucm.user_id=u.user_id
									and e.estate_id =".$estateid.$adtCause.
									" and oi.order_id = o.order_id
									and (cmc.is_primary=1 or cmc.is_primary is NULL)
									and date_format(o.delivery_date,'%Y%m%d')>='".$from.
									"' and date_format(o.delivery_date,'%Y%m%d') <='".$to."'
									group by cm.customer_id,oi.order_id";
							 
        $tables ="customers cm
							 left join customers_contacts cmc on cm.customer_id = cmc.customer_id and cmc.is_primary=1
							 left join contacts c on c.contact_id = cmc.contact_id  and c.deleted=0,
							 orders o, estates e,order_items oi, wines w";
							 
                    $tables =$tables." ,users_customers ucm, users u";
							 
							 
							 
      }
      elseif ( ($search_id ==2&&$searchAdt==1)||($search_id ==3&&$searchAdt==1)) // not paid or not recived
         {	


 $field  = "o.delivery_date delivery_date,o.order_id,
                                                o.invoice_number invoice_number,
                                                cm.customer_id id,cm.customer_name,
                                                stype.license_name store_type,
                                                concat(IFNULL(cm.billing_address_unit,'') ,
							 IFNULL(cm.billing_address_street_number,''), ' ',
							 IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address,
                                                concat('$',format(".$this->getAmountOwned().",2))  as amount_owned,
                                                

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

               $cause =" cm.deleted=0
                            and o.deleted=0
                             and oi.deleted=0
                             and cm.lkup_store_type_id =stype.lkup_store_type_id
                    AND o.customer_id = cm.customer_id
                    AND o.estate_id = e.estate_id
                    AND e.estate_id =".$estateid.$adtCause.
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
                    GROUP BY cm.customer_id,oi.order_id";

            $tables ="customers cm
			LEFT JOIN customers_contacts cmc
			ON cm.customer_id = cmc.customer_id AND cmc.is_primary=1
                                                LEFT JOIN contacts c ON c.contact_id = cmc.contact_id
                                                                                        AND c.deleted=0,
                                                orders o, estates e,order_items oi ,wines w , estates_commissions ec, lkup_bottle_sizes lkbtsz,lkup_store_types stype";

              
                    $tables =$tables." ,users_customers ucm, users u";
              


        }

			
            $report->_dataSource['FIELDS'] = $field;

          //  print
            $report->_dataSource['CLAUSE'] = $cause;
            $report->_dataSource['TABLES'] = $tables;
            $report->_dataSource['ORDERBY']= "o.delivery_date";


             if ( ($search_id ==2&&$searchAdt==2)||($search_id ==3&&$searchAdt==2))
             {
                     $report->setColumnAlias('customer_name', 'Customer');					// set column aliases
                        $report->setColumnAlias('contact_name', 'Contact name');
                        $report->setColumnAlias('contact_number', 'Phone number');
                     //   $report->setColumnAlias('phone_fax', 'Fax number');
                        $report->setColumnAlias('address', 'Address');
                       $report->setColumnAlias('invoice_number', 'Invoice#');
                       $report->setColumnAlias('delivery_date', 'Delivery');
                       $report->setColumnAlias('amount_owned', 'Total');
                       $report->setColumnAlias('total_cs', 'Cases');
                      $report->setColumnAlias('isPaid', 'Status');
                       $report->setColumnAlias('user_name', 'Assigned to');

                        $report->setColumnSizes(array(1,1,1,1,1,1,1,1,1,91)); }
             elseif ( ($search_id ==2&&$searchAdt==1)||($search_id ==3&&$searchAdt==1)) // not paid or not recived
             {
                        $report->setColumnAlias('delivery_date', 'Delivery');					// set column aliases
                        $report->setColumnAlias('invoice_number', 'Invoice#');
                        $report->setColumnAlias('customer_name', 'Customer');
                        $report->setColumnAlias('store_type', 'Story type');
                        $report->setColumnAlias('address', 'Address');
                        $report->setColumnAlias('amount_owned', 'Amount');
                       $report->setColumnAlias('total_cs', 'Cases');
                       
                       
                       $report->setColumnAlias('isPaid', 'Paid');
                       $report->setColumnAlias('isRecieved', 'Status');
                       $report->setColumnAlias('user_name', 'Assigned to');
                      
																

                        $report->setColumnSizes(array(10,10,10,10,10,10,10,10,10,10));

             }
            $report->setHidden("id");
            $report->setHidden("order_id");
      		$report->pageLink ="main.php?page_name=customerAdd";//$_SERVER["REQUEST_URI"];
			$report->idName = "id";
			$report->order_idName="order_id";
			$report->setRequestString($requststr);

       }

 if ($search_id==4) 
        {
	      	$from =$_REQUEST["from"];
            $to =$_REQUEST["to"];
            $estateid =$_REQUEST["estateid"];

            $requststr = "&searchType=".$search_id."&from=".$from."&to=".$to."&estateid=".$estateid;
            $report -> setRequestString($requststr);

           $report->_dataSource['FIELDS']= " distinct w.wine_id id,
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

          //  $cause = " wal.wine_id = w.wine_id and w.lkup_wine_color_type_id = lkcl.lkup_wine_color_type_id                      and w.lkup_bottle_size_id =lkbs.lkup_bottle_size_id                    and w.estate_id = e.estate_id and e.estate_id = ".$estateid;
          
       $cause = " w.lkup_wine_color_type_id = lkcl.lkup_wine_color_type_id
AND w.lkup_bottle_size_id =lkbs.lkup_bottle_size_id
and w.price_per_unit!=0
and w.is_international<>1
and w.deleted=0
AND w.estate_id = e.estate_id and e.estate_id = ".$estateid;
           $report->_dataSource['CLAUSE'] = $cause;
           // $report->_dataSource['TABLES']= " wine_allocations  wal ,estates e,wines w,lkup_wine_color_types lkcl,lkup_bottle_sizes lkbs ";
          $report->_dataSource['TABLES']= " wines w left outer join
                                            wine_allocations wal on wal.wine_id = w.wine_id,
                                            estates e,
                                            lkup_wine_color_types lkcl,
                                            lkup_bottle_sizes lkbs ";

            $report->_dataSource['ORDERBY']= " wine_name ";


            $report->setColumnAlias('wine', 'Wine');
            $report->setColumnAlias('cspc_code', 'CSPC');
            $report->setColumnAlias('color', 'Color');
            $report->setColumnAlias('size', 'Size');
            $report->setColumnAlias('price', 'Price');
            $report->setColumnAlias('unallocated', 'Allocated');
            $report->setColumnAlias('sample', 'Samples');
            $report->setColumnAlias('breakage_corked', 'Brk/Corked');
 
            $report->setColumnAlias('cases', 'Cases');
            $report->setColumnAlias('btls', 'Bottles');
             $report->setColumnAlias('bottles', 'Ava Btls');
         // $report->setColumnAlias('sold', 'Sold');
  
            $report->setColumnSizes(array(15,8,8,10,10,10,10,10,10,8,1));
            $report->setHidden("id");
           // $report->setHidden("customer_id");
          // $report->setHidden("search_key");


      		$report->pageLink ="main.php?page_name=wineAdd&editMode=1";//$_SERVER["REQUEST_URI"];
  			   $report->idName = "id";

       }
  if ($search_id==5 ) 
        {
            $wine_id =$_REQUEST["wine_id"];
            $estateid =$_REQUEST["estateid"];
            $requststr = "&searchType=".$search_id."&wine_id=".$wine_id."&estateid=".$estateid;
            $report -> setRequestString($requststr);
							 
							 
            $report->_dataSource['FIELDS'] = " distinct cm.customer_id id,   cm.customer_name,
             lkstype.license_name store_type,
       		 concat(IFNULL(c.first_name,''),' ',IFNULL(c.last_name,'')) contact_name,
             CONCAT_WS('.', SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end), 1, 3), SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end)  , 4, 3), SUBSTRING( (case when cm.lkup_phone_type_id =1 then cm.phone_office1  else (case when cm.lkup_phone_type_id =2 then cm.phone_other1 else cm.phone_fax end) end) , 7))  contact_number,
                                               
 							 concat(IFNULL(cm.billing_address_unit,'') ,
							 IFNULL(cm.billing_address_street_number,''), ' ',
							 IFNULL(cm.billing_address_street,''),' ',IFNULL(concat('- ',cm.billing_address_city),'')) address,
							 alw2c.allocated allocated, alw2c.sold sold ";
							 

            $cause = "cm.deleted=0
							 and alw2c.customer_id = cm.customer_id
							 and w.estate_id = e.estate_id
							 and e.estate_id =".$estateid.
							 " and w.wine_id=".$wine_id.
                             " and alw2c.wine_id = w.wine_id
                             and w.is_international<>1
                              and w.lkup_wine_color_type_id = lkcl.lkup_wine_color_type_id
                              and lkstype.lkup_store_type_id=cm.lkup_store_type_id and w.deleted=0
										and w.price_per_unit!=0";

          $report->_dataSource['CLAUSE'] = $cause;
            $report->_dataSource['TABLES']= "customers cm
							 left join customers_contacts cmc on cm.customer_id = cmc.customer_id and cmc.is_primary =1
							 left join contacts c on c.contact_id = cmc.contact_id  and c.deleted=0,
							 customer_wine_allocations alw2c, estates e,wines w,lkup_wine_color_types lkcl, lkup_store_types lkstype ";
            $report->_dataSource['ORDERBY'] .= "customer_name";

         //  $report->setColumnAlias('color', 'Color');					// set column aliases
         //  $report->setColumnAlias('wine', 'Wine');					// set column aliases
         //     $report->setColumnAlias('cspc', 'CSPC');
          $report->setColumnAlias('customer_name', 'Customer');					// set column aliases
            $report->setColumnAlias('store_type', 'Store type');
            $report->setColumnAlias('contact_name', 'Contact name');
            $report->setColumnAlias('contact_number', 'Phone number');
            $report->setColumnAlias('address', 'Address');
           $report->setColumnAlias('allocated', 'Allocated');
           $report->setColumnAlias('sold', 'Sold');
           //$report->setColumnAlias('amount_owned', 'Amount owned');
           //$report->setColumnAlias('amount_owned', 'amount_owned');


            $report->setColumnSizes(array(1,1,1,1,1,1,94));
            $report->setHidden("id");
           // $report->setHidden("customer_id");
          // $report->setHidden("search_key");


      		$report->pageLink ="main.php?page_name=customerAdd&&isorder=1";//$_SERVER["REQUEST_URI"];
  			$report->idName = "id";

       }
        $report->build();
        $report->setPagingStyle(REPORT_FIRSTPREVNEXTLAST, array('useButtons' => TRUE, 'useSymbols' => FALSE));
        $report->setVisiblePages(10);

        $contents = & $this->getContents();
        $contents->assign('reports', $report->getContent());
        
	     $search_keys="$search_id|$user_id|$estateid|$from|$to|$wine_id|$storetype_id|$searchAdt";
   
   
/*    var slink = "main.php?page_name=excelF60Reports&searchType="+searchKeys[0]+"user_id="+searchKeys[1]+"estateid="+searchKeys[2]+"from="+searchKeys[3]+"to="+searchKeys[4]+"wine_id="+searchKeys[5]+"store_type_id="+searchKeys[6]+"searchAdt="+searchKeys[7]; */
  
  
      //  $contents->assign('search_key', $search_key);
        
        $recordCounts = $report->getrows();

        $sURL = "";
       /*  if ($recordCounts == 1)
        {

            $id=$report->getid();
             $sURL = $this->getURL($search_id,$id);
            // print $sURL;
             HtmlUtils::redirect($sURL);
        }
        */// {searchType}  {from}   {to}    {estateid}     {store_type_id}     {searchAdt}   (wine_id) $storetypeidAdt="";
 //       $searchAdt="";
       
       
       
         $strPara = "'".$search_id."|".$from."|".$to."|".$estateid.
                 "|".$storetypeidAdt."|".$searchAdt."|".$wine_id."|".$user_id."'";
                 
                 if($search_id ==6)
          				 $strPara = "'".$search_id."|".$from."|".$to."|".$estateid.
                 			"|".$_REQUEST["store_type_id"]."|".$searchAdt."|".$wine_id."|".$user_id."'";
               
                 
        $strScript="javascript:printf60report(".$strPara.");";
        
       $strScriptExcel="javascript:exportToExcel('".$search_keys."');";
        
         $action = array( "Print report" => $strScript,
				         "Export report" => $strScriptExcel

                );
          $this->setActions($action);
          
		F60PageBase::display();
    }
//============================ common function =======
    function getAmountOwned()
    {
      $amount= & F60DbUtil::getAmountOwned();
      
      return $amount;


    }
    
	function getStoreType($id)
	{
        $name="";
        switch ($id)
        {
            case 1:
                $name ='L.R.S';
              break;
            case 2:
                $name ='Agency';
              break;
            case 3:
                $name ='Licensee';
              break;
            case 4:
                $name ='Bulk';
               break;
            case 5:
                $name ='VQA';
               break;
            case 6:
                $name ='BCLDB';
               break;
        }

        return $name;

     }
    function getFormatDate($sDate) //yyyymmdd    return: mm/dd/yyyy
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

 	function getWine($wine_id)
	{
        $sql = "select concat(wine_name,' ',vintage) wine_name from wines where wine_id = ".$wine_id;
        $result = & F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();

        return  $row['wine_name'];

     }

    function getCommissionRate($estate_id,$store_type_id)
    {
        $sql="select commission from estates_commissions where estate_id = ".$estate_id." and lkup_commission_types_id = ".$store_type_id;

        $result = & F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();

        return  $row['commission'];
    }
    
  
    
    function totalCommissions($estate_id)
    {
        $LRSComm = $this->getCommission($estate_id,1);
        $agencyComm = $this->getCommission($estate_id,2);
        $licenceeComm = $this->getCommission($estate_id,3);
        $VQAComm =0;// $this->getCommission($estate_id,5);

        $commssions=" case cm.lkup_store_type_id
                            when 1 then".$LRSComm. "
                            when 2 then".$agencyComm. "
                            when 3 then".$licenceeComm. "
                            when 4 then".$licenceeComm. "
                             when 5 then ".$VQAComm. "
                           else 'TBD'
                        end";
        return $commssions;
}


           function getCommission($estate_id,$store_type_id)
            {
                $discRate ="1";
                $commRate="1";
                
                if($store_type_id==4)
                    $store_type_id=3;
                    
				$estate_name = $this->getEstate($estate_id);
				
			//	print strrchr($estate_name,"Hill");
				
				
                switch ($store_type_id)
                {
                    case 1://lrs
                        $discRate = "13/100";
                        $commRate="15/100";
                        break;
                    case 2: //agentcy
                       $discRate = "15/100";
                       $commRate="15/100";
                        break;
                    case 3: //licencee
                        $discRate = "0";
                        $commRate="17/100";
                        break;
                    case 5: //VQA
                        $discRate = "30/100";
                        $commRate="10/100";
                        break;
                }
                
                
                
                $total ="sum(oi.price_winery*ordered_quantity)";
                $totalDisc= $total."*".$discRate;
                $taxRate = "0.51220";
                $exciseTax = "sum(oi.ordered_quantity)*lkbtsz.size_value/1000"."*".$taxRate;

                if($store_type_id != 3)
                {
                    $subTotal =$total."-".$totalDisc."-".$exciseTax;
                }
                else
                    $subTotal =$total."-".$exciseTax;

                if($store_type_id == 5)//vqa
                {
                    $containerDesp="sum(oi.ordered_quantity)*2/100";
                    $subTotal = $subTotal."-".$containerDesp;
                }

                $totalCommissions="(".$subTotal.")*".$commRate;

                return $totalCommissions;
            }
  }
?>

















