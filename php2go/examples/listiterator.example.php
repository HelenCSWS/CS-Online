<?php

	// $Header: /www/cvsroot/php2go/examples/listiterator.example.php,v 1.4 2004/03/08 22:22:27 mpont Exp $
	// $Revision: 1.4 $
	// $Date: 2004/03/08 22:22:27 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.util.AbstractList');
	
	echo '<B>PHP2Go Example</B> : php2go.util.AbstractList<BR><BR>';
	
	$list = new AbstractList(array(1,2,3,4,5,6,7,8,9,0));
	$iterator =& $list->iterator();
	echo 'From start to end<BR>';
	while ($iterator->hasNext()) {
		echo $iterator->next() . '<BR>';
	}
	echo 'From end to start<BR>';
	while ($iterator->hasPrevious()) {
		echo $iterator->previous() . '<BR>';
	}
	echo 'Move to index 4, to end<BR>';
	if ($iterator->moveToIndex(4)) {
		while ($iterator->hasNext()) {
			echo $iterator->next() . '<BR>';
		}
	}
	

?>