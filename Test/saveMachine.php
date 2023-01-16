<?php
    //Adds a new machine to the database and saves it to the text file
    require_once("MachineObject.php");
    require_once("FunctionList.php");
    
    $filename = filter_input(INPUT_POST,"filename");
    $machines = readMachineList($filename);
    $idx = filter_input(INPUT_POST,"idx");
    
    $tmp = new Machine();
    $machines[$idx] = $tmp;
    
    $machines[$idx]->setName(filter_input(INPUT_POST,"name"));
    $machines[$idx]->setType(filter_input(INPUT_POST,"type"));
    $machines[$idx]->setSize(filter_input(INPUT_POST,"size"));
    $machines[$idx]->setNumThrown(filter_input(INPUT_POST,"numThrown"));
    $machines[$idx]->setOwner(filter_input(INPUT_POST,"owner"));
    $machines[$idx]->setNotes(filter_input(INPUT_POST,"notes"));
    
    $fid = fopen($filename,"w");
    foreach($machines as $item) {
        fwrite($fid,$item->displayText());
    }
    fclose($fid);
    $machines[$idx]->displayHTMLbutton($idx,"clr" . (($idx%2)+1));

?>