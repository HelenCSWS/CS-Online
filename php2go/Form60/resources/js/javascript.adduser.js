function changesectlevel(level_id)
{

    var blockId;
    blockId= document.getElementById("blockid").value;
    document.getElementById("level"+blockId).style.display="none";
    document.getElementById("blockid").value=level_id;
    document.getElementById("level"+level_id).style.display="block";
    
    
}


