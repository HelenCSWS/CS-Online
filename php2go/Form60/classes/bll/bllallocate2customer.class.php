<?php

import('Form60.dal.dalwine_allocates');
import('Form60.dal.dal_alct_wines_customers');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Date');

class bllallocate2customer extends dal_alct_wines_customers
{

    var $allocations;
    var $alllcate_customers;
    var $wines;

    var $grpisChanged;
    var $grpCms;

    //var $grpisInserts;


    function bllallocate2customer()
    {
       // parent::dalwine_allocates();
        $this->allocations = & new dalwine_allocatesCollection();
   }

   function getValue4number($name)
    {   // print $name;
        if ($_REQUEST[$name]==null or $_REQUEST[$name] =="")
        {
            //print here;
            return "0";
        }
        else
            return $_REQUEST[$name];
    }

    function getData4AlctWine($form)
    {
        // $edtNos = & $form->getField("wine_numbers");
        $wineNos =$this->getValue4number("wine_numbers");


        $dbFieldNames = array("unallocated","sample","buffer","breakage_corked");

        //prepair data for allocations table
        for ($i = 0; $i <= $wineNos - 1; $i++)
        {
            $idName = 'wine_id_' . $i;
            $wine_id =$_REQUEST[$idName];

            $totname = "edt_total_".$wine_id;
      //      $alctname ="allocated_".$wine_id;

            $this->grpisChanged[$i] =false;

            $allocation =& $this->allocations->add_new();
            $allocation->set_data("wine_id", $wine_id);
      

            for ($j = 0; $j <= 3; $j++)
           {
              $edtAllocateName ="edt_allocate_".$j."_".$wine_id;
              $valAllocate =$this->getValue4number($edtAllocateName) ;

               $allocation->set_data($dbFieldNames[$j], $valAllocate);
              /* $allocation->set_data("when_entered", F60Date::sqlDateTime());
               $allocation->set_data("user_id", $this->get_current_user_id());
             //  $allocation->set_data("modified_user_id", $this->get_current_user_id());
                */
          
              if( $j>0)
              {
                  $edtAllocateName_old ="hid_old_".$j."_".$wine_id;

             }
              else
              {
                  $edtAllocateName_old ="allocated_".$wine_id;
              }

                $valAllocate_old =$this->getValue4number($edtAllocateName_old) ;
               if ($valAllocate_old != $valAllocate)
                   {
                     //   print $i;
                        $this->grpisChanged[$i] =true;
                   }
             }
            $edtIsFirst = "is_first_".$wine_id;
          // print $_REQUEST[$edtIsFirst];
            if ($_REQUEST[$edtIsFirst]==1)
            {
                $allocation->is_new = false;
            }
            else
                $allocation->is_new = true;
        }

    }
    function getDataFromForm($form , $isCm)
    {

       $this->getData4AlctWine($form);

        if ( $isCm==1)
        {
           // print save1;
            //$edtCms = & $form->getField("customers");
            $nCms = $this->getValue4number("customers");

            $dbFieldNames = array("allocated");

            //prepair data for allocations table
            for ($i = 0; $i <= $nCms - 1; $i++)
            {
                $idName =  $i."_customerid";
                $cmid =$_REQUEST[$idName];

                //$edtNo = & $form->getField("wine_numbers");
                $wineNos = $this->getValue4number("wine_numbers");


    //            $totname = "edt_total_".$wine_id;
    //             $alctname ="allocated_".$wine_id;

                $this->grpCms[$i] =new grpCmAlcts();

                $this->alllcate_customers[$i] = & new dal_alct_wines_customersCollection();

               for ($j = 0; $j < $wineNos; $j++)
               {
                  //  print $j.":".$wineNos;

                  $edtWineid =$j."_wine_id";
                  $wine_id = $this->getValue4number($edtWineid);

                   $allocation2cm =& $this->alllcate_customers[$i]->add_new();

                   $allocation2cm->set_data("customer_id", $cmid);
                   $allocation2cm->set_data("wine_id", $wine_id);

                    //new allocates
                   $edtSold =$i."_".$wine_id."_sold"; //0_1_sold
                   $valSold =$this->getValue4number($edtSold);

                   $edtcmAlct =$i."_".$wine_id."_allocated"; //0_1_allocated
                   $valcmAlct =$this->getValue4number($edtcmAlct);

                    $valcmAlct = $valcmAlct +$valSold;
                   $allocation2cm->set_data("allocated", $valcmAlct);
                   $allocation2cm->set_data("sold", $valSold);

                   if ( $valcmAlct==0 )
                   {
                      $allocation2cm->is_deleted = true;
                   }
                   else
                   {
                       $allocation2cm->is_deleted = false;
                   }
              
          

   
                  //old allocates
                   $edtcmAlct_bk =$i."_".$wine_id."_db_alct"; //0_1_ld_alct
                   $valcmAlct_bk =$this->getValue4number($edtcmAlct_bk);

                  //  print "1bk: " .$valcmAlct_bk;
                  //  print "2: " .$valcmAlct;
						$currentAlct = $valcmAlct-$valSold;
						
                   if ($valcmAlct_bk != $currentAlct)
                   {
                        $this->grpCms[$i]->setChanged($j,true);
                   }
                   else
                   {
                        $this->grpCms[$i]->setChanged($j,false);
                   }

                    $sql ="select customer_wine_allocation_id from customer_wine_allocations where wine_id = ".$wine_id;
                    $sql = $sql." And customer_id = ".$cmid;

                    $result = &F60DbUtil :: runSQL($sql);

                     if(!$result->EOF)
                     {
                          $row = & $result->FetchRow();
                          $allocation2cm->is_new = false;
                          $allocation2cm->set_data("customer_wine_allocation_id", $row["customer_wine_allocation_id"]);
                    }
                   $edtIsFirst= $i."_".$wine_id."_is_first";
                     if ($_REQUEST[$edtIsFirst]!="")
                     {
                        $allocation2cm->is_new = false;
                          $allocation2cm->set_data("customer_wine_allocation_id", $_REQUEST[$edtIsFirst]);
                     }
                    else
                        $allocation2cm->is_new = true;
                }
            }
        }//end isCM
    }
    function save2DB($isCm,$cm_id=NULL)
    {
//   return false;
        $retVal =true;
        $i=0;
    

        if($isCm==1)
        {

            //save cm allocations
            $cms = sizeof($this->grpCms);

            for ($i=0;$i<$cms;$i++)
            {
              // print $i;
              $j =0;
               foreach($this->alllcate_customers[$i]->items as $allocation2cm)
               {
                 
                   if ($this->grpCms[$i]->isChanged($j))
                    {
                     
                     if(!$allocation2cm->is_new)
                        {

                            $sql = "INSERT INTO customer_wine_allocation_history
                                (
                                customer_wine_allocation_id,
                                user_id,
                                modified_user_id,
                                created_user_id,
                                when_entered,
                                allocated,
                                sold
                                ) ";


                            $sql = $sql. " Select 
                                           customer_wine_allocations.customer_wine_allocation_id,
                                           customer_wine_allocations.created_user_id,
                                           customer_wine_allocations.modified_user_id,
                                           customer_wine_allocations.created_user_id,
                                           customer_wine_allocations.when_entered,
                                           customer_wine_allocations.allocated,
                                           customer_wine_allocations.sold";
                            $sql = $sql." From customer_wine_allocations where wine_id = ".$allocation2cm->get_data("wine_id");
                            $sql = $sql." And customer_id = ".$allocation2cm->get_data("customer_id");


                            $result = &F60DbUtil :: runSQL($sql);

                        }
                        $retVal=$allocation2cm ->save();
                        
                    }
                    $j++;
               }

            }
        }//if($isCm)

        if ($retVal )
        {
            $i=0;
            //save allocation
            foreach($this->allocations->items as $allocation)
            {

                if ($this->grpisChanged[$i])
                {
                   if(!$allocation->is_new)
                    {
                        //insert history table
                        $sql = "INSERT INTO wine_allocation_history
                                (user_id,
                                modified_user_id,
                                created_user_id,
                                wine_id,
                                when_entered,
                                unallocated,
                                sample,
                                buffer,
                                breakage_corked) ";
                        $sql = $sql. " Select wine_allocations.created_user_id,
                                            wine_allocations.modified_user_id,
                                            wine_allocations.created_user_id,
                                              wine_allocations.wine_id,
                                              wine_allocations.when_entered,
                                              wine_allocations.unallocated,
                                              wine_allocations.sample,
                                              wine_allocations.buffer,
                                              wine_allocations.breakage_corked ";
                        $sql = $sql." From wine_allocations where wine_allocations.wine_id = ".$allocation->get_data("wine_id");
                        $result = &F60DbUtil :: runSQL($sql);
                    }
                    $retVal= $allocation->save();
                        
                }
                $i++;
            }
        }
        
        if ($cm_id!=NULL)
        {
         
            $sURL ='main.php?page_name=customerAdd&id='.$_REQUEST['customer_id'].'&estate_id_order='.$_REQUEST['estate_id_order'];
            HtmlUtils::redirect($sURL);
        }
       return $retVal;
    }


}

class grpCmAlcts extends php2go
{
    var $cmAlcts;
    function grpCmAlcts()
    {

    }
    function setChanged($col_index, $isChanged)
    {
        $this->cmAlcts[$col_index]=$isChanged;
    }

    function isChanged($col_index)
    {
        return $this->cmAlcts[$col_index];
    }
    
  /*  function setDelete($col_index, $isDelete)
    {
        $this->cmAlcts[$col_index]=$isChanged;
    }
     function isDelete($col_index)
    {
        return $this->cmAlcts[$col_index];
    }*/
    
}
?>
