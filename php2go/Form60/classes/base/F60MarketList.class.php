<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');

define('PAGE_SIZE', 10);
import('Form60.util.F60Common');

class F60MarketList extends PagedDataSet  // For market update
{
    var $_Document;
    var $orderBy = "customer_name";
    var $orderType = "a";
    var $page = 1;
    var $sqlCode;

    var $Template = NULL;
    var $templateFile;
    var $sortSymbols;

    var $statusID;
    var $pageSize;

	var $prn;

    function F60MarketList($Document,$statusId,$pagesize,$print=FALSE)
    {
       $this->pageSize = $pagesize;
       $this->statusID = $statusId;
       $this->prn = $print;

       PagedDataSet::PagedDataSet('db');
       PagedDataSet::setPageSize($this->pageSize);

       if ($Document)
       {
			if(!$this->prn)
	            $Document->addScript('resources/js/javascript.customercompare.js');
            $this->_Document = $Document;
       }

		if($this->prn)
		    $this->templateFile = 'marketList_prn.tpl';
		else
		    $this->templateFile = 'marketList.tpl';
       $this->templateFile = TEMPLATE_PATH. $this->templateFile;
       $this->Template =& new Template($this->templateFile);
       if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
            $this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));

       $this->Template->parse();

       $this->sortSymbols = & F60Common::sortSymbols();
    }


	function _loadDataset()
	{
	switch($this->statusID)
	{
	case 0:   //new
		$sqlTemplate ="SELECT * from uploaded_customers uc inner join uploaded_customer_changes_new ucc on uc.row_id = ucc.customer_row_id order by uc.%s %s";
		break;
	case 1: //changed
		$sqlTemplate ="SELECT * from uploaded_customers uc inner join uploaded_customer_changes ucc on uc.row_id = ucc.customer_row_id order by uc.%s %s";
		break;
	case 2: //oob
		$sqlTemplate ="SELECT distinct cm.licensee_number as license_number, cm.customer_id id, customer_name,
							concat(IFNULL(first_name,''),' ',IFNULL(last_name,'')) contact_name,
							cm.phone_office1 phone, cm.phone_fax fax,
							concat(IFNULL(billing_address_unit,'') ,
							IFNULL(billing_address_street_number,''), ' ',
							IFNULL(billing_address_street,'')) address,
							IFNULL(billing_address_city,'') city,
      	                     IFNULL(billing_address_postalcode,'') postalcode
							from customers cm join uploaded_customer_changes_oob ucc on cm.customer_id = ucc.customer_id
							left join customers_contacts cmc on cm.customer_id = cmc.customer_id and cmc.is_primary=1
							left join contacts c on c.contact_id = cmc.contact_id and c.deleted=0
							where cm.deleted=0 order by %s %s";
	break;
	}
	
	$this->sqlCode = sprintf($sqlTemplate, $this->orderBy,
	(
		$this->orderType == "d")?" DESC":"ASC");
		PagedDataSet::setCurrentPage($this->page);
		PagedDataSet::load($this->sqlCode);
	}

	function getType()
	{
		switch($this->statusID)
		{
		case 0:   //new
		    return "New customers";
		case 1: //changed
		    return "Customers updated";
		case 2: //oob
		    return "Customers out of business";
		}
	}

	function getYTD()
	{
		switch($this->statusID)
		{
		case 0:   //new
			$sql = "select sum(st.new_number) ytd from uploaded_customers_statistics st inner join uploaded_customers_sessions se on st.session_id = se.session_id where se.step_id = 5 and YEAR(st.upload_date) = YEAR(NOW())";
			break;
		case 1: //changed
			$sql = "select sum(st.updated_number) ytd from uploaded_customers_statistics st inner join uploaded_customers_sessions se on st.session_id = se.session_id where se.step_id = 5 and YEAR(st.upload_date) = YEAR(NOW())";
			break;
		case 2: //oob
			$sql = "select sum(st.oob_number) ytd from uploaded_customers_statistics st inner join uploaded_customers_sessions se on st.session_id = se.session_id where se.step_id = 5 and YEAR(st.upload_date) = YEAR(NOW())";
		    break;
		}
		$res = $this->runSQL($sql);
		$row = $res->fetchRow();
		$ytd = $row['ytd'];
		return $ytd;
	}

	function _buildPrnContent()
	{
		//don't use template in print mode
        $this->_loadDataset();

		$ytd = $this->getYTD();
		$content = '<table width="99%" cellpadding="0" cellspacing="0" border="0" align="center"><tr>';
		$content .= '<td class="label" width="200" style="font:bold;">' . $this->getType() . ': ' . PagedDataSet::getTotalRecordCount() . '</td>';
		$content .= '<td class="label" width="160" style="font:bold;">YTD: ' . ($ytd?$ytd:0) . '</td><td>&nbsp;</td></tr></table>';

		$content .= '<table width="99%" cellpadding="0" cellspacing="0" border="0" align="center"><tr bgcolor="#7F9DB9">';
		$content .= '<td nowrap class="mlcolheader" width="40">License#</td>';
		$content .= '<td nowrap class="mlcolheader" width="200">Customer</td>';
		//$content .= '<td nowrap class="mlcolheader" width="100">Contact</td>';
		$content .= '<td nowrap class="mlcolheader" width="100">Postal Code</td>';
		$content .= '<td nowrap class="mlcolheader" width="200">Address</td>';
		$content .= '<td nowrap class="mlcolheader" width="100">City</td>';
		$content .= '<td nowrap class="mlcolheader" width="30">Phone</td>';
		$content .= '<td nowrap class="mlcolheader" width="30">Fax</td></tr>';
		$content .= '';
        $aRow = 0;
        $changed_color_style = "style='color:red;'";
        while($lineData = PagedDataSet::fetch())
        {
			$aRow++;
			$content .= '<tr class="ml' . (($aRow % 2)?"cellA":"cellB") . '">';
            $content .= '<td nowrap class="CPgridrowCell" ' . (($lineData["license_changed"]==1) ? $changed_color_style : "") . ' valign="middle">' . (($lineData["license_number"]!="")?$lineData["license_number"]:"&nbsp;") . '</td>';
            $content .= '<td nowrap class="CPgridrowCell" ' . (($lineData["name_changed"]==1) ? $changed_color_style : "") . ' valign="middle">' . (($lineData["customer_name"]!="")?$lineData["customer_name"]:"&nbsp;") . '</td>';
    //        $content .= '<td nowrap class="CPgridrowCell" ' . (($lineData["contact_name_changed"]==1) ? $changed_color_style : "") . ' valign="middle">' . (($lineData["contact_name"]!="")?$lineData["contact_name"]:"&nbsp;") . '</td>';
            $content .= '<td nowrap class="CPgridrowCell" ' . ' valign="middle">' . (($lineData["postalcode"]!="")?$lineData["postalcode"]:"&nbsp;") . '</td>';
            $content .= '<td nowrap class="CPgridrowCell" ' . (($lineData["address_changed"]==1) ? $changed_color_style : "") . ' valign="middle">' . (($lineData["address"]!="")?$lineData["address"]:"&nbsp;") . '</td>';
            $content .= '<td nowrap class="CPgridrowCell" ' . (($lineData["city_changed"]==1) ? $changed_color_style : "") . ' valign="middle">' . (($lineData["city"]!="")?$lineData["city"]:"&nbsp;") . '</td>';
            $content .= '<td nowrap class="CPgridrowCell" ' . (($lineData["phone_changed"]==1) ? $changed_color_style : "") . ' valign="middle">' . (($lineData["phone"]!="")?$this->formatPhone($lineData["phone"]):"&nbsp;") . '</td>';
            $content .= '<td nowrap class="CPgridrowCell" ' . (($lineData["fax_changed"]==1) ? $changed_color_style : "") . ' valign="middle">' . (($lineData["fax"]!="")?$this->formatPhone($lineData["fax"]):"&nbsp;") . '</td>';
			$content .= '</tr>';
		}
		$content .= '</table>';
		return $content;
	}

	function _buildContent()
    {
        $this->_loadDataset();

        $aRow = 0;
        $changed_color_style = "style='color:red;'";
        while ($lineData = PagedDataSet::fetch())
        {
            $aRow++;
            $this->Template->createBlock('loop_line');
            $this->Template->assign("row_style", ($aRow % 2)?"cellA":"cellB");
            $this->Template->assign("license_number", ($lineData["license_number"]!="")?$lineData["license_number"]:"&nbsp;");
            $this->Template->assign("license_updcolor", ($lineData["license_changed"]==1)?$changed_color_style:"");

            //cut value
            $this->Template->assign("customer_name",($lineData["customer_name"]!="")?$this->getCut($lineData["customer_name"],40):"&nbsp;");
            $this->Template->assign("customer_name_updcolor", ($lineData["name_changed"]==1)?$changed_color_style:"");

    //        $this->Template->assign("contact_name",($lineData["contact_name"]!="")?$this->getCut($lineData["contact_name"],20):"&nbsp;");
   //         $this->Template->assign("contact_name_updcolor", ($lineData["contact_name_changed"]==1)?$changed_color_style:"");
            $this->Template->assign("postalcode",($lineData["postalcode"]!="")?$this->getCut($lineData["postalcode"],20):"&nbsp;");
        //    $this->Template->assign("contact_name_updcolor", ($lineData["contact_name_changed"]==1)?$changed_color_style:"");

           //$this->Template->assign("contact_name", $lineData["contact_name"]);
           $this->Template->assign("address",($lineData["address"]!="")?$this->getCut($lineData["address"],25):"&nbsp;");
            $this->Template->assign("address_updcolor", ($lineData["address_changed"]==1)?$changed_color_style:"");

            $this->Template->assign("city",($lineData["city"]!="")?$this->getCut($lineData["city"],15):"&nbsp;");
            $this->Template->assign("city_updcolor", ($lineData["city_changed"]==1)?$changed_color_style:"");

          $this->Template->assign("phone",($lineData["phone"]!="")?$this->formatPhone($lineData["phone"]):"&nbsp;");
            $this->Template->assign("phone_updcolor", ($lineData["phone_changed"]==1)?$changed_color_style:"");
            
           $this->Template->assign("fax",($lineData["fax"]!="")?$this->formatPhone($lineData["fax"]):"&nbsp;");
            $this->Template->assign("fax_updcolor", ($lineData["fax_changed"]==1)?$changed_color_style:"");

			//Title
            $this->Template->assign("customer_name_t",($lineData["customer_name"]!="")?str_replace('"', '&quot;', $lineData["customer_name"]):"&nbsp;");
            $this->Template->assign("postalcode_t",($lineData["postalcode"]!="")?str_replace('"', '&quot;', $lineData["postalcode"]):"&nbsp;");
           //$this->Template->assign("contact_name", $lineData["contact_name"]);
           $this->Template->assign("address_t",($lineData["address"]!="")?str_replace('"', '&quot;', $lineData["address"]):"&nbsp;");
           $this->Template->assign("city_t",($lineData["city"]!="")?str_replace('"', '&quot;', $lineData["city"]):"&nbsp;");
           $this->Template->assign("phone_t",($lineData["phone"]!="")?$this->formatPhone($lineData["phone"]):"&nbsp;");
           $this->Template->assign("fax_t",($lineData["fax"]!="")?$this->formatPhone($lineData["fax"]):"&nbsp;");
          // $this->Template->assign("address", $lineData["address"]);
          //  $this->Template->assign("phone", $lineData["phone"]);
          //  $this->Template->assign("fax", $lineData["fax"]);
       }
	    $this->Template->globalAssign("type", $this->getType());
		$ytd = $this->getYTD();
		$this->Template->globalAssign("ytd_number", $ytd?$ytd:0);

       $this->Template->globalAssign("statusid", $this->statusID);
       $this->Template->globalAssign("statues_id", $this->statusID);
       $this->Template->globalAssign("total",  PagedDataSet::getTotalRecordCount());
		if (!$this->prn)
        {
           $this->Template->globalAssign("page",  (PagedDataSet::getPageCount()==0)?0:PagedDataSet::getCurrentPage());
           $this->Template->globalAssign("total_page",  PagedDataSet::getPageCount());
           $this->Template->globalAssign("isDisplay", "block" );

            if (PagedDataSet::getPreviousPage())
            {
                $this->Template->createBlock('prev_page_link');
                $this->Template->assign("prev_page", PagedDataSet::getPreviousPage());
            }
            if (PagedDataSet::getPageCount()>1 &&  PagedDataSet::getCurrentPage() <PagedDataSet::getPageCount())
            {
                $this->Template->createBlock('next_page_link');
                $this->Template->assign("next_page", PagedDataSet::getNextPage());
            }
        }
        else
        {
             $this->Template->globalAssign("isDisplay", "none" );
        }
        $this->Template->globalAssign($this->orderBy . "_sort", $this->sortSymbols[$this->orderType]);
        $this->Template->globalAssign("order_by", $this->orderBy);
        $this->Template->globalAssign("order_type", $this->orderType);
    }

    function getContent()
    {
		if($this->prn)
		{
	        return $this->_buildPrnContent();
		}
		else {
	        $this->_buildContent();
	        return $this->Template->getContent();
        }
    }

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

	function formatPhone($number)
	{
		$ret = $number;
		$l = strlen($number);
		if($l > 4) {
			if($l < 8)
			    $ret = substr($number, 0, -4) . '-' . substr($number, -4);
			else if($l < 11)
			    $ret = substr($number, 0, $l-7) . '-' . substr($number, $l-7, -4) . '-' . substr($number, -4);
			else
			    $ret = substr($number, 0, $l-10) . '-' . substr($number, $l-10, $l-7) . '-' . substr($number, $l-7, -4) . '-' . substr($number, -4);
		}
		return $ret;
	}

	function getCut($listVal,$l)
	{
		$retVal = "";
		if ($listVal != Null && trim($listVal)!="")
		{
            $retVal =$listVal;
           // print strlen($listVal).'   ';
			if (strlen($listVal)>$l)
			{
				//print herer;
				$retVal = substr($listVal,0,$l).'...';
  			}
		}
		return $retVal;
 	}
}
?>
