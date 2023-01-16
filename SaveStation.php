<?php

    require_once("FunctionList.php");
    require_once("ShootingObjects.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $machines = Machine::loadData();
    $dir->toEvent();
    $stations = Station::loadData($machines);
    $idx = (int)filter_input(INPUT_POST,"idx");
    
    $stations[$idx] = new Station();
    $stations[$idx]->getInput($machines);
    $err = $stations[$idx]->checkStnErrorAll($stations,$idx);
    
    if ($err["noErr"]) {
        Station::saveData($stations,$machines);
        Shoot::updateStations($dir,$machines);
    }

    print json_encode($err);

?>