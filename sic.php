<?php

	#note shitty noting: SYMTB : symbol table
	#					 OPTB  : operand table (without object code)
	#				   OPTB_WOC : operand table (with object code)

	# include my config
	include_once("./config.php");
	
	$figureDirectory = getcwd() . "\\Figure\\";	## \\ -> \
	$exitProgram = false;

	function help_menu() {
		echo "------------------------------------------------------------------------\n";
		echo "1. Enter check for found out object code\n";
		echo "2. Enter SIC or SIC/XE code file name (include extension)\n";
		echo "3. Enter command statement type, default by SIC/XE\n";
		echo "\n";
		echo "\t Usage : check target_name [type]\n";
		echo "\t Example : check Figure2.1.txt SIC\n";
		echo "\t Example : check Figure2.5.txt SIC/XE\n";
		echo "\t Example : check Figure2.5.csv\n";

		echo "\n";
		echo "\n";
		echo "Other Usage :\n";

		$menuOption = array(
				array("show_progress", "show program progress"),
				array("show_file"	, "show files"		),
				array("delete_result", "delete result folder"),
				array("help"		, "show help menu"	),
				array("quit"		, "Exit the program"	),
				array("exit"		, "Exit the program"	),
				array(""			, "Exit the program"	),
				array("cls" 		, "Clear the scrren"	),
				array("optab"		,"Display the optable"	)
		);
		$menuMask = "%15s : %-s\n";
		foreach($menuOption as $_menuOption) {
			printf($menuMask, $_menuOption[0], $_menuOption[1]);
		}
	}


function show_progress() {
	echo "------------------------------------------------------------------------\n";
	echo "Program progress : " . PHP_EOL;
	$readmeFile = dirname(__FILE__).'/readme.txt';
	do_overwrite($readmeFile,false);
	$handle = @fopen($readmeFile, "r");
	if ($handle) {
		while (($buffer = fgets($handle, 4096)) !== false) {
			echo "  " . $buffer ;
		}
		if (!feof($handle)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($handle);
	}
}

	function delete_result() {
		delTree(RESULT_PATH);
	}

	function show_file() {

		$figureDirectory = str_replace('/', '\\', dirname(__FILE__).FIGURE_PATH);
		echo "\n";
		echo "Current Figure directory : ".$figureDirectory."\n";
		echo "The file in Figure direcotry \n";
		echo "\n";

		## just listing the program file...
		$programFileArr = array("symtb.csv");

		echo "Csv file : \n";
		$line = 0;
		foreach(glob(FIGURE_PATH."*.csv" ) as $fileName) {
			++$line;
			if($line %2 == 1) echo "\t";
			## avoid print out program files..
			if(in_array($fileName,$programFileArr) == false)
				echo "".basename($fileName)."\t";	## print out the file name in a path
			if($line % 2 == 0)
				echo "\n";
		} // end foreach

		echo "\n";

		echo "Txt file : \n";
		$line = 0;
		foreach(glob(FIGURE_PATH."*.txt" ) as $fileName) {
			++$line;
			if($line %2 == 1) echo "\t";
			## avoid print out program files..
			if(in_array($fileName,$programFileArr) == false)
				echo "".basename($fileName)."\t"; ## print out the file name in a path
			if($line % 2 == 0) echo "\n";
		} // end foreach
		echo "\n";
	} // end show_file function

	help_menu();

	while(!$exitProgram) {

		createFolder();

		echo "\n";
		echo "Enter your command : \n";
		## set fopen sources for system standard input
		$handle = fopen ("php://stdin","r");
		$command = fgets($handle);
		$command = trim($command);
		$command = preg_replace('/\s+/', ' ',$command); // Remove multiple whitespaces
		$argument = explode(" ", $command);

		if(count($argument) == 0) // avoid undefined variable
			$argument[2] = $argument[1] = $argument[0] = "";
		if(!isset($argument[2]))
			$argument[2] = '';

		$validCommand = array('quit', 'exit', 'show_progress', 'delete_result', 'help', 'show_file', 'cls', 'optab', '', 'check');

		if(!in_array($argument[0], $validCommand)) {
			echo "Invalid command\n";
			help_menu();
		}
		else if($argument[0] == '' || $argument[0] == "quit" || $argument[0] =="exit") {
			echo "";
			echo "\n";
			$exitProgram = true;
		}
		else if($argument[0] == 'show_progress') 	{show_progress(); }
		else if($argument[0] == 'delete_result') 	{delete_result(); }
		else if($argument[0] == 'help')	 			{help_menu(); }
		else if($argument[0] == 'show_file') 		{show_file(); }

		else if($argument[0] == "cls") 				{system("cls"); }## $exitProgram = false;
		else if($argument[0] == "optab") 			{display_optab(); } ## $exitProgram = false;

		else if($argument[0] == 'check') {	## determine the opcode table

			if(empty($argument[1])) {
				echo "Empty target_name\n";
				continue;
			}

			$ReadFile = new ReadFile; ## use my ReadFile class [ReadFile.php] to store the operand table into array and return it
			## seem the defaultDirectory never finish it..
			## let disable" absolution path " use in readFile
			if( ($OPTB = $ReadFile->readFile(FIGURE_PATH.$argument[1])) == false) {
				echo "Error in source file..\n";
			}
			else {

				$path_parts = pathinfo($argument[1]);
				$OUTPUT_LOG_PREFIX =  date("Y-m-d-His")."_".$path_parts['filename'].".".$path_parts['extension']."_";;

				echo "\n";
				## ## convert operand table into operand table(with object code)
				## ## fix into respond column...				
				$OPTB_WOC = optb_to_optb_woc ($OPTB);
				if($argument[2] == '') $argument[2] = 'SIC/XE';
				$GenerateObjectCode = new GenerateObjectCode($OPTB_WOC, $argument[2]);
				echo "\n";
				if($GenerateObjectCode->displayObjectCode() == false) {
					echo "Error, check your operand table\n";
				}

				// ObjectCode
				// Operation table with object code array to html
				$OPTB_WOC = $GenerateObjectCode->getObjectCode();
				$OPTB_passOne = $GenerateObjectCode->getAfterPassOne();

				print_r_to_html(RESULT_PATH.$OUTPUT_LOG_PREFIX."optb-woc.html", $OPTB_WOC,"Operation Table with Object Code");
				print_r_to_html(RESULT_PATH.$OUTPUT_LOG_PREFIX."optb-passOne.html", $OPTB_passOne,"Operation Table passOne");
				// Symbol table array into to html
				$SYMTAB = $GenerateObjectCode->getSYMTAB();
				print_r_to_html(RESULT_PATH.$OUTPUT_LOG_PREFIX."symtab.html", $SYMTAB,"Symbol Table");
				// generate result file
				$OPTB_WOCtxtFile = RESULT_PATH.$OUTPUT_LOG_PREFIX."objectcode.txt";
				$GenerateObjectCode->writeTxt($OPTB_WOCtxtFile);
				$GenerateObjectCode->writeCsv(RESULT_PATH.$OUTPUT_LOG_PREFIX."optb-woc.csv");
				$GenerateObjectCode->writeTxtPassOne(RESULT_PATH.$OUTPUT_LOG_PREFIX."optb-passOne.txt");
				// operation code array to html
				print_r_to_html(RESULT_PATH.$OUTPUT_LOG_PREFIX."optab.html", $OPTAB,"Operation Code Table");

				echo "You can review the result in sub folder \"result\"\n";

				// ObjectProgram
				$GenerateObjectProgram = new GenerateObjectProgram($OPTB_WOC);
				$obectProgramTxtFile = RESULT_PATH.$OUTPUT_LOG_PREFIX."object-program.txt";
				$GenerateObjectProgram->writeTxt($obectProgramTxtFile);

				//*********************************************************
				// find out wrong object code
				//						my generate code	compare to 	original object code
				$originalCodeFile = ORIGIN_FILE_PATH.$path_parts['filename'].'-objectcode.txt';
				if(file_exists($originalCodeFile)) {
					$orignalCode = csv_to_array($originalCodeFile,"\t");
					echo "Compare Target : " . ORIGIN_FILE_PATH.$path_parts['filename'].'-objectcode.txt'."\n";

					$comKey = array("LOC", "LABEL", "OPCODE", "OPERAND", "OBJECTCODE");
					$keep = true;
					$OPTB_WOC = array_array_key_filter($OPTB_WOC, $comKey, $keep);
					$orignalCode = array_array_key_filter($orignalCode, $comKey, $keep);
					//print_r($OPTB_WOC);
					//print_r($orignalCode);
					//print_r( array_diff($OPTB_WOC, $orignalCode) );

					echo "LOC Differentiations : \n";
					print_r( array_diff(array_column($OPTB_WOC, 'LOC'), array_column($orignalCode, 'LOC')) );
					echo "LABEL Differentiations : \n";
					print_r( array_diff(array_column($OPTB_WOC, 'LABEL'), array_column($orignalCode, 'LABEL')) );
					echo "OPCODE Differentiations : \n";
					print_r( array_diff(array_column($OPTB_WOC, 'OPCODE'), array_column($orignalCode, 'OPCODE')) );
					echo "OPERAND Differentiations : \n";
					print_r( array_diff(array_column($OPTB_WOC, 'OPERAND'), array_column($orignalCode, 'OPERAND')) );
					echo "OBJECTCODE Differentiations : \n";
					print_r( array_diff(array_column($OPTB_WOC, 'OBJECTCODE'), array_column($orignalCode, 'OBJECTCODE')) );
					// compare two files line by line
				}
				else {
					echo PHP_EOL;
					echo $originalCodeFile . PHP_EOL. 'File do not exist' . PHP_EOL;
					echo 'program could not check the code whether correct or not' . PHP_EOL;
				}




				echo "-----------------Diff show me the differ : ------------------\n";

				$originalOPTB_WOCFile = ORIGIN_FILE_PATH.$path_parts['filename'].'-objectcode.txt';
				if(file_exists($originalOPTB_WOCFile)) {
					$diffOPTB_WOC = Diff::compareFiles($originalOPTB_WOCFile, $OPTB_WOCtxtFile);
					echo Diff::toString($diffOPTB_WOC);
					create_file(RESULT_PATH.$OUTPUT_LOG_PREFIX."diff-OPTB_WOC.html", Diff::toHTML($diffOPTB_WOC));
					echo "\n";
				}
				else {
					echo $originalOPTB_WOCFile ."\n"  . 'do not exist' . "\n";
				}


				$orignalObjectPFile = ORIGIN_FILE_PATH.$path_parts['filename'].'-objectprogram.txt';
				if(file_exists($orignalObjectPFile)) {
					$diffObjectP = Diff::compareFiles($orignalObjectPFile, $obectProgramTxtFile);
					echo Diff::toString($diffObjectP);
					create_file(RESULT_PATH.$OUTPUT_LOG_PREFIX."diff-ObjectProgram.html", Diff::toHTML($diffObjectP));
					echo "\n";
				}
				else {
					echo $orignalObjectPFile ."\n" . 'do not exist' . "\n";
				}

				echo "-------------------------Diff end ----------------------------\n";




				compare_array_in_html_js(RESULT_PATH.$OUTPUT_LOG_PREFIX."compare_html_js.html", $OPTB_WOC, $orignalCode, "Compare Array", false);

				
			}	## end ## print out OPTB
			
		}	## end ## determine the opcode table
	}	## end ## while


	echo "\n";
	echo "------------------------------------------------------------------------\n";

?>