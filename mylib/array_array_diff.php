<?php


function array_array_diff($array, $arr2, $key ="") {
	$diff = "";
	foreach($array as $k => $v) {
		if(is_array($v)) {
			$arrKey = array_keys($v);
		}
		else 
			$arrKey = array_keys($array);
		}
			
	}
}



?>