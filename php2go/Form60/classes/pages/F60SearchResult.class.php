<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60PageBase');
import('php2go.data.Report');
import('Form60.base.F60GridBase');
import('php2go.util.HtmlUtils');
import('Form60.util.F60Date');
import('Form60.base.F60DbUtil');

//$F60_PAGE_ID['CUSTOMER'] = "4";
define('F60_PAGE_ID_CUSTOMER', '4');
define('F60_PAGE_ID_ORDER', '9');
define('F60_PAGE_ID_INVOICENM', '5');
define('F60_PAGE_ID_UPDATE_WINE', '25');
define('F60_PAGE_ID_ADD_WINE', '24');
define('F60_PAGE_ID_WINE_DELIVERY', '29');
define('F60_PAGE_ID_ADD_WINE_DELIVERY', '19');
define('F60_PAGE_ID_UPDATE_SAMPLE', '20');
define('F60_PAGE_ID_WINE_ALLOCATE', '18');
define('F60_PAGE_ID_USER', '14');
define('F60_PAGE_ID_ESTATE', '1');

class F60SearchResult extends F60PageBase
{
    var $pageid;
    var $customer_id;
    var $province_id;
    var $estate_id;

    function F60SearchResult()
    {
        F60PageBase::F60PageBase('F60SearchResult', 'Search result',
            'F60SearchResult.tpl');
        // $this->addtoPageStack();
        $this->addScript('resources/js/javascript.pageAction.js');

        $this->province_id = $_COOKIE["F60_PROVINCE_ID"];


    }


    function display()
    {


        $search_id = $_REQUEST["search_id"];


        $search_key = $_REQUEST["search_key"];


        $this->pageid = $_REQUEST["pageid"];
        $isStart = $_REQUEST["is_start"];
        $user_id = $_REQUEST["user_id"];

        if ($_REQUEST["estate_id"] != "")
            $this->estate_id = $_REQUEST["estate_id"];

        $report = &new F60GridBase('resources/xml/F60SearchResult.xml',
            'resources/template/F60SearchResultReport.tpl', $this);

        // $report->enableHighlight('#ffffff', '#ff6800'); 				// highlight search values
        $report->setStyleMapping('links', 'input', 'input', 'title'); // set link, input, button and title styles

        $report->hasHeader = true; // enable column headers
        $report->setAlternateStyle('cellA', 'cellB'); // set alternate style (at least 2)

        $report->setSearchPara($search_id, $search_key);
        $report->setPageid($this->pageid);

        $report->setStartWith($isStart);
        $report->setUserid($user_id);

        if ($search_id == F60_PAGE_ID_ESTATE) //ESTATE
        {
            //updated for database upgrade (MYSQL4 to MYSQL5) , by Helen, OCT 23th 2011
            $report->_dataSource['FIELDS'] .=
                                            " e.estate_id search_key,e.estate_id,e.estate_id id,estate_name,
                                            concat(IFNULL(c.first_name,''),' ',IFNULL(c.last_name,'')) contact_name,
                                            
                                            
                                            CONCAT_WS('.', SUBSTRING( (case when e.lkup_phone_type_id =1 then e.phone_office1  else (case when e.lkup_phone_type_id =2 then e.phone_other1 else e.phone_fax end) end)
                                            , 1, 3), SUBSTRING( (case when e.lkup_phone_type_id =1 then e.phone_office1  else (case when e.lkup_phone_type_id =2 then e.phone_other1 else e.phone_fax end) end)  , 4, 3), SUBSTRING( (case when e.lkup_phone_type_id =1 then e.phone_office1  else (case when e.lkup_phone_type_id =2 then e.phone_other1 else e.phone_fax end) end) , 7))  contact_number,
                                            concat(IFNULL(billing_address_unit,'') ,
                                            IFNULL(billing_address_street_number,''), ' ',
                                            IFNULL(billing_address_street,'')) address";
            $report->_dataSource['CLAUSE'] .= "ec.is_primary=1
                                               and estate_name like '%$search_key%' 
                                               and e.deleted=0 and (c.deleted=0 or c.deleted is null) 
                                               and e.is_international=0 and e.estate_id<> 2";
            $report->_dataSource['TABLES'] .= "estates e left join estates_contacts ec 
                                                on e.estate_id = ec.estate_id join contacts c 
                                                on ec.contact_id =c.contact_id";
            $report->_dataSource['ORDERBY'] .= "estate_name";


            $report->setColumnAlias('estate_name', 'Estate'); // set column aliases
            $report->setColumnAlias('contact_name', 'Contact name');
            $report->setColumnAlias('contact_number', 'Phone number');
            $report->setColumnAlias('address', 'Address');

            $report->setColumnSizes(array(5,10,10,75));
            $report->setHidden("id");
            $report->setHidden("estate_id");
            $report->setHidden("search_key");


            $URL = "";
            $sLink = "main.php?";

            if ($this->pageid == F60_PAGE_ID_ESTATE) //goto change estate page
            {
                $report->idName = "id";
                $URL = "page_name=estateAdd";
            } elseif ($this->pageid == F60_PAGE_ID_ADD_WINE) //go to add wine page
            {
                $report->idName = "estate_id";
                $URL = "page_name=wineAdd";
            } elseif ($this->pageid == F60_PAGE_ID_WINE_DELIVERY)
            //goto select wine page -> add a new delivery
            {
                $report->idName = "search_key";
                $URL = 'page_name=F60SearchResult&search_id=29&pageid=' . $this->pageid;
            } elseif ($this->pageid == F60_PAGE_ID_ADD_WINE_DELIVERY)
            //goto select wine page -> add a new delivery
            {
                $report->idName = "search_key";
                $URL = 'page_name=F60SearchResult&search_id=19&pageid=' . $this->pageid;
            } elseif ($this->pageid == F60_PAGE_ID_WINE_ALLOCATE)
            //goto select wine page -> add a new delivery
            {

                $report->idName = "estate_id";
                $URL = 'page_name=wineSelect&pageid=18';

                if ($_REQUEST["customer_id"] != "")
                    $URL = 'page_name=wineSelect&pageid=18&customer_id=' . $_REQUEST["customer_id"];
            } elseif ($this->pageid == F60_PAGE_ID_UPDATE_SAMPLE)
            //goto select wine page -> add a new delivery
            {
                $report->idName = "estate_id";
                $URL = 'page_name=wineSelect&pageid=20';
            } elseif ($this->pageid == F60_PAGE_ID_UPDATE_WINE)
            //update wine->go to select wine and delivery date time page to update a wine
            {
                $report->idName = "search_key";
                $URL = 'page_name=F60SearchResult&search_id=25';
            }

            $report->pageLink = $sLink . $URL; //"main.php?page_name=estateAdd";//$_SERVER["REQUEST_URI"];
            //$report->setSearchstring($URL);
        } elseif ($search_id == F60_PAGE_ID_CUSTOMER || $search_id == F60_PAGE_ID_ORDER)
        //customer
        {
           $phone_column = 'CASE WHEN cm.lkup_phone_type_id =1 THEN cm.phone_office1
								WHEN cm.lkup_phone_type_id =2 THEN cm.phone_other1
								ELSE cm.phone_fax
							 END';
            $contact_number = "CONCAT_WS('.', SUBSTRING((" . $phone_column .
                "), 1, 3), SUBSTRING( (" . $phone_column . ") , 4, 3), SUBSTRING( (" . $phone_column .
                ") , 7))";
            $contact_number7 = "CONCAT_WS('.', SUBSTRING((" . $phone_column .
                "), 1, 3), SUBSTRING( (" . $phone_column . ") , 4))";
            $fax_number = "IFNULL(CONCAT_WS('.', SUBSTRING(cm.phone_fax,1,3),SUBSTRING(cm.phone_fax,4,3),SUBSTRING(cm.phone_fax,7)),'')";
            $fax_number7 = "IFNULL(CONCAT_WS('.', SUBSTRING(cm.phone_fax,1,3),SUBSTRING(cm.phone_fax,4)),'')";
            $search_key = str_replace("'", "\'", $search_key);
            
            $address = " concat(IFNULL(billing_address_unit,'')  ,
                        IFNULL(billing_address_street_number,''), ' ',                       
                        IFNULL(billing_address_street,''),' ',IFNULL(billing_address_city,''))";

            if ($this->province_id == 1)
            {
                $report->_dataSource['FIELDS'] .=
                                                "cm.customer_id id,cm.customer_name,lksty.license_name,cm.licensee_number license_number,
                                                concat(IFNULL(c.first_name,''),' ',IFNULL(c.last_name,'')) contact_name,
                                                case length(" . $phone_column .
                                                ")
                                                when 0 then ''
                                                when 7 then " . $contact_number7 . "
                                                else " . $contact_number . " end contact_number," . $address .
                                                "address,
                                                concat(IFNULL(u.first_name,'Not'),' ',IFNULL(u.last_name,'Assigned')) user_name";
            } else
            {
                $report->_dataSource['FIELDS'] .= "cm.customer_id id,cm.customer_name,
                                                    cm.licensee_number contact_name,
                                                    case length(" . $phone_column .
                                                    ")
                                                    when 0 then ''
                                                    when 7 then " . $contact_number7 . "
                                                    else " . $contact_number . " end contact_number," . $address .
                                                    "address,
                                                    concat(IFNULL(u.first_name,'Not'),' ',IFNULL(u.last_name,'Assgined')) user_name";
            }

            $is_invoice = false;
            if ($_REQUEST['is_OOB'] == 1)
            {
                $report->setOOB(1);
                $adt_field = " cm.status = 2 and ";
            }
            $report->setAdtId($_REQUEST["adt_field"]);
            $report->setEstateId($this->estate_id);

            $store_type = $_REQUEST["store_type"];
            //	print $_REQUEST["adt_field"];
            if ($_REQUEST["adt_field"] == "3")
            {

                $adt_field = " cm.licensee_number like '%" . $search_key . "%'";
                if ($isStart)
                    $adt_field = " cm.licensee_number like '" . $search_key . "%'";

                $adt_field = $adt_field . " AND cm.lkup_store_type_id=lksty.lkup_store_type_id";
            } else
                if ($_REQUEST["adt_field"] == "5")
                {
                    $is_invoice = true;

                    $searchAll = "%";
                    if ($isStart)
                        $searchAll = "";

                    $adt_field = " cm.lkup_store_type_id=lksty.lkup_store_type_id And od.customer_id = cmc.customer_id and od.estate_id = $this->estate_id and od.invoice_number like '$searchAll" .
                        $search_key . "%' and od.deleted=0";

                } else
                {
                    $adt_field = "cm.lkup_store_type_id=lksty.lkup_store_type_id and customer_name like '%" .
                        $search_key . "%'";
                    if ($isStart)
                        $adt_field = " cm.lkup_store_type_id=lksty.lkup_store_type_id and customer_name like '" .
                            $search_key . "%'";
                }

                if ($_REQUEST["adt_field"] != "")
                {
                    if ($_REQUEST["adt_field"] == "2")
                    {
                        $adt_field = " c.first_name like '%" . $search_key . "%'";
                        if ($isStart)
                            $adt_field = " c.first_name like '" . $search_key . "%'";
                    } elseif ($_REQUEST["adt_field"] == "1")
                    {
                        $adt_field = " c.last_name like '%" . $search_key . "%'";
                        if ($isStart)
                            $adt_field = " c.last_name like '" . $search_key . "%'";
                    } elseif ($_REQUEST["adt_field"] == "10") //phone number
                    {
                        $adt_field = " cm.phone_office1 like '%" . $search_key . "%'";
                        if ($isStart)
                            $adt_field = " c.phone_office1 like '" . $search_key . "%'";

                        //		$adt_field = $adt_field." or cm.phone_other1 like '%" . $search_key . "%')";

                    } elseif ($_REQUEST["adt_field"] == "11") //street name
                    {

                        $adt_field = "  (cm.billing_address_street_number like '%" . $search_key . "%'";
                        $adt_field = $adt_field . " or cm.billing_address_street like '%" . $search_key .
                            "%' ) ";

                        if ($isStart)
                        {
                            $adt_field = "  (cm.billing_address_street_number like '" . $search_key . "%'";
                            $adt_field = $adt_field . " or cm.billing_address_street like '" . $search_key .
                                "%' ) ";
                        }
                    } elseif ($_REQUEST["adt_field"] == "12") //city
                    {
                        $adt_field = " cm.billing_address_city like '%" . $search_key . "%'";
                        if ($isStart)
                            $adt_field = " cm.billing_address_city like '" . $search_key . "%'";

                    }


                }

            if ($user_id != "")
            {
                if ($user_id == 0)
                {
                    $adt_field = $adt_field . " and (ucm.user_id is NULL or ucm.user_id = $user_id)";
                } else
                    $adt_field = $adt_field .
                        " and cm.customer_id = ucm.customer_id and ucm.user_id=u.user_id and ucm.user_id = " .
                        $user_id;


            }

            $adt_field = $adt_field . " and lksty.province_id = $this->province_id and lksty.lkup_store_type_id =cm.lkup_store_type_id ";

            if ($is_invoice)
                $adt_field = $adt_field . " and od.deleted=0";

            if ($store_type != "")
            {

                $adt_field .= " and cm.lkup_store_type_id = " . $store_type;
                $report->setStoreType($store_type);

            }
            $isOOB = 0;


            if ($_REQUEST['is_OOB'] == 1)
            {
                $report->setOOB(1);
                $isOOB = 2;
                $report->_dataSource['CLAUSE'] .= $adt_field .
                    " and cm.deleted=0 and cm.status=2 group by cm.customer_id ";
            } else //not oob

                $report->_dataSource['CLAUSE'] .= $adt_field .
                    " and cm.deleted=0 and cm.status<>2 group by cm.customer_id ";
            //}


            $tables = " lkup_store_types lksty,customers cm
                        left join customers_contacts cmc on cm.customer_id = cmc.customer_id and cmc.is_primary=1
                        left join contacts c on c.contact_id = cmc.contact_id 	and c.deleted=0";

            if ($user_id == "" || $user_id == 0)
            {
                //print herere;
                $tables = $tables . " left outer join users_customers ucm on ucm.customer_id = cm.customer_id
                                      left outer join users u on u.user_id = ucm.user_id";
            } else
            {
                $tables = "users_customers ucm, users u, " . $tables;
            }

            if ($is_invoice)
            {
                $estate_country = F60DbUtil::getEstateCountry($this->estate_id);

                if ($estate_country == "CSWS Products")
                {
                    $tables = "cs_product_orders od," . $tables;
                } else
                    $tables = "orders od," . $tables;


            }

            $report->_dataSource['TABLES'] = $tables;

            $report->_dataSource['ORDERBY'] = "customer_name ";

            if ($this->province_id == 1)
            {
                $report->setColumnAlias('customer_name', 'Customer'); // set column aliases
                $report->setColumnAlias('license_name', 'Store type');
                $report->setColumnAlias('license_number', 'License#');
                $report->setColumnAlias('contact_name', 'Contact name');
                $report->setColumnAlias('contact_number', 'Phone number');
                //  $report->setColumnAlias('phone_fax', 'Fax number');
                $report->setColumnAlias('address', 'Address');
                $report->setColumnAlias('user_name', 'Assgined to');
                $report->setColumnSizes(array(
                    10,
                    10,
                    10,
                    10,
                    10,
                    40,
                    10));
            } else
            {
                $report->setColumnAlias('customer_name', 'Customer'); // set column aliases
                $report->setColumnAlias('contact_name', 'License#');
                $report->setColumnAlias('contact_number', 'Phone number');
                //  $report->setColumnAlias('phone_fax', 'Fax number');
                $report->setColumnAlias('address', 'Address');
                $report->setColumnAlias('user_name', 'Assgined to');
                $report->setColumnSizes(array(
                    10,
                    10,
                    10,
                    60,
                    10));
            }

            $report->setHidden("id");
            $report->pageLink = "main.php?page_name=customerAdd"; //$_SERVER["REQUEST_URI"];
            if ($search_id == F60_PAGE_ID_ORDER)
                $report->pageLink = "main.php?page_name=customerAdd&isorder=1";
            $report->idName = "id";


        }
        elseif ($search_id == F60_PAGE_ID_UPDATE_WINE) //for update wine
        {
            $report->_dataSource['FIELDS'] .=
                " w.wine_id id,w.wine_name wine_name,w.vintage,wc.display_name";
            $report->_dataSource['TABLES'] .= "wines w,lkup_wine_color_types wc,estates e";
            $report->_dataSource['CLAUSE'] .= "w.deleted=0
                                                 and w.lkup_wine_color_type_id = wc.lkup_wine_color_type_id
                                                 and w.price_per_unit!=0
                                                and w.estate_id = e.estate_id
                                                and w.is_international<>1
                                                and e.estate_id = " . $search_key;
            $report->_dataSource['ORDERBY'] .= "w.wine_name";

            $report->setColumnAlias('wine_name', 'Wine name'); // set column aliases
            $report->setColumnAlias('vintage', 'Vintage'); // set column aliases
            $report->setColumnAlias('display_name', 'Color'); // set column aliases
            $report->setColumnSizes(array(
                30,
                30,
                40));

            $report->setHidden("id");
            // $report->setHidden("wine_id");
            $report->pageLink = "main.php?page_name=wineAdd&editMode=1"; //$_SERVER["REQUEST_URI"];
            $report->idName = "id";
            $action = array("Add another wine" => "javascript:goAddWine(" . $search_key .
                    ");", );
            $this->setActions($action);
        } elseif ($search_id == F60_PAGE_ID_WINE_DELIVERY)
        // for add new delivery , need  show delivery time
        {
            $report->_dataSource['FIELDS'] .= " wd.wine_delivery_date_id id,
                                                w.wine_name wine_name,
                                                w.vintage,
                                                wc.display_name,
                                                wd.delivery_date delivery_date
                                                ";
            $report->_dataSource['TABLES'] .="wines w,wine_delivery_dates wd,lkup_wine_color_types wc,estates e";
            $report->_dataSource['CLAUSE'] .= "w.deleted=0
                                                and w.lkup_wine_color_type_id = wc.lkup_wine_color_type_id
                                                and wd.deleted=0
                                                and w.estate_id = e.estate_id
                                                and w.wine_id =wd.wine_id
                                                and w.is_international<>1
                                                and e.estate_id = " . $search_key;
            $report->_dataSource['ORDERBY'] .= "w.wine_name,wd.delivery_date";

            $report->setColumnAlias('wine_name', 'Wine name'); // set column aliases
            $report->setColumnAlias('vintage', 'Vintage'); // set column aliases
            $report->setColumnAlias('display_name', 'Color'); // set column aliases
            $report->setColumnAlias('delivery_date', 'Wine delivery date');
            $report->setColumnSizes(array(
                25,
                25,
                25,
                25));


            $report->setHidden("id");

            //  $report->setHidden("wine_delivery_date_id");
            $report->pageLink = "main.php?page_name=wineAddCa&editMode=3"; //$_SERVER["REQUEST_URI"];
            $report->idName = "id";
            $action = array("Add another wine" => "javascript:goAddWine(" . $search_key .
                    ");", );
            $this->setActions($action);
        } elseif ($search_id == F60_PAGE_ID_ADD_WINE_DELIVERY)
        // for add new delivery , need  show delivery time
        {
            $report->_dataSource['FIELDS'] .=" w.wine_id id,w.wine_name wine_name,w.vintage,wc.display_name";
            $report->_dataSource['TABLES'] .= "wines w,lkup_wine_color_types wc,estates e";
            $report->_dataSource['CLAUSE'] .= "w.deleted=0
                                                 and w.lkup_wine_color_type_id = wc.lkup_wine_color_type_id
                                                and w.estate_id = e.estate_id
                                                and w.price_per_unit!=0
                                                and w.is_international<>1
                                                and e.estate_id = " . $search_key;
            $report->_dataSource['ORDERBY'] .= "w.wine_name";

            $report->setColumnAlias('wine_name', 'Wine name'); // set column aliases
            $report->setColumnAlias('vintage', 'Vintage'); // set column aliases
            $report->setColumnAlias('display_name', 'Color'); // set column aliases
            $report->setColumnSizes(array(
                30,
                30,
                40));

            $report->setHidden("id");
            //  $report->setHidden("wine_id");
            //            $id=$report->getid();
            $report->pageLink = "main.php?page_name=wineAddCa&editMode=2"; //$_SERVER["REQUEST_URI"];
            $report->idName = "id";
            $action = array("Add another wine" => "javascript:goAddWine(" . $search_key .
                    ");", );
            $this->setActions($action);

        } elseif ($search_id == F60_PAGE_ID_USER) //update user
        {
            $report->_dataSource['FIELDS'] .= " u.user_id id,
                                                concat(first_name,' ',last_name) total_name,
                                                u.username,
                                                u.userpass,
                                                ul.caption";
            $report->_dataSource['TABLES'] .= " users u,user_levels ul";
            $report->_dataSource['CLAUSE'] .= " deleted=0
                                                and u.user_level_id = ul.user_level_id";
            $report->_dataSource['ORDERBY'] .= " username";

            $report->setColumnAlias('total_name', 'Name'); // set column aliases
            $report->setColumnAlias('username', 'User name'); // set column aliases
            // $report->setColumnAlias('userpass', 'Password');					// set column aliases
            $report->setColumnAlias('caption', 'User level'); // set column aliases

            $report->setColumnSizes(array(
                15,
                15,
                70));

            //	$report->setColumnSizes(array(10,10,10,70)); // hide password 2016/10/26

            $report->setHidden("userpass"); // hide password 2016/10/26
            $report->setHidden("id");
            $report->pageLink = "main.php?page_name=userAdd"; //$_SERVER["REQUEST_URI"];
            $report->idName = "id";
        }
        $report->build();
        $report->setPagingStyle(REPORT_FIRSTPREVNEXTLAST, array('useButtons' => true,
                'useSymbols' => false));
        $report->setVisiblePages(10);

        $contents = &$this->getContents();
        $contents->assign('reports', $report->getContent());
        //traceLog($report->getContent());

        $recordCounts = $report->getrows();

        $sURL = "";
        if ($recordCounts == 1)
        {
            $id = $report->getid();
            $sURL = $this->getURL($search_id, $id);
            // print $sURL;
            HtmlUtils::redirect($sURL);
        }

        F60PageBase::display();

    }


    function getURL($search_id, $id)
    {
        $sURL = "";
        if ($search_id == F60_PAGE_ID_ESTATE) //go to edit customer page
        {
            $sURL = "main.php?page_name=customerAdd&id=" . $id;

            if ($this->pageid == F60_PAGE_ID_ESTATE) //goto change estate page
            {
                $sURL = "main.php?page_name=estateAdd&id=" . $id;
            } elseif ($this->pageid == F60_PAGE_ID_ADD_WINE) //go to add wine page
            {
                $sURL = "main.php?page_name=wineAdd&estate_id=" . $id . "&pageid=" . $this->pageid;
            } elseif ($this->pageid == F60_PAGE_ID_WINE_DELIVERY)
            //goto select wine page -> add a new delivery
            {

                $sURL = 'main.php?page_name=F60SearchResult&search_id=29&search_key=' . $id;

            } elseif ($this->pageid == F60_PAGE_ID_ADD_WINE_DELIVERY)
            //goto select wine page -> add a new delivery
            {

                $sURL = "main.php?page_name=F60SearchResult&search_id=19&editMode=2&search_key=$id";

            }
            elseif ($this->pageid == F60_PAGE_ID_WINE_ALLOCATE)
            //goto select wine page -> add a new delivery
            {
                $sURL = 'main.php?page_name=wineSelect&pageid=18&estate_id=' . $id;
                if ($_REQUEST["customer_id"] != "")
                    $sURL = 'main.php?page_name=wineSelect&pageid=18&estate_id=' . $id .
                        "&customer_id=" . $_REQUEST["customer_id"];

            } elseif ($this->pageid == F60_PAGE_ID_UPDATE_SAMPLE)
            //goto select wine page -> add a new delivery
            {
                $sURL = 'main.php?page_name=wineSelect&pageid=20&estate_id=' . $id;
            } else //update wine->go to select wine and delivery date time page to update a wine
            {

                $sURL = 'main.php?page_name=F60SearchResult&search_id=25&search_key=' . $id;
            }

        }
        if ($search_id == F60_PAGE_ID_CUSTOMER) //go to edit customer page
        {
            $sURL = "main.php?page_name=customerAdd&id=" . $id;

        }
        if ($search_id == F60_PAGE_ID_INVOICENM) //go to edit form60 page
        {
            $sURL = "main.php?page_name=customerAdd&id=" . $id; //temp use

        }
        if ($search_id == F60_PAGE_ID_WINE_DELIVERY)
        {
            $sURL = "main.php?page_name=wineAddCa&editMode=3&id=" . $id;
        }
        if ($search_id == F60_PAGE_ID_ADD_WINE_DELIVERY)
        {
            $sURL = "main.php?page_name=wineAddCa&editModeCa=2&id=" . $id;
        }
        if ($search_id == F60_PAGE_ID_UPDATE_WINE)
        {
            $sURL = "main.php?page_name=wineAdd&editMode=1&id=" . $id;

        }
        if ($search_id == F60_PAGE_ID_USER)
        {
            $sURL = "main.php?page_name=userAdd&id=" . $id;

        }
        return $sURL;
    }
}

?>
