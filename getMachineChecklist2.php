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
        $eventData = $machine->getEventData();
        $totalBoxes = 0;

print<<<HERE
<div class="Full" style="font-size: small">
Name: <strong>$name</strong><br/>
Type: $type<br/>
Carousel size: $size<br/>
Notes: $notes<br/>
<table style="font-size: small">
<tr>
    <th>Event Name</th>
    <th>Station used</th>
    <th>Boxes with filled carousel</th>
</tr>
HERE;
    for ($i=0;$i<sizeof($eventData);$i++) {
        $event = $eventData[$i];
        $eName = $event["eName"];
        $sName = $event["sName"];
        $numTargets = $event["numTargets"];
        $numBoxes = round($numTargets/getTargetsPerBox($type),1);
        $remBoxes = round(($numTargets-$size)/getTargetsPerBox($type),1);
        $remBoxes = ($remBoxes>=0)?$remBoxes:0;
        $totalBoxes += $numBoxes;
        
print<<<HERE
<tr>
    <td>$eName</td>
    <td>$sName</td>
    <td>$remBoxes</td>
</tr>
HERE;
    }
    print "</table>\n";  
    $remBoxes = round($totalBoxes-($size)/getTargetsPerBox($type),1);
print<<<HERE
Total number of boxes: <strong>$totalBoxes</strong><br/>
Number of boxes with filled carousel: <strong>$remBoxes</strong><br/>
</div>
HERE;

    }




?>