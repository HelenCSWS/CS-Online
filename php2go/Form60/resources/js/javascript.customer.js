function changestoretype(store_id)
{
    if (store_id == 6 )
    {
      document.getElementById("lkup_store_priority_id").disabled=false;
    }
    else
       document.getElementById("lkup_store_priority_id").disabled=true;
}
