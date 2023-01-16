<?php
    //Sorts stations by name and saves to databse
    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    $dir->toEvent();
    $stations = Station::loadData($machines);
    
    
    usort($stations,"stncmp");
    print json_encode(Station::stationArray($stations));
    print DIVIDER;
    
    Station::saveData($stations,$machines);
    
    $i=0;
    foreach ((array)$stations as $item) {
        $item->displayHTML(true,$i,"clr" . (($i%2)+1));
        $i++;         
    }




?>