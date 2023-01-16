<?php
    //Reads machine database and sends HTML code with buttons to client
    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();    
    $dir->toShoot();
    $machines = Machine::loadData();
    $dir->toEvent();
    $stations = Station::loadData($machines);
    $idx = filter_input(INPUT_POST,"idx");
    print json_encode(Station::stationArray($stations));
    print DIVIDER;
    if ($idx==-2) { //Send HTML for all machines
        $i=0;
        //print json_encode(Station::stationArray($stations));
        //print DIVIDER;
        foreach ($stations as $item) {
            $item->displayHTML(true,$i,"clr" . (($i%2)+1));
            $i++;         
        }
    } else {    //Send HTML for only specified machine
        //print json_encode($stations[$idx]->toArray($idx));
        //print DIVIDER;
        $stations[$idx]->displayHTML(false,$idx,"clr" . (($idx%2)+1));
    }
    
    
?>