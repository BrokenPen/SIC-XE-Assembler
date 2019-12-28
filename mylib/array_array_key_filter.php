<?php




function array_array_key_filter($array, $key, $inArray = true) {
	$returnArr = array();
	foreach($array as $k => $v) {
		if(is_array($array)) {
			$line = array_key_filter($v, $key, $inArray);
			array_push($returnArr, $line);
		}
		else
			return array_key_filter($array, $key, $inArray);
	}
	return $returnArr;
}



	function array_key_filter($array, $key, $inArray) {
		foreach($array as $k => $v) {
			if(!in_array($k, $key) == $inArray)
				unset($array[$k]);
		}
		return $array;
	}

/*
$array = array(array("sex" => 'boy', "feel" => "hate" ,"test" => "on99"),
		array("sex" => "girl", "feel" => "love" , "test" => "cute"));
//	$array = array("sex" => 'boy', "feel" => "hate");
$key = array("sex");
$keep = false;
$fuck = array_array_key_filter($array, $key, $keep);
print_r($fuck);
*/

?>