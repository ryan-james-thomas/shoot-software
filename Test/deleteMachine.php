<?php
    require_once("MachineObject.php");
    require_once("FunctionList.php");
    
    $filename = filter_input(INPUT_POST,"filename");
    $machines = readMachineList($filename);
    $idx = filter_input(INPUT_POST,"id");
    
    $fid = fopen($filename,"w");
    $count = 0;
    for ($i=0;$i<sizeof($machines);$i++) {
        if ($i != $idx) {
            fwrite($fid,$machines[$i]->displayText());
            $machines[$i]->displayHTMLbutton($count,"clr" . (($count%2)+1));
            $count++;
        }
    }
    fclose($fid);

?>