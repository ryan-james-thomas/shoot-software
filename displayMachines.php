<?php
    //Reads machine database and sends HTML code with buttons to client
    require_once("FunctionList.php");
    require_once("ShootingObjects.php");
    

    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    
    $machines = Machine::loadData();
    $idx = filter_input(INPUT_POST,"idx");
    if ($idx==-2) { //Send HTML for all machines
        $i=0;
        print json_encode(Machine::machineArray($machines));
        print DIVIDER;
        foreach ($machines as $item) {
            $item->displayHTMLbutton($i,"clr" . (($i%2)+1));
            $i++;         
        }
    } else {    //Send HTML for only specified machine
        print json_encode($machines[$idx]->toArray($idx));
        print DIVIDER;
        $machines[$idx]->displayHTMLbutton($idx,"clr" . (($idx%2)+1));
    }
    
    
?>