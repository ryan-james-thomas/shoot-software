<?php
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
    if ($idx == -1) {
        $stations = array();
    } else {
        $stations = Station::deleteStation($stations,$idx);
    }
    
    print json_encode(Station::stationArray($stations));
    print DIVIDER;
    
    Station::saveData($stations,$machines);
    Shoot::updateStations($dir,$machines);
    
    for ($i=0;$i<sizeof($stations);$i++) {
        $stations[$i]->displayHTML(true,$i,"clr" . (($i%2)+1));
    }

?>