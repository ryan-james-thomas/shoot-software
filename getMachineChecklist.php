<?php

    require_once("MachineObject.php");
    require_once("ShootingObjects.php");
    require_once("FunctionList.php");
    
    $dir = new DirList();
    $dir->getInput();
    $dir->toShoot();
    $shoot = new Shoot();
    $shoot->loadData();
    $shoot->loadEvents($dir);
    
    $machines = $shoot->getMachineTargets();
    
    $count = 0;
    foreach ($machines as $machine) {
        $name = $machine->getName();
        $type = $machine->getType();
        $size = $machine->getSize();
        $notes = $machine->getNotes();
        $stnNames = $machine->getStationNames();
        $totalTargets = $machine->getTotalTargets();
        $totalBoxes = round($totalTargets/getTargetsPerBox($type),1);
        $remBoxes = round(($totalTargets - $size)/getTargetsPerBox($type),1);
        $remBoxes = ($remBoxes>=0)?$remBoxes:0;
        $numEvents = $machine->getNumEvents();
        $eventNames = implode(", ",$machine->getEventNames());
        
        
print<<<HERE
<div class="Full">
Name: <strong>$name</strong><br/>
Type: $type<br/>
Notes: $notes<br/>
Events: $eventNames<br/>
Associated stations: $stnNames<br/>
Carousel size: $size<br/>
Total number of boxes: <strong>$totalBoxes</strong><br/>
Number of boxes with filled carousel: <strong>$remBoxes</strong><br/>
</div>

HERE;
    }




?>