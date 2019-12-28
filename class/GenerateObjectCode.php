<?php
/**********************************************************
 *@read this												  *
 * http://stackoverflow.com/questions/29440934/confusion-on-generating-object-code-in-sic-assemblers-output

Convert them to binary and back if you have trouble counting bits. 1039=001 0000 0011 1001 (this is 15 bits). Prepend a 1 to indicate the indirect mode, so you have 1001 0000 0011 1001 which is 9039 in hex. ? Jester Apr 4 at 16:47

 **********************************************************/
/*********************************************************
 * @ what need to do implement the
 *  		SIC/XE Instruction Set and Addressing Mode
 * 		in p461											*
 *********************************************************/

class GenerateObjectCode
{

	private $OPTB_WOC = array();
	private $computerType = "";
	# OPERAND table copy from global %OPTAB_
	private $OPTAB_;
	# symbol table from recording label, locctr during the pass1 and pass2
	private $SYMTAB = array();
	private $OPTB_WOC_afterPassOne = array();
	private $OPTB_WOC_afterPassTwo = array();
	#error flag..
	private $errorFlag;
	## doPassOne error full record
	private $errorLogPassOne = "";    ## record error from doPassOne
	private $errorLogPassTwo = "";    ## record error from doPassTwo
	private $startingAdd = 0;        ## starting address
	private $endingAddArr = array();	## endingAddArr .. Arr use for EXTREF case==
	private $programLength = 0;        ## program length
	private $literalsArr = array();			## some how need to fix a unknow bug.. but however a magic auto fix it..
	private $EXTDEF = array();		## store EXTDEF use for search value= =... // remind search value not fix this condition yet
	private $EXTREF = array();
	#register
	private $bReg = 'no_value'; // BASE register

	function __construct($OPTB_WOC, $computerType ='SIC/XE'){

		$this->name = "GenerateObjectCode";
		echo "In ". $this->name." constructor\n";
		global $OPTAB;
		$this->OPTAB_ = $OPTAB;            ## copy symbol table from global $OPTAB
		$this->OPTB_WOC = $OPTB_WOC;    ## fetch operand table ...
		if(!in_array($computerType, array("SIC", "SIC/XE")))
			$computerType ="SIC/XE";

		$this->computerType = $computerType;
		echo "Computer Type : " . $computerType . "\n";

		$this->doPassOne();
	}

	public function setObjectCode($OPTB_WOC) {
		$this->OPTB_WOC = $OPTB_WOC;
	}

	public function onlyDoPassTwo() {
		return $this->doPassTwo();
	}

	public function getAfterPassTwo() {
		return $this->OPTB_WOC_afterPassTwo;
	}

	public function getObjectCode() {
		return $this->OPTB_WOC;
	}

	public function getSYMTAB() {
		return $this->SYMTAB;
//		/return $this->SYMTAB;
	}

	private function formatNumber($value = 0x0) {
		## use sprintf return a formatted string
		$formatted_value = sprintf("%04.X", $value);
		return $formatted_value;
	}

	private function formatOPCODEVAL_STORAGE($value = 0) {
		## for OPCODE = 'BYEE' or 'WORD
		## use sprintf return a formatted string
		$formatted_value = sprintf("%06d", $value);
		return $formatted_value;
	}

	private function formatOPCODEVAL_STORAGE_BYTE($value = 0) {
		## for OPCODE = 'BYEE' or 'WORD
		$formatted_value = "";
		if (substr($value, 0, 1) == 'C') {
			## ord get ascii code but it is decimal value...
			$char = get_string_between($value, "'", "'");
			## beware : coz my php skill too poor... so turn out this wreak
			for ($index = 0; $index < strlen($char); $index++) {
				$formatted_value .= sprintf("%02s", dechex(ord(substr($char, $index, $index + 1))));
			}
			$formatted_value = strtoupper($formatted_value);
		} else
			if (substr($value, 0, 1) == 'X') {
				$formatted_value = get_string_between($value, "'", "'");
			}

		## use sprintf return a formatted string
		##$formatted_value = sprintf("%06d", $value);
		return $formatted_value;
	}

	private function toHex($value) {
		$value = (string)$value;
		$value = "0x" . $value;
		return $value;
	}

	public function getErrorLogPassOne() {
		return $this->errorLogPassOne;
	}

	/**
	 * @return array
     */
	private function doPassOne() {
		echo "doPassOne()\n";
		$LOCCTR = 0;		## LOCCTR..
		$lineCount = 0;		## for counting first foreach 

		//$startingAdd = 0;	## starting address	## define in class now..
		$errorFlag = false;	## initial errorFlag as false -> no error found
		// user defined symbol use a another function to do it
		$OPTB_SKIP = array(".","BASE","END","EXTDEF","EXTREF");	## skip line with these label..
		$errorMsg = "";	## store error message....

		$literalsArr = array();

		//$_OPTB_WOC['BLOCK'] = 0;
		$block = 0;
		$SECTION = 0;
		$blockLocArr = array( 0 => 0);
		$blockArr = array('' => 0);	// then array_push new block...


		foreach($this->OPTB_WOC as $_OPTB_WOC)
		{
			$lineCount++;
			## have given a start position...
			$_OPTB_WOC['BLOCK'] = $block;
			$_OPTB_WOC['SECTION'] = $SECTION;
			if( $_OPTB_WOC['LINE'] == 5 ) {

				$_OPTB_WOC['BLOCK'] = $blockArr[''];	//  BLOCK key to 0 // mean using BLOCK 0

				if($_OPTB_WOC['OPCODE'] == 'START') {
					//$LOCCTR += $this->toHex($_OPTB_WOC['OPERAND']);
					$blockLocArr[$_OPTB_WOC['BLOCK']] += $this->toHex($_OPTB_WOC['OPERAND']);
					//$LOCCTR = $blockLocArr[$_OPTB_WOC['BLOCK']];
					// $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
					$_OPTB_WOC['LOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
					$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
				}
				else {
					$blockLocArr[$_OPTB_WOC['BLOCK']] = $blockLocArr[''];
					$_OPTB_WOC['LOC'] = $this->formatNumber(0x0);
					$_OPTB_WOC['ELOC'] = $this->formatNumber(0x0);
				}
				$block = $_OPTB_WOC['BLOCK'];
				array_push($this->OPTB_WOC_afterPassOne,$_OPTB_WOC);
				continue;	## read next line
			}
			else if( empty($_OPTB_WOC['LABEL']) && empty($_OPTB_WOC['OPCODE']) && empty($_OPTB_WOC['OPERAND'])) {
				array_push($this->OPTB_WOC_afterPassOne, $_OPTB_WOC);
				continue; // read next line
			}

			if($_OPTB_WOC['OPCODE'] == 'CSECT') {
				//$blockLocArr[$_OPTB_WOC['BLOCK']] = 0;
				$blockLocArr[$_OPTB_WOC['BLOCK']] = hexdec(array_search_value($this->OPTB_WOC, 'OPCODE', 'START' , 'OPERAND'));
				$_OPTB_WOC['LOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
				++$SECTION;
				$_OPTB_WOC['SECTION'] = $SECTION;
				array_push($this->OPTB_WOC_afterPassOne,$_OPTB_WOC);
				continue;	## read next line
			}


			// new block declared
			if($_OPTB_WOC['OPCODE'] == 'USE' && !empty($_OPTB_WOC['OPERAND']) && !array_key_exists ($_OPTB_WOC['OPERAND'], $blockArr)) {
				$_OPTB_WOC['BLOCK'] = max($blockArr) + 1;
				$blockArr[$_OPTB_WOC['OPERAND']] =  $_OPTB_WOC['BLOCK'];
				array_push($blockArr, $blockArr[$_OPTB_WOC['OPERAND']] );
				$blockLocArr[$_OPTB_WOC['BLOCK']] = 0x0;
				array_push($blockLocArr, $blockLocArr[$_OPTB_WOC['BLOCK']]);
			}

			// block found in record
			else if($_OPTB_WOC['OPCODE'] == 'USE' && array_key_exists ($_OPTB_WOC['OPERAND'], $blockArr)) {
				$_OPTB_WOC['BLOCK'] = $blockArr[$_OPTB_WOC['OPERAND']];
			}

			if($_OPTB_WOC['OPCODE'] == 'USE') {
				$block = 	$_OPTB_WOC['BLOCK'];
				$_OPTB_WOC['LOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
				$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
				array_push($this->OPTB_WOC_afterPassOne, $_OPTB_WOC);
				continue;
			}

			if($_OPTB_WOC['OPCODE'] == 'LTORG') {
				array_push($this->OPTB_WOC_afterPassOne, $_OPTB_WOC);
				if(count($literalsArr >= 1)) {
					$literalsArrTmp = $literalsArr;
					foreach($literalsArrTmp as $literals) {
						var_dump($literals);

						$_OPTB_WOC['LINE'] = '';
						$_OPTB_WOC['LOC'] =  $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
						$_OPTB_WOC['LABEL'] = $literals['LABEL'];
						$_OPTB_WOC['OPCODE'] = $literals['OPCODE'];

						if($literals['OPCODE']{1} == 'X') {
							$_OPTB_WOC['SIZE'] = strlen(get_string_between($_OPTB_WOC['OPCODE'], "'", "'")) / 2 ;
						}
						else if($literals['OPCODE']{1} == 'C') {
							$_OPTB_WOC['SIZE'] = strlen(get_string_between($_OPTB_WOC['OPCODE'], "'", "'")) ;
						}
						$_OPTB_WOC['ELOC'] = $blockLocArr[$_OPTB_WOC['BLOCK']] + $_OPTB_WOC['SIZE'];

						array_push($this->OPTB_WOC_afterPassOne, $_OPTB_WOC);
						array_push($this->SYMTAB,  array('LABEL' => $_OPTB_WOC['OPCODE'], 'LOC' => $_OPTB_WOC['LOC'] , $_OPTB_WOC['SECTION']));
						array_shift($literalsArr);

						$blockLocArr[$_OPTB_WOC['BLOCK']] += $_OPTB_WOC['SIZE'];	// new LOCCTR

					}
				}
				continue;
			}

			/*
			// something do with LTORG - -.... need a loop inside
			else if($_OPTB_WOC['OPCODE'] == 'LTORG') {
				array_push($this->OPTB_WOC_afterPassOne,$_OPTB_WOC);
				if(count($literalsArr) >= 1)
					foreach($literalsArr as $literals) {
						echo "on99999999999999999999999999999";
						print_r($literals);
						$_OPTB_WOC['LINE'] = '';
						$_OPTB_WOC['LOC'] =  $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
						$_OPTB_WOC['LABEL'] = $literals['LABEL'];
						$_OPTB_WOC['OPCODE'] = $literals['OPCODE'];
						array_push($this->OPTB_WOC_afterPassOne, $_OPTB_WOC);
						// add literals to SMYTAB too- -.. also added at the end
						array_push($this->SYMTAB,  array('LABEL' => $_OPTB_WOC['OPCODE'], 'LOC' => $_OPTB_WOC['LOC']));
						//array_push($this->OPTB_WOC_afterPassOne, array("LOC" => $this->formatNumber($LOCCTR),
						//		"LABEL"	 => 	 $literals['LABEL'] ,
						//		"OPCODE" =>		$literals['OPCODE']));
						array_shift($literalsArr);
						//reset($literalsArr);
						//$key = key($literalsArr);
						//unset($literalsArr[$key]);
						$blockLocArr[$_OPTB_WOC['BLOCK']] += 3;
						$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
					}
				continue;
			}
			*/

			if($_OPTB_WOC['OPCODE'] == 'EQU') {
				if($_OPTB_WOC['OPERAND'] == "*") {
					$_OPTB_WOC['LOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
				}
				else {
					//$operand = preg_split('/[^A-Z]/', $_OPTB_WOC['OPERAND']);
					//$operation = preg_replace("([^+-/*])", '', $_OPTB_WOC['OPERAND']);
					$operation = preg_replace("/[^+\/*-]/", '', $_OPTB_WOC['OPERAND']);
					$operand = explode($operation, $_OPTB_WOC['OPERAND']);
					//$operation =  $_OPTB_WOC['OPERAND']{strlen($operand[0])};
					//print_r($operand);

					$locOne = array_search_value($this->OPTB_WOC_afterPassOne, 'LABEL', $operand[0], 'LOC');
					$locTwo = array_search_value($this->OPTB_WOC_afterPassOne, 'LABEL', $operand[1], 'LOC');
					//echo $locOne . " | " . $locTwo . "\n";
					$locOne = hexdec($locOne);
					$locTwo = hexdec($locTwo);


					switch($operation) {
						case '+':
							$loc = $locOne + $locTwo;
							break;
						case '-':
							$loc = $locOne - $locTwo;
							break;
						case '*':
							$loc = $locOne * $locTwo;
							break;
						case '/' :
							$loc = $locOne / $locTwo;
							break;
						default :
							$loc = "0000";
							echo "Wrong operation symbol in Symbol-Definint Statements\n";
							break;
					}
					//echo "locccccccccccccccc  operation : " .  $this->formatNumber($loc)  . " | " . $operation ."\n";
					//exit;
					$_OPTB_WOC['LOC'] =  $this->formatNumber($loc);
					$_OPTB_WOC['BLOCK'] = '';
				}

				// don't ask why.. fix unkonwn bug
				$tmpArr = array('LOC' => $_OPTB_WOC['LOC'], 'LABEL' => $_OPTB_WOC['LABEL'],
						'OPERAND' => $_OPTB_WOC['OPERAND'], 'OPCODE' => "", "OBJECTCODE" => "", 'SECTION' => $_OPTB_WOC['SECTION'], 'LINE' => '', 'COMMENT' =>'');
				array_push($this->SYMTAB, $tmpArr);
				if(!isset($_OPTB_WOC['LINE'])) $_OPTB_WOC['LINE'] = '';
				if(!isset($_OPTB_WOC['LOC'])) $_OPTB_WOC['LOC'] = '';
				array_push($this->SYMTAB, $tmpArr);
				array_push($this->OPTB_WOC_afterPassOne, $_OPTB_WOC);
				//array_push($this->OPTB_WOC_afterPassOne, $tmpArr);
				//array_push($this->SYMTAB, $_OPTB_WOC);
				//array_push($this->OPTB_WOC_afterPAssOne, $_OPTB_WOC);
				continue;
			}	// end EQU




			if($_OPTB_WOC['OPCODE'] != 'END')
			{
				//echo "while(_OPTB_WOC['OPCODE'] != 'END')\n";
				## if this is not a comment line then
				## Fig 2.5 have BASE....

				if(in_array($_OPTB_WOC['OPCODE'],$OPTB_SKIP))
				{
					array_push($this->OPTB_WOC_afterPassOne,$_OPTB_WOC);
					continue;	## read next line
				}

				if($_OPTB_WOC['LABEL'] != '.')
				{
					//echo "if(_OPTB_WOC['LABEL'] != '.')\n";
					## if there is a symbol in the LABEL field then
					if(empty($_OPTB_WOC['LABEL']) == false)
					{
						//echo "f(empty(_OPTB_WOC['LABEL']) == false)\n";
						## serch SYMTAB for LABEL
						if(is_in_array($this->SYMTAB, "LABEL", $_OPTB_WOC['LABEL']))
						{
							//echo "if(is_in_array( this->SYMTAB, \"LABEL\",  OPTB_WOC['LABEL']))\n";
							## set error flag(duplicate symbol)

							$errorMsg = str_pad("Error at line ",15). $_OPTB_WOC['LINE'] . " : " . $_OPTB_WOC['LABEL'] . " duplicate symbol\n";
							echo $errorMsg;
							$this->errorLogPassOne .= $errorMsg;
							$this->errorFlag = true;
							// return false;	## return false to stop the program...
						}	## found END
						## not found
						else
						{
							//echo "if(is_in_array( this->SYMTAB, \"LABEL\",  OPTB_WOC['LABEL'])) else\n";
							## IDK--> insert (LABEL, LOCCTR) into SYMTAB
							$_OPTB_WOC['LOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
							array_push($this->SYMTAB,array("LABEL" => $_OPTB_WOC['LABEL'],"LOC" => $_OPTB_WOC['LOC'] , 'SECTION' => $_OPTB_WOC['SECTION']));
						}	## not found END


					}	## if there is a symbol in the LABEL field  END
					else {
						## no label found
						//echo "	## no label found\n";
						$_OPTB_WOC['LOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
					}

					$errorFlag == true;
					## search OPTAB for OPCODE
					foreach($this->OPTAB_ as $value) {
						//echo "foreach(this->OPTAB_ as value)\n";
						## WORD , RESW , RESB, BYTE case
						if($_OPTB_WOC['OPCODE'] == 'WORD') {
							//$_OPTB_WOC['LOC'] = $this->formatNumber($LOCCTR);
							$blockLocArr[$_OPTB_WOC['BLOCK']] += 3;
							$_OPTB_WOC['SIZE'] = 3;
							$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
							$errorFlag = false;
							break;	## break foreach;
						}
						else if($_OPTB_WOC['OPCODE'] == 'RESW') {
							if($_OPTB_WOC['OPERAND'] == 0) $_OPTB_WOC_fix['OPERAND'] = 1 ;


							else
								$_OPTB_WOC_fix['OPERAND'] =  $_OPTB_WOC['OPERAND'] ;


							$blockLocArr[$_OPTB_WOC['BLOCK']] += 3*$_OPTB_WOC_fix['OPERAND'];
							$_OPTB_WOC['SIZE'] = 3*$_OPTB_WOC_fix['OPERAND'];
							$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
							$errorFlag = false;
							break;	## break foreach;
						}
						else if($_OPTB_WOC['OPCODE'] == 'RESB') {
							if($_OPTB_WOC['OPERAND'] == 0) $_OPTB_WOC['OPERAND'] =1 ;
							$blockLocArr[$_OPTB_WOC['BLOCK']] += $_OPTB_WOC['OPERAND'];
							$_OPTB_WOC['SIZE'] = $_OPTB_WOC['OPERAND'];
							$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
							$errorFlag = false;
							break;	## break foreach;
						}
						else if($_OPTB_WOC['OPCODE'] == 'BYTE') {
							## find length of constant in byes and length to LOCCTR... no idea
							//$_OPTB_WOC['LOC'] = $this->formatNumber($LOCCTR);
							if(substr($_OPTB_WOC['OPERAND'],0,1) == 'X') {
								$blockLocArr[$_OPTB_WOC['BLOCK']] += strlen(get_string_between($_OPTB_WOC['OPERAND'], "'", "'")) / 2 ;
								$_OPTB_WOC['SIZE'] = strlen(get_string_between($_OPTB_WOC['OPERAND'], "'", "'")) / 2 ;
								$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
								$errorFlag = false;
								break;	## break foreach;
							}
							if(substr($_OPTB_WOC['OPERAND'],0,1) == 'C') {
								$blockLocArr[$_OPTB_WOC['BLOCK']] +=  strlen(get_string_between($_OPTB_WOC['OPERAND'], "'", "'")) ;
								$_OPTB_WOC['SIZE'] = strlen(get_string_between($_OPTB_WOC['OPERAND'], "'", "'")) ;
								$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
								$errorFlag = false;
								break;	## break foreach;
							}
							else {
								$mask = "Error at line %s : %s wrong syntax\n";
								#$errorMsg = str_pad("Error at line ",15). $_OPTB_WOC['LINE'] . " : " . $_OPTB_WOC['OPERAND'] . " wrong syntax\n";
								#echo $errorMsg;
								echo "sprintf \n";
								$error = sprintf($mask,$_OPTB_WOC['LINE'],$_OPTB_WOC['OPERAND']);
								echo $error;
								$this->errorLogPassOne .= $errorMsg;
								//$errorFlag = true;
								//return false;
								break;	## break foreach;
							}
						}


						## if it is extended format add 4
						else if( (substr($_OPTB_WOC['OPCODE'],0,1) == '+') && ( substr($_OPTB_WOC['OPCODE'], 1) == $value['OPCODE'])  &&  ($value['FORMAT'] == "3/4") ) {
							$blockLocArr[$_OPTB_WOC['BLOCK']] += 4;
							$_OPTB_WOC['SIZE'] = 4;
							$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
							$errorFlag = false;
							break;	## break foreach;
						}
						else if( ($_OPTB_WOC['OPCODE'] == $value['OPCODE']) && ($value['FORMAT'] == "3/4") ) {
							if(substr($_OPTB_WOC['OPERAND'],0,1) == '=') {
								//$literalLength =  strlen(get_string_between($_OPTB_WOC['OPERAND'], "'", "'"));
								$literalTmp =  array('SIZE' => '3', 'LABEL' => '*', 'OPCODE' => $_OPTB_WOC['OPERAND']);
								if(!in_array($literalTmp, $literalsArr)) {
									array_push($literalsArr,$literalTmp);
									array_push($this->literalsArr,$literalTmp);
								}
								//print_r($literalsArr);
							}
							$blockLocArr[$_OPTB_WOC['BLOCK']] += 3;
							$_OPTB_WOC['SIZE'] = 3;
							$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
							$errorFlag = false;
							break;	## break foreach;
						}
						else if( ($_OPTB_WOC['OPCODE'] == $value['OPCODE']) && ($value['FORMAT'] == "2") ) {
							$_OPTB_WOC['SIZE'] = 2;
							$blockLocArr[$_OPTB_WOC['BLOCK']] += 2;
							$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
							$errorFlag = false;
							break;	## break foreach;
						}
						else if( ($_OPTB_WOC['OPCODE'] == $value['OPCODE']) && ($value['FORMAT'] == "1") ) {
							$_OPTB_WOC['SIZE'] = 1;
							$blockLocArr[$_OPTB_WOC['BLOCK']] += 1;
							$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
							$errorFlag = false;
							break;	## break foreach;
						}
					} ## END ## search OPTAB for OPCODE


					if($errorFlag == !false) {	//echo 	"if(errorFlag == !false)";
						$errorMsg = str_pad("Error at line ",15) . $_OPTB_WOC['LINE'] . " : " .$_OPTB_WOC['OPCODE'] . " invalid operation code\n";
						echo $errorMsg;
						$this->errorLogPassOne .= $errorMsg;
						$this->errorFlag = true;
						//return false;	## return false to stop the program...
					}
				}	## if this is not a comment line END
				## write line to intermediate file
				## read next input line

			}	## while END
			else { ## if OPCODE == END
				array_push($this->OPTB_WOC_afterPassOne,$_OPTB_WOC);	## if(LABEL != 'END') also do array_push..
				break;	## read next line
			}
			//$LOCCTR -= 3;	## subtract the precondition from last run..
			## write last line to intermediate file
			## save( LOCCTR - starting address) as program length
			//


			array_push($this->OPTB_WOC_afterPassOne,$_OPTB_WOC);	## array_push result..
			//print_r($this->OPTB_WOC);
		}

		if(count($literalsArr) >= 1) {
			foreach($literalsArr as $literals) {
				//print_r($literals);
				array_push($this->SYMTAB,  array('LABEL' => $literals['OPCODE'], 'LOC' => $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']] , $_OPTB_WOC['SECTION'])));
				array_push($this->OPTB_WOC_afterPassOne, array(
						"LOC" 			=> 	$this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]),

						'SECTION'		=>	 $_OPTB_WOC['SECTION'],
						"LABEL"	 		=> 	 $literals['LABEL'] ,
						"OPCODE"	 	=>	$literals['OPCODE'],
						'OPERAND' 		=> '',
						"OBJECTCODE" 	=> "",
						'LINE' 			=> '',
						'COMMENT' 		=>''));
				array_pop($literalsArr);
				$blockLocArr[$_OPTB_WOC['BLOCK']] += 3;
				$_OPTB_WOC['ELOC'] = $this->formatNumber($blockLocArr[$_OPTB_WOC['BLOCK']]);
			}
		}

		print_r($blockLocArr);
		print_r($blockArr);


		$this->OPTB_WOC = $this->OPTB_WOC_afterPassOne;	## update OPTB_WOC with LOC info
		$this->programLength =  (9999 -  $this->startingAdd);
// 999 = $blockLocArr[$_OPTB_WOC['BLOCK']]

		echo "Program length : ".$this->formatNumber($this->programLength) ."\n";

		return $this->doPassTwo();
	}

	public function getAfterPassOne() {
		return $this->OPTB_WOC_afterPassOne;
	}

	/**
	 * @param int $loc
	 */
	private function find_PC($_OPTB_WOC) {
		$loc = $_OPTB_WOC['LOC'] ;
		//$block = $_OPTB_WOC['BLOCK'];
		$section = $_OPTB_WOC['SECTION'];
		/*
		foreach (array_column($this->OPTB_WOC, 'LOC') as $value) {
			//echo $value;
			if ($value > $loc) {
				return $value;
			}
		}
		*/
		foreach ($this->OPTB_WOC as $_OPTB_WOC) {
			// fix bug- -... as you see
			if(!isset($_OPTB_WOC['SECTION']) || empty($_OPTB_WOC['SECTION']))
				$_OPTB_WOC['SECTION'] = '0';

			if($_OPTB_WOC['LOC'] > $loc && $_OPTB_WOC['SECTION'] == $section) {
				return $_OPTB_WOC['LOC'];
			}
		}
		return false;
	}

	private function find_LOC($_OPTB_WOC, $fixTargetLabel ='no_value'){
		//array_search_value($this->SYMTAB, "LABEL", $addTa, "LOC");	// I use my array_search_value as template to modify this function-  -...
		if(!isset($_OPTB_WOC['OPERAND']) || empty($_OPTB_WOC['OPERAND'])) {
			$_OPTB_WOC['OPERAND'] = 'no_vlaue';
		}
		$targetLabel = $_OPTB_WOC['OPERAND'];
		if($fixTargetLabel != 'no_value')
			$targetLabel = $fixTargetLabel;
		if(!isset($_OPTB_WOC['SECTION']) || empty($_OPTB_WOC['SECTION']))
			$_OPTB_WOC['SECTION'] = '0';
		$targetSection = $_OPTB_WOC['SECTION'];


		$return_value = false;
		foreach($this->SYMTAB as $v ) {
			if( is_array($v) == !false) {
				$return_value = $this->in_find_LOC($v, $targetLabel, $targetSection);
				if($return_value == false)
					continue;	// not found// ensure only..
				else
					break;		// found...	// ensure only..
			}
			else {
				return $this->in_find_LOC($v, $targetLabel, $targetSection);
			}
		}
		return $return_value;	// just ensure return.. always false..
	}

	private function in_find_LOC($_OPTB_WOC, $targetLabel , $targetSection){
		// fix bug
		if(!isset($_OPTB_WOC['LABEL']) || empty($_OPTB_WOC['LABEL']))
			$_OPTB_WOC['LABEL'] = 'no_value';
		if(!isset($_OPTB_WOC['SECTION']) || empty($_OPTB_WOC['SECTION']))
			$_OPTB_WOC['SECTION'] = '0';
		if($_OPTB_WOC['LABEL'] == $targetLabel && $_OPTB_WOC['SECTION'] == $targetSection) {
			return $_OPTB_WOC['LOC'];
		} // something wrong.. // fix tmr
		else if(isset($this->EXTDEF[$targetSection]) && !empty($this->EXTDEF[$targetSection])) {
			if($_OPTB_WOC['LABEL'] == $targetLabel && in_array($targetLabel, $this->EXTDEF[$targetSection])) {
				return $_OPTB_WOC['LOC'];
			} // something wrong.. // fix tmr
		}
		else if(isset($this->EXTREF[$targetSection]) && !empty($this->EXTREF[$targetSection])) {
		 	if($_OPTB_WOC['LABEL'] == $targetLabel && in_array($targetLabel, $this->EXTREF[$targetSection])) {
				return '0000';	// as extref - -..
				//return $_OPTB_WOC['LOC'];
			}

		}
		else
			return false;
	}

	 /**
 * @param $_OPTB_WOC
 * @return mixed
 */
	private function chkEQUaddMode($_OPTB_WOC ) {
		if($_OPTB_WOC['OPERAND'] == "*") {
			$_OPTB_WOC['LOC'] = $this->formatNumber($_OPTB_WOC['LOC']);
			return $_OPTB_WOC;
		}
		else if(false) {
			//print_r($this->OPTB_WOC);
			echo "operaddddddddddd" . $_OPTB_WOC['OPERAND'] . "\n";

			$operand = preg_split('/[^A-Z]/', $_OPTB_WOC['OPERAND']);
			//print_r($operand);
			//exit;

			$locOne = array_search_value($this->OPTB_WOC, "LABEL", $operand[0], "LOC");
			$locTwo = array_search_value($this->OPTB_WOC, "LABEL", $operand[1], "LOC");
			$locOne = hexdec($locOne);
			$locTwo = hexdec($locTwo);
			$operation = substr($_OPTB_WOC['OPERAND'], strlen($operand[0]), strlen($operand[0])+1);
			switch($operation) {
				case '+':
					$loc = $locOne + $locTwo;
					break;
				case '-':
					$loc = $locOne - $locTwo;
				case '*':
					$loc = $locOne * $locTwo;
					break;
				case '/' :
					$loc = $locOne / $locTwo;
					break;
				default :
					$loc = "0000";
					echo "Wrong operation symbol in Symbol-Definint Statements\n";
					break;

			}
			// ** Important also assign the loc to the response line
			return dechex($loc);
		}
	}

	private function foundDefSym() {
		foreach($this->OPTB_WOC as $_OPTB_WOC) {
			if($_OPTB_WOC['OPCODE'] == 'EQU') {
				$_OPTB_WOC['LOC'] = $this->chkEQUaddMode($_OPTB_WOC);
				array_push($this->SYMTAB, array($_OPTB_WOC['LABEL'], $_OPTB_WOC['LOC'], $_OPTB_WOC['SECTION']));
			}
 		} // end foreach
	}



	/**************************************************************
	 *@ Addressing mode :
	 * calculation of target address TA and Flag bits sum
	 * more : read origin_file\addressingMode.xlsx
	 ***********************************************************/
	private function addModeVal($opcode, $loc, $n='',	 $i='', $x='', $b='', $p='', $e='') {
		//$loc =  base_convert($loc, 16, 10);
		//$loc = $loc + base_convert("1000000000000000",2,10);
		//$loc = base_convert($loc, 10, 16) . "\n";

		/*******************************************************
		 *@ Format 3 (3 bytes):
		 *---------------------------------------------------
		 * 6 	|1|1|1|1|1|1|			12			|
		 *--------------------------------------------------
		 * op	|n|i|x|b|p|e|			disp		|
		 *--------------------------------------------------
		@ Format 4 (4 bytes):
		 *---------------------------------------------------
		 * 6 	|1|1|1|1|1|1|			20			|
		 *--------------------------------------------------
		 * op	|n|i|x|b|p|e|			address		|
		 *---------------------------------------------------
		 *******************************************************/
		## @ important , make sure always each work have pre zero ...

		// compatibility for SIC


		if($this->computerType == "SIC") {

			if($loc == 0) $loc = "0000";

			$loc = hexdec($loc);
			$bin_loc = sprintf("%016b", $loc);
			//echo "1. ####loc : " . $bin_loc . "\n";
			$bin_loc = substr($bin_loc, 0,1) + $x . substr($bin_loc, 1);
			//echo "2. bin loc : " .  $bin_loc  . "\n";
			$dec_loc = bindec($bin_loc);
			//echo "3. dec loc : " .  $dec_loc  . "\n";
			$hex_loc = sprintf("%04.X", $dec_loc);
			//echo "4. hex loc : " .  $hex_loc  . "\n";
			$newloc = $opcode . $hex_loc;
			//echo "new loc : " . $newloc;

			return  $newloc;
		}


		else if($this->computerType == "SIC/XE") {
			$loc = hexdec($loc);

			// op code 4bit...
			$partOneA = base_convert(substr($opcode,0,1), 16, 10);

			// op code 2bit + n . i
			$flagN_I = base_convert($n.$i, 2, 10);
			$partOneB = base_convert(substr($opcode,1), 16, 10) + $flagN_I;

			// carry if need
			if( $partOneB > 15) $partOneA++;
			// formatting
			$partOne = sprintf("%01.X", $partOneA) . sprintf("%01.X", $partOneB);

			// flag bit x b p e
			$partTwo = base_convert($x.$b.$p.$e, 2, 10);
			$partTwo = sprintf("%01.X", $partTwo);

			// format 3 // address 12 bit
			if($e == 0) $partThree = sprintf("%03.X", ($loc));
			// format 4  // address 20 bit
			else $partThree = sprintf("%05.X",  ($loc));

			$newloc = $partOne . $partTwo . $partThree;
			return  $newloc;
		}

		// should never reach here
		return false;
	}

	/** @ Base relate part wrong
	 * @param $disp <- hex value
	 */
	private function chkAddMode($_OPTB_WOC, $TA,  $format4 = 0, $askAdd = false) {
		$addType = false;
		$LOC = $_OPTB_WOC['LOC'];
		$TA = base_convert($TA, 16, 10); // convert target address into base 10

		if($format4 ==0) {

			// Program counter relate
			 if (($PC = $this->find_PC($_OPTB_WOC)) == !false) {
				$PC = base_convert($PC, 16, 10);
				$disp = $TA - $PC;    // initial address for program counter relate check
	
					
				if (-2048 <= $disp && $disp <= 2047) {
					$addType = "PC";        //	b = 1, p = 0;
					if ($disp < 0 && $askAdd == true) {

						$disp *= -1;
						$disp = base_convert($disp, 10, 2);
						$disp = sprintf("%012d", $disp);
						//echo $disp . "\n";
						$disp = preg_replace('[0]', 'A', $disp);
						//echo $disp . "\n";
						$disp = preg_replace('[1]', '0', $disp);
						//echo $disp . "\n";
						$disp = preg_replace('[A]', '1', $disp);
						//echo $disp . "\n";
						$disp = base_convert($disp, 2, 10) + 1;
					}
					$add = $disp;
				}
				else if ($this->bReg != 'no_value') {
					$disp = $TA - base_convert($this->bReg, 16, 10);
					if (0 <= $disp && $disp <= 4095) {
						$addType = "B";    //	b = 0, p = 1;
						$add = $disp;
					} //
				}
			 }
				// Base relate
				//	maximum : 2^12 - 1
			else if ($this->bReg != 'no_value') {
				$disp = $TA - base_convert($this->bReg, 16, 10);
				echo "##############################\n";
				echo $disp;
				echo "\n#######################\n";
				if (0 <= $disp && $disp <= 4095) {
					$addType = "B";    //	b = 0, p = 1;
					$add = $disp;
				} //
			} // Base relate end
		} // end format 4
		else if($format4 == 1) {
			if (0 <= $TA && $TA <= 1048575 && $format4 == 1) {
				$addType = "EX";    //	b = 0, p = 0, e = 1;
				$add = $TA;
			}
		}

	if($askAdd == false) return $addType;
	if($askAdd == true) return base_convert($add,10,16);

	if($addType == false) {
		echo "Address : : : :. " . $TA . "\n";
		echo "\n : " . $TA . " out of address rage ";
		return false;
	}

	// not format 3 and 4.... and skip above else
	// should never reach here...
	echo "Unknown error found.\n";
	return false;
}

	/**
	 * @param $opcode
	 * @param $operand
     */
	private function formatTwoCode($opcode, $operand = '') {
		$objectCode = $opcode;

		$reg = array('A' => '0000', 'X' => '0001', 'L' => '0010', 'B' => '0011',
					'S' => '0100', 'T' => '0101', 'F' =>'0110', 'PC' => '1000', 'SW' => '1001');

		$operand = explode(',',$operand);
		$objectCode .= base_convert($reg[$operand[0]], 2, 16) ;

		if(isset($operand[1]) == false)
			$objectCode .= "0";
		else
			$objectCode .= base_convert($reg[$operand[1]], 2, 16);

		return $objectCode;
	}


	/** Variable guide
	 * $OPTAB['OPCODE'] represent the code in operation code table
	 * $opcode represnt the code in $_OPTB_WOC['OPCODE']	fix for both format 3 and format 4 +
	 */
	private function doPassTwo() {
		$this->foundDefSym();
		echo "doPassTwo();\n";
		$errorFlag = false;	## initial errorFlag as false -> no error found
		$errorMsg = "";	## store error message....
		/*********************************************************************
		 * Mnemonic	|Number	|Special use							*
		 * A		|	0	|Accumulator; used for arithmetic operations*
		 * X		|	1	|Index register; used for addressing		(
		 * L		|	2	|Linkage regsiter; the Jump to Subroutine(JSUB)
		 * 					 instruction stores the return address in the register
		 * B		|	3	|Base register; used for addressing
		 * S		|	4	|General working register--no special use
		 * T		|	5	|General working register--no special use
		 * F		|	6	|Floating-point accumulator(48 bits)
		 * PC		|	8	|Program counter; contains the address of the
		 * 					 next instruction to be fetched for execution
		 * SW		|	9	|Status word; contains a variety of information,
		 * 					 including a Condition Code(CC)
		 * *****************************************************************/
		//$xReg = 0;	//	actualy this should do in passOne..// I don't use index. don't break my code plesae
		$bReg = 0;		// Base register
		// $pcReg = 0; // invalid ...no use in here

		// find base register value first...

		if ( ($baseTA = array_search_value($this->OPTB_WOC, 'OPCODE', "BASE", 'OPERAND')) == !false
				&&   ( $bReg = array_search_value($this->OPTB_WOC, "LABEL" ,$baseTA, "LOC")) == !false ) {
			$this->bReg = $bReg;
			echo "BASE register value : " . $bReg . "\n";
			// exit();
			// end reading base address
		} else {
			$bReg = 'no_value';
			$this->bReg = $bReg;
			// exit();
		}

		// storage .. RESW and RESB are include in skip array
		$OPTB_SKIP = array(".","*","BASE","END","RESW","RESB","LTORG","EXTDEF","EXTREF");	## skip line with these label..
		$OPTB_WOC_afterPassTwo = array();

		foreach($this->OPTB_WOC as $_OPTB_WOC) {

			if(isset($_OPTB_WOC['OBJECTCODE']) && !empty($_OPTB_WOC['OBJECTCODE'])) $_OPTB_WOC['OBJECTCODE'] = '';

			if($_OPTB_WOC['LINE'] == 5 && $_OPTB_WOC['OPCODE'] == 'START') {
				array_push($OPTB_WOC_afterPassTwo, $_OPTB_WOC);	// keep it.. no change
				continue;	## read next line
			} // end read first input line
			else if($_OPTB_WOC['OPCODE'] == 'EXTDEF') {
				$extdef = explode(",", $_OPTB_WOC['OPERAND']);
				array_push($this->EXTDEF, $extdef);
				array_push($OPTB_WOC_afterPassTwo, $_OPTB_WOC);
				continue; // read next line
			}
			else if($_OPTB_WOC['OPCODE'] == 'EXTREF') {
				$extref = explode(",", $_OPTB_WOC['OPERAND']);
				array_push($OPTB_WOC_afterPassTwo, $_OPTB_WOC);
				array_push($this->EXTREF, $extref);
				continue; // read next line
			}
			else if( empty($_OPTB_WOC['LINE']) && empty($_OPTB_WOC['LOC']) && empty($_OPTB_WOC['LABEL']) && empty($_OPTB_WOC['OPCODE']) && empty($_OPTB_WOC['OPERAND']) && empty($_OPTB_WOC['OBJECTCODE']) ) {
				array_push($OPTB_WOC_afterPassTwo, $_OPTB_WOC);
				continue; // read next line
			}
			else if( array_search_value($this->OPTAB_, 'OPCODE', $_OPTB_WOC['OPCODE'], 'FORMAT') == '1') {
				$_OPTB_WOC['FORMAT'] = '1';
					$_OPTB_WOC['OBJECTCODE'] = array_search_value($this->OPTAB_, 'OPCODE', $_OPTB_WOC['OPCODE'], 'OPCODEVAL');
					$errorMsg .= "Line at ". $_OPTB_WOC['LINE']." : symbol ".$_OPTB_WOC['OPERAND']. " is undefined symbol\n";
					$this->errorLogPassTwo .= $errorMsg;
					$errorFlag = true;
				array_push($OPTB_WOC_afterPassTwo, $_OPTB_WOC);
				continue; // read next line
			}
			else if( array_search_value($this->OPTAB_, 'OPCODE', $_OPTB_WOC['OPCODE'], 'FORMAT') == '2') {
				$_OPTB_WOC['FORMAT'] = '2';
					$_OPTB_WOC['OBJECTCODE'] = array_search_value($this->OPTAB_, 'OPCODE', $_OPTB_WOC['OPCODE'], 'OPCODEVAL');
					$errorMsg .= "Line at ". $_OPTB_WOC['LINE']." : symbol ".$_OPTB_WOC['OPERAND']. " is undefined symbol\n";
					$this->errorLogPassTwo .= $errorMsg;
					$errorFlag = true;

					$_OPTB_WOC['OBJECTCODE'] = $this->formatTwoCode($_OPTB_WOC['OBJECTCODE'], $_OPTB_WOC['OPERAND']);
					array_push($OPTB_WOC_afterPassTwo, $_OPTB_WOC);
					continue; // read next line

			}
			//  special for Literals= =...
			else if($_OPTB_WOC['LABEL'] == '*' || empty($_OPTB_WOC['LINE'])) {
				$_OPTB_WOC['OBJECTCODE'] = $this->formatOPCODEVAL_STORAGE_BYTE(substr($_OPTB_WOC['OPCODE'],1));
				array_push($OPTB_WOC_afterPassTwo, $_OPTB_WOC);
				continue; // read next line
			}
			else if($_OPTB_WOC['LABEL'] == '*' && !empty($_OTB_WOC['LINE'])) {
				continue;
			}
			//if($_OPTB_WOC['OPCODE'] != 'END') {
			//else if(true){
				// getting base register value
				 if(in_array($_OPTB_WOC['OPCODE'], $OPTB_SKIP)) {
					array_push($OPTB_WOC_afterPassTwo, $_OPTB_WOC);
					continue; // read next line
				 }
				// storage opcode.....
				else if($_OPTB_WOC['OPCODE'] == 'BYTE') {
					## C'EOF' = 454F46 | X'F1" = F1 ... need a special function for this..
					$_OPTB_WOC['OBJECTCODE'] = $this->formatOPCODEVAL_STORAGE_BYTE($_OPTB_WOC['OPERAND']);	## store 0 as operand address
					array_push($OPTB_WOC_afterPassTwo,$_OPTB_WOC);
					continue;	// read next line
				}
				else if($_OPTB_WOC['OPCODE'] == 'WORD') {
					$_OPTB_WOC['OBJECTCODE'] = $this->formatOPCODEVAL_STORAGE(base_convert($_OPTB_WOC['OPERAND'], 10, 16));	## store 0 as operand address
					array_push($OPTB_WOC_afterPassTwo,$_OPTB_WOC);
					continue;	// read next line
				}
				else {
					// flag bit
					$_OPTB_WOC['N'] = $_OPTB_WOC['I'] = $_OPTB_WOC['X'] = $_OPTB_WOC['P'] = $_OPTB_WOC['B'] = $_OPTB_WOC['E'] = 0;

					if(substr($_OPTB_WOC['OPCODE'],0,1) == '+') {
						$_OPTB_WOC['FORMAT'] = '4';
						$opcode  = substr($_OPTB_WOC['OPCODE'],1);
						$_OPTB_WOC['B'] = 0;
						$_OPTB_WOC['P'] = 0;
						$_OPTB_WOC['E'] = 1;
					} else {
						$_OPTB_WOC['FORMAT'] = '3';
						$opcode = $_OPTB_WOC['OPCODE'];
						$_OPTB_WOC['E'] = 0;
					}


					// @c or @m Indirect addressing type
					if(substr($_OPTB_WOC['OPERAND'],0,1) == '@') { $_OPTB_WOC['N'] = 1;    $_OPTB_WOC['I'] = 0; }
					// #c or #m Immediate addressing type
					if(substr($_OPTB_WOC['OPERAND'],0,1) == '#') { $_OPTB_WOC['N'] = 0;    $_OPTB_WOC['I'] = 1; }
					/*****************************************************
					 * ## SIC/XE.. if neither indirect and immediate      *
					 *	so that is simple addressing type n = 1 , i = 1; *
					 *****************************************************/
					else  if( $_OPTB_WOC['N'] == 0 && $_OPTB_WOC['I'] == 0 ) { $_OPTB_WOC['N'] = $_OPTB_WOC['I'] = 1; }

					// c.X j,X  use index  register
					if(strpos($_OPTB_WOC['OPERAND'], ","))
						$_OPTB_WOC['X'] = 1;
					else
						$_OPTB_WOC['X'] = 0;

					// to gain readability
					$fN = $_OPTB_WOC['N'];
					$fI = $_OPTB_WOC['I'];
					$fX = $_OPTB_WOC['X'];
					$fB = $_OPTB_WOC['B'];
					$fP = $_OPTB_WOC['P'];
					$fE = $_OPTB_WOC['E'];

					$addr = false;
					## serach OPTAB for OPCODE
					// found that opcode in operation code table // if found
					if( ($OPTAB['OPCODEVAL'] = array_search_value($this->OPTAB_, 'OPCODE', $opcode, 'OPCODEVAL')) == !false ) {
						// do someting

						if(empty($_OPTB_WOC['OPERAND']))	//fix unknown bug= =
							$targetLOC = 0;

						else if ($_OPTB_WOC['X'] == 1) {
							$OPERAND = explode(",", $_OPTB_WOC['OPERAND']);
							$addTa = $OPERAND[0];    // addressing target..
							// need replace by find_LOC
							//$targetLOC = array_search_value($this->SYMTAB, "LABEL", $addTa, "LOC");
							$targetLOC = $this->find_LOC($_OPTB_WOC, $addTa);
						} // Simple addressing type without index resiger  also mean $_OPTB_WOC['X'] == 0
						else if ($_OPTB_WOC['N'] == 1 && $_OPTB_WOC['I'] == 1) {
							$addTa = $_OPTB_WOC['OPERAND'];
							// need replace by find_LOC
							//$targetLOC = array_search_value($this->SYMTAB, "LABEL", $addTa, "LOC");
							$targetLOC = $this->find_LOC($_OPTB_WOC);
							// echo "errorrrr : \n"; print("Line : " . $_OPTB_WOC['LINE'] . "\n"); // debug finished=.=
						} // Indirect addressing type
						else if ($_OPTB_WOC['N'] == 1 && $_OPTB_WOC['I'] == 0) {
							$addTa = substr($_OPTB_WOC['OPERAND'], 1);
							// need replace by find_LOC
							//$targetLOC = array_search_value($this->SYMTAB, "LABEL", $addTa, "LOC");
							$targetLOC = $this->find_LOC($_OPTB_WOC, $addTa);
						} // Immediate addressing
						else if ($_OPTB_WOC['N'] == 0 && $_OPTB_WOC['I'] == 1) {

							$addTa = substr($_OPTB_WOC['OPERAND'], 1);
							if (is_numeric($addTa)) {
								$targetLOC = base_convert($addTa, 10, 16);    // copy the value as loc
								$addr = true;
							}
							else {
								$addTa = substr($_OPTB_WOC['OPERAND'], 1);
								// need replace by find_LOC
								//$targetLOC = array_search_value($this->SYMTAB, "LABEL", $addTa, "LOC");
								$targetLOC = $this->find_LOC($_OPTB_WOC,$addTa);
							}
						}


						if(substr($_OPTB_WOC['OPERAND'], 0,1) == '=') {
							$targetLOC = array_search_value($this->OPTB_WOC, 'OPCODE', $_OPTB_WOC['OPERAND'], 'LOC');
						}


						if($addr == false) {
							if($fE == 0) {
								if ($this->chkAddMode($_OPTB_WOC ,$targetLOC,  $fE) == "B") {
									$add = $this->chkAddMode($_OPTB_WOC ,$targetLOC,  $fE, true);
									$_OPTB_WOC['B'] = 1;
									$_OPTB_WOC['P'] = 0;
								}
								 else if ($this->chkAddMode($_OPTB_WOC ,$targetLOC,  $fE) == "PC") {
									$add = $this->chkAddMode($_OPTB_WOC ,$targetLOC,  $fE, true);
									$_OPTB_WOC['B'] = 0;
									$_OPTB_WOC['P'] = 1;
								}

							} // end format 3

							else if($fE == 1 && $this->chkAddMode($_OPTB_WOC ,$targetLOC,  $fE) == "EX") {
								// I just forget .... final add this part at 12/28/2015 night = = ..
								 if ($_OPTB_WOC['X'] == 1) {
										$OPERAND = explode(",", $_OPTB_WOC['OPERAND']);
										$addTa = $OPERAND[0];    // addressing target..
									 // need replace by find_LOC
										//$targetLOC = array_search_value($this->SYMTAB, "LABEL", $addTa, "LOC");
									 $targetLOC = $this->find_LOC($this->SYMTAB,substr($_OPTB_WOC['OPERAND'],1));
									} // Simple addressing type without index resiger  also mean $_OPTB_WOC['X'] == 0

								else if($_OPTB_WOC['OPERAND']{0} == '#' || $_OPTB_WOC['OPERAND']{0} == '@') {
									// need replace by find_LOC
									//$targetLOC = array_search_value($this->SYMTAB, "LABEL", substr($_OPTB_WOC['OPERAND'],1), "LOC");
									$targetLOC = $this->find_LOC($_OPTB_WOC,substr($_OPTB_WOC['OPERAND'],1));
								}
								else
									// need replace by find_LOC
									//$targetLOC = array_search_value($this->SYMTAB, "LABEL", $_OPTB_WOC['OPERAND'], "LOC");
								$targetLOC = $this->find_LOC($_OPTB_WOC);

								//echo "targetLoc : " . $targetLOC;
								$add = $this->chkAddMode($_OPTB_WOC ,$targetLOC,  $fE, true);
								$_OPTB_WOC['B'] = $_OPTB_WOC['P'] = 0;

							} // end forat 4
							else
								echo "Error : chkdAddMode error found..\n";

							$_OPTB_WOC['ADD'] = $add;
						} // end addr



						$fB = $_OPTB_WOC['B'];
						$fP = $_OPTB_WOC['P'];
						$_OPTB_WOC['TA'] = $targetLOC;
						$_OPTB_WOC['OPCODEVAL'] = $OPTAB['OPCODEVAL'];

						if($this->computerType == 'SIC')
							$add =  $targetLOC;

						if($addr == true || !isset($addr))
							$_OPTB_WOC['addr'] = "true";
						else
							$_OTB_WOC['addr'] = "false";

						if(empty($_OPTB_WOC['OPERAND']) == false) {
							if($addr == !false) {
								$_OPTB_WOC['OBJECTCODE'] = $this->addModeVal($OPTAB['OPCODEVAL'] , $targetLOC, $fN, $fI, $fX, $fB, $fP, $fE);
							} else
								$_OPTB_WOC['OBJECTCODE'] = $this->addModeVal($OPTAB['OPCODEVAL'] , $add ,$fN, $fI, $fX, $fB, $fP, $fE);
						}
						else {
							if($this->computerType == 'SIC')
								$_OPTB_WOC['OBJECTCODE'] = sprintf("%02.X",(hexdec($OPTAB['OPCODEVAL']))) . "0000";
							else if($this->computerType == 'SIC/XE')
								$_OPTB_WOC['OBJECTCODE'] = sprintf("%02.X",(hexdec($OPTAB['OPCODEVAL']) + 3)) . "0000";
						}

					} // end ## serach OPTAB for OPCODE
					else {
						// tell no found...
					}
				}	// else end
				array_push($OPTB_WOC_afterPassTwo,$_OPTB_WOC);

			//} // end true
			// $_OPTB_WOC['OPCODE'] == 'END'
			//else {
			//	break; // break the foreach
			//}
	


		} // foreach end

		// fix unkonwn bug= =... haha this so useful to delete duplicate literals= =...
		$OPTB_WOC_afterPassTwoFix = array();
		$previous_label = 'no_value';
		$previous_line = 'no_value';
		$previous_loc = 'no_value';
		$previous_operand = 'no_value';
		$pervious_opcode = 'no_value';
		foreach($OPTB_WOC_afterPassTwo as $fixOPTB) {
			if($fixOPTB['LINE'] == $previous_line && $fixOPTB['LOC'] == $previous_loc && $fixOPTB['OPCODE'] == $pervious_opcode && $fixOPTB['OPERAND'] == $previous_operand)
				continue;
			else if($fixOPTB['LABEL'] == $previous_label && $fixOPTB['LABEL'] == '*' && $fixOPTB['OPCODE'] ==  $pervious_opcode)
				continue;
			else {
				$previous_label = $fixOPTB['LABEL'];
				$previous_line = $fixOPTB['LINE'];
				$previous_loc = $fixOPTB['LOC'];
				$pervious_opcode = $fixOPTB['OPCODE'];
				$previous_operand = $fixOPTB['OPERAND'];

				array_push($OPTB_WOC_afterPassTwoFix, $fixOPTB);
			}
		}


		$this->OPTB_WOC_afterPassTwo = $OPTB_WOC_afterPassTwoFix;
		$this->OPTB_WOC = $OPTB_WOC_afterPassTwoFix;
		//$this->OPTB_WOC = $OPTB_WOC_afterPassTwo;
		//print_r($this->OPTB_WOC);

		print_r($this->EXTDEF);
		print_r($this->EXTREF);

		return $this->doPassThree();
		//return $this->OPTB_WOC;

	} // function end

	private function sortByBlock($a, $b) {
		$a = $a['BLOCK'];
		$b = $b['BLOCK'];
		if ($a == $b) return 0;
		return ($a < $b) ? -1 : 1;
	}



	private function doPassThree() {
		if(max(array_column($this->OPTB_WOC,'BLOCK')) == 0) {
			return $this->OPTB_WOC;
		}
		else {

			$OPTB_WOCnoBlock = $this->OPTB_WOC;

			$index = 0;
			foreach($OPTB_WOCnoBlock as $_OPTB_WOCnoBlock) {
				$OPTB_WOCnoBlock[$index]['O_INDEX'] = $index;
				++$index;
			}


			$loc = array();
			$block = array();
			foreach ($OPTB_WOCnoBlock as $key => $row) {
				$loc[$key] = $row['LOC'];
				$block[$key] = $row['BLOCK'];
			}
			array_multisort($block, SORT_ASC, array_keys($OPTB_WOCnoBlock), SORT_ASC, $OPTB_WOCnoBlock);
			$debugg = $OPTB_WOCnoBlock;
			$OPTB_WOCnoBlock = array_array_key_filter($OPTB_WOCnoBlock, array('O_INDEX', 'LABEL', 'OPCODE', 'OPERAND'), true);


			$index = 0;
			$line = 5;
			foreach($OPTB_WOCnoBlock as $_OPTB_WOCnoBlock) {
				if( $_OPTB_WOCnoBlock['OPCODE'] == 'USE' ||  $_OPTB_WOCnoBlock['LABEL'] === '*' || $_OPTB_WOCnoBlock['OPCODE'] == 'END' ) {
					unset($OPTB_WOCnoBlock[$index]);
				}
				else {
					$OPTB_WOCnoBlock[$index]['LINE'] = $line;
					$line += 5;
					$OPTB_WOCnoBlock[$index]['LOC'] = '';
					$OPTB_WOCnoBlock[$index]['OBJECTCODE'] = '';
					$OPTB_WOCnoBlock[$index]['COMMENT'] = '';
				}
				++$index;
			}


			//print_r($OPTB_WOCnoBlock);
			$generateObjectCode = new GenerateObjectCode($OPTB_WOCnoBlock);
			$OPTB_WOCnoBlock = $generateObjectCode->getAfterPassTwo();

			// fix unknown bug..
			$index = 0;
			$pervious_O_INDEX= 'no_value';
			foreach($OPTB_WOCnoBlock as $_OPTB_WOCnoBlock) {
				if( $_OPTB_WOCnoBlock['OPCODE'] == 'LTORG') {
					unset($OPTB_WOCnoBlock[$index]);
				}
				/*
				if(isset($_OPTB_WOCnoBlock['O_INDEX'])) {
					if($_OPTB_WOCnoBlock['O_INDEX'] == $pervious_O_INDEX) {
						++$OPTB_WOCnoBlock[$index]['O_INDEX'];
					}
					$pervious_O_INDEX = $OPTB_WOCnoBlock[$index]['O_INDEX'];
				}
				*/
				////$this->OPTB_WOC[$_OPTB_WOCnoBlock['O_INDEX']]['realBLOCK'] = $OPTB_WOCnoBlock[$index]['BLOCK'];
				++$index;
			}

			$generateObjectCode->writeTxt(RESULT_PATH.OUTPUT_LOG_PREFIX."optbnoBlock-woc.txt");
			$this->writeTxt(RESULT_PATH.OUTPUT_LOG_PREFIX."optbnoHaveBlock-woc.txt");



			// the foreach solution fail.. for loop success . don't ask why . don't modify the code
			$previousO_INDEX = 'no_value';
			for ($i = 0; $i < count($OPTB_WOCnoBlock); ++$i) {
				if($previousO_INDEX == 'no_value' && trim($OPTB_WOCnoBlock[$i]['LABEL']) == '*'){
					$previousO_INDEX = $OPTB_WOCnoBlock[$i]['O_INDEX'];
				}
				if($previousO_INDEX != 'no_value' && trim($OPTB_WOCnoBlock[$i]['LABEL']) == '*') {
					$OPTB_WOCnoBlock[$i]['O_INDEX'] = ++$previousO_INDEX;
				}
			}


			//  $_OPTB_WOCnoBlock['OPERAND'] != '#MAXLEN')  fix unknow bug= =... kind of cheat-_-
			// so explain in other way. no idea why format 4 immediate address mode will fail in $OPTB_WOCnoBlock
			$index = 0;
			foreach($OPTB_WOCnoBlock as $_OPTB_WOCnoBlock) {
				if(!empty($_OPTB_WOCnoBlock['O_INDEX']) && substr($_OPTB_WOCnoBlock['OPERAND'],0,1) != '#') {
					$this->OPTB_WOC[$_OPTB_WOCnoBlock['O_INDEX']]['OBJECTCODE'] = $_OPTB_WOCnoBlock['OBJECTCODE'];
					$this->OPTB_WOC[$_OPTB_WOCnoBlock['O_INDEX']]['realLOC'] = $_OPTB_WOCnoBlock['LOC'];
				}
				++$index;
			}

			print_r_to_html(RESULT_PATH.OUTPUT_LOG_PREFIX."optbnoBlock-woc.html",$OPTB_WOCnoBlock);
			print_r_to_html(RESULT_PATH.OUTPUT_LOG_PREFIX."optbnoHaveBlock-woc.html",$this->OPTB_WOC);

			//$this->OPTB_WOC = $OPTB_WOCnoBlock;
			//$this->OPTB_WOC = $debugg;
			//$this->OPTB_WOC = $OPTB_WOCnoBlock;
			return $this->OPTB_WOC;
		}
	} // function end


	public function displayObjectCode()
	{
		if($this->errorFlag == false)
		{
			display_optb_woc($this->OPTB_WOC);
			return true;
		}
		else
		{
			echo $this->errorLogPassOne;
			echo "\n";
			//echo $this->errorLogPassTwo;
			return false;
		}

	}


	#public function disp


	public function writeTxtPassOne($filename)
	{
		if( do_overwrite($filename,false) == !false)
		{
			$content = "Line\tLoc\tSource statement\tObject code\t";
			$content .= "\r\n";
			if(max(array_column($this->OPTB_WOC, 'BLOCK')) == 0) {
				foreach ($this->OPTB_WOC_afterPassOne as $_OPTB_WOC) {
					$content .= $_OPTB_WOC['LINE'] . "\t" . $_OPTB_WOC['LOC'] . "\t" . $_OPTB_WOC['LABEL']
						. "\t" . $_OPTB_WOC['OPCODE'] . "\t" . $_OPTB_WOC['OPERAND'] . "\t" . $_OPTB_WOC['OBJECTCODE'];
					$content .= "\r\n";
				}
			}
			else {
				foreach ($this->OPTB_WOC_afterPassOne as $_OPTB_WOC) {
					$content .= $_OPTB_WOC['LINE'] . "\t" . $_OPTB_WOC['LOC'] . "\t" . $_OPTB_WOC['BLOCK'] . "\t" . $_OPTB_WOC['LABEL']
						. "\t" . $_OPTB_WOC['OPCODE'] . "\t" . $_OPTB_WOC['OPERAND'] . "\t" . $_OPTB_WOC['OBJECTCODE'];
					$content .= "\r\n";
				}
			}
			file_put_contents($filename, $content);
		}
	}

	public function writeTxt($filename)
	{
		if( do_overwrite($filename,false) == !false)
		{
			$content = "Line\tLoc\tSource statement\tObject code\t";
			$content .= "\r\n";
			if(max(array_column($this->OPTB_WOC, 'BLOCK')) == 0) {

				// fix bug-_-
				$column = array('LINE', 'LOC', 'LABEL', 'OPCODE', 'OPERAND', 'OBJECTCODE');
				foreach($column as $key) {
					if(!isset($_OPTB_WOC[$key])) $_OPTB_WOC[$key] = '';
				}


				foreach ($this->OPTB_WOC as $_OPTB_WOC) {
					if(empty($_OPTB_WOC['LOC'])) { $_OPTB_WOC['BLOCK'] = ''; $_OPTB_WOC['OBJECTCODE'] = '';	}// fix bug= =
					$content .= $_OPTB_WOC['LINE'] . "\t" . $_OPTB_WOC['LOC'] . "\t" . $_OPTB_WOC['LABEL']
						. "\t" . $_OPTB_WOC['OPCODE'] . "\t" . $_OPTB_WOC['OPERAND'] . "\t" . $_OPTB_WOC['OBJECTCODE'];
					$content .= "\r\n";
				}
			}
			else {
				foreach ($this->OPTB_WOC as $_OPTB_WOC) {
					if(empty($_OPTB_WOC['LOC'])) { $_OPTB_WOC['BLOCK'] = ''; $_OPTB_WOC['OBJECTCODE'] = '';	}// fix bug= =
					$content .= $_OPTB_WOC['LINE'] . "\t" . $_OPTB_WOC['LOC'] . "\t" . $_OPTB_WOC['BLOCK'] . "\t" . $_OPTB_WOC['LABEL']
						. "\t" . $_OPTB_WOC['OPCODE'] . "\t" . $_OPTB_WOC['OPERAND'] . "\t" . $_OPTB_WOC['OBJECTCODE'];
					$content .= "\r\n";
				}
			}
			file_put_contents($filename, $content);
		}
	}

	public function writeCsv($filename)
	{
		if( do_overwrite($filename,false) == !false)
		{

			if(max(array_column($this->OPTB_WOC, 'BLOCK')) == 0) {

				// fix bug-_-
				$column = array('LINE', 'LOC', 'LABEL', 'OPCODE', 'OPERAND', 'OBJECTCODE');
				foreach($column as $key) {
					if(!isset($_OPTB_WOC[$key])) $_OPTB_WOC[$key] = '';
				}


				$content = "LINE\tLOC\tLABEL\tOPCODE\tOPERAND\tOBJECTCODE";
				$content .= "\r\n";
				foreach ($this->OPTB_WOC as $_OPTB_WOC) {
					if(empty($_OPTB_WOC['LOC'])) { $_OPTB_WOC['BLOCK'] = ''; $_OPTB_WOC['OBJECTCODE'] = '';	}// fix bug= =
					$content .= $_OPTB_WOC['LINE'] . "," . $_OPTB_WOC['LOC'] . "," . $_OPTB_WOC['LABEL']
						. "," . $_OPTB_WOC['OPCODE'] . "," . $_OPTB_WOC['OPERAND'] . "," . $_OPTB_WOC['OBJECTCODE'];
					$content .= "\r\n";
				}
			}
			else {
				$content = "LINE\tLOC\tBLOCK\tLABEL\tOPCODE\tOPERAND\tOBJECTCODE";
				$content .= "\r\n";
				foreach($this->OPTB_WOC as $_OPTB_WOC) {
					if(empty($_OPTB_WOC['LOC'])) { $_OPTB_WOC['BLOCK'] = ''; $_OPTB_WOC['OBJECTCODE'] = '';	}// fix bug= =
					$content .= $_OPTB_WOC['LINE'] . "," . $_OPTB_WOC['LOC'] . "," . $_OPTB_WOC['BLOCK'] . "," . $_OPTB_WOC['LABEL']
						. "," . $_OPTB_WOC['OPCODE'] . "," . $_OPTB_WOC['OPERAND'] .  "," . $_OPTB_WOC['OBJECTCODE'];
					$content .= "\r\n";
				}
			}
			file_put_contents($filename, $content);
		}
	}


	function __destruct()
	{
		echo "Destroying " . $this->name. "\n";
	}

}

?>