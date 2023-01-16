<?php
    //Reads machine database and sends HTML code with buttons to client
    require_once("MachineObject.php");
    require_once("FunctionList.php");

    $filename = filter_input(INPUT_POST,"filename");
    $machines = readMachineList($filename);
    $idx = filter_input(INPUT_POST,"id");
    if ($idx==-1) { //Send HTML for all machines
        $i=0;
        foreach ($machines as $item) {
            $item->displayHTMLbutton($i,"clr" . (($i%2)+1));
            $i++;         
        }
    } else {    //Send HTML for only specified machine
        $machines[$idx]->displayHTMLbutton($idx,"clr" . (($idx%2)+1));
    }
    
    
    
    
?>