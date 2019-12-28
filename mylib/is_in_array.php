<?php

## tested already...
## 2015/12/3

	function is_in_array($array, $key, $key_value)
	{
		$within_array = false;		
		foreach( $array as $k=>$v )
		{
			## array in_array
			if( is_array($v) == !false)
			{
				$within_array = is_in_array($v, $key, $key_value);
				if( $within_array == false )
				{
					break;
				}
			}	## END ## array in_array
			else ## in_array
			{	
				if( $v == $key_value && $k == $key )
				{
					$within_array = true;
					break;
				}
			}	## END ## in_array
		}	## END forearch
		return $within_array;
	}	## end function
?>