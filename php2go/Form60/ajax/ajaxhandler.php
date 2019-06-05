<?php

require ('xajax.inc.php');


//define the remote functions here

function getNotesContent($ownerType, $ownerID, $orderBy, $orderType, $page, $year)
{
    import('Form60.base.F60NotesList');

    $notesControl = &new F60NotesList(null, $ownerType, $ownerID, false, $year);
    $notesControl->orderBy = $orderBy;
    $notesControl->orderType = $orderType;
    $notesControl->page = $page;

    return $notesControl->getContent();

}

function refreshNotes($ownerType, $ownerID, $orderBy, $orderType, $page, $noteYear,
    $objResponse = null)
{
    if (!$objResponse)
        $objResponse = new xajaxResponse();


    $objResponse->addAssign("arrow_" . $orderBy, "innerHTML", ($orderType == 'a') ?
        '5' : '6');
    $objResponse->addAssign("notesList", "innerHTML", getNotesContent($ownerType, $ownerID,
        $orderBy, $orderType, $page, $noteYear));
    $objResponse->addScript("if (window.setNoteHeight) setNoteHeight();");
    return $objResponse->getXML();

}

function deleteNote($note_id, $ownerType, $ownerID, $orderBy, $orderType, $page,
    $noteYear)
{
    import('Form60.bll.bllnotes');

    $notes = &new bllnotes();
    $note = $notes->getByPrimaryKey($note_id);
    $note->mark_deleted();
    $note->save();

    return refreshNotes($ownerType, $ownerID, $orderBy, $orderType, $page, $noteYear);
}

function addNoteOwner($ownerType, $objResponse)
{
    switch ($ownerType)
    {
        case 'estate':
            import('Form60.bll.bllestates');
            $estate = new bllestate();
            $estate->set_data("deleted", 1);
            $estate->set_data("phone_office1", "604");
            $estate->save(null);
            $ownerID = $estate->get_data("estate_id");
            $objResponse->addAssign("estate_id", "value", $ownerID);
            break;

        case 'customer':
            import('Form60.bll.bllcustomers');
            $customer = new bllcustomer();
            $customer->set_data("deleted", 1);
            $customer->set_data("phone_office1", "604");
            $customer->save(0);
            $ownerID = $customer->get_data("customer_id");
            $objResponse->addAssign("customer_id", "value", $ownerID);
            break;
    }

    $objResponse->addAssign("noteOwnerID", "value", $ownerID);

    return $ownerID;
}

function saveNote($note_id, $note_text, $ownerType, $ownerID, $orderBy, $orderType,
    $page)
{
    $objResponse = new xajaxResponse();

    import('Form60.bll.bllnotes');
    $notes = &new bllnotes();

    if ($note_id == 0)
    {
        $note = $notes->add_new();
        $note->owner_type = $ownerType;
        if ($ownerID == 0)
            $ownerID = addNoteOwner($ownerType, &$objResponse);
        $note->owner_id = $ownerID;
    } else
        $note = $notes->getByPrimaryKey($note_id);

    if ($note_text != "")
        $note->set_data("note_text", $note_text);
    $note->save();

    return refreshNotes($ownerType, $ownerID, $orderBy, $orderType, $page, &$objResponse);

}

//----------------------- marketList -------------------
function getMarketListContent($orderBy, $orderType, $page, $statusid)
{
    import('Form60.base.F60MarketList');

    $listControl = &new F60MarketList(null, $statusid, 10);
    $listControl->orderBy = $orderBy;
    $listControl->orderType = $orderType;
    $listControl->page = $page;

    return $listControl->getContent();

}

function refreshMarketList($orderBy, $orderType, $page, $statusid)
{
    $objResponse = new xajaxResponse();

    $objResponse->addAssign("arrow_" . $orderBy . "_" . $statusid, "innerHTML", ($orderType ==
        'a') ? '5' : '6');
    $objResponse->addAssign("reportList_" . $statusid, "innerHTML",
        getMarketListContent($orderBy, $orderType, $page, $statusid));
    return $objResponse->getXML();

}
//-----------------------end of market list functions


//-------------- search wines results for customersl ist who purchased wine----------------

function getSalesResultListsContent($order_by, $order_type, $list_page, $search_id,
    $sales_year, $sales_period, $isQtr, $store_type_id, $user_id, $search_adt1, $search_adt2,
    $city, $product_id)
{
    import('Form60.base.F60WinesSearchResultList');

    //($Document, $search_id,$sales_period,$sales_year,$isQuarter,$store_type_id,$user_id,$search_adt="",$page =1)

    $listControl = &new F60WinesSearchResultList(null, $product_id, $search_id, $sales_period,
        $sales_year, $isQtr, $store_type_id, $user_id, $search_adt1, $search_adt2, $city);
    $listControl->orderBy = $order_by;
    $listControl->orderType = $order_type;
    $listControl->page = $list_page;

    return $listControl->getContent();

}


function refresf60searchResultLists($order_by, $order_type, $list_page, $search_id,
    $sales_year, $sales_period, $isQtr, $store_type_id, $user_id, $search_adt1, $search_adt2,
    $city, $product_id)
{

    $objResponse = new xajaxResponse();

    $objResponse->addAssign("arrow_" . $order_by, "innerHTML", ($order_type == 'a') ?
        '5' : '6');
    /*  $fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp,  getSalesResultListsContent($order_by, $order_type, $list_page,$search_id,$sales_year,$sales_period,$isQtr,$store_type_id,$user_id,$search_adt1,$search_adt2)."\n");
    fclose($fp);*/
    $objResponse->addAssign("customersList", "innerHTML", getSalesResultListsContent
        ($order_by, $order_type, $list_page, $search_id, $sales_year, $sales_period, $isQtr,
        $store_type_id, $user_id, $search_adt1, $search_adt2, $city, $product_id));
    return $objResponse->getXML();
}
//--------------------Wine list functions ----------------------
function getWinelistContent($customerID, $orderBy, $orderType, $estateID)
{
    import('Form60.base.F60WineList');

    $winlistControl = &new F60WineList(null, $customerID);
    $winlistControl->orderBy = $orderBy;
    $winlistControl->orderType = $orderType;
    $winlistControl->estateID = $estateID;


    $ret = $winlistControl->getContent();


    return $ret;

}

function refreshWines($customerID, $orderBy, $orderType, $estateID)
{
    $objResponse = new xajaxResponse();
    $objResponse->addAssign("arrow_" . $orderBy, "innerHTML", ($orderType == 'a') ?
        '5' : '6');


    $objResponse->addAssign("winesList", "innerHTML", getWinelistContent($customerID,
        $orderBy, $orderType, $estateID));
    $objResponse->addScript("if (window.setWineListHeight) setWineListHeight();");
    return $objResponse->getXML();

}
//--------------end wine list functions --------------

//--------------suppliers functions --------------
function getUsers($province_id, $estate_id)
{
    import('Form60.pages.supplierSales');


    $scrUsers = supplierSales::getUSersCtl4SelectScript($province_id, $estate_id);

    /*	$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, $scrEstates."\n");
    fclose($fp);*/

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrUsers);
    return $objResponse->getXML();

}

function getStoreTypes($province_id, $estate_id)
{
    import('Form60.pages.supplierSales');


    $scrText = supplierSales::getStoreTypesCtl4SelectScript($province_id, $estate_id);


    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrText);
    return $objResponse->getXML();

}
function getYears($province_id, $estate_id)
{
    import('Form60.pages.supplierSales');

    $scrText = supplierSales::getYearsCtl4SelectScript($province_id, $estate_id);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrText);
    //  $objResponse->addScript("setSaleYear(2009);");


    return $objResponse->getXML();

}

function getMonths($province_id, $estate_id, $sales_year)
{
    import('Form60.pages.supplierSales');

    $scrText = supplierSales::getMonthsCtl4SelectScript($province_id, $estate_id, $sales_year);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrText);

    /* 	$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, "test here $scrText"."\n");
    fclose($fp);*/


    return $objResponse->getXML();

}

function getAnaSalesMonths($province_id, $sales_year)
{
    import('Form60.pages.reportsMain');

    $scrMonths = reportsMain::setSalesAnaMonth($province_id, $sales_year);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrMonths);

    /*$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, $scrMonths."\n");
    fclose($fp);
    */
    return $objResponse->getXML();


}

function BI_checkASAvaData($sales_year, $sales_month, $province_id)
{
    import('Form60.pages.generateAnaData');

    $htmlText = generateAnaData::checkAnaSalesDataAva($sales_year, $sales_month, $province_id);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($htmlText);

    /*$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, $scrMonths."\n");
    fclose($fp);
    */
    return $objResponse->getXML();


}

function BI_generateASAnaData($sales_year, $sales_month, $province_id)
{
    import('Form60.pages.generateAnaData');

    $htmlText = generateAnaData::generateASAnaData($sales_year, $sales_month, $province_id);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($htmlText);

    /*$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, $scrMonths."\n");
    fclose($fp);
    */
    return $objResponse->getXML();


}
function getAnaSalesUsers($province_id, $sales_year, $sale_month)
{
    import('Form60.pages.reportsMain');

    $scripts = reportsMain::setSalesAnaUsers($province_id, $sales_year, $sale_month);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scripts);

    /*	$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, $scrMonths."\n");
    fclose($fp);
    */
    return $objResponse->getXML();


}
function getWines4Supplier($province_id, $estate_id)
{
    import('Form60.pages.supplierSales');

    $scrText = supplierSales::getWinesCtl4SelectScript($province_id, $estate_id);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrText);
    return $objResponse->getXML();

}

function getVintageList($SKU)
{
    import('Form60.pages.supplierSales');

    $scrText = supplierSales::getVintageCtl4SelectScript($SKU);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrText);
    return $objResponse->getXML();

}


function setProductTypes($product_id)
{
    import('Form60.pages.searchf60');

    $scrText = searchf60::setProductTypes($product_id);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrText);

    return $objResponse->getXML();

}

//overdue list
//	xajax_refreshOverdueList(order_by,order_type,estate_id, store_type_id, user_id,overdue_type, currentpage);
function refreshOverdueList($order_by, $order_type, $estate_id, $store_type_id,
    $user_id, $overdue_type, $currentpage)
{

    $objResponse = new xajaxResponse();

    $objResponse->addAssign("arrow_" . $order_by, "innerHTML", ($order_type == 'a') ?
        '5' : '6');

    $objResponse->addAssign("f60OverdueList", "innerHTML", getOverdueList($order_by,
        $order_type, $estate_id, $store_type_id, $user_id, $overdue_type, $currentpage));
    $objResponse->addScript("checkIfNoRecords();");

    return $objResponse->getXML();
}

function getOverdueList($order_by, $order_type, $estate_id, $store_type_id, $user_id,
    $overdue_type, $currentpage)
{
    import('Form60.base.F60OverDueList');

    $listControl = &new F60OverDueList(null, $order_by, $order_type, $estate_id, $store_type_id,
        $user_id, $overdue_type, $currentpage);

    return $listControl->getContent();

}

function supplierUpdateInvoice($order_id, $customer_id, $inovice_number, $payment_type,
    $user_id, $estate_id, $order_status = null)
{

    $objResponse = new xajaxResponse();

    import('Form60.bll.bllsupplierData');

    $spData = &new suppliersData();


    if ($order_status == null)
    {
        if ($spData->updateInvoice($order_id, $customer_id, $inovice_number, $payment_type,
            $user_id, $estate_id, $order_status))
            $objResponse->addScript("getParentlistRefresh();");
        else
            $objResponse->addAlert("save failed");
    } else
    {


        if ($spData->updateInvoiceStatus($order_id, $order_status))
        {
            $fp = fopen("logs/inoviceupdate.log", "a");
            fputs($fp, ". STep1  Should be updated.");
            fclose($fp);

            $objResponse->addScript("reload_parent_page();");
        } else
            $objResponse->addAlert("save failed");
    }


    return $objResponse->getXML();

}
//end overdue list

function refreshSupplierSalesList($estate_id, $date1, $date2, $order_by, $order_type,
    $dateType, $store_type_id, $user_id, $province_id, $wine_id, $vintage, $currentpage,
    $isSearch = false)
{

    $objResponse = new xajaxResponse();

    $objResponse->addAssign("arrow_" . $order_by, "innerHTML", ($order_type == 'a') ?
        '5' : '6');

    if ($isSearch)
    {
        $objResponse->addAssign("supplierSalesList", "innerHTML", getSupplierSalesList($estate_id,
            $date1, $date2, $order_by, $order_type, $dateType, $store_type_id, $user_id, $province_id,
            $wine_id, $vintage, $currentpage, true));
    } else
    {
        $objResponse->addAssign("supplierSalesList", "innerHTML", getSupplierSalesList($estate_id,
            $date1, $date2, $order_by, $order_type, $dateType, $store_type_id, $user_id, $province_id,
            $wine_id, $vintage, $currentpage, false));

    }
    $objResponse->addScript("checkIfNoRecords();");

    //        ($Document,$estate_id, $date1, $date2, $order_by,$order_type, $dateType=2, $store_type_id=-1, $user_id=-1,$province_id=1,$wine_id="", $reportType=1, $page = 1)

    return $objResponse->getXML();
}

function getSupplierSalesList($estate_id, $date1, $date2, $order_by, $order_type,
    $dateType, $store_type_id, $user_id, $province_id, $wine_id, $vintage, $currentpage,
    $isSearch)
{
    import('Form60.base.SupplierSalesList');

    if ($isSearch)
    {
        //SupplierSalesList($Document,$estate_id, $date1, $date2, $order_by,$order_type, $dateType=2, $store_type_id=-1, $user_id=-1,$province_id=1,$wine_id="", $reportType=1, $page = 1, $isSearch=false,$isFirst=false)
        $listControl = &new SupplierSalesList(null, $estate_id, $date1, $date2, $order_by,
            $order_type, $dateType, $store_type_id, $user_id, $province_id, $wine_id, $vintage,
            0, $currentpage, true, false);
    } else
        $listControl = &new SupplierSalesList(null, $estate_id, $date1, $date2, $order_by,
            $order_type, $dateType, $store_type_id, $user_id, $province_id, $wine_id, $vintage,
            0, $currentpage);

    return $listControl->getContent();

}


//--------------end here --------------

//----------- order functions ----------------

function checkAvaForm604Cities($estate_id, $store_type, $user_id, $from, $to, $cities)
{
    import('Form60.bll.bllf60Reports');

    $objResponse = new xajaxResponse();

    $report = &new F60ReportsData();

    if ($report->getInvoicesData(14, $user_id, $estate_id, $from, $to, $store_type,
        "", $cities) == 0)
    {
        $objResponse->addScript("messageNoRec4City();");
    } else
    {

        //$objResponse->addScript("messageNoRec4City();");

        //createSalesReport4Cities($estate_id,$cities,$store_type,$user_id,$from,$to)
        //$strFunction ="createSalesReport4Cities($estate_id,$cities,$store_type,$user_id,$from,$to);";
        $strFunction = "createSalesReport4Cities($estate_id,'$cities',$store_type,'$user_id',$from,$to);";

        /* 		$fp = fopen("logs/Ajax_logfile.log","a");
        fputs($fp, "$strFunction");
        fclose($fp);
        */
        $objResponse->addScript($strFunction);


    }
    return $objResponse->getXML();
}


function createCSOrder($province_id, $customerID, $estate_id,$pst_no,$other_info, $cs_product_list)
{
    $objResponse = new xajaxResponse();

    import('Form60.bll.bllCsOrder');


    $order = &new bllCsOrder();

    //    $inventoryNum = $order->getCsInventory($estate_id);

    /*  if($inventoryNum<$orderQty)
    {
    $objResponse->addScript("showNotEnoughMsg(" . $inventoryNum .",true);");    
    }
    else
    {*/
    $order_id = $order->createCSOrder($customerID, $estate_id, $province_id,$pst_no,$other_info, $cs_product_list);

    if ($order_id)
    {
        //$objResponse->addAlert($order_id);
        $objResponse->addScript("showCsOrderForm(" . $order_id . ");");
    }
    //	}

    return $objResponse->getXML();
}


function saveCSOrderForm($aFormValues)
{
    $objResponse = new xajaxResponse();

    import('Form60.bll.bllCsOrder');
    import('Form60.util.F60Date');
    import('Form60.util.F60Common');

    $order_id = $aFormValues['order_id'];
    if ($order_id)
    {
        $order = &new bllCsOrder();
        //      $order->loadByPrimaryKey($order_id);

        //get the data from form array
        /*   foreach($aFormValues as $fieldName => $fieldValue)
        {
        if (array_key_exists($fieldName, $order->field_metadata))
        {
        if ($fieldName == 'delivery_date')
        {
        $deliveryDate=$fieldValue;
        }
        
        }
        }*/
        $paymentStatusID = $aFormValues['lkup_payment_status_id'];
        $paymentTypeID = $aFormValues['lkup_payment_type_id'];
        $orderStatusID = $aFormValues['lkup_order_status_id'];
        $other_info = $aFormValues['other_info'];

        $deliveryDate = $aFormValues['delivery_date'];
        $adjustment1 = F60Common::currency2decimal($aFormValues['adjustment_1']);
        $PST_No = $aFormValues['pst_no'];
        $discType = $aFormValues['discType'];
        
        $is_other_delivery = $aFormValues['is_other_delivery'];

        //   $province_id = $aFormValues['$province_id'];

        $discVal = 0;

        if ($discType == 1)
            $discVal = $aFormValues['disc_1'];

        if ($discType == 2)
            $discVal = $aFormValues['disc_2'];


        $province_id = $_COOKIE["F60_PROVINCE_ID"];

        $order->upDateCSOrderITems($order_id, $aFormValues["CSOrder"], $aFormValues["CSOrder_old"],
            $province_id);

        $order->updateCSOrderTable($order_id, $paymentStatusID, $paymentTypeID, $orderStatusID,
            $deliveryDate, $adjustment1, $PST_No, $discType, $discVal,$is_other_delivery,$other_info);
    }
    return $objResponse->getXML();
}

function updateCsOrder($order_id, $cs_product_id, $province_id, $qtyDifferece, $order_qty,
    $adjustment1, $adjustment2, $paymentStatus, $paymentType, $orderStatus, $deliveryDate,
    $isPST,$other_info)
{
    $objResponse = new xajaxResponse();

    import('Form60.bll.bllCsOrder');


    $order = &new bllCsOrder();

    $inventoryNum = $order->getCsInventory($cs_product_id);

    if ($inventoryNum < $qtyDifferece)
    {
        $objResponse->addScript("showNotEnoughMsg(" . $inventoryNum . ",false);");
    } else
    {
        $order->updateCSOrder($order_id, $cs_product_id, $province_id, $qtyDifferece, $order_qty,
            $adjustment1, $adjustment2, $paymentStatus, $paymentType, $orderStatus, $deliveryDate,
            $isPST,$other_info);


        $objResponse->addScript("showCsOrderForm(" . $order_id . ");");

    }


    return $objResponse->getXML();

}


function createOrder($customerID, $estateID, $wineList)
{
    $objResponse = new xajaxResponse();

    import('Form60.bll.bllorders');

    $o = "";
    foreach ($wineList as $wines)
        foreach ($wines as $wine => $qunatity)
            $o = $o . ($wine . "=>" . $qunatity) . "\n";

    $order = &new bllorder();

    $order_id = $order->create($customerID, $estateID, $wines);

    if ($order_id == 3) //oliver oil mix in the same order with wines, they can't be together
    {
        $objResponse->addAlert("Wine orders must be on separate Form 60 invoice from the Olive oil or the Mosto cotto.");
    } else
        if ($order_id)
        {
            $objResponse->addScript("showOrderForm(" . $order_id . ");");
        }
    return $objResponse->getXML();

}

function refreshCSProList($estate_id, $province_id, $orderBy, $orderType)
{
    $objResponse = new xajaxResponse();

    //	$objResponse->addAlert("save failed");
    $objResponse->addAssign("arrow_" . $orderBy, "innerHTML", ($orderType == 'a') ?
        '5' : '6');


    $objResponse->addAssign("csproductList", "innerHTML", getCSProductlistContent($estate_id,
        $province_id, $orderBy, $orderType));

    $objResponse->addScript("if (window.setCSProductsListHeight) setCSProductsListHeight();");
    return $objResponse->getXML();

}

function getCSProductlistContent($estate_id, $province_id, $orderBy, $orderType)
{
    import('Form60.base.CSProductsList');

    $prolistControl = &new CSProductsList(null, $estate_id, $province_id);
    $prolistControl->orderBy = $orderBy;
    $prolistControl->orderType = $orderType;

    $ret = $prolistControl->getContent();

    return $ret;

}


function showtrace($content)
{
    $fp = fopen("logs/ajax.log", "a");
    fputs($fp, $content);
    fclose($fp);
}

function saveOrderForm($aFormValues)
{
    $objResponse = new xajaxResponse();

    import('Form60.bll.bllorders');
    import('Form60.util.F60Date');
    import('Form60.util.F60Common');

    $order_id = $aFormValues['order_id'];
    if ($order_id)
    {
        $order = &new bllorder();
        $order->loadByPrimaryKey($order_id);

        //get the data from form array
        foreach ($aFormValues as $fieldName => $fieldValue)
        {
            if (array_key_exists($fieldName, $order->field_metadata))
            {
                if ($fieldName == 'delivery_date')
                {
                    $fieldValue = F60Date::getsqlDate($fieldValue);
                }
                $order->set_data($fieldName, $fieldValue);
            }
        }

        $order->set_data("adjustment_1", F60Common::currency2decimal($order->get_data("adjustment_1")));
        $order->set_data("adjustment_2", F60Common::currency2decimal($order->get_data("adjustment_2")));
        $order->set_data("deposit", F60Common::currency2decimal($order->get_data("deposit")));
        $order->save();
        $order->AddUpdateOrderItems($aFormValues["Order"]);
    }
    $objResponse->addScript("updateOldQuantityAfterSaveOrder()");
    return $objResponse->getXML();
}

function deleteOrder($customerID, $orderBy, $orderType, $order_id, $estate_id, $order_year, $period, $isQuater)
{
    import('Form60.bll.bllorders');

    $orders = &new bllorders();
    $order = $orders->getByPrimaryKey($order_id);
    $order->mark_deleted();
    $order->save();

    return refreshOrders($customerID, $orderBy, $orderType, $estate_id, $order_year,
        $period, $isQuater);
}

function getOrderlistContent($customerID, $orderBy, $orderType, $estate_id, $order_year,
    $period, $isQuater)
{
    import('Form60.base.F60OrderList');
    //   function F60OrderList($Document, $customerID,$period, $isQuater)

    $ordelistControl = &new F60OrderList(null, $customerID, $period, $order_year, $isQuater);
    $ordelistControl->orderBy = $orderBy;
    $ordelistControl->orderType = $orderType;
    $ordelistControl->estate_id = $estate_id;
    $ordelistControl->order_year = $order_year;

    return $ordelistControl->getContent();
}

function refreshOrders($customerID, $orderBy, $orderType, $estate_id, $order_year,
    $period, $isQuater)
{
    $objResponse = new xajaxResponse();
    $objResponse->addAssign("arrow_" . $orderBy, "innerHTML", ($orderType == 'a') ?
        '5' : '6');
    $objResponse->addAssign("ordersList", "innerHTML", getOrderlistContent($customerID,
        $orderBy, $orderType, $estate_id, $order_year, $period, $isQuater));
    $objResponse->addScript("if (window.setorderListHeight) setorderListHeight();");

    return $objResponse->getXML();

}

//(estate_id,customer_id, order_by, order_type,order_id,  order_year,period,isQut,province_id);

function deleteCSOrder($estate_id, $customerID, $orderBy, $orderType, $order_id,
    $order_year, $period, $isQuater, $province_id)
{
    import('Form60.bll.bllCsOrder');

    $orders = &new bllCsOrder();
    $order = $orders->deleteCSOrderByID($order_id, $province_id);


    return refreshCSOrders($estate_id, $customerID, $orderBy, $orderType, $order_year,
        $period, $isQuater);
}

function getCSOrderlistContent($estate_id, $customerID, $orderBy, $orderType, $order_year,
    $period, $isQuater)
{
    import('Form60.base.F60CSOrderList');
    //   function F60OrderList($Document, $customerID,$period, $isQuater)
    /*	$fp = fopen("logs/testajax.log","a");
    fputs($fp, "step getCSOrderlistContent");
    fclose($fp);*/
    $ordelistControl = &new F60CSOrderList(null, $customerID, $period, $order_year,
        $isQuater);
    $ordelistControl->orderBy = $orderBy;
    $ordelistControl->orderType = $orderType;
    $ordelistControl->estate_id = $estate_id;
    $ordelistControl->order_year = $order_year;

    return $ordelistControl->getContent();
}
function refreshCSOrders($estate_id, $customerID, $orderBy, $orderType, $order_year,
    $period, $isQuater)
{
    $objResponse = new xajaxResponse();
    $objResponse->addAssign("arrow_" . $orderBy, "innerHTML", ($orderType == 'a') ?
        '5' : '6');
    $objResponse->addAssign("cs_ordersList", "innerHTML", getCSOrderlistContent($estate_id,
        $customerID, $orderBy, $orderType, $order_year, $period, $isQuater));
    $objResponse->addScript("if (window.setCSOrderListHeight) setCSOrderListHeight();");

    $objResponse->addScript("refreshCSProduts4OrderList($estate_id);");


    return $objResponse->getXML();

}
//--------------end order functions --------------
// ---------------------------- sales list in customer ----------------------

function getSaleslistContent($customerID, $orderBy, $orderType, $sales_year, $sales_period,
    $isQuarter, $store_type_id, $currentpage, $province_id)
{
    import('Form60.base.F60SalesList');

    $salselistControl = &new F60SalesList(null, $customerID, $sales_period, $sales_year,
        $isQuarter, $store_type_id, $currentpage, true, $province_id);
    $salselistControl->orderBy = $orderBy;
    $salselistControl->orderType = $orderType;
    //  $ordelistControl->estate_id = $estate_id;
    //   $ordelistControl->order_year = $order_year;


    /*    	$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, $salselistControl->getContent()."\n");
    fclose($fp);*/

    return $salselistControl->getContent();

}

function deleteWineByProId($is_international, $wine_id, $proid, $estate_id)
{
    import('Form60.bll.bllf60wines');


    $bllwine = &new bllf60wines();

    $retVal = $bllwine->deleteWine4Province($is_international, $wine_id, $proid, $estate_id);

    $objResponse = new xajaxResponse();
    if ($retVal)
    {
        $objResponse->addScript("disableCtls4Delete($proid);");
    } else
    {
        $objResponse->addScript("alert('Can't delete the wine.');");
    }
    return $objResponse->getXML();
}


function deleteBeer($beer_id, $proid)
{
    import('Form60.bll.bllBeers');


    $bllBeer = &new bllBeers();

    $retVal = $bllBeer->deleteBeer($beer_id, $proid);


    $objResponse = new xajaxResponse();
    if ($retVal)
    {

        $objResponse->addScript("beerDeleted($proid);");

    } else
    {
        $objResponse->addScript("alert('Can't delete the beer. Please try again');");
    }

    return $objResponse->getXML();
}

function refreshSalesList($customerID, $orderBy, $orderType, $sales_year, $sales_period,
    $isQuarter, $store_type_id, $currentpage, $province_id)
{
    /*	$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp,  getSaleslistContent($customerID, $orderBy, $orderType, $sales_year, $sales_period, $isQuarter,$store_type_id)."\n");
    fclose($fp);*/
    $objResponse = new xajaxResponse();
    $objResponse->addAssign("arrow_" . $orderBy, "innerHTML", ($orderType == 'a') ?
        '5' : '6');
    $objResponse->addAssign("salesList", "innerHTML", getSaleslistContent($customerID,
        $orderBy, $orderType, $sales_year, $sales_period, $isQuarter, $store_type_id, $currentpage,
        $province_id));

    $objResponse->addScript("if (window.setSalesListHeight) setSalesListHeight();");

    return $objResponse->getXML();
}
//-------------- end of sale list -------------------------


//-------------- reports ---------------------------
function getWines($controlID, $estateID)
{
    import('Form60.pages.reportsMain');

    $scrWines = reportsMain::getWines4SelectScript($controlID, $estateID);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrWines);

    //	$fp = fopen("logs/Ajax_logfile.log","a");
    //fputs($fp, $scrWines."\n");
    //fclose($fp);

    return $objResponse->getXML();
}

//getestatesbycountry

function getEstatesByCountry($controlID, $country)
{
    import('Form60.pages.estateSelect');
    $scrEstates = estateSelect::getInEstate4SelectHtml($controlID, $country);

    /*	$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, $scrEstates."\n");
    fclose($fp);*/

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrEstates);
    return $objResponse->getXML();
}

function getMonthBySaleYear($fsyear)
{
    import('Form60.pages.selectSSDSMonth');

    //	$htmlWines = reportsMain::getWines4SelectHtml($estateID);
    $ssdsMonth = &new selectSSDSMonth();
    $scrTxt = $ssdsMonth->getMonthsSelectHtml($fsyear);

    /*	$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, $scrTxt."\n");
    fclose($fp);*/

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrTxt);
    return $objResponse->getXML();
}

//---------------- AB vender sales start------------
function getABVenderSalesYears($reprotTypeId, $estate_id)
{
    import('Form60.pages.reportsMain');

    //	$htmlWines = reportsMain::getWines4SelectHtml($estateID);
    $ssdsDate = &new reportsMain();
    $scrTxt = $ssdsDate->setSalesYearsToControl($reprotTypeId, $estate_id);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrTxt);
    return $objResponse->getXML();
}

function getABVenderSalesMonths($reprotTypeId, $sYear, $estate_id)
{
    import('Form60.pages.reportsMain');

    //	$htmlWines = reportsMain::getWines4SelectHtml($estateID);
    $ssdsMonth = &new reportsMain();
    $scrTxt = $ssdsMonth->setSalesMonthsToControl($sYear, $reprotTypeId, $estate_id);

    /*	$fp = fopen("logs/Ajax_logfile.log","a");
    fputs($fp, $scrTxt."\n");
    fclose($fp);*/

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrTxt);
    return $objResponse->getXML();
}

//---------------- AB vender sales end ------------


//summary report

//----------------------- summaryList ------------------- ssdsCommissionList
function getSummaryListContent($user_id, $sale_month, $sale_year, $store_type, $bonus_type,
    $display_page)
{
    import('Form60.base.ssdsSummaryList');

    $listControl = &new ssdsSummaryList(null, $user_id, $sale_month, $sale_year, $store_type,
        $bonus_type, $display_page);

    return $listControl->getContent();

}

function getCommissionListContent($user_id, $sale_month, $sale_year)
{
    import('Form60.base.ssdsCommissionList');

    $listControl = &new ssdsCommissionList(null, $user_id, $sale_month, $sale_year);

    return $listControl->getContent();

}


function refreshSummaryLists($user_id, $sale_month, $sale_year, $store_type, $bonus_type,
    $display_page)
{
    $objResponse = new xajaxResponse();

    //    $objResponse->addAssign("arrow_". $orderBy."_".$statusid, "innerHTML",($orderType=='a')?'5':'6');
    $objResponse->addAssign("tdSummaryList", "innerHTML", getSummaryListContent($user_id,
        $sale_month, $sale_year, $store_type, $bonus_type, $display_page));
    // $objResponse->addAssign("commissions0", "innerHTML", getSummaryListContent($user_id, $period_id, $store_type,$display_page));
    return $objResponse->getXML();

}


//Store pentration report
function collectSpData($isCurrentDate = true)
{
    import('Form60.bll.bllstorepenetrationdata.');

    $bllSP = &new bllStorePenetrationData();

    $bllSP->collectData(true);

    $objResponse = new xajaxResponse();

    $objResponse->addScript("disableWaitImg();");

    return $objResponse->getXML();
}

function getSPReportMonths($controlID, $reportYear)
{
    import('Form60.bll.bllstorepenetrationdata.');

    $bllSP = &new bllStorePenetrationData();
    $selectHTML = $bllSP->getMonthsSelectHtml($controlID, $reportYear);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($selectHTML);
    return $objResponse->getXML();
}

//commission level

function setCommissionType($user_id)
{
    import('Form60.bll.bllSalesCommission');


    $bllData = &new salesCommissionData();

    $type_id = $bllData->getCommissionTypeByUser($user_id);

    $objResponse = new xajaxResponse();
    $objResponse->addScript("setCommissionType($type_id);");


    return $objResponse->getXML();
}

function getUsersByProvince($province_id)
{

    import('Form60.pages.selectCommissionType');
    $scrUsers = selectCommissionType::getUsersHtmlByProvince($province_id);

    $objResponse = new xajaxResponse();
    $objResponse->addScript($scrUsers);

    return $objResponse->getXML();


}
function fillCSInventory($cs_product_id, $province_id, $tota_units)
{

    import('Form60.bll.bllCSProduct');
    $bllData = &new bllCSProduct();

    $inventory = $bllData->addUnitsToInventory($cs_product_id, $province_id, $tota_units);

    $objResponse = new xajaxResponse();

    $objResponse->addScript("setInventory($inventory);");

    return $objResponse->getXML();


}
//-------- end define functions --------


$xajax = new xajax();
//$xajax->debugOn();

//register the functions to ajax

//CS Product
$xajax->registerFunction("fillCSInventory");


//CS_product orders
$xajax->registerFunction("refreshCSProList");
$xajax->registerFunction("createCSOrder");
$xajax->registerFunction("updateCsOrder");
$xajax->registerFunction("refreshCSOrders");
$xajax->registerFunction("deleteCSOrder");

$xajax->registerFunction("saveCSOrderForm");


//--- Alberta vender report
$xajax->registerFunction("getABVenderSalesYears");
$xajax->registerFunction("getABVenderSalesMonths");


//---- Note
$xajax->registerFunction("deleteNote");
$xajax->registerFunction("saveNote");
$xajax->registerFunction("refreshNotes");


//-- Market list
$xajax->registerFunction("refreshMarketList");


//-- wine list
$xajax->registerFunction("refreshWines");
$xajax->registerFunction("getWines");


//--- order list
$xajax->registerFunction("createOrder");
$xajax->registerFunction("saveOrderForm");
$xajax->registerFunction("refreshOrders");
$xajax->registerFunction("deleteOrder");
$xajax->registerFunction("refreshSalesList");


$xajax->registerFunction("getEstatesByCountry");

//$xajax->registerFunction("getPeriodInfo");
$xajax->registerFunction("getMonthBySaleYear");

$xajax->registerFunction("refreshSummaryLists");

$xajax->registerFunction("deleteWineByProId");

$xajax->registerFunction("refresf60searchResultLists");

$xajax->registerFunction("setProductTypes");
//supplier pages
$xajax->registerFunction("getSPReportMonths");
$xajax->registerFunction("collectSpData");

$xajax->registerFunction("getUsers");
$xajax->registerFunction("refreshSupplierSalesList");

$xajax->registerFunction("getStoreTypes");
$xajax->registerFunction("getWines4Supplier");
$xajax->registerFunction("getYears");
$xajax->registerFunction("getMonths");
$xajax->registerFunction("getVintageList");

//supplier update inovices
$xajax->registerFunction("supplierUpdateInvoice");

//beer
$xajax->registerFunction("deleteBeer");


//overdue list report
$xajax->registerFunction("checkAvaForm604Cities");
$xajax->registerFunction("refreshOverdueList");


//commission
$xajax->registerFunction("setCommissionType");
$xajax->registerFunction("getUsersByProvince");

//sales analsys
$xajax->registerFunction("getAnaSalesMonths");
$xajax->registerFunction("getAnaSalesUsers");
$xajax->registerFunction("BI_checkASAvaData");
$xajax->registerFunction("BI_generateASAnaData");
//process ajax calls
$xajax->processRequests();

?>
