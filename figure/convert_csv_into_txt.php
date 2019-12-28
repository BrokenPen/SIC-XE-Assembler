<?php
    require_once("../function/do_overwrite.php");
    require_once("./write_txt.php");
    require_once("../function/csv_to_array.php");

    $handle = fopen ("php://stdin","r");
    $file = fgets($handle);
    $file = trim($file);

    $figure = csv_to_array($file, ",");

    //unset($figure['LINE']);
    //unset($figure['COMMENT']);

    //print_r($figure);

    write_txt(basename($file,".csv").".txt", $figure);



?>