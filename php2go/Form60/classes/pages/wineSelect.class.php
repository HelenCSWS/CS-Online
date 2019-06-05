<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.bll.bllwines');
import('Form60.dal.dalwines');
import('Form60.base.F60DbUtil');
import('php2go.util.HtmlUtils');

class wineSelect extends F60FormBase
{
	var $estate_id ;
	var $pageid;

    var $is_international=0;
    var $product_name="Product";
	function wineSelect()
	{
	 
              if (F60FormBase::getCached()) exit(0);
                
                $this->pageid=$_REQUEST["pageid"];
                
           
  
                $this->estate_id = $_REQUEST["estate_id"];
            //    $this->product_name="wine";
  			
  					$title = "Select product to update";
  			
                F60FormBase::F60FormBase('wineSelect', $title, 'selectwine.xml', 'selectwine.tpl');
                $this->addScript('resources/js/javascript.pageAction.js');

                //$this->addToPageStack();

                $form = & $this->getForm();
                $form->setFormAction('main.php?page_name=wineSelect');

  	          //  $this->elements["product_name"] = $product_name;  
  	            
				

            // $this->registerActionhandler(array("btnNext", array($this, processForm), "URL", "main.php?page_name=allocatewine"));
               $this->registerActionhandler(array("btnClose", array($this, processForm), "URL", "main.php"));
               $this->form->setButtonStyle('btnOK');
                $this->form->setInputStyle('input');
                $this->form->setLabelStyle('label');

                if ($_REQUEST["customer_id"]!="")
                {
                    $edtcmid =& $form->getField("customer_id");
                    $edtcmid ->setValue($_REQUEST["customer_id"]);
                }
                
                if ($_REQUEST["is_international"]==1)
                {
                    $this->is_international=1;
                    
                    $edtInter =& $form->getField("is_international");
                    $edtInter ->setValue("1");

                }
                    
                    
                $edtpageid =& $form->getField("pageid");
                $edtpageid ->setValue($_REQUEST["pageid"]);
                
                
                
                $this->attachBodyEvent('onLoad', 'checkWine();');
                
            
    }

	function display()
	{
            if (!$this->handlePost())
                $this->displayForm();
   }
   
   function displayForm()
   {
   // print $this->estate_id;
   
   		if($this->pageid>=45)
        {
            if($this->pageid==56)
                $this->displayForm4CSWSProducts();
            else
			  $this->displayForm4Beers();
        }
   		else
   		{
			if($this->is_international==1 )
			{
			 	$this->displayForm4Wines();
			}
			else
			{
				$this->displayForm4Ca();
			}
		}
		$this->product_name="product";
		$this->elements['product_name']=$this->product_name;
		$this->Template->assign("product_name", $this->product_name);	
	}
	
	function displayForm4Wines()
	{
	
	   $form = & $this->getForm();
		
		$edtWineids = & $form->getField("wine_ids");
		$edtWineid = & $form->getField("wine_id");
		
	//	if($this->pageid==46)
		//	$sql="select * from beer where $this->estate_id and deleted=0 order by beer_name";
	//	else
			$sql="select distinct w.* from wines w, wines_info wf where w.wine_id=wf.wine_id and w.estate_id = $this->estate_id and w.deleted=0 order by w.wine_name ";
		$result = & F60DbUtil::returnSQL($sql);
		
	//	$row = & $result->FetchRow();
		$i=0;
		$ids=0;
		
		
		$rows= count($result);
			
		if($rows!=0)
		{
			for($row=0;$row<$rows;$row++)
		   {
		    
				//$row=& $result->FetchRow();
				if($i==0)
		       {
		           $first_id =$result[$row]["wine_id"];
		          
		       }
		        $cmbWines = & $form->getField("wine_id");
		        $wine_name = $result[$row]["wine_name"];
		        $cmbwine_id = $result[$row]["wine_id"];
		       $wine_color= & F60DbUtil::getWineTyepByTypeId( $result[$row]["lkup_wine_color_type_id"]);
		        $vintage  = $result[$row]["vintage"];
		       
		       
		       $wine_id =$result[$row]["wine_id"];
		        $wine_name = $wine_name . " " . $vintage . " " . $wine_color;
		        $cmbWines ->addOption($cmbwine_id,$wine_name);
		        $ids=$ids.$wine_id."|";
		     
		        $i++;		
		      
			}
			$cmbWines ->removeOption(0);
			$edtIndexs = & $form->getField("indexs");
			$edtIndexs ->setValue ($i);
			$edtEstate = & $form->getField("estate_id");
			$edtEstate ->setValue ($this->estate_id);
			$edtIswine = & $form->getField("isWine");
			$edtIswine -> setValue("1");
			$edtWineids ->setValue($ids);
			
			
			$edtWineid->setFirstOption($first_id);
			
			Registry::set('first_id', $first_id);
		}
		else
		{
		 
			HtmlUtils::redirect("main.php?page_name=wineAdd&editMode=0&estate_id=$this->estate_id&is_international=$this->is_international");
		}
	  F60FormBase::display();
	 
	}
    function displayForm4CSWSProducts()
	{
	
	   $form = & $this->getForm();
		
		$edtWineids = & $form->getField("wine_ids");
		$edtWineid = & $form->getField("wine_id");
		

		$sql="select * from cs_products where estate_id = $this->estate_id and deleted=0 order by product_name";
		
		$result = & F60DbUtil::returnSQL($sql);
		
	//	$row = & $result->FetchRow();
		$i=0;
		$ids=0;
		
		
		$rows= count($result);
			
		if($rows!=0)
		{
			for($row=0;$row<$rows;$row++)
		   {
		    
				//$row=& $result->FetchRow();
				if($i==0)
		       {
		           $first_id =$result[$row]["cs_product_id"];
		          
		       }
		        $cmbWines = & $form->getField("wine_id");
		        $wine_name = $result[$row]["product_name"];
		        $cmbwine_id = $result[$row]["cs_product_id"];
		     //   $wine_color= & F60DbUtil::getBeerTyepByTypeId( $result[$row]["lkup_beer_type_id"]);
		       	       
		       
		        $wine_id =$result[$row]["cs_product_id"];
		        $wine_name = $wine_name ;
		        		        
		        $cmbWines ->addOption($cmbwine_id,$wine_name);
		        $ids=$ids.$wine_id."|";
		     
		        $i++;		
		      
			}
			$cmbWines ->removeOption(0);
			$edtIndexs = & $form->getField("indexs");
			$edtIndexs ->setValue ($i);
			$edtEstate = & $form->getField("estate_id");
			$edtEstate ->setValue ($this->estate_id);
			$edtIswine = & $form->getField("isWine");
			$edtIswine -> setValue("2");
			$edtWineids ->setValue($ids);
			
			
			$edtWineid->setFirstOption($first_id);
			
			Registry::set('first_id', $first_id);
		}
		else
		{
		  //first wine
			HtmlUtils::redirect("main.php?page_name=csProductAdd&editMode=0&estate_id=$this->estate_id&pageid=$this->pageid");
		}
	  F60FormBase::display();
	 
	}
    
	function displayForm4Beers()
	{
	
	   $form = & $this->getForm();
		
		$edtWineids = & $form->getField("wine_ids");
		$edtWineid = & $form->getField("wine_id");
		


		if($this->pageid ==45)//SAKE
		      $sql="select * from beers where estate_id = $this->estate_id and deleted=0  and lkup_beer_type_id=200 order by beer_name";
		
		if($this->pageid ==46)//beer
		      $sql="select * from beers where estate_id = $this->estate_id and deleted=0 and lkup_beer_type_id<200 order by beer_name ";
		
		if($this->pageid ==50)//SPIRITS		
				$sql="select * from beers where estate_id = $this->estate_id and deleted=0 and lkup_beer_type_id>200 order by beer_name ";		
		$result = & F60DbUtil::returnSQL($sql);
		
	//	$row = & $result->FetchRow();
		$i=0;
		$ids=0;
		
		
		$rows= count($result);
			
		if($rows!=0)
		{
			for($row=0;$row<$rows;$row++)
		   {
		    
				//$row=& $result->FetchRow();
				if($i==0)
		       {
		           $first_id =$result[$row]["beer_id"];
		          
		       }
		        $cmbWines = & $form->getField("wine_id");
		        $wine_name = $result[$row]["beer_name"];
		        $cmbwine_id = $result[$row]["beer_id"];
		        $wine_color= & F60DbUtil::getBeerTyepByTypeId( $result[$row]["lkup_beer_type_id"]);
		       	       
		       
		        $wine_id =$result[$row]["beer_id"];
		        $wine_name = $wine_name . " " . $wine_color;
		        		        
		        $cmbWines ->addOption($cmbwine_id,$wine_name);
		        $ids=$ids.$wine_id."|";
		     
		        $i++;		
		      
			}
			$cmbWines ->removeOption(0);
			$edtIndexs = & $form->getField("indexs");
			$edtIndexs ->setValue ($i);
			$edtEstate = & $form->getField("estate_id");
			$edtEstate ->setValue ($this->estate_id);
			$edtIswine = & $form->getField("isWine");
			$edtIswine -> setValue("1");
			$edtWineids ->setValue($ids);
			
			
			$edtWineid->setFirstOption($first_id);
			
			Registry::set('first_id', $first_id);
		}
		else
		{
			HtmlUtils::redirect("main.php?page_name=beerAdd&editMode=0&estate_id=$this->estate_id&pageid=$this->pageid");
		}
	  F60FormBase::display();
	 
	}
	function displayForm4Ca()
	{
	 
		$form = & $this->getForm();
		
		$edtWineids = & $form->getField("wine_ids");
		$edtWineid = & $form->getField("wine_id");
		
		$wines = new dalwinesCollection();
		$wines->add_filter("estate_id", "=", $this->estate_id);
		$wines->add_filter("and deleted =0 ");
		
        $wines->add_filter("and is_international <> 1 and cspc_code<>''");
		   
		$wines->add_filter("order by wine_name");
		$unwine_ids ="";
		$first_id ="";
		$i=0;
		if ($wines->load())
		{
		    $ids ="";
		    foreach($wines->items as $wine)
		    {
		       if($i==0)
		       {
		           $first_id =$wine->get_data("wine_id");
		       }
		       $cmbWines = & $form->getField("wine_id");
		       $wine_name = $wine->get_data("wine_name");
		       $wine_color= & F60DbUtil::getWineTyepByTypeId( $wine->get_data("lkup_wine_color_type_id"));
		       $vintage  = $wine->get_data("vintage");
		       $allocated = $wine->get_data("allocated");
		       $sku = $wine->get_data("cspc_code");
		       $cmbwine_id = $wine->get_data("wine_id");
		       $wine_id =$wine->get_data("wine_id");
		      
		       $wine_name =str_pad($sku,6,'0',STR_PAD_LEFT)." - ". $wine_name . " " . $vintage . " " . $wine_color;
		       $cmbWines ->addOption($cmbwine_id,$wine_name);
		       $ids=$ids.$wine_id."|";
		     
		       $i++;
		    }
	
			if($i==1)
			{
				if( $this->pageid!=18&&$this->pageid!=20)
				{
				    if($this->is_international)//edit wine
				    {
				        $sURL="main.php?page_name=wineAdd&editMode=1&is_international=1&wine_id=".$first_id."&estate_id=".$this->estate_id;
				    }
				    else
				    {
				        $sURL="main.php?page_name=wineAdd&editMode=1&wine_id=".$first_id."&estate_id=".$this->estate_id;
				    }
				    
				  //  HtmlUtils::redirect($sURL);
				}
			}
			
			$cmbWines ->removeOption(0);
			$edtIndexs = & $form->getField("indexs");
			$edtIndexs ->setValue ($i);
			$edtEstate = & $form->getField("estate_id");
			$edtEstate ->setValue ($this->estate_id);
			$edtIswine = & $form->getField("isWine");
			$edtIswine -> setValue("1");
			$edtWineids ->setValue($ids);
		}
		if($i==0)
		{
			HtmlUtils::redirect("main.php?page_name=wineAdd&editMode=0&estate_id=$this->estate_id&is_international=0");
		}
		$edtWineid->setFirstOption($first_id);
		
		Registry::set('first_id', $first_id);
	
	  F60FormBase::display();
	}

       
     function processForm()
     {
         return true; //add by wenling
     }

}

?>
