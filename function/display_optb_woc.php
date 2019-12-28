<?php


	## display operand table(with object code) in a well formatting 
	function display_optb_woc($OPTB_WOC="") {


		if(empty($OPTB_WOC) == !false)
		{
			echo "Error : display_optb_woc empty parameter\n";
			return false;
		}


		## display object code	## the column name
		echo str_pad("Line",10).str_pad("Loc",15).str_pad("Source statement",25).str_pad("ObjectCode",10);
		echo "\n";
		echo str_pad("Line",10).str_pad("Loc",10).str_pad("Label",10).str_pad("OPCODE",10).str_pad("OPERAND",10).str_pad("ObjectCode",10);
		echo "\n";

		if(max(array_column($OPTB_WOC, 'BLOCK')) == 0) {
			//print_r($OPTB_WOC);
			foreach ($OPTB_WOC as $_OPTB)
			{
				// fix bug-_-
				$column = array('LINE', 'LOC', 'LABEL', 'OPCODE', 'OPERAND', 'OBJECTCODE');
				foreach($column as $key) {
					if(!isset($_OPTB[$key])) $_OPTB[$key] = '';
				}
				if(empty($_OPTB_WOC['LOC'])) $_OPTB_WOC['BLOCK'] = '';

				# formatted ## enable this cuase printf method no fix
				echo str_pad($_OPTB['LINE'],10).str_pad($_OPTB['LOC'],10).str_pad($_OPTB['LABEL'],10).str_pad($_OPTB['OPCODE'],10).str_pad($_OPTB['OPERAND'],10).str_pad($_OPTB['OBJECTCODE'],10).str_pad($_OPTB['COMMENT'],10);
				# not formatted
				// echo $value[0]."|".$value[1]."|".$value[2];
				echo "\n";

				## use printf() to formatting the output
				## however I found out the new line r suck with a printf...
				## 		Line	Loc 	Label      Operator   		Content 	ObjectCode 	 Comment
				//
				//	$mash = "%10.10s %10.10s %-10.10s %-10.10s %-10.10s %-10.10s %-50.50s";
				//	printf($mash,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
				//	echo "\n";

			}	## end	## foreach ($OPTB_WOC as $value)
		}
		else {
			foreach($OPTB_WOC as $_OPTB) {
				// fix bug-_-
				$column = array('LINE', 'LOC', 'LABEL', 'OPCODE', 'OPERAND', 'OBJECTCODE');
				foreach($column as $key) {
					if(!isset($_OPTB[$key])) $_OPTB[$key] = '';
				}
				if(empty($_OPTB['LOC'])) { $_OPTB['BLOCK'] = ''; $_OPTB['OBJECTCODE'] = '';	}// fix bug= =

				if(empty($_OPTB['LOC'])) $_OPTB['BLOCK'] = '';
				echo str_pad($_OPTB['LINE'],10).str_pad($_OPTB['LOC'].' '.$_OPTB['BLOCK'],10).str_pad($_OPTB['LABEL'],10).str_pad($_OPTB['OPCODE'],10).str_pad($_OPTB['OPERAND'],10).str_pad($_OPTB['OBJECTCODE'],10).str_pad($_OPTB['COMMENT'],10);
				echo "\n";

			}
		}

	}
	
?>