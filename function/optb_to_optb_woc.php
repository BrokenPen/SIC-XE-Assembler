<?php


	
	## convert operand table into operand table(with object code)
	## ## fix into respond column...
	function optb_to_optb_woc ($OPTB)
	{
		## initial the OPTB_WOC array	## initial operand table with object code talk
		$OPTB_WOC_LINE = array();		## a line in OPTB_WOC
		$OPTB_WOC = array();	## contain OPTB_WOC_LINE
		
		## store operand table value into operand table(with object code)
		## store $OPTB into $OPTB_WOC
		foreach ($OPTB as $_OPTB)	#	$value[0] = $_OP['LINE']..1= LABEL...2=.OPERAND,..3=. VALUE ..4=. COMMENT
		{
						## use "" instead of NULL....
						## NULL will make follow process error
						## suck as : Notice Underfined offset 1... 2 3 .4
						## 				Line		Loc Label  		OPERAND  	VALUE 	ObjectCode 	 Comment
						
			# hidden $_OPTB['COMMENT'] content...
			$_OPTB['COMMENT'] = "";
			
			## the reason to use trim because readTxt array... will contain white space.... moreover 
			## I do not know why....
			$OPTB_WOC_LINE = array("LINE" => trim($_OPTB['LINE']), "LOC" => "", "LABEL" =>	trim($_OPTB['LABEL']), "OPCODE" =>trim($_OPTB['OPCODE']), "OPERAND" =>trim($_OPTB['OPERAND']), "OBJECTCODE" => "","COMMENT" => trim($_OPTB['COMMENT']));
			//$OPTB_WOC_LINE = array("LINE" => $_OPTB['LINE'], "LOC" => "", "LABEL" =>	$_OPTB['LABEL'], "OPERAND" =>$_OPTB['OPERAND'], "VALUE" =>$_OPTB['VALUE'], "OBJECTCODE" => "","COMMENT" => $_OPTB['COMMENT']);
			
			array_push($OPTB_WOC,$OPTB_WOC_LINE);	## push $OPTB_WOC_LINE into $OPTB_WOC
			
		}
	
		return $OPTB_WOC;
	}

?>