<?php

	## this just output the detail..
	/*
	$row = 1;
	if (($handle = fopen("opcode.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            echo $data[$c] . "<br />\n";
        }
    }
    fclose($handle);
	}
	*/
	## I think this quite stupid, let store than in csx..
	## this file for storing the op code in sic and sic/se machine
	/*
	$opcodeArr = array
	(
		##	Mnemonic  	Format	Opcode		Effect						Notes
		array("ADD"		,	"3/4"	,	"18", 	"A <-- (A) + (m..m+2)"				,	""		);
		array("ADDF"	,	"3/4"	,	"58", 	"F <-- (F) + (m..m+5)"				,	"X F"	);
		array("ADDR"	,   "2"		,  	"90",	"r2 <-- (r2) + (r1)"				,	"X"		);
		array("AND"		,	"3/4"	,	"40",	"A <-- (A) & (m..m+2)"				,	""		);
		array("CLEAR"	,	"2"		, 	"B4",	"r1 <-- 0"							,	"X"		);
		array("COMP"	,	"3/4"	, 	"28",	"(A) : (m..m+2)"					,	"    C"	);
		array("COMPF"	,	"3/4"	, 	"88",	"(F) : (m..m+5)"					,	"X F C"	);
		array("COMPR"	,	"2"		, 	"A0",	"(r1) : (r2)"						,	"X   C"	);
		array("DIV"		,	"3/4"	,	"24",	"A <-- (A) / (m..m+2)"				,	""		);
		array("DIVF"	,	"3/4"	,	"64",	"F <-- (F) / (m..m+5)"				,	"X F"	);
		array("DIVR"	,	"2"		,	"9C",	"r2 <-- (r2) / (r1)"				,	"X"		);
		array("FIX"		,	"1"		,	"C4",	"A <-- (F) [convert to integer]"	,	"X F"	);
		array("FLOAT"	,	"1"		,	"C0",	"A <-- (F) [convert to floating]"	,	"X F"	);
		array("HIO"		,	"1"		,	"F4",	"A <-- (F) [convert to floating]"	,	"X F"	);
		## I think this quite stupid, let store than in csx..
	)
	*/
	
?>