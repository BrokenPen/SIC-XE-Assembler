<?php

## @author : Brokenpen (me)
## last modified: 2015/12/03
## statistics: tested, basic need accomplish
## only return one array
## possible in feature return array

	function array_search_value($array, $key, $value, $return_key)
	{
		$return_value = false;
		foreach( $array as $v )
		{
			if( is_array($v) == !false)
			{
				$return_value = search_value($v, $key, $value, $return_key);
				if($return_value == false)
					continue;	// not found// ensure only..
				else	
					break;		// found...	// ensure only..
			}
			else
			{
				return search_value($array, $key, $value, $return_key);
			}
			
		}
		return $return_value;	// just ensure return.. always false..
	}

	function search_value($array, $key, $value, $return_key)
	{

		if($array[$key] == $value)
		{	
			return $array[$return_key];
		}
		else
			return false;
		
	}
	
	
	
/*
$arr = array();
$boy 	= array( "sex" => "male", 	"feel" => "hate", "name" => "boy");
$girl 	= array( "sex" => "female", "feel" => "love", "name" => "girl");
array_push($arr,$boy);
array_push($arr,$girl);

print_r($arr);

echo search_value($girl, "feel", "love","name");
//$key = array_search('love', $arr);
//var_dump($key);
echo search_array_value($arr, "sex", "male","name");
echo search_array_value($boy, "sex", "female","name");
*/
?>