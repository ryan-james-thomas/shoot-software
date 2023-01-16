<?php
    //Sends machine data to client
    require_once("MachineObject.php");
    require_once("FunctionList.php");

    $filename = filter_input(INPUT_POST,"filename");
    $machines = readMachineList($filename);
    
    $idx = filter_input(INPUT_POST,"id");
    print json_encode($machines[$idx]->toArray($idx));

?>