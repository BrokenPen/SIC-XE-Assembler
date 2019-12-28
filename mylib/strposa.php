<?php
// scoures :
// http://stackoverflow.com/questions/6284553/using-an-array-as-needles-in-strpos?answertab=oldest#tab-top
function strposa($haystack, $needle, $offset=0, $getWhich = false, $getIndex = false) {
    if(!is_array($needle)) $needle = array($needle);
    $index = 0;
	foreach($needle as $query) {
        if(strpos($haystack, $query, $offset) !== false) {
			if($getWhich == true)
				return $query;
			else if($getIndex == true)
				return $index;
			else
				return true; // stop on first true result
		}
		++$index;
			
    }
    return false;
}
//$string = 'Whis string contains word "cheese" and "tea".';
//$string = 'Whissstringacontainsxwordwccheese#and tea".';
//$array  = array('burger', 'melon', 'cheese', 'milk');
//var_dump($array);
//var_dump(strposa($string, $array)); // will return true, since "cheese" has been found
//var_dump(strposa($string, $array, 0, true));
//var_dump(strposa($string, $array, 0, false, true));
?>