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
    $event = new ShootEvent();
    $event->loadData();
    $event->setStations($stations);
    
    print json_encode($event->toArray());
    print DIVIDER;
    
    $event->dispTargetTable();
    
    print "---STATION SUMMARY BELOW---\n";
    
    $event->dispStationSummary();
    
?>