<?php
    //Adds a new machine to the database and saves it to the text file
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
    
    $stations[$idx]->setUse(filter_input(INPUT_POST,"inUse"));
    Station::checkMachineUseAll($stations);
    
    //print json_encode($stations[$idx]->toArray($idx));
    print json_encode(Station::stationArray($stations));
    print DIVIDER;
    
    Station::saveData($stations,$machines);
    

?>