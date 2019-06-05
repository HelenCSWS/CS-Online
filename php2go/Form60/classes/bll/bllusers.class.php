<?php

import('Form60.dal.dalusers');

class bllusers extends dalusersCollection
{
	function bllusers()
	{
		parent::dalusersCollection();
	}
	
    function usernameExists($username, $user_id = NULL)
    {
        $users = & new dalusersCollection();
        $users->add_filter("username", "=", $username);
        if (isset($user_id))
        {
            $users->add_filter("AND ");
            $users->add_filter("user_id", "<>", $user_id);
        }
            $users->add_filter(" AND ");
            $users->add_filter("deleted", "=", "0");
        $users->load();
        return ($users->get_count() != 0);
    }
    
    
	
}
?>