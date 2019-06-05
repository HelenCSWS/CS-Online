<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.bll.bllCSProduct');
import('Form60.bll.bllestates');
import('Form60.bll.bllcsOrder');

import('Form60.base.F60DbUtil');


class csProductAdd extends F60FormBase
{

    var $estate_id;

    var $editMode = 0; //0: add new wine 1: update wine info 2: add new delivery 3: edit delivery
    var $is_international = 0;
    var $province_id = "";

    var $pros = 2;
    var $cs_product_id = "";

    var $isNewProduct = true;

    var $arryProductInfo = array();
    var $oldProductData = array();

    var $pageid = 0;
    var $login_user_id;
    var $isAdmin = false;


    function csProductAdd()
    {

        if (F60FormBase::getCached())
            exit(0);


        $this->pageid = $_REQUEST["pageid"];


        $this->province_id = $_COOKIE["F60_PROVINCE_ID"];

        if ($_REQUEST["province_id"] != null && $_REQUEST["province_id"] != "")
        {
            $this->province_id = $_REQUEST["province_id"];
        }

        if ($_REQUEST["cs_product_id"] != null && $_REQUEST["cs_product_id"] != "")
        {

            $this->cs_product_id = $_REQUEST["cs_product_id"];

            $this->isNewProduct = false;
        } else
        {

            $this->isNewProduct = true;
        }

        $this->estate_id = $_REQUEST["estate_id"];

        $typefilter = " estate_id= $this->estate_id";

        Registry::set('productTypeFilter', $typefilter);

        $estate_name = F60DbUtil::getEstateName($this->estate_id);

        $title = "Save product for $estate_name";

        F60FormBase::F60FormBase('CSProductAdd', $title, 'addCSProduct.xml',
            'addCSProduct.tpl', 'btnAdd');

        $this->addScript('resources/js/javascript.jquery.js');
        $this->addScript('resources/js/javascript.addCSProduct.js');
        $this->addScript('resources/js/javascript.common.js');
        
        $this->addScript('resources/js/javascript.accounting.js');

        $form = &$this->getForm();


        $form->setFormAction($_SERVER["REQUEST_URI"]);

        import('Form60.base.F60PageStack');
        F60PageStack::addtoPageStack();

        $sUrl = 'main.php';
        $this->registerActionhandler(array(
            "btnAdd",
            array($this, processForm),
            "LASTPAGE",
            $sUrl));

        $this->registerActionhandler(array(
            "btnDeleteCSProduct",
            array($this, deleteProduct),
            "URL",
            $sUrl));

        $URL = "main.php?page_name=csProductAdd&estate_id=$this->estate_id&pageid=$this->pageid";
        $this->registerActionhandler(array(
            "btnAddAnother",
            array($this, saveAddAnother),
            "URL",
            $URL));

        $this->form->setButtonStyle('btnOK');
        $this->form->setInputStyle('input');
        $this->form->setLabelStyle('label');

        $estate = &$form->getField("estate_id");
        $estate->setValue($this->estate_id);

        $addAnother = &$form->getField("isAddAnother");
        $addAnother->setValue(0);


        $this->login_user_id = F60DALBase::get_current_user_id();
        $user_level_id = F60DbUtil::get_user_level($this->login_user_id);

        if ($user_level_id == 1)
        {
            $this->isAdmin = true;
            $this->setValue2Ctl("1", "isAdmin", $form);
        }

    }

    function display()
    {
        if (!$this->handlePost())
            $this->displayForm();
    }

    function displayForm()
    {
        $form = &$this->getForm();


        $this->setValue2Ctl($this->province_id, "province_id", $form);
        $this->setValue2Ctl($this->province_id, "current_province_id", $form);


        if (!$this->isNewProduct)
        {

            /*	$action = array(
            
            "Delete product"=>"javascript:delProduct(0);",
            
            );
            
            $this->setActions($action);	*/
            $this->loadProductInfo();

        }


        F60FormBase::display();

    }

    function loadProductInfo()
    {

        $this->setValue2Ctl($this->cs_product_id, "product_id", $this->form);
        $bllProduct = &new bllcsproduct();

        $productBasicInfo = $bllProduct->getProductBasicInfo($this->cs_product_id);

        $this->setValue2Ctl($productBasicInfo["product_name"], "product_name", $this->
            form);
        $this->setValue2Ctl($productBasicInfo["product_code"], "product_number", $this->
            form);
        $this->setValue2Ctl($productBasicInfo["units_per_case"], "bottles_per_case", $this->
            form);
        $this->setValue2Ctl($productBasicInfo["lkup_product_type_id"],
            "lkup_product_type_id", $this->form);


        $current_province_id = $this->getValueFromCtl("current_province_id");


        $productInfo = $bllProduct->getProductInfo($this->cs_product_id, $current_province_id);

        $this->setValue2Ctl($productInfo["price_per_unit"], "display_price", $this->
            form);
        $this->setValue2Ctl($productInfo["promotion_price"], "special_price", $this->
            form);
        $this->setValue2Ctl($productInfo["cost_per_unit"], "cost", $this->form);
        $this->setValue2Ctl($productInfo["commission"], "commission", $this->form);

        // inventory
        $inventoryInfo = $bllProduct->getProductInventory($this->cs_product_id, $this->
            province_id);


        $total_units = 0;
        if ($inventoryInfo != -1)
            $total_units = $inventoryInfo;

        $this->setValue2Ctl(intval($total_units), "total_units", $this->form);


        return true;
    }


    function setValue2Ctl($val, $ctlName, $form)
    {
        $ctl = &$form->getField($ctlName);
        $ctl->setValue($val);
    }

    function setValueToCtl($ctlName, $value, $isCurrency = false)
    {


        $form = $this->form;

        $ctl = &$form->getField($ctlName);

        if ($isCurrency)
            $value = "$" . $value;

        $ctl->setValue($value);

        //$retValue=str_replace("$","",$retValue);
        //	return $retValue;
    }

    function getValueFromCtl($ctlName, $form = null)
    {

        $form = $this->form;

        $ctl = &$form->getField($ctlName);


        $retValue = $ctl->getValue();

        $retValue = str_replace("$", "", $retValue);

        return trim($retValue);
    }


    function addInventory()
    {
        $bllProduct = &new bllcsproduct();
        $bllProduct->addUnitsToInventory(1, 3, 20, $this->login_user_id);
    }

    function saveAddAnother()
    {
        $this->saveCSProduct(true);
        return true;

    }
    function processForm()
    {
        $this->saveCSProduct(false);
        $this->login_user_id = F60DALBase::get_current_user_id();
        $user_level_id = F60DbUtil::get_user_level($this->login_user_id);

        if ($user_level_id == 1)
        {
            return false;
        } else
        {

            return true;
        }

    }
    function saveCSProduct($isAddAnother)
    {
        $saveInfo = false;
        $orderInfoBll = &new bllcsorder();
        $bllProduct = &new bllcsproduct();
        $isChecked = 0;
        $form = $this->form;
        $retVal = false;

        $this->login_user_id = F60DALBase::get_current_user_id();
        if ($this->isFormValid()) // add new beer
        {
            if ($this->isNewProduct) // save product base
            {
                $this->cs_product_id = $bllProduct->saveProductBasicInfo($this->getValueFromCtl
                    ("estate_id"), $this->getValueFromCtl("product_name"), $this->getValueFromCtl("product_number"),
                    $this->getValueFromCtl("bottles_per_case"), $this->getValueFromCtl("lkup_product_type_id"),
                    $this->login_user_id);

                $provincesInfo = F60DbUtil::getProvinces();

                //   print_r ($provincesInfo);
                if (count($provincesInfo) != 0)
                {

                    for ($i = 0; $i < count($provincesInfo); $i++)
                    {

                        $db_province_id = $provincesInfo[$i]["province_id"];


                        //                                               ($product_id,$display_price,$special_price,$commission,$cost,$user_id,$province_id)
                        $retVal = $bllProduct->saveProductProvinceInfo($this->cs_product_id, 0, 0, 0, 0,
                            $this->login_user_id, $db_province_id);


                    }
                }


                $retVal = $bllProduct->updateProductProvinceInfo($this->cs_product_id, $this->
                    getValueFromCtl("display_price"), $this->getValueFromCtl("special_price"), $this->
                    getValueFromCtl("commission"), $this->getValueFromCtl("cost"), $this->
                    login_user_id, $this->getValueFromCtl("current_province_id"));

                $saveInfo = true;

            } else
            {
                $this->login_user_id = F60DALBase::get_current_user_id();
                if ($this->getValueFromCtl("basicChanged") == 1)
                {

                    $retVal = $bllProduct->updateProductBasicInfo($this->cs_product_id, $this->
                        getValueFromCtl("product_name"), $this->getValueFromCtl("product_number"), $this->
                        getValueFromCtl("bottles_per_case"), $this->getValueFromCtl("lkup_product_type_id"),
                        $this->login_user_id);
                    $saveInfo = true;
                }
                if ($this->getValueFromCtl("infoChanged") == 1)
                {

                    $retVal = $bllProduct->updateProductProvinceInfo($this->cs_product_id, $this->
                        getValueFromCtl("display_price"), $this->getValueFromCtl("special_price"), $this->
                        getValueFromCtl("commission"), $this->getValueFromCtl("cost"), $this->
                        login_user_id, $this->getValueFromCtl("current_province_id"));
                    $saveInfo = true;
                }

            }

            if ($saveInfo && !$isAddAnother)
                $this->form->addErrors("Current province's product has been save!");
            else
            {
                if (!$isAddAnother)
                    $this->form->addErrors("Nothing was changed for this product!");
            }


            $this->setValue2Ctl(1, "isAddNew", $this->form);
            $this->setValue2Ctl(0, "infoChanged", $this->form);
            $this->setValue2Ctl(0, "basicChanged", $this->form);
            $this->setValue2Ctl($this->province_id, "province_id", $this->form);
            $this->setValue2Ctl($this->cs_product_id, "product_id", $this->form);

        } // END if($this->isFormValid())

    }


    function isFormValid()
    {

        $bllProduct = new bllCSProduct();

        $isSavePro = false;

        //check empty form

        $product_name = $this->getValueFromCtl("product_name");
        if ($product_name == "")
        {
            $this->form->addErrors("Please fill the product name first.");
            return false;
        }

        if ($this->isNewProduct)
        {
            if ($bllProduct->checkDuplicatProductName($product_name))
            {
                $this->form->addErrors("There is a same product in CS Online.");
                return false;
            }
        }
        return true;

    }

    function deleteProduct()
    {
        $isBc = false;
        if ($this->is_international == 0)
        {
            $isBc = true;
        }
        return $this->bllWines->deleteWine($this->wine_id, $isBc);

    }

    /*
    
    */


}

?>
