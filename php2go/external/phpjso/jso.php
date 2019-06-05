<?php

	require_once( 'phpJSO.class.php' );  
	$argv = $GLOBALS['argv'];
	$argc = $GLOBALS['argc'];	
	
	if ($argc < 2) {
		echo "ERR";
	} else {
		$file = $argv[1];
		$jso = new phpJSO();
		if (!file_exists($file)) {
			echo "ERR";
		} elseif (!is_writeable($file)) {
			echo "ERR";
		} else {	
			$jso =& new phpJSO();
			$jso->setSourceCodeByRef(readFileContents($file));
			$obfuscatedCode =& $jso->getObfuscatedCode();
			$jso->freeMemory();
			$f = fopen($file, 'wb');
			fwrite($f, $obfuscatedCode);
			fclose($f);
			echo "OK";
		}
	}
	
	function readFileContents($fileName) {
		ob_start();
		readfile($fileName);
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
	}
	
?>