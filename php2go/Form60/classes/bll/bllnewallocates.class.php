<?php

import('Form60.dal.dalwine_allocates');
import('Form60.dal.dalwines');


class bllnewallocates extends dalwine_allocates
{

    var $new_allocations;
    var $wines;

    function bllnewallocates()
    {
       // parent::dalwine_allocates();
        $this->new_allocations = & new dalwine_allocatesCollection();
         $this->wines = & new dalwinesCollection();
   }

    function getDataFromForm($form)
    {
        $edtNos = & $form->getField("wine_numbers");
        $wineNos = $edtNos->getValue();

        $dbFieldNames = array("unallocated","sample","buffer","breakage_corked","other");
        for ($i = 0; $i <= $wineNos - 1; $i++)
        {
            $idName = 'wine_id_' . $i;


             $wineid =$_REQUEST[$idName];



            $totname = "edt_total_".$wineid;
            $alctname ="allocated_".$wineid;
         if ($this->getValue4number($totname)!=0 )
            {

                 $new_allocation =& $this->new_allocations->add_new();
                  $new_allocation->set_data("wine_id", $wineid);
                 $wine= & $this->wines->add_new();
                $wine->set_data("wine_id", $wineid);
                $avaname = "edt_available_".$wineid;
                $bottles =$this->getValue4number($avaname) -$this->getValue4number($totname);
                $wine->set_data("total_bottles",$bottles);
                $wine->set_data("allocated",1);

                for ($j = 0; $j <= 3; $j++)
                {

                    $edtAllocateName ="edt_allocate_".$j."_".$wineid;
                    $valAllocate =$this->getValue4number($edtAllocateName) ;
                    $new_allocation->set_data($dbFieldNames[$j], $valAllocate);
                       
                     if ($_REQUEST[$alctname]==0)
                       $new_allocation ->is_new=true;
                    else
                    {
                         $new_allocation ->is_new=false;
                    }
                }
             }
        }

    }
    function getValue4number($name)
    {
        if ($_REQUEST[$name]==null or $_REQUEST[$name] =="")
            return "0";
        else
            return $_REQUEST[$name];
    }
    function saveNew2DB($editMode)
    {
       // print "begin save";
       $retVal =true;
        //save allocateion new
        foreach($this->new_allocations->items as $new_allocation)
        {
            $retVal=$new_allocation ->save();
        }
        
        //update wines total bottoles
        if($retVal)
        {
            foreach($this->wines->items as $wine)
            {
                  $wine ->is_new=false;
                  $retVal=$wine->save();
            }
        }
        return $retVal;
    }
    
     function allcateExsit($estate_id)
    {
        $allcates = & new dalwine_allocatesCollection();
        $allcates->table_name = "wines w, wine_allocations wa";

        $allcates->add_filter("wa.wine_id", "=", "w.wine_id");
        $allcates->add_filter("and w.estate_id", "=", $estate_id);
        $allcates->load();

         return ($allcates->get_count()== 0);
    }
}
?>





